<?php

namespace App\Services\Delivery;

use App\Models\Delivery;
use App\Models\Rider;
use App\Models\Subscription;
use App\Events\Delivery\DeliveryAssigned;
use App\Events\Delivery\DeliveryCompleted;
use App\Events\Delivery\DeliveryMissed;
use App\Services\Wallet\WalletService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeliveryService
{
    public function __construct(private WalletService $walletService) {}

    public function assignRider(Delivery $delivery, Rider $rider): Delivery
    {
        $delivery->update([
            'rider_id'    => $rider->id,
            'status'      => 'assigned',
            'assigned_at' => now(),
        ]);

        event(new DeliveryAssigned($delivery, $rider));

        return $delivery->refresh();
    }

    public function bulkAssign(array $deliveryIds, Rider $rider): int
    {
        $count = Delivery::whereIn('id', $deliveryIds)
            ->whereIn('status', ['scheduled', 'pending'])
            ->update([
                'rider_id'    => $rider->id,
                'status'      => 'assigned',
                'assigned_at' => now(),
            ]);

        // Fire events for each delivery
        Delivery::whereIn('id', $deliveryIds)->with('rider')->each(function ($delivery) use ($rider) {
            event(new DeliveryAssigned($delivery, $rider));
        });

        return $count;
    }

    public function startDelivery(Delivery $delivery): Delivery
    {
        $delivery->update([
            'status'     => 'in_progress',
            'started_at' => now(),
        ]);

        return $delivery->refresh();
    }

    public function complete(Delivery $delivery, array $data): Delivery
    {
        return DB::transaction(function () use ($delivery, $data) {
            $delivery->update([
                'status'              => 'delivered',
                'delivered_at'        => now(),
                'otp_verified'        => $data['otp_verified'] ?? false,
                'proof_image'         => $data['proof_image'] ?? null,
                'delivery_latitude'   => $data['latitude'] ?? null,
                'delivery_longitude'  => $data['longitude'] ?? null,
                'rider_notes'         => $data['rider_notes'] ?? null,
            ]);

            // Mark all items as delivered
            $delivery->items()->update(['is_delivered' => true]);

            // Debit wallet for delivery cost
            if ($delivery->total_amount > 0 && !$delivery->is_paid) {
                try {
                    $this->walletService->processDeliveryPayment(
                        $delivery->user,
                        (float) $delivery->total_amount,
                        $delivery->id
                    );
                    $delivery->update(['is_paid' => true]);
                } catch (\RuntimeException $e) {
                    // Log insufficient balance — don't block delivery completion
                    \Log::warning("Wallet debit failed for delivery #{$delivery->id}: {$e->getMessage()}");
                }
            }

            // Update rider stats
            if ($delivery->rider) {
                $delivery->rider->increment('total_deliveries');
                $delivery->rider->increment('successful_deliveries');
            }

            event(new DeliveryCompleted($delivery));

            return $delivery->refresh();
        });
    }

    public function markMissed(Delivery $delivery, string $reason): Delivery
    {
        $delivery->update([
            'status'        => 'missed',
            'missed_reason' => $reason,
        ]);

        if ($delivery->rider) {
            $delivery->rider->increment('total_deliveries');
        }

        event(new DeliveryMissed($delivery));

        return $delivery->refresh();
    }

    public function verifyOtp(Delivery $delivery, string $otp): bool
    {
        if ($delivery->delivery_otp === $otp) {
            $delivery->update(['otp_verified' => true]);
            return true;
        }
        return false;
    }

    public function generateDailyDeliveries(int $tenantId, ?Carbon $date = null): array
    {
        $date = $date ?? today();

        $subscriptions = Subscription::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->where('starts_at', '<=', $date)
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $date))
            ->with('activeItems.product')
            ->get();

        $generated = [];

        foreach ($subscriptions as $subscription) {
            // Check if skipped
            if ($subscription->isSkippedOn($date)) continue;

            // Check if delivery already exists
            $exists = Delivery::where('subscription_id', $subscription->id)
                ->where('delivery_date', $date->toDateString())
                ->exists();

            if (!$exists) {
                $generated[] = $this->createDeliveryForSubscription($subscription, $date);
            }
        }

        return $generated;
    }

    private function createDeliveryForSubscription(Subscription $subscription, Carbon $date): Delivery
    {
        $delivery = Delivery::create([
            'uuid'            => \Str::uuid(),
            'tenant_id'       => $subscription->tenant_id,
            'subscription_id' => $subscription->id,
            'user_id'         => $subscription->user_id,
            'address_id'      => $subscription->address_id,
            'delivery_date'   => $date,
            'scheduled_time'  => $subscription->preferred_delivery_time,
            'status'          => 'scheduled',
            'delivery_otp'    => (string) random_int(100000, 999999),
            'qr_code_token'   => \Str::random(32),
            'total_amount'    => $subscription->total_amount,
        ]);

        foreach ($subscription->activeItems as $item) {
            $delivery->items()->create([
                'product_id'   => $item->product_id,
                'variant_id'   => $item->variant_id,
                'product_name' => $item->product->name,
                'quantity'     => $item->quantity,
                'unit_price'   => $item->unit_price,
                'subtotal'     => $item->subtotal,
            ]);
        }

        return $delivery;
    }

    public function getDailyList(int $tenantId, string $date, ?int $riderId = null): \Illuminate\Database\Eloquent\Collection
    {
        return Delivery::where('tenant_id', $tenantId)
            ->where('delivery_date', $date)
            ->when($riderId, fn ($q) => $q->where('rider_id', $riderId))
            ->with(['user', 'address', 'items.product', 'rider'])
            ->orderBy('status')
            ->get();
    }
}

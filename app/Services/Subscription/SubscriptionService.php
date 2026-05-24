<?php

namespace App\Services\Subscription;

use App\Models\Subscription;
use App\Models\Delivery;
use App\Models\SubscriptionSkip;
use App\Events\Subscription\SubscriptionCreated;
use App\Events\Subscription\SubscriptionPaused;
use App\Events\Subscription\SubscriptionCancelled;
use App\DTOs\Subscription\CreateSubscriptionDTO;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SubscriptionService
{
    public function create(CreateSubscriptionDTO $dto): Subscription
    {
        $subscription = Subscription::create([
            'uuid'                   => Str::uuid(),
            'tenant_id'              => $dto->tenantId,
            'user_id'                => $dto->userId,
            'address_id'             => $dto->addressId,
            'frequency'              => $dto->frequency,
            'delivery_days'          => $dto->deliveryDays,
            'preferred_delivery_time' => $dto->preferredDeliveryTime,
            'starts_at'              => $dto->startsAt,
            'ends_at'                => $dto->endsAt,
            'next_delivery_date'     => $this->calculateNextDeliveryDate($dto),
            'status'                 => 'active',
            'total_amount'           => 0,
            'notes'                  => $dto->notes,
        ]);

        foreach ($dto->items as $item) {
            $subscription->items()->create([
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'quantity'   => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal'   => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $subscription->update(['total_amount' => $subscription->items->sum('subtotal')]);

        event(new SubscriptionCreated($subscription));

        return $subscription->load('items.product');
    }

    public function pause(Subscription $subscription, ?string $pauseUntil = null): Subscription
    {
        $subscription->update([
            'status'     => 'paused',
            'paused_at'  => now(),
            'pause_until' => $pauseUntil ? Carbon::parse($pauseUntil) : null,
        ]);

        event(new SubscriptionPaused($subscription));

        return $subscription->refresh();
    }

    public function resume(Subscription $subscription): Subscription
    {
        $subscription->update([
            'status'      => 'active',
            'paused_at'   => null,
            'pause_until' => null,
            'next_delivery_date' => $this->calculateNextDeliveryDate(
                new CreateSubscriptionDTO(
                    tenantId: $subscription->tenant_id,
                    userId: $subscription->user_id,
                    addressId: $subscription->address_id,
                    frequency: $subscription->frequency,
                    deliveryDays: $subscription->delivery_days,
                    startsAt: now()->toDateString(),
                )
            ),
        ]);

        return $subscription->refresh();
    }

    public function cancel(Subscription $subscription, string $reason): Subscription
    {
        $subscription->update([
            'status'                => 'cancelled',
            'cancelled_at'          => now(),
            'cancellation_reason'   => $reason,
        ]);

        // Cancel pending scheduled deliveries
        $subscription->deliveries()
            ->where('delivery_date', '>=', today())
            ->whereIn('status', ['scheduled', 'pending'])
            ->update(['status' => 'cancelled']);

        event(new SubscriptionCancelled($subscription));

        return $subscription->refresh();
    }

    public function skipDate(Subscription $subscription, string $date, string $reason = ''): SubscriptionSkip
    {
        $skip = $subscription->skips()->firstOrCreate(
            ['skip_date' => $date],
            ['user_id' => $subscription->user_id, 'reason' => $reason, 'type' => 'customer_skip']
        );

        // Cancel the delivery for that date if it exists
        $subscription->deliveries()
            ->where('delivery_date', $date)
            ->whereIn('status', ['scheduled', 'pending', 'assigned'])
            ->update(['status' => 'cancelled']);

        return $skip;
    }

    public function generateDeliveries(Subscription $subscription, Carbon $fromDate, Carbon $toDate): array
    {
        $deliveries = [];
        $current = $fromDate->copy();
        $skippedDates = $subscription->skips()
            ->whereBetween('skip_date', [$fromDate, $toDate])
            ->pluck('skip_date')
            ->map(fn ($d) => $d->toDateString())
            ->toArray();

        while ($current->lte($toDate)) {
            if ($this->isDeliveryDay($subscription, $current) && !in_array($current->toDateString(), $skippedDates)) {
                // Avoid duplicate deliveries
                $existing = Delivery::where('subscription_id', $subscription->id)
                    ->where('delivery_date', $current->toDateString())
                    ->exists();

                if (!$existing) {
                    $deliveries[] = $this->createDelivery($subscription, $current->copy());
                }
            }
            $current->addDay();
        }

        return $deliveries;
    }

    private function createDelivery(Subscription $subscription, Carbon $date): Delivery
    {
        $delivery = Delivery::create([
            'uuid'            => Str::uuid(),
            'tenant_id'       => $subscription->tenant_id,
            'subscription_id' => $subscription->id,
            'user_id'         => $subscription->user_id,
            'address_id'      => $subscription->address_id,
            'delivery_date'   => $date,
            'scheduled_time'  => $subscription->preferred_delivery_time,
            'status'          => 'scheduled',
            'delivery_otp'    => random_int(100000, 999999),
            'qr_code_token'   => Str::random(32),
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

    private function isDeliveryDay(Subscription $subscription, Carbon $date): bool
    {
        return match ($subscription->frequency) {
            'daily'    => true,
            'weekly'   => in_array($date->dayOfWeekIso, $subscription->delivery_days ?? [1, 2, 3, 4, 5, 6, 7]),
            'biweekly' => $date->weekOfYear % 2 === 0,
            'monthly'  => $date->day === 1,
            'custom'   => in_array($date->dayOfWeekIso, $subscription->delivery_days ?? []),
            default    => false,
        };
    }

    private function calculateNextDeliveryDate(CreateSubscriptionDTO $dto): string
    {
        $start = Carbon::parse($dto->startsAt);
        $current = $start->gte(today()) ? $start : today();

        for ($i = 0; $i < 30; $i++) {
            if ($this->isDeliveryDayForFrequency($dto->frequency, $current, $dto->deliveryDays)) {
                return $current->toDateString();
            }
            $current->addDay();
        }

        return $start->toDateString();
    }

    private function isDeliveryDayForFrequency(string $frequency, Carbon $date, ?array $days): bool
    {
        return match ($frequency) {
            'daily'  => true,
            'weekly', 'custom' => in_array($date->dayOfWeekIso, $days ?? [1]),
            'biweekly' => $date->weekOfYear % 2 === 0,
            'monthly'  => $date->day === 1,
            default    => false,
        };
    }
}

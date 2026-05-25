<?php

namespace App\Jobs\Notification;

use App\Models\Delivery;
use App\Notifications\Customer\DeliveryCompletedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDeliveryReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(public readonly int $deliveryId) {}

    public function handle(): void
    {
        $delivery = Delivery::with(['user', 'items.product', 'invoice'])->find($this->deliveryId);

        if (!$delivery) {
            Log::warning("SendDeliveryReceiptJob: delivery #{$this->deliveryId} not found.");
            return;
        }

        if (!$delivery->user) {
            return;
        }

        $delivery->user->notify(new DeliveryCompletedNotification($delivery));
    }

    public function failed(\Throwable $e): void
    {
        Log::error("SendDeliveryReceiptJob failed for delivery #{$this->deliveryId}: {$e->getMessage()}");
    }
}

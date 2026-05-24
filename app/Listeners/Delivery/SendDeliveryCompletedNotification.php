<?php

namespace App\Listeners\Delivery;

use App\Events\Delivery\DeliveryCompleted;
use App\Jobs\Wallet\ProcessWalletDeductionJob;
use App\Notifications\Customer\DeliveryCompletedNotification;

class SendDeliveryCompletedNotification
{
    public function handle(DeliveryCompleted $event): void
    {
        $delivery = $event->delivery;

        // Notify customer
        $delivery->user->notify(new DeliveryCompletedNotification($delivery));
    }
}

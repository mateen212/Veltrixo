<?php

namespace App\Listeners\Delivery;

use App\Events\Delivery\DeliveryMissed;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Customer\DeliveryMissedNotification;

class SendDeliveryMissedNotification implements ShouldQueue
{
    public string $queue = 'notifications';

    public function handle(DeliveryMissed $event): void
    {
        $delivery = $event->delivery;

        if ($delivery->user) {
            $delivery->user->notify(new DeliveryMissedNotification($delivery));
        }
    }
}

<?php

namespace App\Listeners\Delivery;

use App\Events\Delivery\DeliveryAssigned;
use App\Notifications\Rider\NewDeliveryAssignedNotification;

class SendDeliveryAssignedNotification
{
    public function handle(DeliveryAssigned $event): void
    {
        $event->rider->user->notify(new NewDeliveryAssignedNotification($event->delivery));
    }
}

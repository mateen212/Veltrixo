<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\SubscriptionCreated;
use App\Notifications\Customer\SubscriptionConfirmedNotification;

class SendSubscriptionConfirmation
{
    public function handle(SubscriptionCreated $event): void
    {
        $event->subscription->user->notify(
            new SubscriptionConfirmedNotification($event->subscription)
        );
    }
}

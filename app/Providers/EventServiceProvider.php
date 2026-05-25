<?php

namespace App\Providers;

use App\Events\Delivery\DeliveryAssigned;
use App\Events\Delivery\DeliveryCompleted;
use App\Events\Delivery\DeliveryMissed;
use App\Events\Subscription\SubscriptionCancelled;
use App\Events\Subscription\SubscriptionCreated;
use App\Events\Subscription\SubscriptionPaused;
use App\Events\Wallet\LowBalanceDetected;
use App\Events\Wallet\WalletCredited;
use App\Events\Wallet\WalletDebited;
use App\Listeners\Delivery\SendDeliveryAssignedNotification;
use App\Listeners\Delivery\SendDeliveryCompletedNotification;
use App\Listeners\Delivery\SendDeliveryMissedNotification;
use App\Listeners\Subscription\SendSubscriptionConfirmation;
use App\Listeners\Subscription\SendSubscriptionPausedNotification;
use App\Listeners\Wallet\SendLowBalanceNotification;
use App\Listeners\Wallet\SendWalletCreditedNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SubscriptionCreated::class => [
            SendSubscriptionConfirmation::class,
        ],
        SubscriptionPaused::class => [
            SendSubscriptionPausedNotification::class,
        ],
        SubscriptionCancelled::class => [],

        DeliveryAssigned::class => [
            SendDeliveryAssignedNotification::class,
        ],
        DeliveryCompleted::class => [
            SendDeliveryCompletedNotification::class,
        ],
        DeliveryMissed::class => [
            SendDeliveryMissedNotification::class,
        ],

        WalletCredited::class => [
            SendWalletCreditedNotification::class,
        ],
        WalletDebited::class  => [],
        LowBalanceDetected::class => [
            SendLowBalanceNotification::class,
        ],
    ];

    public function boot(): void {}

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

<?php

namespace App\Listeners\Wallet;

use App\Events\Wallet\LowBalanceDetected;
use App\Notifications\Customer\LowBalanceNotification;

class SendLowBalanceNotification
{
    public function handle(LowBalanceDetected $event): void
    {
        $event->wallet->user->notify(new LowBalanceNotification($event->wallet));
    }
}

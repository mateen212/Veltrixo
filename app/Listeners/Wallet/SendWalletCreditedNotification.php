<?php

namespace App\Listeners\Wallet;

use App\Events\Wallet\WalletCredited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendWalletCreditedNotification implements ShouldQueue
{
    public string $queue = 'notifications';

    public function handle(WalletCredited $event): void
    {
        $wallet      = $event->wallet;
        $transaction = $event->transaction;

        $user = $wallet->user;

        if (!$user) {
            return;
        }

        // Only notify for significant credits (recharge / refund categories)
        if (in_array($transaction->category, ['recharge', 'refund', 'adjustment'], true)) {
            try {
                $user->notify(new \App\Notifications\Customer\WalletCreditedNotification($wallet, $transaction));
            } catch (\Throwable $e) {
                Log::warning("WalletCreditedNotification failed for user #{$user->id}: {$e->getMessage()}");
            }
        }
    }
}

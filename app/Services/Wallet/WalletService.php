<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Events\Wallet\WalletCredited;
use App\Events\Wallet\WalletDebited;
use App\Events\Wallet\LowBalanceDetected;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletService
{
    public function getOrCreate(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['tenant_id' => $user->tenant_id, 'user_id' => $user->id],
            ['balance' => 0, 'status' => 'active']
        );
    }

    public function credit(User $user, float $amount, string $category, string $description, array $meta = []): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $category, $description, $meta) {
            $wallet = $this->getOrCreate($user);

            if (!$wallet->isActive()) {
                throw new \RuntimeException('Wallet is not active');
            }

            $balanceBefore = (float) $wallet->balance;
            $balanceAfter  = $balanceBefore + $amount;

            $wallet->update([
                'balance'        => $balanceAfter,
                'total_credited' => DB::raw("total_credited + {$amount}"),
            ]);

            $transaction = WalletTransaction::create([
                'uuid'           => Str::uuid(),
                'wallet_id'      => $wallet->id,
                'tenant_id'      => $user->tenant_id,
                'user_id'        => $user->id,
                'type'           => 'credit',
                'category'       => $category,
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => $description,
                'status'         => 'completed',
                'meta'           => $meta,
            ]);

            event(new WalletCredited($wallet, $transaction));

            return $transaction;
        });
    }

    public function debit(User $user, float $amount, string $category, string $description, array $meta = []): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $category, $description, $meta) {
            $wallet = $this->getOrCreate($user);

            if (!$wallet->isActive()) {
                throw new \RuntimeException('Wallet is not active');
            }

            if (!$wallet->hasSufficientBalance($amount)) {
                throw new \RuntimeException("Insufficient wallet balance. Available: {$wallet->balance}, Required: {$amount}");
            }

            $balanceBefore = (float) $wallet->balance;
            $balanceAfter  = $balanceBefore - $amount;

            $wallet->update([
                'balance'       => $balanceAfter,
                'total_debited' => DB::raw("total_debited + {$amount}"),
            ]);

            $transaction = WalletTransaction::create([
                'uuid'           => Str::uuid(),
                'wallet_id'      => $wallet->id,
                'tenant_id'      => $user->tenant_id,
                'user_id'        => $user->id,
                'type'           => 'debit',
                'category'       => $category,
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => $description,
                'status'         => 'completed',
                'meta'           => $meta,
            ]);

            event(new WalletDebited($wallet, $transaction));

            // Check low balance threshold
            if ($wallet->refresh()->isBelowThreshold() && !$wallet->low_balance_notified) {
                event(new LowBalanceDetected($wallet));
                $wallet->update(['low_balance_notified' => true]);
            }

            return $transaction;
        });
    }

    public function getBalance(User $user): float
    {
        return (float) ($this->getOrCreate($user)->balance ?? 0);
    }

    public function processDeliveryPayment(User $user, float $amount, int $deliveryId): WalletTransaction
    {
        return $this->debit($user, $amount, 'delivery', 'Payment for delivery #' . $deliveryId, [
            'delivery_id' => $deliveryId,
        ]);
    }

    public function processRefund(User $user, float $amount, int $refundRequestId): WalletTransaction
    {
        return $this->credit($user, $amount, 'refund', 'Refund for request #' . $refundRequestId, [
            'refund_request_id' => $refundRequestId,
        ]);
    }
}

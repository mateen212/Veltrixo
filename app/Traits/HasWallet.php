<?php

namespace App\Traits;

use App\Models\Wallet;
use App\Services\Wallet\WalletService;

trait HasWallet
{
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function getWalletBalance(): float
    {
        return (float) ($this->wallet?->balance ?? 0);
    }

    public function getOrCreateWallet(): Wallet
    {
        return app(WalletService::class)->getOrCreate($this);
    }

    public function hasSufficientBalance(float $amount): bool
    {
        return $this->getWalletBalance() >= $amount;
    }

    public function isWalletBelowThreshold(): bool
    {
        $wallet = $this->wallet;
        return $wallet ? $wallet->isBelowThreshold() : false;
    }

    public function getFormattedBalance(string $symbol = 'Rs'): string
    {
        return $symbol . ' ' . number_format($this->getWalletBalance(), 2);
    }
}

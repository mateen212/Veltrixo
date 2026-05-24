<?php

namespace App\Events\Wallet;

use App\Models\Wallet;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowBalanceDetected
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Wallet $wallet) {}
}

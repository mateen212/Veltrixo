<?php

namespace App\Events\Wallet;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WalletCredited implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Wallet $wallet,
        public readonly WalletTransaction $transaction,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel("customer.{$this->wallet->user_id}")];
    }

    public function broadcastAs(): string { return 'wallet.credited'; }
}

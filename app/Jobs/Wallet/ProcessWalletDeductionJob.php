<?php

namespace App\Jobs\Wallet;

use App\Models\Delivery;
use App\Services\Wallet\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWalletDeductionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public readonly int $deliveryId) {}

    public function handle(WalletService $walletService): void
    {
        $delivery = Delivery::with('user')->find($this->deliveryId);

        if (!$delivery || $delivery->is_paid || $delivery->total_amount <= 0) {
            return;
        }

        try {
            $walletService->processDeliveryPayment($delivery->user, (float) $delivery->total_amount, $delivery->id);
            $delivery->update(['is_paid' => true]);
        } catch (\RuntimeException $e) {
            Log::warning("Wallet deduction failed for delivery #{$this->deliveryId}: {$e->getMessage()}");
        }
    }

    public function tags(): array
    {
        return ['wallet', "delivery:{$this->deliveryId}"];
    }
}

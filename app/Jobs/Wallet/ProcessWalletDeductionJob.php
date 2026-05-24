<?php

namespace App\Jobs\Wallet;

use App\Models\Delivery;
use App\Services\Wallet\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWalletDeductionJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries     = 3;
    public int $timeout   = 60;
    public int $backoff   = 30;
    public int $uniqueFor = 600;

    public function __construct(public readonly int $deliveryId)
    {
        $this->onQueue('wallets');
    }

    public function uniqueId(): string
    {
        return "wallet_deduction:delivery:{$this->deliveryId}";
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping("wallet:delivery:{$this->deliveryId}"))
                ->releaseAfter(15)
                ->expireAfter(120),
        ];
    }

    public function handle(WalletService $walletService): void
    {
        $delivery = Delivery::with('user')->find($this->deliveryId);

        if (!$delivery) {
            Log::warning("[WalletDeduction] delivery not found id={$this->deliveryId}");
            return;
        }

        if ($delivery->is_paid || $delivery->total_amount <= 0) {
            return; // idempotent — already processed
        }

        try {
            $walletService->processDeliveryPayment(
                $delivery->user,
                (float) $delivery->total_amount,
                $delivery->id
            );
            $delivery->update(['is_paid' => true]);
        } catch (\RuntimeException $e) {
            Log::warning("[WalletDeduction] insufficient balance delivery={$this->deliveryId}: "
                . $e->getMessage());
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("[WalletDeduction] FAILED delivery={$this->deliveryId} error={$e->getMessage()}");
    }

    public function tags(): array
    {
        return ['wallets', "delivery:{$this->deliveryId}"];
    }
}

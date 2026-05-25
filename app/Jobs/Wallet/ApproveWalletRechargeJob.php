<?php

namespace App\Jobs\Wallet;

use App\Models\WalletRechargeRequest;
use App\Services\Wallet\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApproveWalletRechargeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly int    $rechargeRequestId,
        public readonly int    $approvedById
    ) {}

    public function handle(WalletService $walletService): void
    {
        $request = WalletRechargeRequest::with('user')->find($this->rechargeRequestId);

        if (!$request) {
            Log::warning("ApproveWalletRechargeJob: recharge #{$this->rechargeRequestId} not found.");
            return;
        }

        if ($request->status !== 'pending') {
            Log::info("ApproveWalletRechargeJob: recharge #{$this->rechargeRequestId} already processed (status: {$request->status}).");
            return;
        }

        DB::transaction(function () use ($request, $walletService) {
            $walletService->credit(
                $request->user,
                (float) $request->amount,
                'recharge',
                "Wallet recharge approved — Ref: {$request->payment_reference}",
                ['recharge_request_id' => $request->id]
            );

            $request->update([
                'status'        => 'approved',
                'approved_by'   => $this->approvedById,
                'approved_at'   => now(),
            ]);
        });
    }

    public function failed(\Throwable $e): void
    {
        Log::error("ApproveWalletRechargeJob failed for recharge #{$this->rechargeRequestId}: {$e->getMessage()}");
    }
}

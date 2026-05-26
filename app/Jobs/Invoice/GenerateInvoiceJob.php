<?php

namespace App\Jobs\Invoice;

use App\Models\Delivery;
use App\Models\Tenant;
use App\Support\TenantContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateInvoiceJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries     = 3;
    public int $timeout   = 120;
    public int $backoff   = 60;
    public int $uniqueFor = 3600;

    public function __construct(public readonly int $deliveryId)
    {
        $this->onQueue('invoices');
    }

    public function uniqueId(): string
    {
        return "generate_invoice:delivery:{$this->deliveryId}";
    }

    public function handle(): void
    {
        $delivery = Delivery::with(['user', 'items.product', 'subscription'])->find($this->deliveryId);

        if (!$delivery) {
            Log::warning("[GenerateInvoice] delivery not found id={$this->deliveryId}");
            return;
        }

        // Set tenant context for any downstream services
        if ($delivery->tenant_id && ($tenant = Tenant::find($delivery->tenant_id))) {
            TenantContext::set($tenant);
        }

        // Invoice generation logic handled by InvoiceService (injected when needed)
        Log::info("[GenerateInvoice] delivery={$this->deliveryId}");
    }

    public function failed(\Throwable $e): void
    {
        Log::error("[GenerateInvoice] FAILED delivery={$this->deliveryId} error={$e->getMessage()}");
    }

    public function tags(): array
    {
        return ['invoices', "delivery:{$this->deliveryId}"];
    }
}

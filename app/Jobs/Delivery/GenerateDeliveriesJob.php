<?php

namespace App\Jobs\Delivery;

use App\Services\Delivery\DeliveryService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\Middleware\WithTenantContext;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateDeliveriesJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries        = 3;
    public int $timeout      = 300;
    public int $backoff      = 60;
    public int $uniqueFor    = 3600; // 1 hour uniqueness window

    public function __construct(
        public readonly int $tenantId,
        public readonly string $date,
    ) {
        $this->onQueue('deliveries');
    }

    /** Prevent duplicate generation for same tenant+date */
    public function uniqueId(): string
    {
        return "generate_deliveries:{$this->tenantId}:{$this->date}";
    }

    public function middleware(): array
    {
        return [
            new WithTenantContext($this->tenantId),
            (new WithoutOverlapping("tenant:{$this->tenantId}:deliveries:{$this->date}"))
                ->releaseAfter(30)
                ->expireAfter(600),
        ];
    }

    public function handle(DeliveryService $service): void
    {
        $date = Carbon::parse($this->date);

        Log::info("[GenerateDeliveries] tenant={$this->tenantId} date={$this->date}");

        $deliveries = $service->generateDailyDeliveries($this->tenantId, $date);

        Log::info("[GenerateDeliveries] generated=" . count($deliveries)
            . " tenant={$this->tenantId}");
    }

    public function failed(\Throwable $e): void
    {
        Log::error("[GenerateDeliveries] FAILED tenant={$this->tenantId} date={$this->date}"
            . " error={$e->getMessage()}");
    }

    public function tags(): array
    {
        return ['deliveries', "tenant:{$this->tenantId}", "date:{$this->date}"];
    }
}

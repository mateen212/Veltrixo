<?php

namespace App\Jobs\Delivery;

use App\Services\Delivery\DeliveryService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateDeliveriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(
        public readonly int $tenantId,
        public readonly ?string $date = null,
    ) {}

    public function handle(DeliveryService $service): void
    {
        $date = $this->date ? Carbon::parse($this->date) : today();

        Log::info("Generating deliveries for tenant #{$this->tenantId} on {$date->toDateString()}");

        $deliveries = $service->generateDailyDeliveries($this->tenantId, $date);

        Log::info("Generated " . count($deliveries) . " deliveries for tenant #{$this->tenantId}");
    }

    public function tags(): array
    {
        return ['deliveries', "tenant:{$this->tenantId}"];
    }
}

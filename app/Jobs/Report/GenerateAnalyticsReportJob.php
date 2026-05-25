<?php

namespace App\Jobs\Report;

use App\Models\Delivery;
use App\Models\Subscription;
use App\Models\WalletTransaction;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GenerateAnalyticsReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 300;

    public function __construct(
        public readonly int    $tenantId,
        public readonly string $date
    ) {}

    public function handle(): void
    {
        $tenant = Tenant::find($this->tenantId);

        if (!$tenant) {
            return;
        }

        $date  = \Carbon\Carbon::parse($this->date);
        $start = $date->copy()->startOfDay();
        $end   = $date->copy()->endOfDay();

        $deliveries = Delivery::where('tenant_id', $this->tenantId)
            ->whereDate('delivery_date', $this->date)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $revenue = WalletTransaction::where('tenant_id', $this->tenantId)
            ->where('type', 'debit')
            ->where('category', 'delivery')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $activeSubscriptions = Subscription::where('tenant_id', $this->tenantId)
            ->where('status', 'active')
            ->count();

        $kpis = [
            'date'                => $this->date,
            'tenant_id'           => $this->tenantId,
            'total_deliveries'    => array_sum($deliveries),
            'delivered'           => $deliveries['delivered'] ?? 0,
            'missed'              => $deliveries['missed'] ?? 0,
            'pending'             => $deliveries['pending'] ?? 0,
            'revenue_pkr'         => (float) $revenue,
            'active_subscriptions' => $activeSubscriptions,
            'generated_at'        => now()->toIso8601String(),
        ];

        // Cache KPIs for dashboard (TTL: 6 hours)
        Cache::store('redis')->put(
            "analytics:daily:{$this->tenantId}:{$this->date}",
            $kpis,
            now()->addHours(6)
        );

        Log::info("Analytics generated for tenant #{$this->tenantId} on {$this->date}", $kpis);
    }

    public function failed(\Throwable $e): void
    {
        Log::error("GenerateAnalyticsReportJob failed for tenant #{$this->tenantId} on {$this->date}: {$e->getMessage()}");
    }
}

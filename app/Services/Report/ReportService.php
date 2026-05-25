<?php

namespace App\Services\Report;

use App\Models\Delivery;
use App\Models\Subscription;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Generate a daily summary report for a tenant.
     */
    public function dailySummary(int $tenantId, string $date): array
    {
        $cacheKey = "report:daily:{$tenantId}:{$date}";

        return Cache::remember($cacheKey, now()->addHours(4), function () use ($tenantId, $date) {
            $deliveries = Delivery::where('tenant_id', $tenantId)
                ->whereDate('delivery_date', $date)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $revenue = WalletTransaction::where('tenant_id', $tenantId)
                ->where('type', 'debit')
                ->where('category', 'delivery')
                ->whereDate('created_at', $date)
                ->sum('amount');

            $recharges = WalletTransaction::where('tenant_id', $tenantId)
                ->where('type', 'credit')
                ->where('category', 'recharge')
                ->whereDate('created_at', $date)
                ->sum('amount');

            return [
                'date'             => $date,
                'deliveries'       => $deliveries,
                'total_deliveries' => array_sum($deliveries),
                'delivered'        => $deliveries['delivered'] ?? 0,
                'missed'           => $deliveries['missed'] ?? 0,
                'revenue_pkr'      => (float) $revenue,
                'recharges_pkr'    => (float) $recharges,
                'success_rate'     => $this->calculateSuccessRate($deliveries),
            ];
        });
    }

    /**
     * Generate a weekly summary (Mon–Sun).
     */
    public function weeklySummary(int $tenantId, string $weekStartDate): array
    {
        $start = Carbon::parse($weekStartDate)->startOfWeek();
        $end   = $start->copy()->endOfWeek();

        $deliveries = Delivery::where('tenant_id', $tenantId)
            ->whereBetween('delivery_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('DATE(delivery_date) as day, status, COUNT(*) as count')
            ->groupBy('day', 'status')
            ->get();

        $revenue = WalletTransaction::where('tenant_id', $tenantId)
            ->where('type', 'debit')
            ->where('category', 'delivery')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        return [
            'week_start'  => $start->toDateString(),
            'week_end'    => $end->toDateString(),
            'deliveries'  => $deliveries,
            'revenue_pkr' => (float) $revenue,
        ];
    }

    /**
     * Rider performance report for a date range.
     */
    public function riderPerformance(int $tenantId, string $from, string $to): array
    {
        return DB::table('deliveries')
            ->join('riders', 'deliveries.rider_id', '=', 'riders.id')
            ->join('users', 'riders.user_id', '=', 'users.id')
            ->where('deliveries.tenant_id', $tenantId)
            ->whereBetween('delivery_date', [$from, $to])
            ->selectRaw('
                riders.id,
                users.name,
                COUNT(*) as total,
                SUM(CASE WHEN deliveries.status = "delivered" THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN deliveries.status = "missed" THEN 1 ELSE 0 END) as missed
            ')
            ->groupBy('riders.id', 'users.name')
            ->orderByDesc('delivered')
            ->get()
            ->toArray();
    }

    private function calculateSuccessRate(array $deliveries): float
    {
        $total     = array_sum($deliveries);
        $delivered = $deliveries['delivered'] ?? 0;

        return $total > 0 ? round(($delivered / $total) * 100, 1) : 0.0;
    }
}

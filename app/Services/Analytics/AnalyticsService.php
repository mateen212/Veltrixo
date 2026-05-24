<?php

namespace App\Services\Analytics;

use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getDashboardMetrics(int $tenantId, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $from ??= now()->startOfMonth();
        $to   ??= now();

        $cacheKey = "analytics.dashboard.{$tenantId}.{$from->toDateString()}.{$to->toDateString()}";

        return Cache::remember($cacheKey, 300, function () use ($tenantId, $from, $to) {
            return [
                'revenue'              => $this->getRevenue($tenantId, $from, $to),
                'active_subscriptions' => $this->getActiveSubscriptions($tenantId),
                'delivery_success_rate' => $this->getDeliverySuccessRate($tenantId, $from, $to),
                'new_customers'        => $this->getNewCustomers($tenantId, $from, $to),
                'total_deliveries'     => $this->getTotalDeliveries($tenantId, $from, $to),
                'pending_deliveries'   => $this->getPendingDeliveries($tenantId),
                'wallet_balance_total' => $this->getTotalWalletBalance($tenantId),
                'churn_rate'           => $this->getChurnRate($tenantId, $from, $to),
                'refund_stats'         => $this->getRefundStats($tenantId, $from, $to),
                'rider_performance'    => $this->getRiderPerformance($tenantId, $from, $to),
            ];
        });
    }

    public function getRevenue(int $tenantId, Carbon $from, Carbon $to): array
    {
        $invoices = Invoice::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$from, $to]);

        return [
            'total'      => $invoices->sum('total'),
            'count'      => $invoices->count(),
            'average'    => $invoices->avg('total') ?? 0,
            'daily'      => Invoice::where('tenant_id', $tenantId)
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$from, $to])
                ->selectRaw('DATE(paid_at) as date, SUM(total) as total, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];
    }

    public function getActiveSubscriptions(int $tenantId): array
    {
        $base = Subscription::where('tenant_id', $tenantId);
        return [
            'active'    => (clone $base)->where('status', 'active')->count(),
            'paused'    => (clone $base)->where('status', 'paused')->count(),
            'cancelled' => (clone $base)->where('status', 'cancelled')->count(),
            'total'     => $base->count(),
        ];
    }

    public function getDeliverySuccessRate(int $tenantId, Carbon $from, Carbon $to): array
    {
        $deliveries = Delivery::where('tenant_id', $tenantId)
            ->whereBetween('delivery_date', [$from, $to])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $delivered = $deliveries['delivered']->count ?? 0;
        $missed    = $deliveries['missed']->count ?? 0;
        $total     = $deliveries->sum('count');

        return [
            'rate'      => $total > 0 ? round(($delivered / $total) * 100, 1) : 0,
            'delivered' => $delivered,
            'missed'    => $missed,
            'total'     => $total,
            'by_status' => $deliveries,
        ];
    }

    public function getNewCustomers(int $tenantId, Carbon $from, Carbon $to): int
    {
        return User::where('tenant_id', $tenantId)
            ->whereHas('roles', fn ($q) => $q->where('name', 'customer'))
            ->whereBetween('created_at', [$from, $to])
            ->count();
    }

    public function getTotalDeliveries(int $tenantId, Carbon $from, Carbon $to): int
    {
        return Delivery::where('tenant_id', $tenantId)
            ->whereBetween('delivery_date', [$from, $to])
            ->count();
    }

    public function getPendingDeliveries(int $tenantId): int
    {
        return Delivery::where('tenant_id', $tenantId)
            ->whereDate('delivery_date', today())
            ->whereIn('status', ['scheduled', 'assigned', 'in_progress'])
            ->count();
    }

    public function getTotalWalletBalance(int $tenantId): float
    {
        return (float) DB::table('wallets')->where('tenant_id', $tenantId)->sum('balance');
    }

    public function getChurnRate(int $tenantId, Carbon $from, Carbon $to): float
    {
        $cancelled = Subscription::where('tenant_id', $tenantId)
            ->where('status', 'cancelled')
            ->whereBetween('cancelled_at', [$from, $to])
            ->count();

        $total = Subscription::where('tenant_id', $tenantId)
            ->where('created_at', '<=', $from)
            ->count();

        return $total > 0 ? round(($cancelled / $total) * 100, 2) : 0;
    }

    public function getRefundStats(int $tenantId, Carbon $from, Carbon $to): array
    {
        return DB::table('refund_requests')
            ->where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('status, COUNT(*) as count, SUM(approved_amount) as total_approved')
            ->groupBy('status')
            ->get()
            ->keyBy('status')
            ->toArray();
    }

    public function getRiderPerformance(int $tenantId, Carbon $from, Carbon $to): \Illuminate\Support\Collection
    {
        return DB::table('deliveries')
            ->join('riders', 'deliveries.rider_id', '=', 'riders.id')
            ->join('users', 'riders.user_id', '=', 'users.id')
            ->where('deliveries.tenant_id', $tenantId)
            ->whereBetween('deliveries.delivery_date', [$from, $to])
            ->whereNotNull('deliveries.rider_id')
            ->selectRaw('
                riders.id,
                users.name,
                COUNT(*) as total,
                SUM(CASE WHEN deliveries.status = "delivered" THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN deliveries.status = "missed" THEN 1 ELSE 0 END) as missed,
                AVG(riders.rating) as rating
            ')
            ->groupBy('riders.id', 'users.name')
            ->orderByDesc('delivered')
            ->get();
    }
}

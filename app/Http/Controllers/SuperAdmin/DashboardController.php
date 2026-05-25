<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WalletTransaction;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalTenants  = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $trialTenants  = Tenant::where('status', 'active')
            ->where('trial_ends_at', '>', now())
            ->count();

        $thisMonthRevenue = WalletTransaction::where('type', 'credit')
            ->where('category', 'recharge')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $totalDeliveriesToday = Delivery::whereDate('delivery_date', today())->count();

        $recentTenants = Tenant::with('owner')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($t) => [
                'id'             => $t->id,
                'name'           => $t->name,
                'slug'           => $t->slug,
                'status'         => $t->status,
                'owner_name'     => $t->owner?->name ?? '—',
                'owner_email'    => $t->owner?->email ?? '—',
                'trial_ends_at'  => $t->trial_ends_at?->format('d M Y') ?? '—',
                'created_at'     => $t->created_at->format('d M Y'),
            ]);

        return Inertia::render('SuperAdmin/Dashboard', [
            'stats' => [
                'total_tenants'   => $totalTenants,
                'active_tenants'  => $activeTenants,
                'trial_tenants'   => $trialTenants,
                'monthly_revenue' => 'Rs ' . number_format($thisMonthRevenue, 2),
                'deliveries_today' => $totalDeliveriesToday,
            ],
            'recentTenants' => $recentTenants,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $activeSubscriptions = $user->subscriptions()
            ->where('status', 'active')
            ->count();

        $pendingDeliveries = $user->deliveries()
            ->whereIn('status', ['pending', 'assigned'])
            ->count();

        $walletBalance = optional($user->wallet)->balance ?? 0;

        $recentDeliveries = $user->deliveries()
            ->with('items')
            ->latest('delivery_date')
            ->limit(5)
            ->get()
            ->map(fn ($d) => [
                'id'          => $d->id,
                'date'        => $d->delivery_date?->format('d M Y') ?? '—',
                'status'      => $d->status,
                'items_count' => $d->items->count(),
            ]);

        return Inertia::render('Customer/Dashboard', [
            'activeSubscriptions' => $activeSubscriptions,
            'pendingDeliveries'   => $pendingDeliveries,
            'walletBalance'       => (float) $walletBalance,
            'recentDeliveries'    => $recentDeliveries,
        ]);
    }
}

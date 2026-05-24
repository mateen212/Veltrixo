<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user  = $request->user();
        $rider = $user->rider;

        $today = now()->toDateString();

        $deliveries = $rider
            ? $rider->deliveries()->whereDate('delivery_date', $today)->get()
            : collect();

        $recentDeliveries = $deliveries->sortByDesc('id')->take(10)->map(fn ($d) => [
            'id'             => $d->id,
            'address'        => optional($d->address)->full_address ?? 'N/A',
            'status'         => $d->status,
            'delivery_date'  => $d->delivery_date?->format('d M Y') ?? '—',
        ])->values();

        return Inertia::render('Rider/Dashboard', [
            'todayDeliveries'  => $deliveries->count(),
            'completedToday'   => $deliveries->where('status', 'delivered')->count(),
            'pendingToday'     => $deliveries->whereIn('status', ['pending', 'assigned'])->count(),
            'recentDeliveries' => $recentDeliveries,
        ]);
    }
}

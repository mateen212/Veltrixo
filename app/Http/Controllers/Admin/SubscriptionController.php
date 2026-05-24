<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['user', 'plan'])
            ->when($request->search, fn($q, $s) => $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$s%")))
            ->when($request->status, fn($q, $s) => $q->where('status', $s));

        $stats = [
            'total'     => Subscription::count(),
            'active'    => Subscription::where('status', 'active')->count(),
            'paused'    => Subscription::where('status', 'paused')->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
        ];

        return Inertia::render('Admin/Subscriptions/Index', [
            'subscriptions' => $query->latest()->paginate(20)->through(fn($s) => [
                'id'                 => $s->id,
                'customer_name'      => $s->user?->name ?? '—',
                'plan_name'          => $s->plan?->name ?? '—',
                'status'             => $s->status,
                'start_date'         => $s->start_date?->toDateString(),
                'next_delivery_date' => $s->next_delivery_date?->toDateString(),
                'items_count'        => $s->items_count ?? 0,
            ]),
            'filters' => $request->only('search', 'status'),
            'stats'   => $stats,
        ]);
    }

    public function show(Subscription $subscription)
    {
        return Inertia::render('Admin/Subscriptions/Show', [
            'subscription' => $subscription->load(['user', 'plan', 'items.product']),
        ]);
    }

    public function cancel(Subscription $subscription)
    {
        $subscription->update(['status' => 'cancelled']);
        return back()->with('success', 'Subscription cancelled.');
    }

    public function pause(Subscription $subscription)
    {
        $subscription->update(['status' => 'paused']);
        return back()->with('success', 'Subscription paused.');
    }

    public function resume(Subscription $subscription)
    {
        $subscription->update(['status' => 'active']);
        return back()->with('success', 'Subscription resumed.');
    }
}

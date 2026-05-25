<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class RiderController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Rider::where('tenant_id', $tenantId)
            ->with('user')
            ->when($request->search, fn ($q, $s) =>
                $q->whereHas('user', fn ($u) =>
                    $u->where('name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%")
                )
            )
            ->when($request->status, fn ($q, $s) => $q->where('status', $s));

        return Inertia::render('Admin/Riders/Index', [
            'riders' => $query->latest()->paginate(20)->through(fn ($r) => [
                'id'             => $r->id,
                'name'           => $r->user?->name ?? '—',
                'email'          => $r->user?->email ?? '—',
                'phone'          => $r->user?->phone ?? '—',
                'vehicle_type'   => $r->vehicle_type,
                'vehicle_number' => $r->vehicle_number,
                'status'         => $r->status,
                'is_available'   => $r->is_available,
                'created_at'     => $r->created_at->format('d M Y'),
                'deliveries_today' => Delivery::where('tenant_id', $tenantId)
                    ->where('rider_id', $r->id)
                    ->whereDate('delivery_date', today())
                    ->count(),
            ]),
            'filters' => $request->only('search', 'status'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'phone'          => 'required|string|max:20',
            'password'       => 'required|string|min:8|confirmed',
            'vehicle_type'   => 'required|string|in:bike,bicycle,van,car,walk',
            'vehicle_number' => 'nullable|string|max:30',
        ]);

        $tenantId = auth()->user()->tenant_id;

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'],
            'password'  => bcrypt($validated['password']),
            'tenant_id' => $tenantId,
        ]);
        $user->assignRole('rider');

        Rider::create([
            'tenant_id'      => $tenantId,
            'user_id'        => $user->id,
            'vehicle_type'   => $validated['vehicle_type'],
            'vehicle_number' => $validated['vehicle_number'] ?? null,
            'status'         => 'active',
            'is_available'   => true,
        ]);

        return back()->with('success', 'Rider created successfully.');
    }

    public function show(Rider $rider)
    {
        $rider->load('user');

        $recentDeliveries = Delivery::where('rider_id', $rider->id)
            ->latest('delivery_date')
            ->take(10)
            ->get(['id', 'status', 'delivery_date', 'delivered_at']);

        return Inertia::render('Admin/Riders/Show', [
            'rider' => [
                'id'             => $rider->id,
                'name'           => $rider->user?->name,
                'email'          => $rider->user?->email,
                'phone'          => $rider->user?->phone,
                'vehicle_type'   => $rider->vehicle_type,
                'vehicle_number' => $rider->vehicle_number,
                'status'         => $rider->status,
                'is_available'   => $rider->is_available,
            ],
            'recentDeliveries' => $recentDeliveries->map(fn ($d) => [
                'id'            => $d->id,
                'status'        => $d->status,
                'delivery_date' => $d->delivery_date?->format('d M Y'),
                'delivered_at'  => $d->delivered_at?->format('d M Y, H:i'),
            ]),
        ]);
    }

    public function update(Request $request, Rider $rider)
    {
        $validated = $request->validate([
            'vehicle_type'   => 'sometimes|string|in:bike,bicycle,van,car,walk',
            'vehicle_number' => 'nullable|string|max:30',
            'status'         => 'sometimes|string|in:active,inactive,suspended',
            'is_available'   => 'sometimes|boolean',
        ]);

        $rider->update($validated);

        return back()->with('success', 'Rider updated.');
    }

    public function destroy(Rider $rider)
    {
        $rider->update(['status' => 'inactive']);
        return back()->with('success', 'Rider deactivated.');
    }
}

<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantPlan;
use App\Services\Tenant\TenantProvisioningService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TenantController extends Controller
{
    public function __construct(private TenantProvisioningService $provisioningService) {}

    public function index(Request $request)
    {
        $query = Tenant::with(['owner', 'tenantSubscription.plan'])
            ->when($request->search, fn ($q, $s) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
            )
            ->when($request->status, fn ($q, $s) => $q->where('status', $s));

        $plans = TenantPlan::where('is_active', true)->get(['id', 'name', 'slug']);

        return Inertia::render('SuperAdmin/Tenants/Index', [
            'tenants' => $query->latest()->paginate(25)->through(fn ($t) => [
                'id'           => $t->id,
                'name'         => $t->name,
                'slug'         => $t->slug,
                'status'       => $t->status,
                'email'        => $t->email,
                'owner_name'   => $t->owner?->name ?? '—',
                'plan_name'    => $t->tenantSubscription?->plan?->name ?? 'No plan',
                'trial_ends'   => $t->trial_ends_at?->format('d M Y') ?? '—',
                'created_at'   => $t->created_at->format('d M Y'),
            ]),
            'plans'   => $plans,
            'filters' => $request->only('search', 'status'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_name' => 'required|string|max:100',
            'owner_name'    => 'required|string|max:100',
            'owner_email'   => 'required|email|unique:users,email',
            'owner_phone'   => 'nullable|string|max:20',
            'plan'          => 'required|string|exists:tenant_plans,slug',
            'trial_days'    => 'nullable|integer|min:0|max:365',
        ]);

        $result = $this->provisioningService->provision($data);
        dd($result);
        return redirect()
            ->route('super-admin.tenants.show', $result['tenant'])
            ->with('success', "Tenant '{$result['tenant']->name}' provisioned successfully.");
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['owner', 'tenantSubscription.plan']);

        return Inertia::render('SuperAdmin/Tenants/Show', [
            'tenant' => [
                'id'           => $tenant->id,
                'name'         => $tenant->name,
                'slug'         => $tenant->slug,
                'email'        => $tenant->email,
                'phone'        => $tenant->phone,
                'status'       => $tenant->status,
                'domain'       => $tenant->domain,
                'owner'        => $tenant->owner ? ['name' => $tenant->owner->name, 'email' => $tenant->owner->email] : null,
                'plan'         => $tenant->tenantSubscription?->plan?->name ?? 'No plan',
                'trial_ends'   => $tenant->trial_ends_at?->format('d M Y') ?? '—',
                'created_at'   => $tenant->created_at->format('d M Y'),
            ],
        ]);
    }

    public function suspend(Request $request, Tenant $tenant)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);
        $this->provisioningService->suspend($tenant, $request->reason ?? 'Suspended by super admin');
        return back()->with('success', "Tenant '{$tenant->name}' suspended.");
    }

    public function reactivate(Tenant $tenant)
    {
        $this->provisioningService->reactivate($tenant);
        return back()->with('success', "Tenant '{$tenant->name}' reactivated.");
    }
}

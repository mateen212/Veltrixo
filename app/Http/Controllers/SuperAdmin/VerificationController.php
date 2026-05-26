<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\Tenant\BusinessSignupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VerificationController extends Controller
{
    public function __construct(private readonly BusinessSignupService $service) {}

    /**
     * List all tenants pending verification.
     */
    public function index(): Response
    {
        $pending = Tenant::with('owner')
            ->where('verification_status', 'pending_verification')
            ->latest()
            ->paginate(20);

        $recentlyActioned = Tenant::with(['owner', 'verifiedByAdmin'])
            ->whereIn('verification_status', ['approved', 'rejected'])
            ->latest('verified_at')
            ->limit(10)
            ->get();

        $stats = [
            'pending'  => Tenant::where('verification_status', 'pending_verification')->count(),
            'approved' => Tenant::where('verification_status', 'approved')->count(),
            'rejected' => Tenant::where('verification_status', 'rejected')->count(),
        ];

        return Inertia::render('SuperAdmin/Verifications/Index', [
            'pending'          => $pending,
            'recentlyActioned' => $recentlyActioned,
            'stats'            => $stats,
        ]);
    }

    /**
     * Show a single tenant for detailed review.
     */
    public function show(Tenant $tenant): Response
    {
        $tenant->load(['owner', 'verifiedByAdmin']);

        return Inertia::render('SuperAdmin/Verifications/Show', [
            'tenant' => $tenant,
        ]);
    }

    /**
     * Approve a pending business.
     */
    public function approve(Request $request, Tenant $tenant): RedirectResponse
    {
        $request->validate([
            'plan_slug' => ['nullable', 'string', 'exists:tenant_plans,slug'],
        ]);

        $this->service->approve($tenant, $request->user(), $request->input('plan_slug'));

        return back()->with('success', $tenant->name . ' has been approved and is now active.');
    }

    /**
     * Reject a pending business.
     */
    public function reject(Request $request, Tenant $tenant): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $this->service->reject($tenant, $request->user(), $request->input('reason'));

        return back()->with('success', $tenant->name . ' has been rejected.');
    }
}

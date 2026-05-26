<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\BusinessRegistrationRequest;
use App\Models\TenantPlan;
use App\Services\Tenant\BusinessSignupService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationController extends Controller
{
    public function __construct(private readonly BusinessSignupService $service) {}

    /**
     * Display the public business registration form.
     */
    public function show(): Response
    {
        $plans = TenantPlan::where('is_public', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'price_monthly', 'price_yearly', 'description', 'features']);

        return Inertia::render('Business/Register', [
            'plans' => $plans,
        ]);
    }

    /**
     * Process the business registration.
     */
    public function store(BusinessRegistrationRequest $request): RedirectResponse
    {
        $this->service->register($request->validated());

        return redirect()->route('business.pending');
    }
}

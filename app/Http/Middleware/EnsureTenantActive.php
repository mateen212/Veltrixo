<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures the tenant is in an acceptable state for user access.
 * - Aborts with a "suspended" page if the tenant is suspended.
 * - Aborts with a "pending" page if the tenant has not been approved yet.
 *
 * Applied to all tenant-domain routes (admin / customer / rider).
 */
class EnsureTenantActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = TenantContext::get();

        if (!$tenant) {
            return $next($request); // Central domain — nothing to check
        }

        if ($tenant->status === 'suspended' || $tenant->verification_status === 'suspended') {
            return Inertia::render('Errors/TenantSuspended', [
                'tenantName' => $tenant->name,
            ])->toResponse($request)->setStatusCode(403);
        }

        if ($tenant->verification_status === 'pending_verification') {
            return Inertia::render('Business/Pending')
                ->toResponse($request)
                ->setStatusCode(403);
        }

        if ($tenant->verification_status === 'rejected') {
            return Inertia::render('Errors/TenantNotFound', [
                'subdomain' => $tenant->subdomain,
            ])->toResponse($request)->setStatusCode(403);
        }

        return $next($request);
    }
}

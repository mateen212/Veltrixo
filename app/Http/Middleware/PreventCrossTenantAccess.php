<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prevents an authenticated user from accessing a tenant's panel if their
 * tenant_id does not match the current subdomain's tenant.
 *
 * Scenario blocked: user from alnoor.veltrixo.com navigating to
 * freshmilk.veltrixo.com — they would be logged out.
 *
 * Applied to all authenticated routes inside the tenant domain group.
 */
class PreventCrossTenantAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = TenantContext::get();

        if (!$tenant || !$request->user()) {
            return $next($request);
        }

        $user = $request->user();

        // Super admins are exempt (they can inspect any tenant)
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        if ((int) $user->tenant_id !== (int) $tenant->id) {
            // Silently log out and redirect to the correct tenant's login
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $correctUrl = $user->tenant_id
                ? 'http://' . (optional(\App\Models\Tenant::find($user->tenant_id))->subdomain ?? '') . '.' . config('tenancy.app_domain') . '/login'
                : route('login');

            return redirect($correctUrl)->withErrors([
                'email' => 'Please log in from your business portal.',
            ]);
        }

        return $next($request);
    }
}

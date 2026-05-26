<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reads the subdomain from the incoming hostname and initialises the
 * TenantContext singleton. Runs as global web middleware so all requests
 * (including auth) are tenant-aware.
 *
 * Central domain requests (no subdomain) silently pass through with
 * TenantContext = null.
 */
class InitializeTenancyBySubdomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $host      = $request->getHost();                  // e.g. alnoor.veltrixo.com
        $appDomain = config('tenancy.app_domain');          // e.g. veltrixo.com
        $exempt    = config('tenancy.exempt_subdomains', []);

        // Central domain — no subdomain to extract
        if ($host === $appDomain || !str_ends_with($host, '.' . $appDomain)) {
            return $next($request);
        }

        // Extract subdomain
        $subdomain = str_replace('.' . $appDomain, '', $host); // "alnoor"

        // Platform-reserved subdomains bypass tenant resolution
        if (in_array($subdomain, $exempt, true)) {
            return $next($request);
        }

        // Resolve tenant by subdomain
        $tenant = Tenant::where('subdomain', $subdomain)->first();

        if (!$tenant) {
            return Inertia::render('Errors/TenantNotFound', [
                'subdomain' => $subdomain,
            ])->toResponse($request)->setStatusCode(404);
        }

        TenantContext::set($tenant);

        return $next($request);
    }
}

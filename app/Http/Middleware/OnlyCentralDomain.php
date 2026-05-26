<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route group to the central domain only.
 * Rejects any request arriving from a tenant subdomain.
 *
 * Applied to the Super Admin panel to prevent tenant subdomain access.
 */
class OnlyCentralDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        if (TenantContext::has()) {
            abort(404); // tenant subdomains cannot access central routes
        }

        return $next($request);
    }
}

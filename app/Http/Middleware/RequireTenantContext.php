<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards routes that must be accessed from a tenant subdomain.
 * Returns 404 if no tenant context is active (central domain request).
 *
 * Applied to admin / customer / rider route groups.
 */
class RequireTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!TenantContext::has()) {
            abort(404, 'This route requires a tenant subdomain.');
        }

        return $next($request);
    }
}

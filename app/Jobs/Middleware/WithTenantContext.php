<?php

namespace App\Jobs\Middleware;

use App\Models\Tenant;
use App\Support\TenantContext;
use Closure;
use Illuminate\Support\Facades\Log;

/**
 * Queue job middleware that re-initializes the tenant context before executing
 * a job, then clears it afterward.
 *
 * Add to any job that needs tenant-aware data access:
 *
 *   public function middleware(): array
 *   {
 *       return [new WithTenantContext($this->tenantId)];
 *   }
 */
class WithTenantContext
{
    public function __construct(private readonly int $tenantId) {}

    public function handle(object $job, Closure $next): void
    {
        $tenant = Tenant::find($this->tenantId);

        if (!$tenant) {
            Log::warning('[WithTenantContext] tenant not found', [
                'tenant_id' => $this->tenantId,
                'job'       => get_class($job),
            ]);
            // Still run the job — it may handle the missing tenant itself
            $next($job);
            return;
        }

        TenantContext::set($tenant);

        try {
            $next($job);
        } finally {
            TenantContext::clear();
        }
    }
}

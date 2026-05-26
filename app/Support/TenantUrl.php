<?php

namespace App\Support;

use App\Models\Tenant;

/**
 * Generates URLs scoped to a specific tenant subdomain.
 *
 * Usage:
 *   TenantUrl::to('/customer/invoices/5', $tenant)
 *   → https://alnoor.veltrixo.com/customer/invoices/5
 *
 *   TenantUrl::for($tenant)->path('/admin/dashboard')
 *   → https://alnoor.veltrixo.com/admin/dashboard
 */
class TenantUrl
{
    private string $scheme;
    private string $domain;

    private function __construct(private readonly Tenant $tenant)
    {
        $this->domain = config('tenancy.app_domain', 'veltrixo.com');
        $this->scheme = app()->environment('production') ? 'https' : 'http';
    }

    public static function for(Tenant $tenant): static
    {
        return new static($tenant);
    }

    /**
     * Generate a full URL on the tenant's subdomain.
     */
    public static function to(string $path, Tenant $tenant): string
    {
        return (new static($tenant))->path($path);
    }

    public function path(string $path): string
    {
        $subdomain = $this->tenant->subdomain;

        if (!$subdomain) {
            // Fallback to central domain if tenant has no subdomain
            return url($path);
        }

        return $this->scheme . '://' . $subdomain . '.' . $this->domain . '/' . ltrim($path, '/');
    }

    /** Base URL of the tenant (no trailing slash). */
    public function base(): string
    {
        return $this->scheme . '://' . $this->tenant->subdomain . '.' . $this->domain;
    }
}

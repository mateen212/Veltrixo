<?php

namespace App\Support;

use App\Models\Tenant;

/**
 * Holds the active tenant for the current request / job lifecycle.
 *
 * Used instead of stancl/tenancy to keep compatibility with the existing
 * relational Tenant model and row-level multi-tenancy schema.
 */
final class TenantContext
{
    private static ?Tenant $current = null;

    /** Set the active tenant (called by InitializeTenancyBySubdomain middleware). */
    public static function set(Tenant $tenant): void
    {
        self::$current = $tenant;
    }

    /** Get the active tenant, or null if on the central domain. */
    public static function get(): ?Tenant
    {
        return self::$current;
    }

    /** Returns true when a tenant context is active (i.e. on a subdomain). */
    public static function has(): bool
    {
        return self::$current !== null;
    }

    /**
     * Get the active tenant or throw if missing.
     * Use inside routes / services that REQUIRE tenant context.
     */
    public static function require(): Tenant
    {
        if (self::$current === null) {
            abort(404, 'No tenant context found.');
        }

        return self::$current;
    }

    /** Clear the context (called after job/request completes). */
    public static function clear(): void
    {
        self::$current = null;
    }
}

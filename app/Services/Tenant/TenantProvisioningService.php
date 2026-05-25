<?php

namespace App\Services\Tenant;

use App\Models\Tenant;
use App\Models\TenantPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class TenantProvisioningService
{
    /**
     * Provision a brand-new tenant and create its admin user.
     * Returns the tenant and the admin user credentials.
     */
    public function provision(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $plan = TenantPlan::where('slug', $data['plan'] ?? 'basic')->firstOrFail();

            // Create the admin user (no tenant_id yet)
            $adminUser = User::create([
                'name'              => $data['owner_name'],
                'email'             => $data['owner_email'],
                'phone'             => $data['owner_phone'] ?? null,
                'password'          => Hash::make($data['password'] ?? Str::random(12)),
                'email_verified_at' => now(),
            ]);

            // Create the tenant
            $tenant = Tenant::create([
                'name'          => $data['business_name'],
                'slug'          => Str::slug($data['business_name']) . '-' . Str::random(4),
                'owner_id'      => $adminUser->id,
                'email'         => $data['owner_email'],
                'phone'         => $data['owner_phone'] ?? null,
                'status'        => 'active',
                'trial_ends_at' => now()->addDays((int) ($data['trial_days'] ?? 30)),
                'settings'      => [],
            ]);

            // Link admin user to tenant
            $adminUser->update(['tenant_id' => $tenant->id]);
            $adminUser->assignRole('admin');

            // Subscribe tenant to plan
            $tenant->tenantSubscription()->create([
                'plan_id'    => $plan->id,
                'status'     => 'trialing',
                'started_at' => now(),
                'ends_at'    => now()->addDays((int) ($data['trial_days'] ?? 30)),
            ]);

            return [
                'tenant'     => $tenant,
                'admin_user' => $adminUser,
            ];
        });
    }

    /**
     * Suspend a tenant (disables access for all its users).
     */
    public function suspend(Tenant $tenant, string $reason): void
    {
        $tenant->update(['status' => 'suspended', 'suspension_reason' => $reason]);
    }

    /**
     * Reactivate a suspended tenant.
     */
    public function reactivate(Tenant $tenant): void
    {
        $tenant->update(['status' => 'active', 'suspension_reason' => null]);
    }

    /**
     * Upgrade or downgrade a tenant's SaaS plan.
     */
    public function changePlan(Tenant $tenant, TenantPlan $plan): void
    {
        DB::transaction(function () use ($tenant, $plan) {
            $tenant->tenantSubscription()->updateOrCreate(
                ['tenant_id' => $tenant->id],
                ['plan_id' => $plan->id, 'status' => 'active', 'started_at' => now()]
            );
        });
    }

    /**
     * Get platform-wide aggregate stats for the super admin dashboard.
     */
    public function platformStats(): array
    {
        return [
            'total_tenants'  => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'trial_tenants'  => Tenant::where('status', 'active')
                ->where('trial_ends_at', '>', now())
                ->count(),
        ];
    }
}

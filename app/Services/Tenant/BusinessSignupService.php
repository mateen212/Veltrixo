<?php

namespace App\Services\Tenant;

use App\Models\Tenant;
use App\Models\TenantPlan;
use App\Models\User;
use App\Notifications\Business\BusinessSignupReceived;
use App\Notifications\Business\SuperAdminNewBusinessAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class BusinessSignupService
{
    /**
     * Register a new business from the public signup form.
     * Tenant starts as pending_verification — Super Admin must approve.
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Create admin user with pending account_status
            $adminUser = User::create([
                'name'              => $data['owner_name'],
                'email'             => $data['owner_email'],
                'phone'             => $data['owner_phone'] ?? null,
                'password'          => Hash::make($data['password']),
                'email_verified_at' => now(),
                'account_status'    => 'pending',
            ]);

            // Create tenant in pending_verification state
            $tenant = Tenant::create([
                'name'                => $data['business_name'],
                'slug'                => Str::slug($data['business_name']) . '-' . Str::random(4),
                'subdomain'           => $data['subdomain'],
                'owner_id'            => $adminUser->id,
                'email'               => $data['owner_email'],
                'phone'               => $data['owner_phone'] ?? null,
                'status'              => 'inactive',
                'verification_status' => 'pending_verification',
                'latitude'            => $data['latitude'] ?? null,
                'longitude'           => $data['longitude'] ?? null,
                'delivery_radius_km'  => $data['delivery_radius_km'] ?? 5,
                'address'             => [
                    'line1' => $data['business_address'] ?? null,
                    'city'  => $data['city'] ?? null,
                    'area'  => $data['area'] ?? null,
                ],
                'currency'        => 'PKR',
                'currency_symbol' => 'Rs',
                'trial_ends_at'   => now()->addDays(30),
                'meta'            => ['selected_plan' => $data['selected_plan'] ?? 'starter'],
            ]);

            // Link user → tenant
            $adminUser->update(['tenant_id' => $tenant->id]);
            $adminUser->assignRole('admin');

            // Handle logo upload if provided
            if (!empty($data['logo'])) {
                $adminUser->addMedia($data['logo'])->toMediaCollection('logo');
            }

            // Notify the business owner
            $adminUser->notify(new BusinessSignupReceived($tenant));

            // Notify all super admins
            $superAdmins = User::role('super_admin')->get();
            Notification::send($superAdmins, new SuperAdminNewBusinessAlert($tenant));

            return ['tenant' => $tenant, 'admin_user' => $adminUser];
        });
    }

    /**
     * Super admin approves a pending business.
     */
    public function approve(Tenant $tenant, User $approvedBy, ?string $planSlug = null): void
    {
        DB::transaction(function () use ($tenant, $approvedBy, $planSlug) {
            $plan = TenantPlan::where('slug', $planSlug ?? $tenant->meta['selected_plan'] ?? 'starter')
                ->firstOrFail();

            $tenant->update([
                'status'              => 'active',
                'verification_status' => 'approved',
                'verified_at'         => now(),
                'verified_by'         => $approvedBy->id,
            ]);

            // Activate admin user
            $tenant->owner?->update(['account_status' => 'active']);

            // Create trial subscription
            $tenant->tenantSubscription()->create([
                'plan_id'       => $plan->id,
                'status'        => 'trial',
                'billing_cycle' => 'monthly',
                'starts_at'     => now(),
                'ends_at'       => now()->addDays(30),
                'trial_ends_at' => now()->addDays(30),
                'amount'        => $plan->price_monthly ?? 0,
            ]);

            // Notify owner
            $tenant->owner?->notify(new \App\Notifications\Business\BusinessApproved($tenant, $plan));
        });
    }

    /**
     * Super admin rejects a pending business.
     */
    public function reject(Tenant $tenant, User $rejectedBy, string $reason): void
    {
        $tenant->update([
            'verification_status' => 'rejected',
            'verified_by'         => $rejectedBy->id,
            'rejection_reason'    => $reason,
        ]);

        $tenant->owner?->notify(new \App\Notifications\Business\BusinessRejected($tenant, $reason));
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Force the permission cache to use the array store so Redis is never required
        // (Redis may be absent in dev / CI / plain artisan environments).
        config(['permission.cache.store' => 'array']);
        app()->forgetInstance(\Spatie\Permission\PermissionRegistrar::class);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Admin
            'view admin dashboard',
            'manage deliveries',
            'manage subscriptions',
            'manage products',
            'manage wallets',
            'manage riders',
            'view analytics',
            // Rider
            'view assigned deliveries',
            'update delivery status',
            'update location',
            // Customer
            'manage own subscriptions',
            'view own deliveries',
            'manage own wallet',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin',    'guard_name' => 'web']);
        $rider = Role::firstOrCreate(['name' => 'rider',    'guard_name' => 'web']);
        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $admin->syncPermissions([
            'view admin dashboard', 'manage deliveries', 'manage subscriptions',
            'manage products', 'manage wallets', 'manage riders', 'view analytics',
        ]);

        $rider->syncPermissions([
            'view assigned deliveries', 'update delivery status', 'update location',
        ]);

        $customer->syncPermissions([
            'manage own subscriptions', 'view own deliveries', 'manage own wallet',
        ]);
    }
}

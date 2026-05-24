<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::firstOrCreate(
            ['email' => 'owner@demo.com'],
            [
                'name'              => 'Demo Owner',
                'phone'             => '+919999000001',
                'password'          => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $tenant = Tenant::firstOrCreate(
            ['slug' => 'demo-dairy'],
            [
                'name'         => 'Demo Dairy',
                'slug'         => 'demo-dairy',
                'owner_id'     => $owner->id,
                'email'        => 'admin@demo-dairy.com',
                'domain'       => 'demo-dairy.localhost',
                'status'       => 'active',
                'trial_ends_at' => now()->addDays(30),
            ]
        );

        $owner->update(['tenant_id' => $tenant->id]);
        $owner->assignRole('admin');
    }
}

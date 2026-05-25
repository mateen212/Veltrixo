<?php

namespace Database\Seeders;

use App\Models\TenantPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantPlansSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Basic plan for small businesses',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'max_customers' => 500,
                'max_riders' => 5,
                'max_products' => 100,
                'max_orders_per_month' => 1000,
                'features' => ['basic-reports', 'email-support'],
                'is_active' => true,
                'is_public' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'For growing merchants with more riders and customers',
                'price_monthly' => 49.99,
                'price_yearly' => 499.00,
                'max_customers' => 5000,
                'max_riders' => 50,
                'max_products' => 2000,
                'max_orders_per_month' => 50000,
                'features' => ['priority-support', 'analytics', 'api-access'],
                'is_active' => true,
                'is_public' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Unlimited plan with SLA and dedicated onboarding',
                'price_monthly' => 299.00,
                'price_yearly' => 2999.00,
                'max_customers' => 0,
                'max_riders' => 0,
                'max_products' => 0,
                'max_orders_per_month' => 0,
                'features' => ['sla', 'dedicated-onboarding', 'custom-integration'],
                'is_active' => true,
                'is_public' => false,
                'sort_order' => 30,
            ],
        ];

        foreach ($plans as $p) {
            TenantPlan::updateOrCreate(['slug' => $p['slug']], $p);
        }

        $this->command->info('✓ Tenant plans seeded: starter, business, enterprise');
    }
}

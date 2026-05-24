<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Rider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'demo-dairy')->firstOrFail();

        // Categories
        $milkCat = Category::firstOrCreate([
            'tenant_id' => $tenant->id,
            'name'      => 'Milk',
        ], ['slug' => 'milk', 'is_active' => true, 'sort_order' => 1]);

        $vegCat = Category::firstOrCreate([
            'tenant_id' => $tenant->id,
            'name'      => 'Vegetables',
        ], ['slug' => 'vegetables', 'is_active' => true, 'sort_order' => 2]);

        // Products
        $products = [
            ['name' => 'Full Cream Milk', 'sku' => 'MILK-FCM-500', 'unit' => 'ml',
             'price' => 30, 'category_id' => $milkCat->id, 'stock_quantity' => 1000],
            ['name' => 'Toned Milk',      'sku' => 'MILK-TND-500', 'unit' => 'ml',
             'price' => 25, 'category_id' => $milkCat->id, 'stock_quantity' => 1000],
            ['name' => 'Tomatoes',        'sku' => 'VEG-TOM-500', 'unit' => 'g',
             'price' => 40, 'category_id' => $vegCat->id,  'stock_quantity' => 500],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(
                ['tenant_id' => $tenant->id, 'sku' => $p['sku']],
                array_merge($p, ['tenant_id' => $tenant->id, 'is_active' => true, 'slug' => \Str::slug($p['name'])])
            );
        }

        // Demo customer
        $customer = User::firstOrCreate(
            ['email' => 'customer@demo.com'],
            [
                'name'              => 'Demo Customer',
                'phone'             => '+919999000002',
                'password'          => bcrypt('password'),
                'tenant_id'         => $tenant->id,
                'email_verified_at' => now(),
            ]
        );
        $customer->assignRole('customer');

        // Demo rider user
        $riderUser = User::firstOrCreate(
            ['email' => 'rider@demo.com'],
            [
                'name'              => 'Demo Rider',
                'phone'             => '+919999000003',
                'password'          => bcrypt('password'),
                'tenant_id'         => $tenant->id,
                'email_verified_at' => now(),
            ]
        );
        $riderUser->assignRole('rider');

        Rider::firstOrCreate(
            ['user_id' => $riderUser->id, 'tenant_id' => $tenant->id],
            ['vehicle_type' => 'bike', 'vehicle_number' => 'MH01AB1234', 'status' => 'active']
        );

        $this->command->info('Demo data seeded: 1 admin, 1 customer, 1 rider, 3 products.');
    }
}

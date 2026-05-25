<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            TenantSeeder::class,
            DemoDataSeeder::class,
        ]);

        // Super Admin (platform owner — no tenant)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@veltrixo.test'],
            [
                'name'              => 'Super Admin',
                'phone'             => '+923451234567',
                'password'          => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super_admin');

        $this->command->info('✓ Super Admin: superadmin@veltrixo.test / password');
        $this->command->info('✓ Admin:       admin@demo.test / password');
        $this->command->info('✓ Customer:    customer@demo.test / password');
        $this->command->info('✓ Rider:       rider@demo.test / password');
    }
}

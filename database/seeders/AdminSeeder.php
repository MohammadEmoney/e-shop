<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = Admin::query()->firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'مدیر سایت',
                'password' => 'password',
            ]
        );

        // Create an admin role (guard_name must match the 'admin' guard) and assign it.
        $role = Role::query()->firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'admin'],
        );
        $admin->assignRole($role);
    }
}

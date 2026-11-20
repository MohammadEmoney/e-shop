<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * The guard used for all admin roles/permissions.
     */
    private const GUARD = 'admin';

    /**
     * Every permission that exists in the admin area, keyed by group.
     *
     * @return array<string, list<string>>
     */
    private function permissions(): array
    {
        return [
            'dashboard' => ['dashboard.view'],
            'products' => ['products.view', 'products.create', 'products.edit', 'products.delete'],
            'categories' => ['categories.view', 'categories.create', 'categories.edit', 'categories.delete'],
            'orders' => ['orders.view', 'orders.update_status'],
            'admins' => ['admins.view', 'admins.create', 'admins.update', 'admins.delete'],
            'settings' => ['settings.view', 'settings.update'],
        ];
    }

    /**
     * Role -> permissions mapping.
     *
     * @return array<string, list<string>>
     */
    private function roles(): array
    {
        return [
            'super-admin' => array_merge(...array_values($this->permissions())),
            'manager' => [
                'dashboard.view',
                'products.view', 'products.create', 'products.edit',
                'categories.view', 'categories.create', 'categories.edit',
                'orders.view', 'orders.update_status',
                'settings.view',
            ],
            'viewer' => [
                'dashboard.view',
                'products.view',
                'categories.view',
                'orders.view',
                'settings.view',
            ],
        ];
    }

    /**
     * Seed roles and permissions for the admin guard.
     */
    public function run(): void
    {
        // Create all permissions on the admin guard.
        foreach (array_merge(...array_values($this->permissions())) as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => self::GUARD,
            ]);
        }

        // Create roles and sync their permissions.
        foreach ($this->roles() as $role => $rolePermissions) {
            $roleModel = Role::query()->firstOrCreate([
                'name' => $role,
                'guard_name' => self::GUARD,
            ]);
            $roleModel->syncPermissions($rolePermissions);
        }

        // Attach super-admin to the default admin user.
        $admin = Admin::query()->where('username', 'admin')->first();
        if ($admin) {
            $admin->syncRoles(['super-admin']);
        }
    }
}

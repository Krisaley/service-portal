<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Team Management
            'view teams',
            'create teams',
            'edit teams',
            'delete teams',
            'manage team members',

            // Module Management
            'view modules',
            'install modules',
            'uninstall modules',
            'enable modules',
            'disable modules',
            'configure modules',

            // Role & Permission Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign roles',

            // Activity Log
            'view activity log',
            'export activity log',

            // Settings
            'view settings',
            'edit settings',

            // API Access
            'access api',
            'manage api tokens',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin - All permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        // Admin - Most permissions except super admin tasks
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions([
            'view users',
            'create users',
            'edit users',
            'view teams',
            'edit teams',
            'manage team members',
            'view modules',
            'install modules',
            'uninstall modules',
            'configure modules',
            'view roles',
            'assign roles',
            'view activity log',
            'view settings',
            'edit settings',
            'access api',
        ]);

        // Staff - Limited permissions
        $staff = Role::firstOrCreate(['name' => 'Staff']);
        $staff->syncPermissions([
            'view users',
            'view teams',
            'view modules',
            'view activity log',
            'view settings',
            'access api',
        ]);

        // Customer - Minimal permissions
        $customer = Role::firstOrCreate(['name' => 'Customer']);
        $customer->syncPermissions([
            'view settings',
        ]);

        $this->command->info('Roles and permissions seeded successfully!');
    }
}

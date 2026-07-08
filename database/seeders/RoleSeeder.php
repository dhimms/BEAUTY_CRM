<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $roles = ['Admin', 'Sales', 'Customer Service', 'Manager'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Create permissions
        $permissions = [
            'manage users',
            'manage leads',
            'manage deals',
            'manage customers',
            'manage tickets',
            'manage activities',
            'manage pipeline',
            'manage sources',
            'view reports',
            'view audit logs',
            'import export data',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign all permissions to Admin
        $admin = Role::findByName('Admin');
        $admin->givePermissionTo(Permission::all());

        // Assign permissions to Sales
        $sales = Role::findByName('Sales');
        $sales->givePermissionTo(['manage leads', 'manage deals', 'manage activities']);

        // Assign permissions to CS
        $cs = Role::findByName('Customer Service');
        $cs->givePermissionTo(['manage customers', 'manage tickets', 'manage activities']);

        // Assign permissions to Manager
        $manager = Role::findByName('Manager');
        $manager->givePermissionTo(['view reports', 'view audit logs', 'manage pipeline']);
    }
}
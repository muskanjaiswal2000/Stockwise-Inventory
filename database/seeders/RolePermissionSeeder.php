<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // $admin = Role::findOrCreate('admin', 'web');
        // Role::findOrCreate('staff', 'web');

        // $permissions = config('inventory.permissions', []);
        // foreach ($permissions as $permission) {
        //     Permission::findOrCreate($permission, 'web');
        // }

        // $admin->syncPermissions(Permission::where('guard_name', 'web')->get());



        // Super Admin - All permissions
    $superAdminRole = Role::findOrCreate('super_admin', 'web');
    $superAdminRole->givePermissionTo([
        'view inventory', 'view categories', 'manage categories',
        'view products', 'create products', 'edit products', 'delete products', 'restore products',
        'view suppliers', 'manage suppliers',
        'manage stock', 'view stock',
        'manage staff permissions'
    ]);
    
    // Admin - All inventory + staff management (but not super_admin assignment)
    $adminRole = Role::findOrCreate('admin', 'web');
    $adminRole->givePermissionTo([
        'view inventory', 'view categories', 'manage categories',
        'view products', 'create products', 'edit products', 'delete products', 'restore products',
        'view suppliers', 'manage suppliers',
        'manage stock', 'view stock',
        'manage staff permissions'
    ]);
    
    // Manager - Inventory operations + staff assignment
    $managerRole = Role::findOrCreate('manager', 'web');
    $managerRole->givePermissionTo([
        'view inventory', 'view categories', 'manage categories',
        'view products', 'create products', 'edit products', 'delete products', 'restore products',
        'view suppliers', 'manage suppliers',
        'manage stock', 'view stock',
        'manage staff permissions' // If you want manager to assign staff roles
    ]);
    
    // Staff - Limited permissions based on what you assign
    $staffRole = Role::findOrCreate('staff', 'web');
    $staffRole->givePermissionTo([
        'view inventory',
        'manage stock', // Stock movement
        'view stock'
    ]);
    // Note: Remove any permissions you don't want staff to have
    }
}    


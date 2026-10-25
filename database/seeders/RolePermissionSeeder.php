<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::findOrCreate('admin', 'web');
        Role::findOrCreate('staff', 'web');

        $permissions = config('inventory.permissions', []);
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $admin->syncPermissions(Permission::where('guard_name', 'web')->get());
    }
}

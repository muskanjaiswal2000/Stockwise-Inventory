<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Modules\Inventory\Database\Seeders\InventoryDatabaseSeeder;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $superAdminRole = Role::findOrCreate('super_admin', 'web');
        $adminUser = User::where('email', env('ADMIN_NOTIFICATION_EMAIL', 'mj6550004@gmail.com'))->first();
        if ($adminUser) {
            $adminUser->syncRoles([$superAdminRole]);
        }

        // user->syncRoles([$superAdminRole]);

        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            InventoryDatabaseSeeder::class,
        ]);
    }
}

<?php

namespace Modules\Inventory\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\Supplier;
use Spatie\Permission\Models\Role;

class InventoryDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $staffRole = Role::findOrCreate('staff', 'web');

        $superAdminRole = Role::findOrCreate('super_admin', 'web');
        $adminRole = Role::findOrCreate('admin', 'web');
        $managerRole = Role::findOrCreate('manager', 'web');
        $staffRole = Role::findOrCreate('staff', 'web');

        $staffUsers = [
            [
                'name' => 'Rahul Sharma',
                'email' => 'rahul@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Priya Singh',
                'email' => 'priya@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Amit Verma',
                'email' => 'amit@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Neha Gupta',
                'email' => 'neha@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Ankit Kumar',
                'email' => 'ankit@example.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($staffUsers as $staffData) {
            $user = User::updateOrCreate(
                ['email' => $staffData['email']],
                [
                    'name' => $staffData['name'],
                    'password' => $staffData['password'],
                ]
            );
            $user->syncRoles([$staffRole]);
        }

        // Seed Categories
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Electronic devices and accessories'],
            ['name' => 'Stationery', 'slug' => 'stationery', 'description' => 'Office supplies and stationery items'],
            ['name' => 'Furniture', 'slug' => 'furniture', 'description' => 'Office furniture and equipment'],
            ['name' => 'IT Equipment', 'slug' => 'it-equipment', 'description' => 'Computers, laptops, and IT peripherals'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        // Seed Suppliers
        $suppliers = [
            ['name' => 'TechCorp Supplies', 'email' => 'orders@techcorp.com', 'phone' => '+91-9876543210'],
            ['name' => 'OfficeDepot', 'email' => 'sales@officedepot.com', 'phone' => '+91-9876543211'],
            ['name' => 'FurnitureMart', 'email' => 'info@furnituremart.com', 'phone' => '+91-9876543212'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['name' => $supplier['name']],
                $supplier
            );
        }

        // Seed Products
        $electronicsCategory = Category::where('slug', 'electronics')->first();
        $stationeryCategory = Category::where('slug', 'stationery')->first();
        $furnitureCategory = Category::where('slug', 'furniture')->first();

        $products = [
            [
                'name' => 'Laptop Dell Latitude',
                'sku' => 'LAP-001',
                'category_id' => $electronicsCategory->id,
                'unit' => 'pcs',
                'cost_price' => 45000.00,
                'selling_price' => 55000.00,
                'quantity' => 15,
                'reorder_level' => 5,
            ],
            [
                'name' => 'Wireless Mouse',
                'sku' => 'MOU-001',
                'category_id' => $electronicsCategory->id,
                'unit' => 'pcs',
                'cost_price' => 350.00,
                'selling_price' => 550.00,
                'quantity' => 50,
                'reorder_level' => 10,
            ],
            [
                'name' => 'Notebook A4',
                'sku' => 'NB-001',
                'category_id' => $stationeryCategory->id,
                'unit' => 'pcs',
                'cost_price' => 25.00,
                'selling_price' => 45.00,
                'quantity' => 200,
                'reorder_level' => 50,
            ],
            [
                'name' => 'Ballpoint Pen Blue',
                'sku' => 'PEN-001',
                'category_id' => $stationeryCategory->id,
                'unit' => 'pcs',
                'cost_price' => 10.00,
                'selling_price' => 20.00,
                'quantity' => 500,
                'reorder_level' => 100,
            ],
            [
                'name' => 'Office Chair',
                'sku' => 'CHR-001',
                'category_id' => $furnitureCategory->id,
                'unit' => 'pcs',
                'cost_price' => 3500.00,
                'selling_price' => 5000.00,
                'quantity' => 20,
                'reorder_level' => 5,
            ],
            [
                'name' => 'Office Desk',
                'sku' => 'DSK-001',
                'category_id' => $furnitureCategory->id,
                'unit' => 'pcs',
                'cost_price' => 8000.00,
                'selling_price' => 12000.00,
                'quantity' => 10,
                'reorder_level' => 3,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}

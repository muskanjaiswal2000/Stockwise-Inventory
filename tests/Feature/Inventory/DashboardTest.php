<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockMovement;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_total_product_and_low_stock_counts(): void
    {
        $this->actingAs(User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]));
        $category = Category::create([
            'name' => 'Stationery',
            'slug' => 'stationery',
        ]);

        $this->createProduct($category, 'Low stock notebook', 'DASH-LOW', 1, 2);
        $this->createProduct($category, 'Available notebook', 'DASH-OK', 5, 2);

        $this->get(route('dashboard', absolute: false))
            ->assertOk()
            ->assertSee('Total products')
            ->assertSee('Low-stock products')
            ->assertSeeHtml('<p class="mt-3 text-5xl font-extrabold tracking-tight">2</p>')
            ->assertSeeHtml('<p class="mt-3 text-5xl font-extrabold tracking-tight text-slate-900">1</p>');
    }

    public function test_dashboard_shows_only_the_ten_most_recent_stock_movements(): void
    {
        $user = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);
        $category = Category::create([
            'name' => 'Stationery',
            'slug' => 'stationery',
        ]);
        $product = $this->createProduct($category, 'Activity notebook', 'DASH-ACTIVITY', 20, 2);

        foreach (range(1, 12) as $number) {
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'type' => 'in',
                'quantity' => $number,
                'reason' => "Dashboard activity {$number}",
                'created_at' => now()->subMinutes(12 - $number),
            ]);
        }

        $this->get(route('dashboard', absolute: false))
            ->assertOk()
            ->assertSee('Showing the 10 most recent stock movements')
            ->assertSee('Dashboard activity 12')
            ->assertSee('Dashboard activity 3')
            ->assertDontSee('Dashboard activity 2')
            ->assertSeeInOrder([
                'Dashboard activity 12',
                'Dashboard activity 11',
                'Dashboard activity 10',
                'Dashboard activity 9',
                'Dashboard activity 8',
                'Dashboard activity 7',
                'Dashboard activity 6',
                'Dashboard activity 5',
                'Dashboard activity 4',
                'Dashboard activity 3',
            ]);
    }

    private function createProduct(Category $category, string $name, string $sku, int $quantity, int $reorderLevel): Product
    {
        return Product::create([
            'name' => $name,
            'sku' => $sku,
            'category_id' => $category->id,
            'unit' => 'pcs',
            'cost_price' => 2.50,
            'selling_price' => 4.00,
            'quantity' => $quantity,
            'reorder_level' => $reorderLevel,
        ]);
    }
}

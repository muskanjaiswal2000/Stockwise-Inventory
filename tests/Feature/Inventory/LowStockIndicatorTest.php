<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Inventory\Http\Controllers\Products\Index;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Tests\TestCase;

class LowStockIndicatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_at_or_below_reorder_level_are_counted_and_marked_low(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $category = Category::create([
            'name' => 'Stationery',
            'slug' => 'stationery',
        ]);

        $this->createProduct($category, 'Low stock item', 'LOW-001', 2, 2);
        $this->createProduct($category, 'Healthy stock item', 'GOOD-001', 5, 2);

        Livewire::test(Index::class)
            ->assertSee('Needs restocking')
            ->assertSee('1')
            ->assertSee('Low stock item')
            ->assertSee('Healthy stock item')
            ->assertSee('Low');
    }

    public function test_low_stock_count_includes_products_outside_the_current_search(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $category = Category::create([
            'name' => 'Stationery',
            'slug' => 'stationery',
        ]);

        $this->createProduct($category, 'Low stock item', 'LOW-001', 1, 2);
        $this->createProduct($category, 'Healthy stock item', 'GOOD-001', 5, 2);

        Livewire::test(Index::class)
            ->set('search', 'Healthy')
            ->assertSee('Needs restocking')
            ->assertSee('1')
            ->assertSee('Healthy stock item')
            ->assertDontSee('Low stock item');
    }

    private function createProduct(Category $category, string $name, string $sku, int $quantity, int $reorderLevel): void
    {
        Product::create([
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

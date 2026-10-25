<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Inventory\Http\Controllers\Stock\History;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockMovement;
use Tests\TestCase;

class StockHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_stock_movement_history_newest_first(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);
        $product = $this->createProduct();

        $older = StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'in',
            'quantity' => 5,
            'reason' => 'Supplier delivery',
        ]);
        $newer = StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'out',
            'quantity' => 2,
            'reason' => 'Customer sale',
        ]);
        $older->forceFill(['created_at' => now()->subMinute()])->save();
        $newer->forceFill(['created_at' => now()])->save();

        $this->get(route('inventory.stock.history', absolute: false))
            ->assertOk()
            ->assertSee('Stock history');

        Livewire::test(History::class)
            ->assertSee('Stock history')
            ->assertSee('Supplier delivery')
            ->assertSee('Customer sale')
            ->assertSeeInOrder(['Customer sale', 'Supplier delivery']);
    }

    private function createProduct(): Product
    {
        $category = Category::create([
            'name' => 'Stationery',
            'slug' => 'stationery',
        ]);

        return Product::create([
            'name' => 'Notebook',
            'sku' => 'NOTEBOOK-HISTORY',
            'category_id' => $category->id,
            'unit' => 'pcs',
            'cost_price' => 2.50,
            'selling_price' => 4.00,
            'quantity' => 10,
            'reorder_level' => 2,
        ]);
    }
}

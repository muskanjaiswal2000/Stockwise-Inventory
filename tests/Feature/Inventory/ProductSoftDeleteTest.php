<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Inventory\Http\Controllers\Products\Index;
use Modules\Inventory\Http\Controllers\Products\Trashed;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockMovement;
use Tests\TestCase;

class ProductSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_soft_delete_and_restore_a_product(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $product = $this->createProduct();
        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => 'in',
            'quantity' => 5,
            'reason' => 'Original stock',
        ]);

        Livewire::test(Index::class)
            ->call('delete', $product->id)
            ->assertSee('Product moved to deleted products.');

        $this->assertSoftDeleted('products', ['id' => $product->id]);
        $this->get(route('inventory.products.index', absolute: false))
            ->assertOk()
            ->assertDontSee('Soft-delete notebook');

        $this->get(route('inventory.products.trashed', absolute: false))
            ->assertOk()
            ->assertSee('Deleted products')
            ->assertSee('Soft-delete notebook');
        $this->get(route('inventory.stock.history', absolute: false))
            ->assertOk()
            ->assertSee('Soft-delete notebook');

        Livewire::test(Trashed::class)
            ->call('restore', $product->id)
            ->assertSee('Product restored successfully.');

        $this->assertNotSoftDeleted('products', ['id' => $product->id]);
        $this->get(route('inventory.products.index', absolute: false))
            ->assertSee('Soft-delete notebook');
    }

    private function createProduct(): Product
    {
        $category = Category::create(['name' => 'Stationery', 'slug' => 'stationery']);

        return Product::create([
            'name' => 'Soft-delete notebook',
            'sku' => 'SOFT-DELETE-001',
            'category_id' => $category->id,
            'unit' => 'pcs',
            'cost_price' => 2,
            'selling_price' => 4,
            'quantity' => 5,
            'reorder_level' => 1,
        ]);
    }
}

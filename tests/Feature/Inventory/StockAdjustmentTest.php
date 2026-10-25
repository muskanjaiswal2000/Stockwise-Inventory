<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Inventory\Http\Controllers\Stock\Adjustment;
use Modules\Inventory\Http\Controllers\Stock\History;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockMovement;
use Tests\TestCase;

class StockAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_adjust_stock_up_to_the_counted_quantity(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $product = $this->createProduct(quantity: 5);

        Livewire::test(Adjustment::class)
            ->set('product_id', (string) $product->id)
            ->set('actual_quantity', '8')
            ->set('reason', 'Physical count')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSee('Stock adjusted successfully');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'quantity' => 8]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 3,
            'reason' => 'Manual adjustment to 8 units: Physical count',
            'is_adjustment' => true,
        ]);

        Livewire::test(History::class)
            ->assertSee('Adjustment')
            ->assertSee('Manual adjustment to 8 units: Physical count');
    }

    public function test_admin_can_adjust_stock_down_to_zero_without_negative_stock(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $product = $this->createProduct(quantity: 5);

        Livewire::test(Adjustment::class)
            ->set('product_id', (string) $product->id)
            ->set('actual_quantity', '0')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSee('Stock adjusted successfully');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'quantity' => 0]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 5,
        ]);
    }

    public function test_matching_count_does_not_create_a_movement(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $product = $this->createProduct(quantity: 5);

        Livewire::test(Adjustment::class)
            ->set('product_id', (string) $product->id)
            ->set('actual_quantity', '5')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSee('No adjustment was needed');

        $this->assertSame(5, $product->fresh()->quantity);
        $this->assertSame(0, StockMovement::count());
    }

    public function test_negative_or_unsupported_counted_quantity_is_rejected(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $product = $this->createProduct(quantity: 5);

        Livewire::test(Adjustment::class)
            ->set('product_id', (string) $product->id)
            ->set('actual_quantity', '-1')
            ->call('submit')
            ->assertHasErrors(['actual_quantity']);

        Livewire::test(Adjustment::class)
            ->set('product_id', (string) $product->id)
            ->set('actual_quantity', '2147483648')
            ->call('submit')
            ->assertHasErrors(['actual_quantity']);

        $this->assertSame(5, $product->fresh()->quantity);
        $this->assertSame(0, StockMovement::count());
    }

    public function test_adjustment_rejects_soft_deleted_products(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $product = $this->createProduct(quantity: 5);
        $product->delete();

        Livewire::test(Adjustment::class)
            ->set('product_id', (string) $product->id)
            ->set('actual_quantity', '7')
            ->call('submit')
            ->assertHasErrors(['product_id']);

        $this->assertSame(0, StockMovement::count());
    }

    private function createProduct(int $quantity): Product
    {
        $category = Category::create(['name' => 'Stationery', 'slug' => 'stationery']);

        return Product::create([
            'name' => 'Notebook',
            'sku' => 'ADJUST-' . $quantity,
            'category_id' => $category->id,
            'unit' => 'pcs',
            'cost_price' => 2,
            'selling_price' => 4,
            'quantity' => $quantity,
            'reorder_level' => 1,
        ]);
    }
}

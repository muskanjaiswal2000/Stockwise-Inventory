<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Inventory\Http\Controllers\Stock\Movement;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockMovement;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_the_stock_movement_page(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get(route('inventory.stock.movement', absolute: false))
            ->assertOk()
            ->assertSee('Stock movement')
            ->assertSee('Manage catalog requests');
    }

    public function test_admin_can_record_stock_in(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);
        $product = $this->createProduct(quantity: 5);

        Livewire::test(Movement::class)
            ->set('product_id', (string) $product->id)
            ->set('type', 'in')
            ->set('quantity', 3)
            ->set('reason', 'New shipment')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSee('Stock In successful!');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 8,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 3,
            'reason' => 'New shipment',
            'user_id' => $user->id,
        ]);
    }

    public function test_admin_can_record_stock_out(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $product = $this->createProduct(quantity: 5);

        Livewire::test(Movement::class)
            ->set('product_id', (string) $product->id)
            ->set('type', 'out')
            ->set('quantity', 2)
            ->set('reason', 'Customer sale')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSee('Stock Out successful!');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 3,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 2,
            'reason' => 'Customer sale',
        ]);
    }

    public function test_admin_cannot_record_stock_out_beyond_available_quantity(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $product = $this->createProduct(quantity: 2);

        Livewire::test(Movement::class)
            ->set('product_id', (string) $product->id)
            ->set('type', 'out')
            ->set('quantity', 3)
            ->call('submit')
            ->assertHasErrors(['quantity']);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 2,
        ]);
        $this->assertSame(0, StockMovement::count());
    }

    public function test_invalid_stock_movement_values_are_rejected(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $product = $this->createProduct(quantity: 5);

        Livewire::test(Movement::class)
            ->set('product_id', (string) $product->id)
            ->set('type', 'in')
            ->set('quantity', 0)
            ->call('submit')
            ->assertHasErrors(['quantity']);

        Livewire::test(Movement::class)
            ->set('product_id', (string) $product->id)
            ->set('type', 'invalid')
            ->set('quantity', 1)
            ->call('submit')
            ->assertHasErrors(['type']);

        $this->assertSame(5, $product->fresh()->quantity);
        $this->assertSame(0, StockMovement::count());
    }

    public function test_stock_cannot_be_increased_beyond_the_database_integer_limit(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $product = $this->createProduct(quantity: 2_147_483_647);

        Livewire::test(Movement::class)
            ->set('product_id', (string) $product->id)
            ->set('type', 'in')
            ->set('quantity', 1)
            ->call('submit')
            ->assertHasErrors(['quantity']);

        $this->assertSame(2_147_483_647, $product->fresh()->quantity);
        $this->assertSame(0, StockMovement::count());
    }

    public function test_stock_movement_rejects_soft_deleted_products(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $product = $this->createProduct(quantity: 5);
        $product->delete();

        Livewire::test(Movement::class)
            ->set('product_id', (string) $product->id)
            ->set('type', 'in')
            ->set('quantity', 2)
            ->call('submit')
            ->assertHasErrors(['product_id']);

        $this->assertSame(0, StockMovement::count());
    }

    private function createProduct(int $quantity): Product
    {
        $category = Category::create([
            'name' => 'Stationery',
            'slug' => 'stationery',
        ]);

        return Product::create([
            'name' => 'Notebook',
            'sku' => 'NOTEBOOK-' . $quantity,
            'category_id' => $category->id,
            'unit' => 'pcs',
            'cost_price' => 2.50,
            'selling_price' => 4.00,
            'quantity' => $quantity,
            'reorder_level' => 1,
        ]);
    }
}

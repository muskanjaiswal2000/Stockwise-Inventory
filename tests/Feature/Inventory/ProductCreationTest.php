<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Inventory\Http\Controllers\Products\Create;
use Modules\Inventory\Http\Controllers\Products\Edit;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Tests\TestCase;

class ProductCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_the_product_creation_page(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get(route('inventory.products.create', absolute: false))
            ->assertOk()
            ->assertSee('Create product');
    }

    public function test_admin_can_create_a_product_from_the_livewire_form(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $category = Category::create([
            'name' => 'Stationery',
            'slug' => 'stationery',
        ]);

        Livewire::test(Create::class)
            ->set('name', 'Notebook')
            ->set('sku', 'NOTEBOOK-001')
            ->set('category_id', (string) $category->id)
            ->set('unit', 'pcs')
            ->set('cost_price', '2.50')
            ->set('selling_price', '4.00')
            ->set('quantity', '10')
            ->set('reorder_level', '2')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('inventory.products.index', absolute: false));

        $this->assertDatabaseHas('products', [
            'name' => 'Notebook',
            'sku' => 'NOTEBOOK-001',
            'category_id' => $category->id,
            'quantity' => 10,
        ]);

        $this->get(route('inventory.products.index', absolute: false))
            ->assertOk()
            ->assertSee('Notebook');
    }

    public function test_admin_can_edit_a_product(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $category = Category::create([
            'name' => 'Stationery',
            'slug' => 'stationery',
        ]);
        $product = Product::create([
            'name' => 'Notebook',
            'sku' => 'NOTEBOOK-001',
            'category_id' => $category->id,
            'unit' => 'pcs',
            'cost_price' => 2.50,
            'selling_price' => 4.00,
            'quantity' => 10,
            'reorder_level' => 2,
        ]);

        $this->get(route('inventory.products.edit', $product->id, absolute: false))
            ->assertOk()
            ->assertSee('Edit product')
            ->assertDontSee('Starting quantity');

        Livewire::test(Edit::class, ['product' => $product])
            ->set('name', 'Updated notebook')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('inventory.products.index', absolute: false));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated notebook',
            'sku' => 'NOTEBOOK-001',
            'quantity' => 10,
        ]);
    }

    public function test_product_form_rejects_invalid_price_and_unsupported_quantities(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $category = Category::create([
            'name' => 'Stationery',
            'slug' => 'stationery',
        ]);

        Livewire::test(Create::class)
            ->set('name', 'Notebook')
            ->set('sku', 'NOTEBOOK-INVALID')
            ->set('category_id', (string) $category->id)
            ->set('cost_price', '1.999')
            ->set('selling_price', '4.00')
            ->set('quantity', '2147483648')
            ->set('reorder_level', '0')
            ->call('save')
            ->assertHasErrors(['cost_price', 'quantity']);

        $this->assertDatabaseMissing('products', ['sku' => 'NOTEBOOK-INVALID']);
    }
}

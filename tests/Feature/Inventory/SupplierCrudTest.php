<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Inventory\Http\Controllers\Products\Create as ProductCreate;
use Modules\Inventory\Http\Controllers\Products\Edit as ProductEdit;
use Modules\Inventory\Http\Controllers\Suppliers\Create;
use Modules\Inventory\Http\Controllers\Suppliers\Edit;
use Modules\Inventory\Http\Controllers\Suppliers\Index;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\Supplier;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_a_supplier(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(Create::class)
            ->set('name', 'Northwind Supply')
            ->set('email', 'contact@northwind.test')
            ->set('phone', '555-0100')
            ->set('address', '12 Supply Road')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('inventory.suppliers.index', absolute: false));

        $supplier = Supplier::where('email', 'contact@northwind.test')->firstOrFail();

        Livewire::test(Edit::class, ['supplier' => $supplier])
            ->set('name', 'Northwind Office Supply')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('inventory.suppliers.index', absolute: false));

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Northwind Office Supply',
        ]);
    }

    public function test_admin_can_link_multiple_suppliers_to_a_product_and_edit_the_links(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $category = Category::create(['name' => 'Stationery', 'slug' => 'stationery']);
        $firstSupplier = Supplier::create(['name' => 'First supplier']);
        $secondSupplier = Supplier::create(['name' => 'Second supplier']);
        $thirdSupplier = Supplier::create(['name' => 'Third supplier']);

        Livewire::test(ProductCreate::class)
            ->set('name', 'Notebook')
            ->set('sku', 'SUPPLIER-LINK-001')
            ->set('category_id', (string) $category->id)
            ->set('supplier_ids', [(string) $firstSupplier->id, (string) $secondSupplier->id])
            ->set('unit', 'pcs')
            ->set('cost_price', '2.50')
            ->set('selling_price', '4.00')
            ->set('quantity', '10')
            ->set('reorder_level', '2')
            ->call('save')
            ->assertHasNoErrors();

        $product = Product::where('sku', 'SUPPLIER-LINK-001')->firstOrFail();
        $this->assertEqualsCanonicalizing(
            [$firstSupplier->id, $secondSupplier->id],
            $product->suppliers()->pluck('suppliers.id')->all()
        );

        Livewire::test(ProductEdit::class, ['product' => $product])
            ->set('supplier_ids', [(string) $secondSupplier->id, (string) $thirdSupplier->id])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEqualsCanonicalizing(
            [$secondSupplier->id, $thirdSupplier->id],
            $product->fresh()->suppliers()->pluck('suppliers.id')->all()
        );
    }

    public function test_supplier_linked_to_a_product_cannot_be_deleted(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $category = Category::create(['name' => 'Stationery', 'slug' => 'stationery']);
        $supplier = Supplier::create(['name' => 'Linked supplier']);
        $product = Product::create([
            'name' => 'Notebook',
            'sku' => 'SUPPLIER-DELETE-001',
            'category_id' => $category->id,
            'unit' => 'pcs',
            'cost_price' => 2,
            'selling_price' => 4,
            'quantity' => 2,
            'reorder_level' => 1,
        ]);
        $product->suppliers()->attach($supplier);

        Livewire::test(Index::class)
            ->call('delete', $supplier->id)
            ->assertSee('Remove this supplier from its products before deleting it.');

        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
    }
}

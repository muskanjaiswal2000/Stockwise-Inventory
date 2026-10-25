<?php

namespace Modules\Inventory\Http\Controllers\Products;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\Supplier;
use Modules\Inventory\Events\LowStockDetected;
use Modules\Inventory\Events\ProductCreatedOrUpdated;

class Create extends Component
{
    public string $name = '';
    public string $sku = '';
    public string $category_id = '';
    public string $unit = 'pcs';
    public string $cost_price = '0';
    public string $selling_price = '0';
    public string $quantity = '0';
    public string $reorder_level = '0';
    public array $supplier_ids = [];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            'category_id' => 'required|integer|exists:categories,id',
            'unit' => 'required|string|max:50',
            'cost_price' => 'required|numeric|min:0|max:99999999.99|decimal:0,2',
            'selling_price' => 'required|numeric|min:0|max:99999999.99|decimal:0,2',
            'quantity' => 'required|integer|min:0|max:2147483647',
            'reorder_level' => 'required|integer|min:0|max:2147483647',
            'supplier_ids' => 'array',
            'supplier_ids.*' => ['integer', 'distinct', Rule::exists('suppliers', 'id')],
        ];
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->can('create products'), 403);

        $validated = $this->validate();

        $supplierIds = $validated['supplier_ids'];
        unset($validated['supplier_ids']);

        $product = Product::create($validated);
        $product->suppliers()->sync($supplierIds);
        event(new ProductCreatedOrUpdated($product->fresh(), 'created'));
        $freshProduct = $product->fresh();
        if ($freshProduct->quantity <= $freshProduct->reorder_level) {
            event(new LowStockDetected($freshProduct));
        }

        session()->flash('success', 'Product created successfully.');
        $this->redirect(route('inventory.products.index'), navigate: true);
    }

    public function render()
    {
        return view('inventory::livewire.products.create', [
            'categories' => Category::query()->orderBy('name')->get(),
            'suppliers' => Supplier::query()->orderBy('name')->get(),
            'pageTitle' => 'Create product',
            'buttonText' => 'Create product',
        ])->layout('livewire.layout.app');
    }
}

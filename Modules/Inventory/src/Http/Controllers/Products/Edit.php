<?php

namespace Modules\Inventory\Http\Controllers\Products;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\Supplier;
use Modules\Inventory\Events\LowStockDetected;
use Modules\Inventory\Events\ProductCreatedOrUpdated;

class Edit extends Component
{
    public Product $product;
    public string $name = '';
    public string $sku = '';
    public string $category_id = '';
    public string $unit = 'pcs';
    public string $cost_price = '0';
    public string $selling_price = '0';
    public string $reorder_level = '0';
    public array $supplier_ids = [];

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->category_id = (string) $product->category_id;
        $this->unit = $product->unit;
        $this->cost_price = (string) $product->cost_price;
        $this->selling_price = (string) $product->selling_price;
        $this->reorder_level = (string) $product->reorder_level;
        $this->supplier_ids = $product->suppliers()->pluck('suppliers.id')->map(fn ($id) => (string) $id)->all();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($this->product->id),
            ],
            'category_id' => 'required|integer|exists:categories,id',
            'unit' => 'required|string|max:50',
            'cost_price' => 'required|numeric|min:0|max:99999999.99|decimal:0,2',
            'selling_price' => 'required|numeric|min:0|max:99999999.99|decimal:0,2',
            'reorder_level' => 'required|integer|min:0|max:2147483647',
            'supplier_ids' => 'array',
            'supplier_ids.*' => ['integer', 'distinct', Rule::exists('suppliers', 'id')],
        ];
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->can('edit products'), 403);

        $validated = $this->validate();
        $supplierIds = $validated['supplier_ids'];
        unset($validated['supplier_ids']);

        $this->product->update($validated);
        $this->product->suppliers()->sync($supplierIds);
        event(new ProductCreatedOrUpdated($this->product->fresh(), 'updated'));
        $freshProduct = $this->product->fresh();
        if ($freshProduct->quantity <= $freshProduct->reorder_level) {
            event(new LowStockDetected($freshProduct));
        }

        session()->flash('success', 'Product updated successfully.');
        $this->redirect(route('inventory.products.index'), navigate: true);
    }

    public function render()
    {
        return view('inventory::livewire.products.create', [
            'categories' => Category::query()->orderBy('name')->get(),
            'suppliers' => Supplier::query()->orderBy('name')->get(),
            'pageTitle' => 'Edit product',
            'buttonText' => 'Save changes',
        ])->layout('livewire.layout.app');
    }
}

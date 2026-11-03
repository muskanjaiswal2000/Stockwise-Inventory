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
    public string $cost_price = '';
    public string $selling_price = '';
    public string $quantity = '';
    public string $reorder_level = '';
    public array $supplier_ids = [];

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/.*[a-zA-Z].*/',
            ],

            'sku' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9_-]+$/',
                'unique:products,sku',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],

            'unit' => [
                'required',
                'string',
                'max:20',
                'regex:/^[a-zA-Z]+$/',
            ],

            'cost_price' => [
                'required',
                'numeric',
                'gt:0',
                'max:99999999.99',
                'decimal:0,2',
            ],

            'selling_price' => [
                'required',
                'numeric',
                'gt:0',
                'max:99999999.99',
                'decimal:0,2',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:0',
                'not_in:0',
                'max:1000000',
            ],

            'reorder_level' => [
                'required',
                'integer',
                'min:1',
                'max:1000000',
            ],

            'supplier_ids' => ['array'],
            'supplier_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('suppliers', 'id'),
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Please enter a product name.',
            'name.min' => 'Product name must contain at least 2 characters.',
            'name.regex' => 'Product name must contain at least one letter.',

            'sku.required' => 'Please enter a product SKU.',
            'sku.unique' => 'This SKU is already being used.',
            'sku.regex' => 'SKU can contain only letters, numbers, hyphens and underscores.',

            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',

            'unit.required' => 'Please enter a unit.',
            'unit.regex' => 'Unit can contain only letters.',

            'cost_price.required' => 'Please enter the cost price.',
            'cost_price.gt' => 'Cost price must be greater than zero.',
            'cost_price.numeric' => 'Cost price must be a valid number.',

            'selling_price.required' => 'Please enter the selling price.',
            'selling_price.gt' => 'Selling price must be greater than zero.',
            'selling_price.numeric' => 'Selling price must be a valid number.',

            'quantity.required' => 'Starting quantity is required.',
            'quantity.integer' => 'Starting quantity must be a whole number.',
            'quantity.min' => 'Quantity cannot be negative.',
            'quantity.not_in' => 'Starting quantity must be greater than 0.',
            
            'reorder_level.required' => 'Please enter a reorder level.',
            'reorder_level.integer' => 'Reorder level must be a whole number.',
            'reorder_level.min' => 'Reorder level must be at least 1.',

            'supplier_ids.*.exists' => 'One of the selected suppliers is invalid.',
            'supplier_ids.*.distinct' => 'A supplier cannot be selected more than once.',
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

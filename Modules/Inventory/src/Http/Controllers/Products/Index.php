<?php

namespace Modules\Inventory\Http\Controllers\Products;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Inventory\Models\Product;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $category = '';
    public string $stock = 'all';
    public string $sort = 'newest';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedStock(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function delete(int $productId): void
    {
        abort_unless(auth()->user()?->can('delete products'), 403);

        $product = Product::findOrFail($productId);
        $product->delete();
        $this->resetPage();

        session()->flash('success', 'Product moved to deleted products.');
    }

    public function render()
    {
        $lowStockCount = Product::query()
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->count();

        $productsQuery = Product::query()
            ->with(['category', 'suppliers'])
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->category, fn($query) => $query->where('category_id', $this->category))
            ->when($this->stock === 'low', fn($query) => $query->whereColumn('quantity', '<=', 'reorder_level'))
            ->when($this->stock === 'in_stock', fn($query) => $query->where('quantity', '>', 0))
            ->when($this->stock === 'out_of_stock', fn($query) => $query->where('quantity', '<=', 0))
            ->when($this->sort === 'name_asc', fn($query) => $query->orderBy('name'))
            ->when($this->sort === 'name_desc', fn($query) => $query->orderByDesc('name'))
            ->when($this->sort === 'cost_asc', fn($query) => $query->orderBy('cost_price'))
            ->when($this->sort === 'cost_desc', fn($query) => $query->orderByDesc('cost_price'))
            ->when($this->sort === 'selling_asc', fn($query) => $query->orderBy('selling_price'))
            ->when($this->sort === 'selling_desc', fn($query) => $query->orderByDesc('selling_price'))
            ->when($this->sort === 'stock_desc', fn($query) => $query->orderByDesc('quantity'))
            ->when($this->sort === 'newest', fn($query) => $query->latest());

        $products = $productsQuery
            ->paginate(10);

        return view('inventory::livewire.products.index', [
            'products' => $products,
            'lowStockCount' => $lowStockCount,
            'categories' => \Modules\Inventory\Models\Category::query()->orderBy('name')->get(['id', 'name']),
        ])->layout('livewire.layout.app');
    }
}

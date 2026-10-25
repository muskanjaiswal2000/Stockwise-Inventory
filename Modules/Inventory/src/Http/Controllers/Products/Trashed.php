<?php

namespace Modules\Inventory\Http\Controllers\Products;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Inventory\Models\Product;

class Trashed extends Component
{
    use WithPagination;

    public function restore(int $productId): void
    {
        abort_unless(auth()->user()?->can('restore products'), 403);

        $product = Product::onlyTrashed()->findOrFail($productId);
        $product->restore();
        $this->resetPage();

        session()->flash('success', 'Product restored successfully.');
    }

    public function render()
    {
        return view('inventory::livewire.products.trashed', [
            'products' => Product::onlyTrashed()
                ->with(['category', 'suppliers'])
                ->latest('deleted_at')
                ->paginate(10),
        ])->layout('livewire.layout.app');
    }
}

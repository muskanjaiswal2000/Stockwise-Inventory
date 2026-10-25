<?php

namespace Modules\Inventory\Http\Controllers\Suppliers;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Inventory\Models\Supplier;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sort = 'name_asc';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function delete(int $supplierId): void
    {
        abort_unless(auth()->user()?->can('manage suppliers'), 403);

        $supplier = Supplier::withCount('products')->findOrFail($supplierId);

        if ($supplier->products()->withTrashed()->exists()) {
            session()->flash('error', 'Remove this supplier from its products before deleting it.');
            return;
        }

        $supplier->delete();
        session()->flash('success', 'Supplier deleted successfully.');
    }

    public function render()
    {
        return view('inventory::livewire.suppliers.index', [
            'suppliers' => Supplier::query()
                ->withCount('products')
                ->when($this->search, function ($query) {
                    $query->where(function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->sort === 'name_asc', fn($query) => $query->orderBy('name'))
                ->when($this->sort === 'name_desc', fn($query) => $query->orderByDesc('name'))
                ->when($this->sort === 'products_desc', fn($query) => $query->orderByDesc('products_count'))
                ->when($this->sort === 'products_asc', fn($query) => $query->orderBy('products_count'))
                ->paginate(10),
        ])->layout('livewire.layout.app');
    }
}

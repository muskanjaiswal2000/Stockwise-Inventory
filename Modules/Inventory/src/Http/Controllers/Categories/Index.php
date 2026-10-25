<?php

namespace Modules\Inventory\Http\Controllers\Categories;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Inventory\Models\Category;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sort = 'newest';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        abort_unless(auth()->user()?->can('manage categories'), 403);

        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {
            session()->flash('error', 'Cannot delete category that has products.');
            return;
        }

        $category->delete();
        session()->flash('success', 'Category deleted successfully.');
    }

    public function render()
    {
        $categories = Category::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->when($this->sort === 'name_asc', fn($q) => $q->orderBy('name'))
            ->when($this->sort === 'name_desc', fn($q) => $q->orderByDesc('name'))
            ->when($this->sort === 'products_desc', fn($q) => $q->withCount('products')->orderByDesc('products_count'))
            ->when($this->sort === 'products_asc', fn($q) => $q->withCount('products')->orderBy('products_count'))
            ->when($this->sort === 'newest', fn($q) => $q->latest())
            ->paginate(10);

        return view('inventory::livewire.categories.index', [
            'categories' => $categories,
        ])->layout('livewire.layout.app');
    }
}

<?php

namespace Modules\Inventory\Http\Controllers\Categories;

use Illuminate\Support\Str;
use Livewire\Component;
use Modules\Inventory\Models\Category;

class Edit extends Component
{
    public Category $category;
    public string $name = '';
    public string $description = '';

    public function mount(Category $category)
    {
        $this->category = $category;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name,'.$this->category->id,
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function update()
    {
        abort_unless(auth()->user()?->can('manage categories'), 403);

        $this->validate();

        $this->category->update([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
        ]);

        session()->flash('success', 'Category updated successfully.');
        return redirect()->route('inventory.categories.index');
    }

    public function render()
    {
        return view('inventory::livewire.categories.edit')
            ->layout('livewire.layout.app');
    }
}

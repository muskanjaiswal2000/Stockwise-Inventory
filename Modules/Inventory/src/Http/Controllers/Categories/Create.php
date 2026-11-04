<?php

namespace Modules\Inventory\Http\Controllers\Categories;

use Illuminate\Support\Str;
use Livewire\Component;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Events\CategoryCreated;

class Create extends Component
{
    public string $name = '';
    public string $description = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function save()
    {
        abort_unless(auth()->user()?->can('manage categories'), 403);

        $this->validate();

        $category = Category::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
        ]);

        event(new CategoryCreated($category));

        session()->flash('success', 'Category created successfully.');
        return $this->redirect(route('inventory.categories.index'), navigate: true);
    }

    public function render()
    {
        return view('inventory::livewire.categories.create')
            ->layout('livewire.layout.app');
    }
}

<?php

namespace Modules\Inventory\Http\Controllers\Suppliers;

use Livewire\Component;
use Modules\Inventory\Models\Supplier;

class Create extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:2000',
        ];
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->can('manage suppliers'), 403);

        Supplier::create($this->validate());

        session()->flash('success', 'Supplier created successfully.');
        $this->redirect(route('inventory.suppliers.index'), navigate: true);
    }

    public function render()
    {
        return view('inventory::livewire.suppliers.form', [
            'pageTitle' => 'Add supplier',
            'buttonText' => 'Create supplier',
        ])->layout('livewire.layout.app');
    }
}

<?php

namespace Modules\Inventory\Http\Controllers\Suppliers;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Inventory\Models\Supplier;

class Edit extends Component
{
    public Supplier $supplier;
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';

    public function mount(Supplier $supplier): void
    {
        $this->supplier = $supplier;
        $this->name = $supplier->name;
        $this->email = $supplier->email ?? '';
        $this->phone = $supplier->phone ?? '';
        $this->address = $supplier->address ?? '';
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('suppliers', 'email')->ignore($this->supplier->id),
            ],
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:2000',
        ];
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->can('manage suppliers'), 403);

        $this->supplier->update($this->validate());

        session()->flash('success', 'Supplier updated successfully.');
        $this->redirect(route('inventory.suppliers.index'), navigate: true);
    }

    public function render()
    {
        return view('inventory::livewire.suppliers.form', [
            'pageTitle' => 'Edit supplier',
            'buttonText' => 'Save changes',
        ])->layout('livewire.layout.app');
    }
}

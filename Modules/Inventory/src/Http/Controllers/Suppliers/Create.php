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
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:suppliers,email',
            ],

            'phone' => [
                'required',
                'string',
                'regex:/^(?:\+91[\s-]?)?[6-9]\d{9}$/',
            ],

            'address' => [
                'required',
                'string',
                'min:5',
                'max:2000',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Supplier name is required.',
            'name.min' => 'Supplier name must be at least 2 characters.',

            'email.required' => 'Supplier email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',

            'phone.required' => 'Supplier phone number is required.',
            'phone.regex' => 'Please enter a valid 10-digit phone number',

            'address.required' => 'Supplier address is required.',
            'address.min' => 'Supplier address must be at least 5 characters.',
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

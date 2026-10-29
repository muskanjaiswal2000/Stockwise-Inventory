<?php

namespace Modules\Inventory\Http\Controllers\Staff;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Create extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $staff = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $staff->assignRole(Role::findOrCreate('staff', 'web'));

        session()->flash('success', "Staff member {$staff->name} created.");

        $this->redirect(route('inventory.staff.edit', $staff), navigate: true);
    }

    public function continueToPermissions(): void
    {
        $this->validate();

        $this->redirect(
            route('inventory.staff.permissions'),
            navigate: true
        );
    }

    public function render()
    {
        return view('inventory::livewire.staff.create')
            ->layout('livewire.layout.app');
    }
}

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

    public function mount(): void
    {
        $this->authorizeStaffCreation();
    }

    private function authorizeStaffCreation(): void
    {
        abort_unless(
            auth()->user()?->hasAnyRole(['super_admin', 'admin']),
            403,
            'Only Super Admin and Admin can create staff accounts.'
        );
    }

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
        $this->authorizeStaffCreation();

        $validated = $this->validate();

        $staff = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $staff->assignRole(
            Role::findOrCreate('staff', 'web')
        );

        session()->flash(
            'success',
            "Staff member {$staff->name} created."
        );

        $this->redirect(
            route('inventory.staff.edit', $staff),
            navigate: true
        );
    }

    public function continueToPermissions(): void
    {
        $this->authorizeStaffCreation();

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

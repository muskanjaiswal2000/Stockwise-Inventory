<?php

namespace Modules\Inventory\Http\Controllers\Staff;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

class DetailsEdit extends Component
{
    public User $staff;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(User $staff): void
    {
        abort_unless($staff->hasRole('staff'), 404);

        $this->staff = $staff;
        $this->name = $staff->name;
        $this->email = $staff->email;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->staff->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $this->staff->name = $validated['name'];
        $this->staff->email = $validated['email'];

        if ($validated['password'] ?? null) {
            $this->staff->password = Hash::make($validated['password']);
        }

        $this->staff->save();
        $this->reset(['password', 'password_confirmation']);
        session()->flash('success', "Details updated for {$this->staff->name}.");
    }

    #[Layout('livewire.layout.app')]
    public function render()
    {
        return view('inventory::livewire.staff.details-edit');
    }
}


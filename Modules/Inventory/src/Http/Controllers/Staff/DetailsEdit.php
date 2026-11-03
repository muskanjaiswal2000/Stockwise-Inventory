<?php

namespace Modules\Inventory\Http\Controllers\Staff;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

class DetailsEdit extends Component
{
    public User $staff;

    public string $name = '';

    public string $email = '';

    public string $role = 'staff';

    private function authorizeDetailsManagement(): void
    {
        abort_unless(
            auth()->user()?->hasAnyRole(['super_admin', 'admin']),
            403,
            'You are not authorized to edit account details.'
        );
    }

    private function authorizeTarget(): void
{
    $this->staff->refresh();

    abort_unless($this->staff->exists, 404);

    $user = auth()->user();

    // Super Admin accounts cannot be managed.
    abort_if(
        $this->staff->hasRole('super_admin'),
        404,
        'Super Admin accounts cannot be managed.'
    );

    // Admin users cannot manage other Admin accounts.
    if (!$user->hasRole('super_admin')) {
        abort_if(
            $this->staff->hasRole('admin'),
            403,
            'You cannot manage Admin accounts.'
        );
    }

    abort_if(
        $this->staff->id === $user->id,
        403,
        'You cannot edit your own account here.'
    );
}

    public function mount(User $staff): void
    {
        $this->authorizeDetailsManagement();

        $this->staff = $staff;

        $this->authorizeTarget();

        $this->name = $staff->name;
        $this->email = $staff->email;
        $this->role = $staff->getRoleNames()->first() ?? 'staff';
    }

    public function save(): void
    {
        $this->authorizeDetailsManagement();
        $this->authorizeTarget();

        $user = auth()->user();

        $allowedRoles = $user->hasRole('super_admin')
            ? ['admin', 'manager', 'staff']
            : ['manager', 'staff'];

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->staff->id),
            ],
            'role' => ['required', Rule::in($allowedRoles)],
        ]);

        $this->staff->name = $validated['name'];
        $this->staff->email = $validated['email'];
        $this->staff->save();

        if ($this->staff->getRoleNames()->first() !== $validated['role']) {
            $this->staff->syncRoles([$validated['role']]);
        }

        $this->role = $validated['role'];

        session()->flash(
            'success',
            "Details updated for {$this->staff->name}."
        );
    }

    public function resetPassword(): void
    {
        abort_unless(
            auth()->user()?->hasRole('super_admin'),
            403,
            'Only Super Admin can reset passwords.'
        );

        $this->authorizeTarget();

        $status = Password::sendResetLink([
            'email' => $this->staff->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash(
                'success',
                "Password reset link sent to {$this->staff->email}."
            );
        } else {
            session()->flash(
                'error',
                'Failed to send password reset link: ' . __($status)
            );
        }
    }

    public function deleteStaff(): void
    {
        abort_unless(
            auth()->user()?->hasRole('super_admin'),
            403,
            'Only Super Admin can delete accounts.'
        );

        $this->authorizeTarget();

        abort_if(
            $this->staff->id === auth()->id(),
            403,
            'You cannot delete your own account.'
        );

        $name = $this->staff->name;

        $this->staff->delete();

        session()->flash(
            'success',
            "Account {$name} has been deleted."
        );

        $this->redirect(
            route('inventory.staff.permissions'),
            navigate: false
        );
    }

    #[Layout('livewire.layout.app')]
    public function render()
    {
        $this->authorizeDetailsManagement();
        $this->authorizeTarget();

        return view('inventory::livewire.staff.details-edit');
    }
}

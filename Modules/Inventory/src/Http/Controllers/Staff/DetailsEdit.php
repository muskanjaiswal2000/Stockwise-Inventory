<?php

namespace Modules\Inventory\Http\Controllers\Staff;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Support\Str;

class DetailsEdit extends Component
{
    public User $staff;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public string $role = 'staff'; //new line <------------

    public function mount(User $staff): void
    {

        // new line <------------
        abort_unless($staff->hasAnyRole(['super_admin', 'admin', 'manager', 'staff']), 404);

        $this->staff = $staff;
        $this->name = $staff->name;
        $this->email = $staff->email;

        $this->role = $staff->roles->first()?->name ?? 'staff'; //new line <------------
    }

    public function save(): void
{
    $currentUserRole = auth()->user()->roles->first()?->name ?? 'staff';
    
    // Define what each role can edit
    $editableFields = [
        'super_admin' => ['name', 'email', 'role'],
        'admin' => ['name', 'email', 'role'],
        'manager' => ['role'],
        'staff' => [],
    ];
    
    $allowedFields = $editableFields[$currentUserRole] ?? [];
    
    // Validation rules
    $validationRules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->staff->id)],
        'password' => ['nullable', 'confirmed'], //, Password::defaults()
        'role' => ['required', 'in:admin,manager,staff'],
    ];
    
    // Only validate fields that current user can edit
    $finalRules = [];
    foreach ($allowedFields as $field) {
        if (isset($validationRules[$field])) {
            $finalRules[$field] = $validationRules[$field];
        }
    }
    
    $validated = $this->validate($finalRules);
    
    // Role hierarchy check
    $roleHierarchy = [
        'super_admin' => ['admin', 'manager', 'staff'],
        'admin' => ['manager', 'staff'],
        'manager' => ['staff'],
        'staff' => [],
    ];
    
    if (in_array('role', $allowedFields) && isset($validated['role'])) {
        $allowedRoles = $roleHierarchy[$currentUserRole] ?? [];
        if (!in_array($validated['role'], $allowedRoles)) {
            abort(403, 'You do not have permission to assign this role.');
        }
    }
    
    // Update only allowed fields
    if (in_array('name', $allowedFields) && isset($validated['name'])) {
        $this->staff->name = $validated['name'];
    }
    
    if (in_array('email', $allowedFields) && isset($validated['email'])) {
        $this->staff->email = $validated['email'];
    }
    
    // if (in_array('password', $allowedFields) && ($validated['password'] ?? null)) {
    //     $this->staff->password = Hash::make($validated['password']);
    // }
    
    if (in_array('role', $allowedFields) && isset($validated['role'])) {
        $this->staff->syncRoles([$validated['role']]);
    }
    
    $this->staff->save();
    $this->reset(['password', 'password_confirmation']);
    session()->flash('success', "Details updated for {$this->staff->name}.");
}

public function resetPassword(): void
{
    abort_unless(auth()->user()->hasRole('super_admin'), 403, 'Only Super Admin can reset passwords');
    
    // Generate password reset token
    $status = PasswordFacade::sendResetLink(
        ['email' => $this->staff->email]
    );
    
    if ($status === PasswordFacade::RESET_LINK_SENT) {
        session()->flash('success', "Password reset link sent to {$this->staff->email}.");
    } else {
        session()->flash('error', "Failed to send password reset link: " . __($status));
    }
}

    public function deleteStaff(): void
    {
        abort_unless(auth()->user()->hasRole('super_admin'), 403, 'Only Super Admin can delete staff');

        $this->staff->delete();

        session()->flash('success', "Staff member {$this->staff->name} has been deleted.");

        $this->redirect(route('inventory.staff.permissions'), navigate: false);
    }

    #[Layout('livewire.layout.app')]
    public function render()
    {
        return view('inventory::livewire.staff.details-edit');
    }
}

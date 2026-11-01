<?php

namespace Modules\Inventory\Http\Controllers\Staff;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class Edit extends Component
{
    public User $staff;

    public array $selectedPermissions = [];

    private function authorizePermissionManagement(): void
    {
        $user = auth()->user();

        abort_unless(
            $user && $user->hasAnyRole(['super_admin', 'admin', 'manager']),
            403,
            'You are not allowed to manage permissions.'
        );

        // Super Admin has unrestricted access.
        if ($user->hasRole('super_admin')) {
            return;
        }

        // Admin and Manager need this permission.
        abort_unless(
            $user->can('manage staff permissions'),
            403,
            'You do not have permission to manage staff permissions.'
        );
    }

    private function authorizeTarget(): void
{
    $user = auth()->user();

    $this->staff->refresh();

    abort_unless($this->staff->exists, 404);

    // Nobody can manage a Super Admin account.
    abort_if(
        $this->staff->hasRole('super_admin'),
        404,
        'Super Admin accounts cannot be managed.'
    );

    // Only Super Admin can manage Admin accounts.
    if (!$user->hasRole('super_admin')) {
        abort_if(
            $this->staff->hasRole('admin'),
            403,
            'You do not have permission to manage Admin account permissions.'
        );

        abort_unless(
            $this->staff->hasRole('staff'),
            403,
            'You can manage permissions for Staff accounts only.'
        );
    }
}

    public function mount(User $staff): void
    {
        $this->staff = $staff;

        $this->authorizePermissionManagement();
        $this->authorizeTarget();

        $this->selectedPermissions = $this->staff
            ->getPermissionNames()
            ->values()
            ->all();
    }

    public function save(): void
    {
        $this->authorizePermissionManagement();
        $this->authorizeTarget();

        $validated = $this->validate([
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['string'],
        ]);

        $validPermissions = Permission::query()
            ->where('guard_name', 'web')
            ->where('name', '!=', 'manage staff permissions')
            ->whereIn('name', $validated['selectedPermissions'])
            ->pluck('name')
            ->all();

        $this->staff->syncPermissions($validPermissions);

        $this->selectedPermissions = $validPermissions;

        session()->flash(
            'success',
            "Permissions updated for {$this->staff->name}."
        );
    }

    public function render()
    {
        $this->authorizePermissionManagement();
        $this->authorizeTarget();

        return view('inventory::livewire.staff.edit', [
            'permissions' => Permission::query()
                ->where('guard_name', 'web')
                ->where('name', '!=', 'manage staff permissions')
                ->orderBy('name')
                ->get(),
        ])->layout('livewire.layout.app');
    }
}

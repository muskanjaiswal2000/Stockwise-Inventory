<?php

namespace Modules\Inventory\Http\Controllers\Staff;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class Edit extends Component
{
    public User $staff;
    public array $selectedPermissions = [];

    public function mount(User $staff): void
    {
        abort_unless($staff->hasRole('staff'), 404);

        $this->staff = $staff;
        $this->selectedPermissions = $staff->getPermissionNames()->values()->all();
    }

    public function save(): void
    {
        $this->validate([
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['string'],
        ]);

        $validPermissions = Permission::query()
            ->where('guard_name', 'web')
            ->where('name', '!=', 'manage staff permissions')
            ->whereIn('name', $this->selectedPermissions)
            ->pluck('name')
            ->all();

        $this->staff->syncPermissions($validPermissions);

        session()->flash('success', "Permissions updated for {$this->staff->name}.");
    }

    public function render()
    {
        return view('inventory::livewire.staff.edit', [
            'permissions' => Permission::query()
                ->where('guard_name', 'web')
                ->where('name', '!=', 'manage staff permissions')
                ->orderBy('name')
                ->get(),
        ])->layout('livewire.layout.app');
    }
}

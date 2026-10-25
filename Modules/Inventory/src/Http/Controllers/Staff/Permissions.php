<?php

namespace Modules\Inventory\Http\Controllers\Staff;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class Permissions extends Component
{
    public ?int $selectedStaffId = null;

    /** @var array<int, string> */
    public array $selectedPermissions = [];

    public function mount(): void
    {
        $firstStaff = User::role('staff')->orderBy('name')->first();

        if ($firstStaff) {
            $this->selectedStaffId = $firstStaff->id;
            $this->loadStaffPermissions();
        }
    }

    public function updatedSelectedStaffId(): void
    {
        $this->loadStaffPermissions();
    }

    public function save(): void
    {
        $staff = User::role('staff')->findOrFail($this->selectedStaffId);

        $validPermissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $this->selectedPermissions)
            ->pluck('name')
            ->all();

        $staff->syncPermissions($validPermissions);

        session()->flash('success', "Permissions updated for {$staff->name}.");
    }

    private function loadStaffPermissions(): void
    {
        $staff = User::role('staff')->find($this->selectedStaffId);
        $this->selectedPermissions = $staff?->getPermissionNames()->values()->all() ?? [];
    }

    public function render()
    {
        return view('inventory::livewire.staff.permissions', [
            'staffUsers' => User::role('staff')->orderBy('name')->get(),
            'permissions' => Permission::query()
                ->where('guard_name', 'web')
                ->where('name', '!=', 'manage staff permissions')
                ->orderBy('name')
                ->get(),
        ])->layout('livewire.layout.app');
    }
}

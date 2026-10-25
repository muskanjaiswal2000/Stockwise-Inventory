<?php

namespace Modules\Inventory\Http\Controllers\Staff;

use App\Models\User;
use Livewire\Component;

class PermissionsPage extends Component
{
    public ?int $selectedStaffId = null;

    public function delete(int $staffId): void
    {
        $staff = User::role('staff')->findOrFail($staffId);
        $staff->delete();
        session()->flash('success', "Staff member {$staff->name} deleted.");
    }

    public function render()
    {
        $staffUsers = User::role('staff')->orderBy('name')->get();
        return view('inventory::livewire.staff.permissions', [
            'staffUsers' => $staffUsers,
        ])->layout('livewire.layout.app');
    }
}

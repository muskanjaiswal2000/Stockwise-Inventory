<?php

namespace Modules\Inventory\Http\Controllers\Staff;

use App\Models\User;
use Livewire\Component;

class PermissionsPage extends Component
{
    public ?int $selectedStaffId = null;
   
    public function delete(int $staffId): void
    {
        abort_unless(
            auth()->user()?->hasRole('super_admin'),
            403,
            'Only Super Admin can delete staff accounts.'
        );

        $staff = User::role('staff')->findOrFail($staffId);

        abort_if(
            auth()->id() === $staff->id,
            403,
            'You cannot delete your own account.'
        );

        $staffName = $staff->name;

        $staff->delete();

        session()->flash(
            'success',
            "Staff member {$staffName} deleted successfully."
        );
    }

    public function render()
    {
        $user = auth()->user();

        abort_unless(
            $user?->hasAnyRole(['super_admin', 'admin', 'manager']),
            403
        );

        $staffUsers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'super_admin')
                ->where('guard_name', 'web');
        })
            ->with('roles')
            ->orderBy('name')
            ->get();

        return view('inventory::livewire.staff.permissions', [
            'staffUsers' => $staffUsers,
        ])->layout('livewire.layout.app');
    }
}

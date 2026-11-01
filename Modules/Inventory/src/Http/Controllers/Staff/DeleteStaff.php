<?php
namespace Modules\Inventory\Http\Controllers\Staff;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DeleteStaff extends Controller
{
    public function __invoke(Request $request, User $staff)
    {
        $user = $request->user();

        abort_unless(
            $user && $user->hasRole('super_admin'),
            403,
            'Only super admins can delete staff.'
        );

        abort_if(
            $user->id === $staff->id,
            403,
            'You cannot delete your own account.'
        );

        $staff->delete();

        return redirect()
            ->route('inventory.staff.permissions')
            ->with('success', 'Staff member deleted successfully.');
    }
}

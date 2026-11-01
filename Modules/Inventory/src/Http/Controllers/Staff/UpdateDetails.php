<?php

namespace Modules\Inventory\Http\Controllers\Staff;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class UpdateDetails extends Controller
{
    public function __invoke(Request $request, User $staff)
    {
        $user = $request->user();

        abort_unless(
            $user && $user->hasAnyRole(['super_admin', 'admin']),
            403
        );

        abort_if($staff->hasRole('super_admin'), 404);

        $allowedRoles = $user->hasRole('super_admin')
            ? ['admin', 'manager', 'staff']
            : ['manager', 'staff'];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($staff->id),
            ],

            'role' => [
                'required',
                Rule::in($allowedRoles),
            ],
        ]);

        $staff->name = $validated['name'];
        $staff->email = $validated['email'];
        $staff->save();

        $staff->syncRoles([$validated['role']]);

        return redirect()
            ->route('inventory.staff.details.edit', $staff->id)
            ->with('success', 'Account details updated successfully.');
    }
}
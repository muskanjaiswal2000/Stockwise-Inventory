<?php

namespace Modules\Inventory\Http\Controllers\Staff;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Permission;

class UpdatePermissions extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user && $user->hasAnyRole(['super_admin', 'admin', 'manager']),
            403,
            'You are not authorized to manage permissions.'
        );

        // Admin and Manager need this permission.
        if (!$user->hasRole('super_admin')) {
            abort_unless(
                $user->can('manage staff permissions'),
                403,
                'You do not have permission to manage staff permissions.'
            );
        }

        $validated = $request->validate([
            'staff_id' => ['required', 'integer', 'exists:users,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $staff = User::with('roles')->findOrFail($validated['staff_id']);

        // Nobody can manage Super Admin accounts.
        abort_if(
            $staff->hasRole('super_admin'),
            404,
            'Super Admin accounts cannot be managed.'
        );

        // Only Super Admin can manage Admin account permissions.
        if (!$user->hasRole('super_admin')) {
            abort_if(
                $staff->hasRole('admin'),
                403,
                'You do not have permission to manage Admin account permissions.'
            );

            abort_unless(
                $staff->hasRole('staff'),
                403,
                'You can manage permissions for Staff accounts only.'
            );
        }

        $selectedPermissions = $validated['permissions'] ?? [];

        $validPermissions = Permission::query()
            ->where('guard_name', 'web')
            ->where('name', '!=', 'manage staff permissions')
            ->whereIn('name', $selectedPermissions)
            ->pluck('name')
            ->all();

        $staff->syncPermissions($validPermissions);

        return redirect()
            ->route('inventory.staff.permissions')
            ->with('success', "Permissions updated for {$staff->name}.");
    }
}
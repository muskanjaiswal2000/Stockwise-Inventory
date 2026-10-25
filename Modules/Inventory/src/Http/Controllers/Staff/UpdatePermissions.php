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
        $validated = $request->validate([
            'staff_id' => ['required', 'integer'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $staff = User::role('staff')->findOrFail($validated['staff_id']);
        $selectedPermissions = $validated['permissions'] ?? [];
        $validPermissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $selectedPermissions)
            ->pluck('name')
            ->all();

        $staff->syncPermissions($validPermissions);

        return redirect()
            ->route('inventory.staff.permissions')
            ->with('success', "Permissions updated for {$staff->name}.");
    }
}

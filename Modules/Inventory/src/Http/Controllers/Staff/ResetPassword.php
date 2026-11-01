<?php

namespace Modules\Inventory\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class ResetPassword extends Controller
{
    public function __invoke(User $staff): RedirectResponse
    {
        $user = auth()->user();

        // Only Super Admin can reset passwords.
        abort_unless(
            $user && $user->hasRole('super_admin'),
            403
        );

        // Prevent resetting the logged-in Super Admin's own password.
        abort_if(
            $user->is($staff),
            403,
            'You cannot reset your own password.'
        );

        $status = Password::sendResetLink([
            'email' => $staff->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'success',
                'Password reset link sent successfully.'
            );
        }

        return back()->withErrors([
            'email' => __($status),
        ]);
    }
}
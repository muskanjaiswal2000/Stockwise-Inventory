<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
            <a href="{{ route('inventory.staff.permissions') }}"
                class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-700">
                ← Back to staff management
            </a>

            <div class="mt-6 flex items-center gap-4">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-teal-100 to-cyan-100 text-xl font-bold text-teal-700 ring-8 ring-teal-50">
                    {{ strtoupper(substr($staff->name, 0, 2)) }}
                </span>

                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">
                        Staff details
                    </p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $staff->name }}
                    </h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Edit this account's details.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @php
        $loggedInUser = auth()->user();

        $canManageAccounts = $loggedInUser->hasAnyRole(['super_admin', 'admin']);
        $isSuperAdmin = $loggedInUser->hasRole('super_admin');

        //Only Super Admin can reset other accounts' passwords.
        $canResetPassword = $isSuperAdmin && !$loggedInUser->is($staff);

        $currentRole = $staff->getRoleNames()->first();
    @endphp

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">

        @if (session('success'))
        <div role="status"
            class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div role="alert"
            class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
            Please correct the errors below and try again.
        </div>
        @endif

        <form action="{{ route('inventory.staff.details.update', $staff->id) }}"
            method="POST"
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            @csrf
            @method('PUT')

            <div class="border-b border-slate-100 pb-5">
                <h2 class="text-lg font-bold text-slate-900">
                    Account details
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Update the account information and assigned role.
                </p>
            </div>

            <div class="mt-6 space-y-5">

                {{-- Name --}}
                <div>
                    <label for="details-name"
                        class="block text-sm font-semibold text-slate-700">
                        Name
                    </label>

                    <input
                        id="details-name"
                        name="name"
                        type="text"
                        value="{{ old('name', $staff->name) }}"
                        required
                        @disabled(!$canManageAccounts)
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-slate-100">

                    @error('name')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="details-email"
                        class="block text-sm font-semibold text-slate-700">
                        Email
                    </label>

                    <input
                        id="details-email"
                        name="email"
                        type="email"
                        value="{{ old('email', $staff->email) }}"
                        required
                        @disabled(!$canManageAccounts)
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-slate-100">

                    @error('email')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label for="staff-role"
                        class="block text-sm font-semibold text-slate-700">
                        Role
                    </label>

                    @if ($canManageAccounts)
                    <select
                        id="staff-role"
                        name="role"
                        required
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                        @if ($isSuperAdmin)
                        <option value="admin"
                            {{ old('role', $currentRole) === 'admin' ? 'selected' : '' }}>
                            Admin
                        </option>
                        @endif

                        <option value="manager"
                            {{ old('role', $currentRole) === 'manager' ? 'selected' : '' }}>
                            Manager
                        </option>

                        <option value="staff"
                            {{ old('role', $currentRole) === 'staff' ? 'selected' : '' }}>
                            Staff
                        </option>
                    </select>
                    @else
                    <input
                        type="text"
                        value="{{ ucfirst($currentRole ?? 'No role assigned') }}"
                        disabled
                        class="mt-1 w-full rounded-xl border-slate-300 bg-slate-100 text-sm cursor-not-allowed">
                    @endif

                    @error('role')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Reset --}}
                @if ($canResetPassword)
                <div class="border-t border-slate-100 pt-5">
                    <p class="text-sm font-semibold text-slate-700">
                        Reset Password
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        Send a password reset link to this account's email.
                    </p>

                    <button
                        type="submit"
                        form="reset-password-form"
                        class="mt-3 rounded-xl bg-amber-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-200 transition hover:bg-amber-700">
                        Send Password Reset Link
                    </button>
                </div>
                @endif

            </div>

            {{-- Action Buttons --}}
            <div class="mt-8 flex items-center justify-between">
                <button
                    type="submit"
                    class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">
                    Save details
                </button>

                @if ($canManageAccounts)
                <button
                    type="submit"
                    form="delete-staff-form"
                    class="rounded-xl bg-red-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-red-200 transition hover:bg-red-700">
                    Delete account
                </button>
                @endif
            </div>
        </form>

        {{-- Password Reset Form --}}
        @if ($canResetPassword)
        <form id="reset-password-form"
            action="{{ route('inventory.staff.reset-password', $staff->id) }}"
            method="POST">
            @csrf
        </form>
        @endif

        {{-- Delete Form --}}
        @if ($canManageAccounts)
        <form id="delete-staff-form"
            action="{{ route('inventory.staff.destroy', $staff->id) }}"
            method="POST"
            onsubmit="return confirm('Are you sure you want to delete this account? This action cannot be undone.');">
            @csrf
            @method('DELETE')
        </form>
        @endif

    </div>
</div>
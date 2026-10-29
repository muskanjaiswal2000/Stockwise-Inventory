<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
            <a href="{{ route('inventory.staff.permissions') }}" wire:navigate class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-700">← Back to staff management</a>
            <div class="mt-6 flex items-center gap-4">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-teal-100 to-cyan-100 text-xl font-bold text-teal-700 ring-8 ring-teal-50">{{ strtoupper(substr($staff->name, 0, 2)) }}</span>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Staff details</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ $staff->name }}</h1>
                    <p class="mt-1 text-sm text-slate-500">Edit this staff member’s account details.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
        <div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
        @endif
        <form wire:submit="save" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="border-b border-slate-100 pb-5">
                <h2 class="text-lg font-bold text-slate-900">Account details</h2>
                <p class="mt-1 text-sm text-slate-500">Update the staff member's account details.</p>
            </div>

            <div class="mt-6 space-y-5">
                <!-- Name Field - Manager cannot edit -->
                <div>
                    <label for="details-name" class="block text-sm font-semibold text-slate-700">Name</label>
                    @if(auth()->user()->hasRole('manager'))
                    <input id="details-name" type="text" value="{{ $name }}" disabled
                        class="mt-1 w-full rounded-xl border-slate-300 bg-slate-100 text-sm cursor-not-allowed">
                    @else
                    <input id="details-name" type="text" wire:model="name" required
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @endif
                    @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <!-- Email Field - Manager cannot edit -->
                <div>
                    <label for="details-email" class="block text-sm font-semibold text-slate-700">Email</label>
                    @if(auth()->user()->hasRole('manager'))
                    <input id="details-email" type="email" value="{{ $email }}" disabled
                        class="mt-1 w-full rounded-xl border-slate-300 bg-slate-100 text-sm cursor-not-allowed">
                    @else
                    <input id="details-email" type="email" wire:model="email" required
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @endif
                    @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <!-- Role Field - All can edit according to hierarchy -->
                <div>
                    <label for="staff-role" class="block text-sm font-semibold text-slate-700">Role</label>
                    <select id="staff-role" wire:model="role" required
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                        @if(auth()->user()->hasRole('super_admin'))
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="staff">Staff</option>
                        @endif

                        @if(auth()->user()->hasRole('admin'))
                        <option value="manager">Manager</option>
                        <option value="staff">Staff</option>
                        @endif

                        @if(auth()->user()->hasRole('manager'))
                        <option value="staff">Staff</option>
                        @endif

                    </select>
                    @error('role')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <!-- Password Reset Section - Only Super Admin -->
                @if(auth()->user()->hasRole('super_admin'))
                <div class="border-t border-slate-100 pt-5">
                    <p class="text-sm font-semibold text-slate-700">Reset Password</p>
                    <p class="mt-1 text-xs text-slate-500">Send a password reset link to this staff member's email. They will be able to set a new password using the link.</p>
                    <button type="button" wire:click="resetPassword" class="mt-3 rounded-xl bg-amber-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-200 transition hover:bg-amber-700">
                        Send Password Reset Link
                    </button>
                </div>
                @endif
            </div>

            <!-- Buttons -->
            <div class="mt-8 flex justify-between items-center">
                <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">Save details</button>

                @if(auth()->user()->hasRole('super_admin'))
                <button type="button" onclick="if(!confirm('Are you sure you want to delete this staff member? This action cannot be undone.')) return;" wire:click="deleteStaff" class="rounded-xl bg-red-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-red-200 transition hover:bg-red-700">Delete staff</button>
                @endif
            </div>
        </form>
    </div>
</div>
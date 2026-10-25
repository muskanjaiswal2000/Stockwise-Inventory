<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
            <a href="{{ route('inventory.staff.permissions') }}" wire:navigate class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-700">← Back to staff management</a>
            <div class="mt-6 flex items-center gap-4">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-teal-100 to-cyan-100 text-xl font-bold text-teal-700 ring-8 ring-teal-50">{{ strtoupper(substr($staff->name, 0, 2)) }}</span>
                <div><p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Staff details</p><h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ $staff->name }}</h1><p class="mt-1 text-sm text-slate-500">Edit this staff member’s account details.</p></div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
        @endif
        <form wire:submit="save" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="border-b border-slate-100 pb-5"><h2 class="text-lg font-bold text-slate-900">Account details</h2><p class="mt-1 text-sm text-slate-500">Update the staff member’s name, email, or password.</p></div>
            <div class="mt-6 space-y-5">
                <div><label for="details-name" class="block text-sm font-semibold text-slate-700">Name</label><input id="details-name" type="text" wire:model="name" required class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div><label for="details-email" class="block text-sm font-semibold text-slate-700">Email</label><input id="details-email" type="email" wire:model="email" required class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div class="border-t border-slate-100 pt-5"><p class="text-sm font-semibold text-slate-700">Change password</p><p class="mt-1 text-xs text-slate-500">Leave these fields blank to keep the current password.</p></div>
                <div class="grid gap-5 sm:grid-cols-2"><div><label for="details-password" class="block text-sm font-semibold text-slate-700">New password</label><input id="details-password" type="password" wire:model="password" class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div><div><label for="details-password-confirmation" class="block text-sm font-semibold text-slate-700">Confirm password</label><input id="details-password-confirmation" type="password" wire:model="password_confirmation" class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div></div>
                @error('password')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="mt-8 flex justify-end"><button type="submit" class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">Save details</button></div>
        </form>
    </div>
</div>

<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <a href="{{ route('inventory.staff.permissions') }}" wire:navigate class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-700">← Back to staff management</a>
            <div class="mt-6 flex items-center gap-4">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-teal-100 to-cyan-100 text-xl font-bold text-teal-700 ring-8 ring-teal-50">{{ strtoupper(substr($staff->name, 0, 2)) }}</span>
                <div><p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Staff permissions</p><h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ $staff->name }}</h1><p class="mt-1 text-sm text-slate-500">{{ $staff->email }}</p></div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('inventory.staff.permissions.update') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf
            <input type="hidden" name="staff_id" value="{{ $staff->id }}">
            <div class="flex flex-col gap-2 border-b border-slate-100 pb-6 sm:flex-row sm:items-end sm:justify-between"><div><h2 class="text-lg font-bold text-slate-900">Allowed actions</h2><p class="mt-1 text-sm text-slate-500">Choose which inventory pages and actions this staff member can access.</p></div><span class="w-fit rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">Staff account</span></div>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($permissions as $permission)
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-indigo-300 hover:bg-indigo-50/40 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50"><input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, $selectedPermissions, true)) class="mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"><span><span class="block text-sm font-semibold text-slate-900">{{ ucwords($permission->name) }}</span><span class="mt-1 block text-xs text-slate-500">{{ $permission->name }}</span></span></label>
                @endforeach
            </div>
            <div class="mt-8 flex justify-end"><button type="submit" class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">Save permissions</button></div>
        </form>
    </div>
</div>

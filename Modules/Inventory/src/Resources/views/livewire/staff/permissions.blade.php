<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto flex max-w-7xl flex-col gap-5 px-4 py-10 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Staff access</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Staff management</h1>
                <p class="mt-2 text-sm text-slate-500">Manage staff accounts and their inventory permissions.</p>
            </div>
            <a href="{{ route('inventory.staff.create') }}" wire:navigate class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">
                <span class="text-lg leading-none">+</span> Add new staff
            </a>
        </div>
    </div>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
        @endif

        @if ($staffUsers->isEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-sm">
                <h2 class="font-semibold text-slate-900">No staff members yet</h2>
                <p class="mt-2 text-sm text-slate-500">Create the first staff account to start assigning permissions.</p>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($staffUsers as $staff)
                    <article wire:key="staff-{{ $staff->id }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-teal-200 hover:shadow-xl hover:shadow-slate-200/60">
                        <div class="p-6 text-center">
                            <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-teal-100 to-cyan-100 text-2xl font-bold text-teal-700 ring-8 ring-teal-50">{{ strtoupper(substr($staff->name, 0, 2)) }}</span>
                            <h2 class="mt-6 truncate text-base font-bold text-slate-900" title="{{ $staff->name }}">{{ $staff->name }}</h2>
                            <p class="mt-1 truncate text-xs text-slate-500" title="{{ $staff->email }}">{{ $staff->email }}</p>
                            <span class="mt-4 inline-flex rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">Staff member</span>
                        </div>
                        <div class="flex border-t border-slate-100 bg-slate-50/70">
                            <a href="{{ route('inventory.staff.details.edit', $staff) }}" wire:navigate class="m-2 flex flex-1 items-center justify-center rounded-lg border border-indigo-200 bg-white px-3 py-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-50">Edit details</a>
                            <a href="{{ route('inventory.staff.edit', $staff) }}" wire:navigate class="m-2 flex flex-1 items-center justify-center rounded-lg border border-teal-200 bg-white px-3 py-2 text-sm font-semibold text-teal-700 transition hover:bg-teal-50">Permissions</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</div>

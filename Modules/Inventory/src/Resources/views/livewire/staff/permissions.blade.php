<div class="min-h-screen bg-[#f6f8fc]">
    @php
    $loggedInUser = auth()->user();

    $isSuperAdmin = $loggedInUser->hasRole('super_admin');
    $isAdmin = $loggedInUser->hasRole('admin');
    $isManager = $loggedInUser->hasRole('manager');

    $canManageAccounts = $isSuperAdmin || $isAdmin;
    $canAssignPermissions = $isSuperAdmin || $isAdmin || $isManager;
    @endphp

    <div class="inventory-page-heading">
        <div class="mx-auto flex max-w-7xl flex-col gap-5 px-4 py-10 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">
                    Staff access
                </p>

                <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                    Staff management
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Manage accounts and their inventory permissions.
                </p>
            </div>

            @if ($canManageAccounts)
            <a
                href="{{ route('inventory.staff.create') }}"
                wire:navigate
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">
                <span class="text-lg leading-none">+</span>
                Add new staff
            </a>
            @endif
        </div>
    </div>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">

        {{-- Success message --}}
        @if (session('success'))
        <div role="status"
            class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
        @endif

        {{-- Information message --}}
        @if (session('info'))
        <div role="status"
            class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm font-medium text-blue-800">
            {{ session('info') }}
        </div>
        @endif

        {{-- Empty state --}}
        @if ($staffUsers->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-sm">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400">
                <span>👥</span>
            </div>

            <h2 class="mt-4 font-semibold text-slate-900">
                No accounts found
            </h2>

            <p class="mt-2 text-sm text-slate-500">
                There are currently no accounts available to manage.
            </p>

            @if ($canManageAccounts)
            <a
                href="{{ route('inventory.staff.create') }}"
                wire:navigate
                class="mt-5 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                Create Staff Account
            </a>
            @endif
        </div>
        @else

        {{-- User cards --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

            @foreach ($staffUsers as $staff)

            @php
            $roleNames = $staff->getRoleNames();

            $displayRole = $roleNames->isNotEmpty()
            ? $roleNames->implode(', ')
            : 'No role assigned';

            $isStaffAccount = $staff->hasRole('staff');
            $isAdminAccount = $staff->hasRole('admin');

            $canEditDetails = $canManageAccounts
            && !$staff->hasRole('super_admin')
            && ($isSuperAdmin || !$isAdminAccount);

            $canManagePermissions = $canAssignPermissions
            && !$staff->hasRole('super_admin')
            && (
            $isSuperAdmin
            || (!$isAdminAccount && ($isAdmin || ($isManager && $isStaffAccount)))
            );

            $showRestrictedActions = $isAdmin && $isAdminAccount;
            @endphp

            <article
                wire:key="staff-{{ $staff->id }}"
                class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-teal-200 hover:shadow-xl hover:shadow-slate-200/60">

                <div class="p-6 text-center">

                    {{-- Avatar --}}
                    <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-teal-100 to-cyan-100 text-2xl font-bold text-teal-700 ring-8 ring-teal-50">
                        {{ strtoupper(substr($staff->name, 0, 2)) }}
                    </span>

                    {{-- Name --}}
                    <h2
                        class="mt-6 truncate text-base font-bold text-slate-900"
                        title="{{ $staff->name }}">
                        {{ $staff->name }}
                    </h2>

                    {{-- Email --}}
                    <p
                        class="mt-1 truncate text-xs text-slate-500"
                        title="{{ $staff->email }}">
                        {{ $staff->email }}
                    </p>

                    {{-- Role --}}
                    <span class="mt-4 inline-flex rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">
                        {{ $displayRole }}
                    </span>
                </div>

                {{-- Actions --}}
                @if ($canEditDetails || $canManagePermissions || $showRestrictedActions)
                <div class="flex items-center justify-center gap-2 border-t border-slate-100 bg-slate-50/70 p-3">

                    {{-- Edit Details --}}
                    @if ($canEditDetails)
                    <a
                        href="{{ route('inventory.staff.details.edit', $staff) }}"
                        wire:navigate
                        class="flex w-full items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Edit details
                    </a>
                    @elseif ($showRestrictedActions)
                    <button
                        type="button"
                        onclick="alert('You do not have permission to edit another Admin account.')"
                        class="flex w-full items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Edit details
                    </button>
                    @endif

                    {{-- Permissions --}}
                    @if ($canManagePermissions)
                    <a
                        href="{{ route('inventory.staff.edit', $staff) }}"
                        wire:navigate
                        class="flex w-full items-center justify-center rounded-lg border border-teal-200 bg-white px-3 py-2.5 text-sm font-semibold text-teal-700 transition hover:bg-teal-50">
                        Permissions
                    </a>
                    @elseif ($showRestrictedActions)
                    <button
                        type="button"
                        onclick="alert('You do not have permission to manage another Admin account permissions.')"
                        class="flex w-full items-center justify-center rounded-lg border border-teal-200 bg-white px-3 py-2.5 text-sm font-semibold text-teal-700 transition hover:bg-teal-50">
                        Permissions
                    </button>
                    @endif

                </div>
                @endif

            </article>

            @endforeach

        </div>
        @endif

    </div>
</div>
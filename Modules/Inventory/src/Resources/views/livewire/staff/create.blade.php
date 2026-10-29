<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Staff access</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Add staff member</h1>
            <p class="mt-2 text-sm text-slate-500">Create a staff login, then choose the actions this person is allowed to perform.</p>
        </div>
    </div>

    <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
        <form wire:submit="save" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm" novalidate>
            <div>
                <label for="staff-name" class="block text-sm font-semibold text-slate-900">Name</label>
                <input id="staff-name" type="text" wire:model="name" required autofocus
                    class="mt-2 block w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="staff-email" class="block text-sm font-semibold text-slate-900">Email</label>
                <input id="staff-email" type="email" wire:model="email" required
                    class="mt-2 block w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="staff-password" class="block text-sm font-semibold text-slate-900">Password</label>
                <input id="staff-password" type="password" wire:model="password" required
                    class="mt-2 block w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="staff-password-confirmation" class="block text-sm font-semibold text-slate-900">Confirm password</label>
                <input id="staff-password-confirmation" type="password" wire:model="password_confirmation" required
                    class="mt-2 block w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('password_confirmation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

                <div class="flex items-center justify-between gap-4">
                    <button
                        type="button"
                        wire:click="continueToPermissions"
                        wire:loading.attr="disabled"
                        class="text-sm font-semibold text-slate-600 transition hover:text-indigo-600 disabled:opacity-50">
                        <span wire:loading.remove wire:target="continueToPermissions">
                            Staff permissions
                        </span>
                        <span wire:loading wire:target="continueToPermissions">
                            Validating...
                        </span>
                    </button>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">
                            Create staff member
                        </span>
                        <span wire:loading wire:target="save">
                            Creating...
                        </span>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
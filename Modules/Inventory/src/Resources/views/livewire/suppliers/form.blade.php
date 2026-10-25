<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <a href="{{ route('inventory.suppliers.index') }}" wire:navigate class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">&larr; Back to suppliers</a>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ $pageTitle }}</h1>
            <p class="mt-2 text-sm text-slate-500">Enter the supplier contact information.</p>
        </div>
    </div>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <form wire:submit="save" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <label for="supplier-name" class="mb-2 block text-sm font-semibold text-slate-700">Supplier name <span class="text-rose-500">*</span></label>
                <input id="supplier-name" type="text" wire:model="name" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="supplier-email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                    <input id="supplier-email" type="email" wire:model="email" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="supplier-phone" class="mb-2 block text-sm font-semibold text-slate-700">Phone</label>
                    <input id="supplier-phone" type="tel" wire:model="phone" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('phone') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label for="supplier-address" class="mb-2 block text-sm font-semibold text-slate-700">Address</label>
                <textarea id="supplier-address" wire:model="address" rows="3" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                @error('address') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('inventory.suppliers.index') }}" wire:navigate class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" wire:loading.attr="disabled" wire:target="save" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60">
                    <span wire:loading.remove wire:target="save">{{ $buttonText }}</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>

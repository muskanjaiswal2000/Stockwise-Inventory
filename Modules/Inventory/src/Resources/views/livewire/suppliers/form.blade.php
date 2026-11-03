<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <a href="{{ route('inventory.suppliers.index') }}" wire:navigate class="text-sm font-semibold text-teal-600 hover:text-teal-800">&larr; Back to suppliers</a>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ $pageTitle }}</h1>
            <p class="mt-2 text-sm text-slate-500">Enter the supplier contact information.</p>
        </div>
    </div>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <form wire:submit="save" novalidate class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div>
                <label for="supplier-name" class="mb-2 block text-sm font-semibold text-slate-700">
                    Supplier name <span class="text-rose-500">*</span>
                </label>
                <input
                    id="supplier-name"
                    type="text"
                    wire:model="name"
                    required
                    placeholder="Enter supplier name"
                    class="w-full rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                @error('name') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="supplier-email" class="mb-2 block text-sm font-semibold text-slate-700">
                        Email <span class="text-rose-500">*</span>
                    </label>
                    <input
                        id="supplier-email"
                        type="email"
                        wire:model="email"
                        required
                        placeholder="supplier@example.com"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                    @error('email') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="supplier-phone" class="mb-2 block text-sm font-semibold text-slate-700">
                        Phone <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex rounded-xl shadow-sm">
                        <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 px-4 text-sm font-medium text-slate-600">
                            +91
                        </span>
                        <input
                            id="supplier-phone"
                            type="tel"
                            wire:model="phone"
                            required
                            maxlength="10"
                            inputmode="numeric"
                            placeholder="Enter phone number"
                            class="w-full rounded-r-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    @error('phone') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="supplier-address" class="mb-2 block text-sm font-semibold text-slate-700">
                    Address <span class="text-rose-500">*</span>
                </label>
                <textarea
                    id="supplier-address"
                    wire:model="address"
                    rows="3"
                    required
                    placeholder="Enter complete address"
                    class="w-full rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500"></textarea>
                @error('address') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('inventory.suppliers.index') }}" wire:navigate class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>

                <button type="submit" wire:loading.attr="disabled" wire:target="save" class="rounded-xl bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-teal-700 disabled:opacity-60">
                    <span wire:loading.remove wire:target="save">{{ $buttonText }}</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>

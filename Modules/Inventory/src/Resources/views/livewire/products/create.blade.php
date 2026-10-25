<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <a href="{{ route('inventory.products.index') }}" wire:navigate
                class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-800">
                &larr; Back to products
            </a>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ $pageTitle }}</h1>
            <p class="mt-2 text-sm text-slate-500">
                @if ($pageTitle === 'Create product')
                    Add a product and its starting stock details.
                @else
                    Update product details. Use stock movement or adjustment to change available stock.
                @endif
            </p>
        </div>
    </div>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <form wire:submit="save" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <label for="product-name" class="mb-2 block text-sm font-semibold text-slate-700">Name</label>
                <input id="product-name" type="text" wire:model="name"
                    class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="product-sku" class="mb-2 block text-sm font-semibold text-slate-700">SKU</label>
                    <input id="product-sku" type="text" wire:model="sku"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('sku') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="product-category" class="mb-2 block text-sm font-semibold text-slate-700">Category</label>
                    <select id="product-category" wire:model="category_id"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Choose a category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="{{ $pageTitle === 'Create product' ? 'grid gap-6 sm:grid-cols-2' : '' }}">
                <div>
                    <label for="product-unit" class="mb-2 block text-sm font-semibold text-slate-700">Unit</label>
                    <input id="product-unit" type="text" wire:model="unit"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('unit') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                @if ($pageTitle === 'Create product')
                    <div>
                        <label for="product-quantity" class="mb-2 block text-sm font-semibold text-slate-700">Starting quantity</label>
                        <input id="product-quantity" type="number" min="0" max="2147483647" wire:model="quantity"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('quantity') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>

            <div>
                <label for="product-suppliers" class="mb-2 block text-sm font-semibold text-slate-700">
                    Suppliers <span class="font-normal text-slate-400">(optional, select multiple)</span>
                </label>
                <select id="product-suppliers" wire:model="supplier_ids" multiple size="4"
                    class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @forelse ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @empty
                        <option value="" disabled>No suppliers have been added</option>
                    @endforelse
                </select>
                <p class="mt-2 text-xs text-slate-500">Hold Ctrl (Windows) or Command (Mac) to select more than one.</p>
                @error('supplier_ids') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                @error('supplier_ids.*') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="product-cost" class="mb-2 block text-sm font-semibold text-slate-700">Cost price</label>
                    <input id="product-cost" type="number" min="0" step="0.01" wire:model="cost_price"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('cost_price') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="product-selling" class="mb-2 block text-sm font-semibold text-slate-700">Selling price</label>
                    <input id="product-selling" type="number" min="0" step="0.01" wire:model="selling_price"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('selling_price') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="product-reorder" class="mb-2 block text-sm font-semibold text-slate-700">Reorder level</label>
                <input id="product-reorder" type="number" min="0" wire:model="reorder_level"
                    class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('reorder_level') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('inventory.products.index') }}" wire:navigate
                    class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Cancel
                </a>
                <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60">
                    <span wire:loading.remove wire:target="save">{{ $buttonText }}</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>

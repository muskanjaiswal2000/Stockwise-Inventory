<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="inventory-eyebrow">
                Inventory management
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Manual stock adjustment</h1>
            <p class="mt-2 text-sm text-slate-500">Enter the physically counted quantity to correct the stock record.</p>
        </div>
    </div>

    <div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800">{{ session('error') }}</div>
        @endif

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-violet-50 px-6 py-5">
                <h2 class="font-semibold text-slate-900">Set actual stock quantity</h2>
                <p class="mt-1 text-sm text-slate-600">The system records only the difference between current and counted stock.</p>
            </div>
            <form wire:submit="submit" class="space-y-6 p-6">
                <div>
                    <label for="adjustment-product" class="mb-2 block text-sm font-semibold text-slate-700">Product <span class="text-rose-500">*</span></label>
                    <select id="adjustment-product" wire:model="product_id"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select a product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }}) — system quantity: {{ $product->quantity }} {{ $product->unit }}</option>
                        @endforeach
                    </select>
                    @error('product_id') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    @if ($products->isEmpty())
                        <p class="mt-2 text-sm text-amber-700">Add a product before making a stock adjustment.</p>
                    @endif
                </div>

                <div>
                    <label for="actual-quantity" class="mb-2 block text-sm font-semibold text-slate-700">Counted quantity <span class="text-rose-500">*</span></label>
                    <input id="actual-quantity" type="number" min="0" max="2147483647" step="1" wire:model="actual_quantity"
                        placeholder="Enter the quantity you physically counted"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-2 text-xs text-slate-500">Use zero if the product is completely out of stock.</p>
                    @error('actual_quantity') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="adjustment-reason" class="mb-2 block text-sm font-semibold text-slate-700">Reason <span class="font-normal text-slate-400">(optional)</span></label>
                    <input id="adjustment-reason" type="text" maxlength="255" wire:model="reason"
                        placeholder="e.g. Physical stock count, damaged goods"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('reason') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end border-t border-slate-100 pt-5">
                    <button type="submit" wire:loading.attr="disabled" wire:target="submit" @disabled($products->isEmpty())
                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                        <span wire:loading.remove wire:target="submit">Save adjustment</span>
                        <span wire:loading wire:target="submit">Adjusting stock...</span>
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>

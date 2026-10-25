<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto flex max-w-7xl flex-col gap-5 px-4 py-8 sm:px-6 sm:flex-row sm:items-center sm:justify-between lg:px-8">
            <div>
                <a href="{{ route('inventory.products.index') }}" wire:navigate class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                    &larr; Back to products
                </a>
                <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">Deleted products</h1>
                <p class="mt-2 text-sm text-slate-500">Restore products removed from the active catalog.</p>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
        @endif

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                <div>
                    <h2 class="font-semibold text-slate-900">Archived products</h2>
                    <p class="mt-1 text-sm text-slate-500">Deleted products are kept in the database and can be restored.</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ number_format($products->total()) }} deleted</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Product</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Category</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Deleted</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($products as $product)
                            <tr wire:key="deleted-product-{{ $product->id }}" class="hover:bg-indigo-50/40">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $product->sku }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $product->category?->name ?? 'Uncategorized' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">{{ $product->deleted_at->format('M j, Y g:i A') }}</td>
                                <td class="px-6 py-4 text-right">
                                    @can('restore products')
                                    <button type="button" wire:click="restore({{ $product->id }})"
                                        wire:confirm="Restore this product to the active catalog?"
                                        class="rounded-lg px-3 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">
                                        Restore
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <h3 class="text-sm font-semibold text-slate-900">No deleted products</h3>
                                    <p class="mt-1 text-sm text-slate-500">Products you delete will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($products->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">{{ $products->links() }}</div>
            @endif
        </section>
    </div>
</div>

<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto flex max-w-7xl flex-col gap-5 px-4 py-8 sm:px-6 sm:flex-row sm:items-center sm:justify-between lg:px-8">
            <div>
                <div class="inventory-eyebrow">
                    Inventory management
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Suppliers</h1>
                <p class="mt-2 text-sm text-slate-500">Manage supplier contacts and link suppliers to products.</p>
            </div>
            <a href="{{ route('inventory.suppliers.create') }}" wire:navigate
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">
                Add supplier
            </a>
        </div>
    </div>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800">{{ session('error') }}</div>
        @endif

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">Supplier directory</h2>
                    <p class="mt-1 text-sm text-slate-500">Suppliers assigned to products cannot be deleted.</p>
                </div>
                <div class="flex w-full flex-wrap gap-3 sm:max-w-xl sm:flex-nowrap">
                    <label class="sr-only" for="supplier-search">Search suppliers</label>
                    <input id="supplier-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Search suppliers..."
                        class="w-full rounded-xl border-slate-200 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500 sm:flex-1">
                    <label class="sr-only" for="supplier-sort">Sort suppliers</label>
                    <select id="supplier-sort" wire:model.live="sort"
                        class="w-full rounded-xl border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 sm:w-auto">
                        <option value="name_asc">Name: A to Z</option>
                        <option value="name_desc">Name: Z to A</option>
                        <option value="products_desc">Most products</option>
                        <option value="products_asc">Fewest products</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Supplier</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Contact details</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Products</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($suppliers as $supplier)
                            <tr wire:key="supplier-{{ $supplier->id }}" class="hover:bg-indigo-50/40">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-900">{{ $supplier->name }}</p>
                                    @if ($supplier->address)
                                        <p class="mt-1 max-w-sm text-sm text-slate-500">{{ $supplier->address }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <p>{{ $supplier->email ?: 'No email' }}</p>
                                    <p class="mt-1 text-slate-400">{{ $supplier->phone ?: 'No phone' }}</p>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold text-slate-700">{{ number_format($supplier->products_count) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <a href="{{ route('inventory.suppliers.edit', $supplier) }}" wire:navigate class="rounded-lg px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">Edit</a>
                                    @can('manage suppliers')
                                    <button type="button" wire:click="delete({{ $supplier->id }})"
                                        wire:confirm="Delete this supplier? Suppliers linked to products cannot be deleted."
                                        class="rounded-lg px-3 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50">Delete</button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <h3 class="text-sm font-semibold text-slate-900">No suppliers found</h3>
                                    <p class="mt-1 text-sm text-slate-500">Add a supplier to link it with your products.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($suppliers->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">{{ $suppliers->links() }}</div>
            @endif
        </section>
    </div>
</div>

<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto flex max-w-7xl flex-col gap-5 px-4 py-8 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div>
                <div class="inventory-eyebrow">
                    Inventory management
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Products</h1>
                <p class="mt-2 text-sm text-slate-500">Track products, pricing, and stock levels in one place.</p>
            </div>
            @can('create products')
            <a href="{{ route('inventory.products.create') }}" wire:navigate
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 4a.75.75 0 0 1 .75.75v4.5h4.5a.75.75 0 0 1 0 1.5h-4.5v4.5a.75.75 0 0 1-1.5 0v-4.5h-4.5a.75.75 0 0 1 0-1.5h4.5v-4.5A.75.75 0 0 1 10 4Z" />
                </svg>
                Add product
            </a>
            @endcan
            <a href="{{ route('inventory.products.trashed') }}" wire:navigate
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Deleted products
            </a>
        </div>
    </div>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div role="status" class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">✓</span>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div class="overflow-hidden rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-600 to-violet-600 p-6 text-white shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-indigo-100">Total products</p>
                        <p class="mt-3 text-4xl font-bold">{{ number_format($products->total()) }}</p>
                    </div>
                    <div class="rounded-xl bg-white/15 p-3">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8.25 4.5v9L12 21l-8.25-4.5v-9L12 3Zm0 9 8.25-4.5M12 12 3.75 7.5M12 12v9" />
                        </svg>
                    </div>
                </div>
                <p class="mt-5 text-sm text-indigo-100">Products in your inventory</p>
            </div>

            <div class="rounded-2xl border border-rose-100 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-600">Needs restocking</p>
                        <p class="mt-3 text-4xl font-bold text-slate-900">{{ number_format($lowStockCount) }}</p>
                    </div>
                    <div class="rounded-xl bg-rose-50 p-3 text-rose-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3h.008v.008H12v-.008ZM10.3 4.85 2.9 17.7A1.5 1.5 0 0 0 4.2 20h15.6a1.5 1.5 0 0 0 1.3-2.3L13.7 4.85a1.96 1.96 0 0 0-3.4 0Z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-5 text-sm text-slate-500">At or below the reorder level</p>
            </div>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">Product catalog</h2>
                    <p class="mt-1 text-sm text-slate-500">Browse products and monitor current stock.</p>
                </div>
                <div class="flex w-full flex-wrap items-center justify-end gap-3 sm:max-w-4xl">
                <div class="relative min-w-[15rem] flex-1">
                    <label for="product-search" class="sr-only">Search products by name or SKU</label>
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.5 3a5.5 5.5 0 1 0 3.447 9.785l3.634 3.634a.75.75 0 1 0 1.06-1.06l-3.633-3.634A5.5 5.5 0 0 0 8.5 3ZM4.5 8.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                    </svg>
                    <input id="product-search" type="search" wire:model.live.debounce.300ms="search"
                        placeholder="Search by name or SKU..."
                        class="w-full rounded-xl border-slate-200 py-3 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <label for="product-category" class="sr-only">Filter by category</label>
                <select id="product-category" wire:model.live="category"
                    class="w-full min-w-[9rem] rounded-xl border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 sm:w-auto">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <label for="product-stock" class="sr-only">Filter by stock status</label>
                <select id="product-stock" wire:model.live="stock"
                    class="w-full min-w-[9rem] rounded-xl border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 sm:w-auto">
                    <option value="all">All stock levels</option>
                    <option value="in_stock">In stock</option>
                    <option value="low">Low stock</option>
                    <option value="out_of_stock">Out of stock</option>
                </select>
                <label for="product-sort" class="sr-only">Sort products</label>
                <select id="product-sort" wire:model.live="sort"
                    class="w-full min-w-[9rem] rounded-xl border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 sm:w-auto">
                    <option value="newest">Newest first</option>
                    <option value="name_asc">Name: A to Z</option>
                    <option value="name_desc">Name: Z to A</option>
                    <option value="cost_asc">Cost: Low to high</option>
                    <option value="cost_desc">Cost: High to low</option>
                    <option value="selling_asc">Selling price: Low to high</option>
                    <option value="selling_desc">Selling price: High to low</option>
                    <option value="stock_desc">Stock: Highest first</option>
                </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Product</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Category</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Suppliers</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Cost</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Selling price</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Reorder at</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">In stock</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($products as $product)
                            @php($isLowStock = $product->quantity <= $product->reorder_level)
                            <tr wire:key="product-{{ $product->id }}" class="transition hover:bg-indigo-50/40">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $isLowStock ? 'bg-rose-100 text-rose-700' : 'bg-indigo-100 text-indigo-700' }} text-sm font-bold">
                                            {{ strtoupper(substr($product->name, 0, 1)) }}
                                        </span>
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $product->sku }} <span class="text-slate-300">·</span> {{ $product->unit }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">{{ $product->category?->name ?? 'Uncategorized' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex max-w-xs flex-wrap gap-1.5">
                                        @forelse ($product->suppliers as $supplier)
                                            <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">{{ $supplier->name }}</span>
                                        @empty
                                            <span class="text-sm text-slate-400">—</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-slate-600">{{ number_format($product->cost_price, 2) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-slate-900">{{ number_format($product->selling_price, 2) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-slate-600">{{ number_format($product->reorder_level) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $isLowStock ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $isLowStock ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                        {{ number_format($product->quantity) }} {{ $product->unit }}
                                        @if ($isLowStock)
                                            <span class="font-medium">· Low</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        @can('manage stock')
                                        <a href="{{ route('inventory.stock.movement', ['product_id' => $product->id]) }}" wire:navigate
                                            class="rounded-lg px-3 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">
                                            Restock
                                        </a>
                                        @endcan
                                        @can('edit products')
                                        <a href="{{ route('inventory.products.edit', $product) }}" wire:navigate
                                            class="rounded-lg px-3 py-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100">
                                            Edit
                                        </a>
                                        @endcan
                                        @can('delete products')
                                        <button type="button" wire:click="delete({{ $product->id }})"
                                            wire:confirm="Move this product to deleted products? You can restore it later."
                                            class="rounded-lg px-3 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">
                                            Delete
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8.25 4.5v9L12 21l-8.25-4.5v-9L12 3Zm0 9 8.25-4.5M12 12 3.75 7.5M12 12v9" />
                                        </svg>
                                    </div>
                                    <h3 class="mt-4 text-sm font-semibold text-slate-900">{{ $search ? 'No matching products' : 'No products yet' }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $search ? 'Try another product name or SKU.' : 'Add your first product to start tracking stock.' }}</p>
                                    @unless ($search)
                                        @can('create products')
                                        <a href="{{ route('inventory.products.create') }}" wire:navigate
                                            class="mt-5 inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                            Add your first product
                                        </a>
                                        @endcan
                                    @endunless
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $products->links() }}
                </div>
            @endif
        </section>
    </div>
</div>

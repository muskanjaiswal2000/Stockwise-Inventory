<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto flex max-w-7xl flex-col gap-5 px-4 py-8 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div>
                <div class="inventory-eyebrow">
                    Inventory management
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Categories</h1>
                <p class="mt-2 text-sm text-slate-500">Organize your products into clear, easy-to-manage groups.</p>
            </div>
            <a href="{{ route('inventory.categories.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 4a.75.75 0 0 1 .75.75v4.5h4.5a.75.75 0 0 1 0 1.5h-4.5v4.5a.75.75 0 0 1-1.5 0v-4.5h-4.5a.75.75 0 0 1 0-1.5h4.5v-4.5A.75.75 0 0 1 10 4Z" />
                </svg>
                Add category
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
        @if (session('error'))
            <div role="alert" class="flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-700">!</span>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div class="overflow-hidden rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-600 to-violet-600 p-6 text-white shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-indigo-100">Total categories</p>
                        <p class="mt-3 text-4xl font-bold">{{ number_format($categories->total()) }}</p>
                    </div>
                    <div class="rounded-xl bg-white/15 p-3">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6.5A2.5 2.5 0 0 1 6.5 4h3A2.5 2.5 0 0 1 12 6.5v3A2.5 2.5 0 0 1 9.5 12h-3A2.5 2.5 0 0 1 4 9.5v-3ZM14 6.5A2.5 2.5 0 0 1 16.5 4h1A2.5 2.5 0 0 1 20 6.5v1A2.5 2.5 0 0 1 17.5 10h-1A2.5 2.5 0 0 1 14 7.5v-1ZM14 15.5a2.5 2.5 0 0 1 2.5-2.5h1a2.5 2.5 0 0 1 2.5 2.5v1a2.5 2.5 0 0 1-2.5 2.5h-1a2.5 2.5 0 0 1-2.5-2.5v-1ZM4 15.5A2.5 2.5 0 0 1 6.5 13h3a2.5 2.5 0 0 1 2.5 2.5v1A2.5 2.5 0 0 1 9.5 19h-3A2.5 2.5 0 0 1 4 16.5v-1Z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-5 text-sm text-indigo-100">Product groups in your inventory</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <label for="category-search" class="block text-sm font-semibold text-slate-800">Find a category</label>
                <p class="mt-1 text-sm text-slate-500">Search by category name.</p>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.5 3a5.5 5.5 0 1 0 3.447 9.785l3.634 3.634a.75.75 0 1 0 1.06-1.06l-3.633-3.634A5.5 5.5 0 0 0 8.5 3ZM4.5 8.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                        </svg>
                        <input id="category-search" type="search" wire:model.live="search" placeholder="Search categories..."
                               class="w-full rounded-xl border-slate-200 py-3 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <label for="category-sort" class="sr-only">Sort categories</label>
                    <select id="category-sort" wire:model.live="sort"
                            class="w-full rounded-xl border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 sm:w-auto">
                    <option value="newest">Newest first</option>
                    <option value="name_asc">Name: A to Z</option>
                    <option value="name_desc">Name: Z to A</option>
                    <option value="products_desc">Most products</option>
                    <option value="products_asc">Fewest products</option>
                    </select>
                </div>
            </div>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-1 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">All categories</h2>
                    <p class="mt-1 text-sm text-slate-500">Browse and manage your product groups.</p>
                </div>
                <span class="mt-2 inline-flex w-fit items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 sm:mt-0">
                    {{ $categories->count() }} shown
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Category</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Description</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($categories as $category)
                            <tr wire:key="category-{{ $category->id }}" class="transition hover:bg-indigo-50/40">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-sm font-bold text-indigo-700">
                                            {{ strtoupper(substr($category->name, 0, 1)) }}
                                        </span>
                                        <span class="font-semibold text-slate-900">{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td class="max-w-xl px-6 py-4 text-sm text-slate-600">
                                    {{ $category->description ?: 'No description added' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('inventory.categories.edit', $category) }}"
                                           class="rounded-lg px-3 py-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100">
                                            Edit
                                        </a>
                                        @can('manage categories')
                                        <button type="button" wire:click="delete({{ $category->id }})"
                                                wire:confirm="Are you sure you want to delete this category?"
                                                class="rounded-lg px-3 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50">
                                            Delete
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.3 7.7 7.7 4.4 7.7-4.4M12 12v8.5" />
                                        </svg>
                                    </div>
                                    <h3 class="mt-4 font-semibold text-slate-900">{{ $search ? 'No matching categories' : 'No categories yet' }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $search ? 'Try another search term.' : 'Create your first category to start organizing products.' }}
                                    </p>
                                    @unless($search)
                                        <a href="{{ route('inventory.categories.create') }}"
                                           class="mt-5 inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                            Create your first category
                                        </a>
                                    @endunless
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $categories->links() }}
                </div>
            @endif
        </section>
    </div>
</div>

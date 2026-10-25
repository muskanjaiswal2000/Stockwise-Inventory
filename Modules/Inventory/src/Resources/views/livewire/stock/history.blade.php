<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto flex max-w-7xl flex-col gap-5 px-4 py-8 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div>
                <div class="mb-2 flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-indigo-600">
                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    Inventory management
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Stock history</h1>
                <p class="mt-2 text-sm text-slate-500">Review recorded stock-in and stock-out movements.</p>
            </div>
            <a href="{{ route('inventory.stock.movement') }}" wire:navigate
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Record movement
            </a>
        </div>
    </div>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">All movements</h2>
                    <p class="mt-1 text-sm text-slate-500">Newest stock updates appear first.</p>
                </div>
                <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto">
                    <a href="{{ route('inventory.stock.history.export.excel', array_filter(['date_from' => $dateFrom, 'date_to' => $dateTo, 'movement_type' => $movementType])) }}"
                        class="inline-flex flex-1 items-center justify-center rounded-lg bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100 sm:flex-none">
                        Export Excel
                    </a>
                    <a href="{{ route('inventory.stock.history.export.pdf', array_filter(['date_from' => $dateFrom, 'date_to' => $dateTo, 'movement_type' => $movementType])) }}"
                        class="inline-flex flex-1 items-center justify-center rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 sm:flex-none">
                        Export PDF
                    </a>
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ number_format($movements->total()) }} recorded
                    </span>
                </div>
            </div>

            <div class="border-b border-slate-100 bg-slate-50/70 px-6 py-5">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:items-end">
                    <div>
                        <label for="history-date-from" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">From date</label>
                        <input id="history-date-from" type="date" wire:model.live="dateFrom"
                            class="w-full rounded-xl border-slate-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="history-date-to" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">To date</label>
                        <input id="history-date-to" type="date" wire:model.live="dateTo"
                            class="w-full rounded-xl border-slate-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="history-movement-type" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Movement type</label>
                        <select id="history-movement-type" wire:model.live="movementType"
                            class="w-full rounded-xl border-slate-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All movements</option>
                            <option value="in">Stock in</option>
                            <option value="out">Stock out</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                    <button type="button" wire:click="clearFilters"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        Clear filters
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Product</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Movement</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Quantity</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Reason</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Recorded by</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($movements as $movement)
                            @php($isAdjustment = $movement->is_adjustment)
                            @php($isReturned = str_starts_with((string) $movement->reason, 'Usable return for purchase request'))
                            @php($isIssued = str_starts_with((string) $movement->reason, 'Issued for purchase request'))
                            <tr wire:key="movement-{{ $movement->id }}" class="transition hover:bg-indigo-50/40">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                    <time datetime="{{ $movement->created_at->toIso8601String() }}">
                                        {{ $movement->created_at->format('M j, Y') }}
                                        <span class="mt-1 block text-xs text-slate-400">{{ $movement->created_at->format('g:i A') }}</span>
                                    </time>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($movement->product)
                                        <p class="font-semibold text-slate-900">{{ $movement->product->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $movement->product->sku }}</p>
                                    @else
                                        <span class="text-sm text-slate-500">Product unavailable</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $isAdjustment ? 'bg-violet-50 text-violet-700' : ($isReturned ? 'bg-emerald-50 text-emerald-700' : ($movement->type === 'in' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700')) }}">
                                        <span aria-hidden="true">{{ $movement->type === 'in' ? '↑' : '↓' }}</span>
                                        {{ $isReturned ? 'Returned' : ($isIssued ? 'Purchased' : ($isAdjustment ? 'Adjustment' : ($movement->type === 'in' ? 'Stock in' : 'Stock out'))) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-slate-900">
                                    {{ number_format($movement->quantity) }}
                                    @if ($movement->product)
                                        <span class="font-normal text-slate-500">{{ $movement->product->unit }}</span>
                                    @endif
                                </td>
                                <td class="max-w-xs px-6 py-4 text-sm text-slate-600">{{ $isReturned ? 'Returned' : ($isIssued ? 'Purchased' : ($movement->reason ?: '—')) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">{{ $movement->user?->name ?? 'Unknown' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    </div>
                                    <h3 class="mt-4 text-sm font-semibold text-slate-900">No stock movements yet</h3>
                                    <p class="mt-1 text-sm text-slate-500">Stock updates will appear here after they are recorded.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($movements->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $movements->links() }}
                </div>
            @endif
        </section>
    </div>
</div>

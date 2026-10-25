@php
    $displayRole = auth()->user()->getRoleNames()->first() ?? auth()->user()->name;
@endphp

<div class="min-h-screen bg-[#eefbfd]">
    <div class="border-b border-teal-100 bg-white/75">
        <div
            class="mx-auto flex max-w-7xl flex-col gap-4 px-5 py-6 sm:px-8 lg:flex-row lg:items-center lg:justify-between lg:px-10">

            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                    Welcome back, {{ ucfirst($displayRole) }}!
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Here's your inventory overview for today.
                </p>
            </div>
        </div>

        <div class="mx-auto max-w-7xl space-y-5 px-5 py-6 sm:px-8 lg:px-10">
            <div class="grid gap-4 sm:grid-cols-2">
                <a href="{{ route('inventory.products.index') }}" wire:navigate
                    class="group rounded-2xl bg-gradient-to-br from-[#10a9b3] to-[#08a5ad] p-5 text-white shadow-lg shadow-teal-200 transition hover:-translate-y-0.5 hover:shadow-xl">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold text-teal-50">Total products</p>
                            <p class="mt-3 text-5xl font-extrabold tracking-tight">{{ number_format($productCount) }}</p>
                        </div>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-lg">▣</span>
                    </div>
                </a>

                <a href="{{ route('inventory.products.index') }}" wire:navigate
                    class="group rounded-2xl border border-white bg-white p-5 shadow-lg shadow-cyan-100 transition hover:-translate-y-0.5 hover:shadow-xl">
                    <div class="flex items-start justify-between">
                        <div>
                        <p class="text-xs font-semibold text-slate-500">Low-stock products</p>
                        <p class="mt-3 text-5xl font-extrabold tracking-tight text-slate-900">{{ number_format($lowStockCount) }}</p>
                        </div>
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-lg text-rose-500">△</span>
                    </div>
                </a>
            </div>

            <section class="overflow-hidden rounded-2xl border border-white bg-white shadow-lg shadow-cyan-100">
                <div
                    class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Recent stock activity</h2>
                        <p class="text-xs text-slate-500">Showing the 10 most recent stock movements</p>
                    </div>
                    <a href="{{ route('inventory.stock.history') }}" wire:navigate
                        class="text-xs font-bold text-teal-600 hover:text-teal-800">View all →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-white">
                            <tr>
                                <th
                                    class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Product</th>
                                <th
                                    class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Type</th>
                                <th
                                    class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Quantity</th>
                                <th
                                    class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Recorded by</th>
                                <th
                                    class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentMovements as $movement)
                                <tr wire:key="dashboard-movement-{{ $movement->id }}" class="hover:bg-cyan-50/40">
                                    <td class="px-5 py-3">
                                        <p class="text-xs font-bold text-slate-800">
                                            {{ $movement->product?->name ?? 'Product unavailable' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400">
                                            {{ $movement->reason ?: ($movement->product?->sku ?? 'No additional details') }}
                                        </p>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3"><span
                                            class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold {{ $movement->is_adjustment ? 'bg-sky-100 text-sky-700' : ($movement->type === 'in' ? 'bg-teal-100 text-teal-700' : 'bg-amber-100 text-amber-700') }}">{{ $movement->is_adjustment ? 'Adjustment' : ($movement->type === 'in' ? 'Stock in' : 'Stock out') }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3 text-right text-xs font-bold text-slate-800">
                                        {{ $movement->type === 'in' ? '+' : '−' }}{{ number_format($movement->quantity) }}
                                        <span class="font-normal text-slate-400">{{ $movement->product?->unit }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3 text-xs text-slate-500">
                                        {{ $movement->user?->name ?? 'Unknown' }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3 text-right text-[11px] text-slate-500">
                                        {{ $movement->created_at->format('M j, g:i A') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">No stock activity
                                        yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto flex max-w-7xl flex-col gap-5 px-4 py-8 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div>
                <div class="inventory-eyebrow">Inventory management</div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Stock movement</h1>
                <p class="mt-2 text-sm text-slate-500">Manage catalog requests, assignments, and returns.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="button" wire:click="generateQr" class="rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-700">
                    Generate QR
                </button>
                @if ($qrGenerated && $qrImageUrl)
                <button type="button" onclick="downloadStockwiseQr('{{ $qrImageUrl }}')"
                    class="rounded-xl border border-teal-200 bg-white px-5 py-3 text-sm font-semibold text-teal-700 hover:bg-teal-50">
                    Download QR
                </button>
                @endif
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
        @endif
        @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800">
            {{ session('error') }}
        </div>
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div class="rounded-2xl border border-teal-100 bg-gradient-to-br from-teal-600 to-cyan-600 p-6 text-white shadow-sm">
                <p class="text-sm font-medium text-teal-100">Products tracked</p>
                <p class="mt-3 text-4xl font-bold">{{ number_format($productCount) }}</p>
                <p class="mt-5 text-sm text-teal-100">Products available in the catalog</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold text-slate-600">Units currently in stock</p>
                <p class="mt-3 text-4xl font-bold text-slate-900">{{ number_format($totalQuantity) }}</p>
                <p class="mt-5 text-sm text-slate-500">Combined quantity across all products</p>
            </div>
        </div>

        {{-- Full QR Modal with Scanner --}}
        @if ($showQr && $qrImageUrl)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4" role="dialog" aria-modal="true">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900">Catalog QR code</h2>
                    <button type="button" wire:click="$set('showQr', false)" class="text-2xl text-slate-400 hover:text-slate-700" aria-label="Close">&times;</button>
                </div>

                <img id="stockwise-qr-image" src="{{ $qrImageUrl }}" alt="StockWise catalog QR code"
                    draggable="true"
                    ondragstart="event.dataTransfer.setData('text/uri-list', this.src); event.dataTransfer.setData('text/plain', this.src)"
                    class="mx-auto mt-5 h-64 w-64 cursor-grab rounded-xl border border-slate-200 p-3">

                <div class="mt-5 flex flex-wrap justify-center gap-3">
                    <button type="button" onclick="downloadStockwiseQr('{{ $qrImageUrl }}')"
                        class="rounded-xl bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white">
                        Download QR
                    </button>
                </div>

                <div id="stockwise-scanner" class="relative mt-5 rounded-xl border-2 border-dashed border-teal-300 bg-slate-950 p-8"
                    ondragover="event.preventDefault()" ondrop="dropStockwiseQr(event)">
                    <div id="stockwise-camera" class="flex min-h-32 items-center justify-center text-center text-sm font-semibold text-teal-200">
                        Drag the QR image here to scan
                    </div>
                    <div class="pointer-events-none absolute inset-x-10 top-1/2 h-0.5 animate-pulse bg-teal-300 shadow-[0_0_12px_4px_rgba(45,212,191,0.8)]"></div>
                    <p id="stockwise-scanner-message" class="mt-3 text-center text-xs text-slate-400">
                        <!-- No camera is used. Drop the QR image into this box. -->
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- Request History --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-5">
                <h2 class="font-semibold text-slate-900">Request history</h2>
                <p class="mt-1 text-sm text-slate-500">Users, products and assignment status.</p>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($purchaseRequests as $purchaseRequest)
                <article class="p-6" wire:key="pr-{{ $purchaseRequest->id }}">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-bold text-slate-900">{{ $purchaseRequest->request_code }}</h3>
                                <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-bold capitalize text-teal-700">
                                    {{ ucfirst($purchaseRequest->status) }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $purchaseRequest->customer_name }} · {{ $purchaseRequest->customer_email }} ·
                                {{ $purchaseRequest->created_at->format('M j, Y g:i A') }}
                            </p>
                        </div>

                        @if ($purchaseRequest->status === 'requested')
                        <button type="button" wire:click="assignRequest({{ $purchaseRequest->id }})"
                            class="rounded-lg bg-teal-600 px-4 py-2 text-xs font-bold text-white">
                            Assign
                        </button>
                        @endif
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-left text-xs">
                            <thead class="text-[10px] uppercase tracking-wide text-slate-400">
                                <tr>
                                    <th class="pb-2 pr-4">Product</th>
                                    <th class="pb-2 pr-4">Requested</th>
                                    <th class="pb-2 pr-4">Assigned</th>
                                    <th class="pb-2 pr-4">Returned</th>
                                    <!-- <th class="pb-2">Action</th> -->
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($purchaseRequest->items as $item)
                                <tr>
                                    <td class="py-3 pr-4 font-semibold text-slate-800">
                                        {{ $item->product?->name ?? 'N/A' }}
                                        <span class="text-slate-400">({{ $item->product?->sku }})</span>
                                    </td>
                                    <td class="py-3 pr-4">{{ $item->requested_quantity }}</td>
                                    <td class="py-3 pr-4">{{ $item->issued_quantity }}</td>
                                    <td class="py-3 pr-4">{{ $item->returned_quantity }}</td>
                                    <td class="py-3">
                                        @if ($item->issued_quantity > $item->returned_quantity)
                                        <button type="button" wire:click="processReturn({{ $item->id }})"
                                            class="rounded-lg bg-teal-600 px-3 py-1.5 text-[10px] font-bold text-white">
                                            Return product
                                        </button>
                                        @else
                                        <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
                @empty
                <p class="p-6 text-sm text-slate-500">No purchase requests yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
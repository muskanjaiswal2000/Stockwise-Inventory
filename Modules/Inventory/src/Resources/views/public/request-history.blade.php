<main class="mx-auto max-w-2xl px-4 py-6 sm:px-6">
    <a href="{{ route('inventory.catalog', $token) }}" class="text-sm font-semibold text-teal-700">← Back to catalog</a>
    <section class="mt-5 rounded-2xl border border-white bg-white p-6 shadow-lg shadow-cyan-100 sm:p-8">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-teal-600">StockWise guest page</p>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-900">Your request</h1>
        <p class="mt-1 text-sm text-slate-500">Your request status and product details are shown below.</p>
        @if ($purchaseRequest)
            <div class="mt-6 border-t border-slate-100 pt-5">
                <div class="flex items-center justify-between gap-3"><h2 class="font-bold text-slate-900">{{ $purchaseRequest->request_code }}</h2><span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-bold capitalize text-teal-700">{{ $purchaseRequest->status }}</span></div>
                <details class="mt-3 rounded-xl bg-slate-50 p-3"><summary class="cursor-pointer text-sm font-bold text-teal-700">User details</summary><div class="mt-3 space-y-1 text-sm text-slate-600"><p><strong>Name:</strong> {{ $purchaseRequest->customer_name }}</p><p><strong>Email:</strong> {{ $purchaseRequest->customer_email }}</p><p><strong>Requested:</strong> {{ $purchaseRequest->created_at->format('M j, Y g:i A') }}</p></div></details>
                <div class="mt-4 divide-y divide-slate-100">@foreach ($purchaseRequest->items as $item)<div class="flex flex-col gap-1 py-3 text-sm sm:flex-row sm:justify-between"><span class="font-semibold">{{ $item->product?->name }} ({{ $item->product?->sku }})</span><span>Requested {{ $item->requested_quantity }}</span></div>@endforeach</div>
            </div>
        @else
            <div class="mt-6 rounded-xl bg-slate-50 p-4 text-sm text-slate-500">Complete checkout first. Your request details will open here automatically.</div>
        @endif
    </section>
</main>

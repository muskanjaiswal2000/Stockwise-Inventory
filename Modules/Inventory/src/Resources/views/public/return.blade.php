<main class="mx-auto max-w-2xl px-4 py-6 sm:px-6">
    <a href="{{ route('inventory.catalog', $token) }}" class="text-sm font-semibold text-teal-700">← Back to catalog</a>
    <section class="mt-5 rounded-2xl border border-white bg-white p-6 shadow-lg shadow-cyan-100 sm:p-8">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-teal-600">StockWise return</p>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-900">Request a product return</h1>
        <p class="mt-1 text-sm text-slate-500">Enter your request code and email. Staff will inspect the returned item and decide whether it can be restocked.</p>

        @if (session('success'))
            <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('inventory.catalog.return', $token) }}" class="mt-6 grid gap-4 sm:grid-cols-2">
            <div><label class="mb-1 block text-sm font-semibold">Request ID</label><input name="request_code" value="{{ request('request_code') }}" placeholder="PR-..." class="w-full rounded-xl border-slate-300"></div>
            <div><label class="mb-1 block text-sm font-semibold">Email</label><input name="customer_email" type="email" value="{{ request('customer_email') }}" class="w-full rounded-xl border-slate-300"></div>
            <button class="rounded-xl bg-slate-800 px-4 py-3 text-sm font-bold text-white sm:col-span-2">Find purchased products</button>
        </form>

        @if (request()->filled('request_code') && ! $purchaseRequest)
            <p class="mt-4 text-sm text-rose-600">No purchased request was found with those details.</p>
        @endif

        @if ($purchaseRequest)
            <form method="POST" action="{{ route('inventory.catalog.return.submit', $token) }}" class="mt-6 space-y-4 border-t border-slate-100 pt-6">
                @csrf
                <input type="hidden" name="request_code" value="{{ $purchaseRequest->request_code }}">
                <input type="hidden" name="customer_email" value="{{ $purchaseRequest->customer_email }}">
                <div><label class="mb-1 block text-sm font-semibold">Product</label><select name="purchase_request_item_id" class="w-full rounded-xl border-slate-300">@foreach ($purchaseRequest->items as $item) @if ($item->issued_quantity - $item->returned_quantity > 0)<option value="{{ $item->id }}">{{ $item->product?->name }} ({{ $item->issued_quantity - $item->returned_quantity }} available to return)</option>@endif @endforeach</select></div>
                <div><label class="mb-1 block text-sm font-semibold">Quantity</label><input name="quantity" type="number" min="1" value="1" class="w-full rounded-xl border-slate-300">@error('quantity')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror</div>
                <div><label class="mb-1 block text-sm font-semibold">Notes</label><textarea name="notes" rows="3" class="w-full rounded-xl border-slate-300" placeholder="Optional return details"></textarea></div>
                <button class="w-full rounded-xl bg-teal-600 px-5 py-3 text-sm font-bold text-white hover:bg-teal-700">Submit return request</button>
            </form>
        @endif
    </section>
</main>

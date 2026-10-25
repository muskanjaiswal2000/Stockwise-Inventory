<main class="mx-auto max-w-2xl px-4 py-6 sm:px-6">
        <a href="{{ route('inventory.catalog', $token) }}" class="text-sm font-semibold text-teal-700">← Back to catalog</a>
        <section class="mt-5 rounded-2xl border border-white bg-white p-6 shadow-lg shadow-cyan-100 sm:p-8">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-teal-600">StockWise request</p>
            <h1 class="mt-2 text-2xl font-extrabold text-slate-900">Your details</h1>
            <p class="mt-1 text-sm text-slate-500">Enter your details before submitting this product request.</p>

            @if (session('request_code'))
                <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">Your request <strong>{{ session('request_code') }}</strong> has been submitted.</div>
            @endif

            @error('cart') <p class="mt-4 text-sm text-rose-600">{{ $message }}</p> @enderror
            <div class="mt-6 space-y-4">
                @foreach ($cartProducts as $product)
                    <div class="flex justify-between rounded-xl bg-slate-50 px-4 py-3 text-sm"><span class="font-semibold text-slate-800">{{ $product->name }}</span><span>{{ $cart[$product->id] }} {{ $product->unit }}</span></div>
                @endforeach
            </div>

            <form method="POST" action="{{ route('inventory.catalog.checkout.submit', $token) }}" class="mt-6 space-y-4">
                @csrf
                <div><label for="customer-name" class="mb-1 block text-sm font-semibold">Name</label><input id="customer-name" name="customerName" value="{{ old('customerName') }}" class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">@error('customerName')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror</div>
                <div><label for="customer-email" class="mb-1 block text-sm font-semibold">Email</label><input id="customer-email" name="customerEmail" type="email" value="{{ old('customerEmail') }}" class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">@error('customerEmail')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror</div>
                <button type="submit" class="w-full rounded-xl bg-teal-600 px-5 py-3 text-sm font-bold text-white hover:bg-teal-700">Checkout</button>
            </form>
        </section>
    </main>

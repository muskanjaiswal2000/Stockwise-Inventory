<main class="mx-auto max-w-3xl px-4 py-6 sm:px-6">

    <header class="mb-6 rounded-2xl bg-gradient-to-r from-[#0d7771] to-[#08a5ad] p-6 text-white shadow-lg shadow-teal-200">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-teal-100">StockWise</p>
        <h1 class="mt-2 text-2xl font-extrabold">Product catalog</h1>
        <p class="mt-1 text-sm text-teal-50">Select the products and quantities you want to request.</p>
    </header>

    {{-- Request success message --}}
    @if (session('request_code'))
        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            Request <strong>{{ session('request_code') }}</strong> submitted successfully.
        </div>
    @endif

    {{-- Cart cleared success message --}}
    @if (session('cart_cleared'))
        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
            {{ session('cart_cleared') }}
        </div>
    @endif

    <div class="space-y-5 pb-28">

        @forelse ($categories as $categoryName => $products)

            <section class="overflow-hidden rounded-2xl border border-white bg-white shadow-sm">

                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="font-bold text-slate-900">{{ $categoryName }}</h2>
                </div>

                <div class="divide-y divide-slate-100">

                    @foreach ($products as $product)

                        <div class="flex items-center justify-between gap-4 px-5 py-4">

                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900">{{ $product->name }}</p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $product->sku }} · {{ $product->unit }}
                                </p>
                            </div>

                            <div class="shrink-0">

                                @if (($cart[$product->id] ?? 0) > 0)

                                    <div class="flex items-center gap-3">

                                        {{-- Decrease quantity --}}
                                        <form method="POST"
                                            action="{{ route('inventory.catalog.decrement', [$token, $product->id]) }}">
                                            @csrf

                                            <button type="submit"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-teal-200 text-lg font-bold text-teal-700 hover:bg-teal-50">
                                                −
                                            </button>
                                        </form>

                                        <span class="min-w-[24px] text-center text-sm font-bold text-slate-900">
                                            {{ $cart[$product->id] }}
                                        </span>

                                        {{-- Increase quantity --}}
                                        <form method="POST"
                                            action="{{ route('inventory.catalog.add', [$token, $product->id]) }}">
                                            @csrf

                                            <button type="submit"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-teal-600 text-lg font-bold text-white hover:bg-teal-700">
                                                +
                                            </button>
                                        </form>

                                    </div>

                                @else

                                    {{-- Add product --}}
                                    <form method="POST"
                                        action="{{ route('inventory.catalog.add', [$token, $product->id]) }}">
                                        @csrf

                                        <button type="submit"
                                            class="rounded-xl bg-teal-50 px-3 py-2 text-xs font-bold text-teal-700 hover:bg-teal-100">
                                            Add
                                        </button>
                                    </form>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>
            </section>

        @empty

            <div class="rounded-2xl bg-white p-8 text-center text-sm text-slate-500">
                No products are available.
            </div>

        @endforelse

    </div>

    {{-- Fixed cart summary --}}
    <aside class="fixed inset-x-0 bottom-0 border-t border-teal-100 bg-white/95 p-4 shadow-2xl backdrop-blur">

        <div class="mx-auto flex max-w-3xl items-center justify-between gap-3">

            <div>
                <p class="text-xs text-slate-500">Selected products</p>
                <p class="text-lg font-extrabold text-slate-900">{{ $cartCount }}</p>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">

                @if ($cartCount > 0)

                    {{-- Clear cart --}}
                    <form method="POST"
                        action="{{ route('inventory.catalog.clear', $token) }}">
                        @csrf

                        <button type="submit"
                            class="rounded-xl border border-red-200 px-3 py-3 text-xs font-bold text-red-600 hover:bg-red-50 sm:px-4">
                            Clear Cart
                        </button>
                    </form>

                @endif

                <a href="{{ route('inventory.catalog.checkout', $token) }}"
                    class="rounded-xl bg-teal-600 px-4 py-3 text-sm font-bold text-white hover:bg-teal-700 {{ $cartCount < 1 ? 'pointer-events-none opacity-50' : '' }} sm:px-5">
                    Submit request
                </a>

            </div>
        </div>
    </aside>

</main>

<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\PurchaseRequest;
use Modules\Inventory\Models\QrCatalog;

class PublicCheckoutSubmit
{
    public function __invoke(Request $request, string $token): RedirectResponse
    {
        abort_unless(QrCatalog::where('token', $token)->exists(), 404);

        $validated = $request->validate([
            'customerName' => ['required', 'string', 'max:255'],
            'customerEmail' => ['required', 'email', 'max:255'],
        ]);

        $key = 'catalog_cart_' . $token;
        $cart = session()->get($key, []);
        if ($cart === []) {
            return back()->withErrors(['cart' => 'Select at least one product before checkout.']);
        }

        $purchaseRequest = DB::transaction(function () use ($validated, $cart) {
            $products = Product::query()->whereIn('id', array_keys($cart))->lockForUpdate()->get()->keyBy('id');
            if ($products->count() !== count($cart)) {
                throw ValidationException::withMessages(['cart' => 'One or more selected products are no longer available.']);
            }

            $purchaseRequest = PurchaseRequest::create([
                'request_code' => 'PR-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'customer_name' => $validated['customerName'],
                'customer_email' => $validated['customerEmail'],
                'submitted_by_user_id' => auth()->id(),
                'status' => 'requested',
            ]);

            foreach ($cart as $productId => $quantity) {
                $quantity = filter_var($quantity, FILTER_VALIDATE_INT);
                if (! $quantity || $quantity < 1) {
                    throw ValidationException::withMessages(['cart' => 'Every product quantity must be at least one.']);
                }

                $purchaseRequest->items()->create([
                    'product_id' => $products->get((int) $productId)->id,
                    'requested_quantity' => $quantity,
                ]);
            }

            return $purchaseRequest;
        });

        if (! $purchaseRequest) {
            return back()->withErrors(['cart' => 'One or more selected products are no longer available.']);
        }

        session()->forget($key);
        session()->put([
            'last_request_code' => $purchaseRequest->request_code,
            'last_customer_email' => $purchaseRequest->customer_email,
        ]);

        return redirect()->route('inventory.catalog.requests', $token);
    }
}

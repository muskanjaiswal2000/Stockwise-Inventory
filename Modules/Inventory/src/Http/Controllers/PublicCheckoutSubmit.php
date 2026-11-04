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
use App\Models\User;
use Modules\Inventory\Notifications\InventoryNotification;
use Modules\Inventory\Services\NotificationRecipients;

class PublicCheckoutSubmit
{
    public function __invoke(
        Request $request,
        string $token,
        NotificationRecipients $recipients
    ): RedirectResponse {
        abort_unless(QrCatalog::where('token', $token)->exists(), 404);

        $validated = $request->validate([
            'customerName' => ['required', 'string', 'max:255'],
            'customerEmail' => ['required', 'email', 'max:255'],
        ]);

        $key = 'catalog_cart_' . $token;
        $cart = $request->session()->get($key, []);

        if ($cart === []) {
            return back()->withErrors([
                'cart' => 'Select at least one product before checkout.',
            ]);
        }

        $result = DB::transaction(function () use ($validated, $cart) {

            $products = Product::query()
                ->whereIn('id', array_keys($cart))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($cart)) {
                throw ValidationException::withMessages([
                    'cart' => 'One or more selected products are no longer available.',
                ]);
            }

            // Validate all quantities and available stock before creating the request.
            $stockErrors = [];
            $validatedCart = [];

            foreach ($cart as $productId => $quantity) {
                $quantity = filter_var($quantity, FILTER_VALIDATE_INT);

                if ($quantity === false || $quantity < 1) {
                    throw ValidationException::withMessages([
                        'cart' => 'Every product quantity must be at least one.',
                    ]);
                }

                $product = $products->get((int) $productId);

                if ($quantity > $product->quantity) {
                    $stockErrors[] = sprintf(
                        '%s: Only %d units are currently available. You requested %d. Please reduce the quantity to %d or less.',
                        $product->name,
                        $product->quantity,
                        $quantity,
                        $product->quantity
                    );
                }

                $validatedCart[$productId] = $quantity;
            }

            if (! empty($stockErrors)) {
                throw ValidationException::withMessages([
                    'cart' => $stockErrors,
                ]);
            }

            $purchaseRequest = PurchaseRequest::create([
                'request_code' => 'PR-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'customer_name' => $validated['customerName'],
                'customer_email' => $validated['customerEmail'],
                'submitted_by_user_id' => auth()->id(),
                'status' => 'requested',
            ]);

            foreach ($validatedCart as $productId => $quantity) {
                $purchaseRequest->items()->create([
                    'product_id' => $products->get((int) $productId)->id,
                    'requested_quantity' => $quantity,
                ]);
            }

            return [
                'purchaseRequest' => $purchaseRequest,
                'validatedCart' => $validatedCart,
            ];
        });

        $purchaseRequest = $result['purchaseRequest'];
        $validatedCart = $result['validatedCart'];

        $adminUsers = User::whereIn('email', $recipients->all())->get();

        $productCount = count($validatedCart);

        foreach ($adminUsers as $admin) {
            $admin->notify(new InventoryNotification(
                title: 'New Product Request',
                message: "{$purchaseRequest->customer_name} submitted request {$purchaseRequest->request_code} containing {$productCount} " . ($productCount === 1 ? 'product' : 'products') . '.',
                icon: 'shopping-cart',
                extra: [
                    'type' => 'new_product_request',
                    'request_id' => $purchaseRequest->id,
                    'request_code' => $purchaseRequest->request_code,
                    'customer_name' => $purchaseRequest->customer_name,
                    'customer_email' => $purchaseRequest->customer_email,
                    'product_count' => $productCount,
                    'submitted_at' => now()->toDateTimeString(),
                ],
            ));
        }

        $request->session()->forget($key);

        $request->session()->put([
            'last_request_code' => $purchaseRequest->request_code,
            'last_customer_email' => $purchaseRequest->customer_email,
        ]);

        return redirect()->route('inventory.catalog.requests', $token);
    }
}

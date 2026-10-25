<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\PurchaseRequest;
use Modules\Inventory\Models\QrCatalog;

class PublicCheckout extends Component
{
    public string $token;
    public string $customerName = '';
    public string $customerEmail = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        abort_unless(QrCatalog::where('token', $token)->exists(), 404);
    }

    public function submit(): void
    {
        $validated = $this->validate([
            'customerName' => ['required', 'string', 'max:255'],
            'customerEmail' => ['required', 'email', 'max:255'],
        ])->layout('livewire.layout.public');

        $cart = session()->get($this->cartKey(), []);
        if ($cart === []) {
            throw ValidationException::withMessages(['cart' => 'Select at least one product before checkout.']);
        }

        $request = DB::transaction(function () use ($validated, $cart) {
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
                $product = $products->get((int) $productId);
                $quantity = filter_var($quantity, FILTER_VALIDATE_INT);
                if (! $quantity || $quantity < 1) {
                    throw ValidationException::withMessages(['cart' => 'Every product quantity must be at least one.']);
                }

                $purchaseRequest->items()->create([
                    'product_id' => $product->id,
                    'requested_quantity' => $quantity,
                ]);
            }

            return $purchaseRequest;
        });

        session()->forget($this->cartKey());
        session()->flash('request_code', $request->request_code);
        $this->redirect(route('inventory.catalog.checkout', $this->token), navigate: true);
    }

    public function render()
    {
        return view('inventory::livewire.public.checkout', [
            'cartProducts' => Product::query()->whereIn('id', array_keys(session()->get($this->cartKey(), [])))->get(),
            'cart' => session()->get($this->cartKey(), []),
        ])->layout('livewire.layout.public');
    }

    private function cartKey(): string
    {
        return 'catalog_cart_' . $this->token;
    }
}

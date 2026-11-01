<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Inventory\Models\QrCatalog;

class PublicCatalogCart
{
    public function decrement(Request $request, string $token, int $product): RedirectResponse
    {
        abort_unless(QrCatalog::where('token', $token)->exists(), 404);

        $key = 'catalog_cart_' . $token;
        $cart = $request->session()->get($key, []);

        if (isset($cart[$product])) {
            $cart[$product]--;

            if ($cart[$product] < 1) {
                unset($cart[$product]);
            }
        }

        $request->session()->put($key, $cart);

        return redirect()->route('inventory.catalog', $token);
    }

    public function clear(Request $request, string $token): RedirectResponse
    {
        abort_unless(QrCatalog::where('token', $token)->exists(), 404);

        $request->session()->forget('catalog_cart_' . $token);

        return redirect()
            ->route('inventory.catalog', $token)
            ->with('cart_cleared', 'Your cart has been cleared successfully.');
    }
}

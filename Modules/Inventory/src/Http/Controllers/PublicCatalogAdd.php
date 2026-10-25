<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\QrCatalog;

class PublicCatalogAdd
{
    public function __invoke(string $token, int $product): RedirectResponse
    {
        abort_unless(QrCatalog::where('token', $token)->exists(), 404);
        abort_unless(Product::whereKey($product)->exists(), 404);

        $key = 'catalog_cart_' . $token;
        $cart = session()->get($key, []);
        $cart[$product] = min(2147483647, (int) ($cart[$product] ?? 0) + 1);
        session()->put($key, $cart);

        return redirect()->route('inventory.catalog', $token);
    }
}

<?php

namespace Modules\Inventory\Http\Controllers;

use Livewire\Component;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\QrCatalog;

class PublicCatalog extends Component
{
    public string $token;
    public array $cart = [];

    public function mount(string $token): void
    {
        $this->token = $token;
        abort_unless(QrCatalog::where('token', $token)->exists(), 404);
        $this->cart = session()->get($this->cartKey(), []);
    }

    public function add(int $productId): void
    {
        abort_unless(Product::whereKey($productId)->exists(), 404);
        $this->cart[$productId] = min(2147483647, (int) ($this->cart[$productId] ?? 0) + 1);
        session()->put($this->cartKey(), $this->cart);
    }

    public function remove(int $productId): void
    {
        unset($this->cart[$productId]);
        session()->put($this->cartKey(), $this->cart);
    }

    public function decrement(int $productId): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        $this->cart[$productId]--;
        if ($this->cart[$productId] < 1) {
            unset($this->cart[$productId]);
        }
        session()->put($this->cartKey(), $this->cart);
    }

    public function render()
    {
        return view('inventory::livewire.public.catalog', [
            'categories' => Product::query()
                ->with('category')
                ->orderBy('category_id')
                ->orderBy('name')
                ->get()
                ->groupBy(fn (Product $product) => $product->category?->name ?? 'Uncategorized'),
            'cartProducts' => Product::query()->whereIn('id', array_keys($this->cart))->get()->keyBy('id'),
            'cartCount' => array_sum($this->cart),
        ])->layout('livewire.layout.public');
    }

    private function cartKey(): string
    {
        return 'catalog_cart_' . $this->token;
    }
}

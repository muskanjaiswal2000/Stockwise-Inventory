<?php

namespace Modules\Inventory\Http\Controllers\Stock;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Services\StockMovementService;

class Adjustment extends Component
{
    public string $product_id = '';
    public string $actual_quantity = '';
    public string $reason = '';

    protected function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],
            'actual_quantity' => 'required|integer|min:0|max:2147483647',
            'reason' => 'nullable|string|max:255',
        ];
    }

    public function submit(StockMovementService $service): void
    {
        abort_unless(auth()->user()?->can('manage stock'), 403);

        $validated = $this->validate();

        $movement = $service->adjustToQuantity(
            productId: (int) $validated['product_id'],
            actualQuantity: (int) $validated['actual_quantity'],
            reason: $validated['reason'] ?: null,
        );

        session()->flash(
            'success',
            $movement
                ? 'Stock adjusted successfully. The difference was recorded in stock history.'
                : 'Stock already matches the counted quantity. No adjustment was needed.'
        );
        $this->reset(['product_id', 'actual_quantity', 'reason']);
    }

    public function render()
    {
        return view('inventory::livewire.stock.adjustment', [
            'products' => Product::query()->orderBy('name')->get(),
        ])->layout('livewire.layout.app');
    }
}

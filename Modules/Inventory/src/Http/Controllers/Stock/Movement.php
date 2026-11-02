<?php

namespace Modules\Inventory\Http\Controllers\Stock;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Livewire\Component;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\QrCatalog;
use Modules\Inventory\Models\PurchaseRequest;
use Modules\Inventory\Models\PurchaseRequestItem;
use Modules\Inventory\Services\StockMovementService;

class Movement extends Component
{
    public $product_id = '';
    public $type = 'in';
    public $quantity = 1;
    public $reason = '';
    public bool $showQr = false;
    public bool $qrGenerated = false;

    public function mount(Request $request): void
    {
        $productId = $request->integer('product_id');

        if ($productId > 0 && Product::whereKey($productId)->exists()) {
            $this->product_id = (string) $productId;
        }
    }

    protected function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],
            'type'       => 'required|in:in,out',
            'quantity'   => 'required|integer|min:1|max:2147483647',
            'reason'     => 'nullable|string|max:255',
        ];
    }

    public function submit(StockMovementService $service): void
    {
        abort_unless(auth()->user()?->can('manage stock'), 403);

        $validated = $this->validate();

        if ($validated['type'] === 'in') {
            $service->stockIn(
                productId: (int) $validated['product_id'],
                quantity: (int) $validated['quantity'],
                reason: $validated['reason'] ?: null,
            );
            $message = 'Stock In successful!';
        } else {
            $service->stockOut(
                productId: (int) $validated['product_id'],
                quantity: (int) $validated['quantity'],
                reason: $validated['reason'] ?: null,
            );
            $message = 'Stock Out successful!';
        }

        session()->flash('success', $message);
        $this->reset(['product_id', 'quantity', 'reason']);
        $this->type = 'in';
    }

    public function generateQr(): void
    {
        abort_unless(auth()->user()?->can('manage stock'), 403);

        $catalog = QrCatalog::query()->whereNull('category_id')->first();

        if (!$catalog) {
            $catalog = QrCatalog::create([
                'category_id' => null,
                'token' => (string) Str::uuid(),
                'generated_by' => auth()->id(),
            ]);
        }

        $this->qrGenerated = true;
        $this->showQr = true;
    }

    public function processReturn(int $itemId, StockMovementService $service): void
    {
        abort_unless(auth()->user()?->can('manage stock'), 403);

        $item = PurchaseRequestItem::findOrFail($itemId);

        $quantity = $item->issued_quantity - $item->returned_quantity;

        if ($quantity < 1) {
            session()->flash('error', 'Nothing left to return.');
            return;
        }

        $service->processReturn($item, $quantity);

        session()->flash('success', 'Product returned successfully.');
    }

    public function assignRequest(int $requestId, StockMovementService $service): void
    {
        abort_unless(auth()->user()?->can('manage stock'), 403);

        $service->assignRequest(PurchaseRequest::findOrFail($requestId));

        session()->flash('success', 'Product assigned successfully.');
    }

    public function render()
    {
        $catalog = QrCatalog::query()->whereNull('category_id')->first();
        $catalogUrl = $catalog ? route('inventory.catalog', $catalog->token) : null;
        $qrImageUrl = $catalog ? route('inventory.catalog.qr', $catalog->token) : null;

        if ($catalog && !$this->qrGenerated) {
            $this->qrGenerated = true;
        }

        return view('inventory::livewire.stock.movement', [
            'products' => Product::orderBy('name')->get(),
            'productCount' => Product::count(),
            'totalQuantity' => Product::sum('quantity'),
            'purchaseRequests' => PurchaseRequest::query()
                ->with(['items.product', 'items.returns', 'assignee', 'submittedBy'])
                ->latest()
                ->take(25)
                ->get(),
            'catalogUrl' => $catalogUrl,
            'qrImageUrl' => $qrImageUrl,
            'qrGenerated' => $this->qrGenerated,
        ])->layout('livewire.layout.app');
    }
}

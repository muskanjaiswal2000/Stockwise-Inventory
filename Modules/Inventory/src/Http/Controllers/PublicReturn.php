<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Inventory\Models\PurchaseRequest;
use Modules\Inventory\Models\QrCatalog;
use Modules\Inventory\Services\StockMovementService;

class PublicReturn
{
    public function __invoke(Request $request, string $token, StockMovementService $service): View|RedirectResponse
    {
        abort_unless(QrCatalog::where('token', $token)->exists(), 404);

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'request_code' => ['required', 'string', 'max:50'],
                'customer_email' => ['required', 'email', 'max:255'],
                'purchase_request_item_id' => ['required', 'integer'],
                'quantity' => ['required', 'integer', 'min:1'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ]);

            $purchaseRequest = PurchaseRequest::query()
                ->where('request_code', $validated['request_code'])
                ->where('customer_email', $validated['customer_email'])
                ->firstOrFail();
            $item = $purchaseRequest->items()->findOrFail($validated['purchase_request_item_id']);
            $service->submitReturn($item, (int) $validated['quantity'], $validated['notes'] ?? null);

            return redirect()->route('inventory.catalog.return', $token)
                ->with('success', 'Return request submitted. Staff will inspect it before stock is updated.')
                ->withInput(['request_code' => $validated['request_code'], 'customer_email' => $validated['customer_email']]);
        }

        $purchaseRequest = null;
        if ($request->filled('request_code') && $request->filled('customer_email')) {
            $purchaseRequest = PurchaseRequest::query()
                ->with('items.product')
                ->where('request_code', $request->string('request_code'))
                ->where('customer_email', $request->string('customer_email'))
                ->first();
        }

        return view('livewire.layout.public', [
            'slot' => view('inventory::public.return', [
                'token' => $token,
                'purchaseRequest' => $purchaseRequest,
            ]),
        ]);
    }
}

<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Inventory\Models\PurchaseRequest;
use Modules\Inventory\Models\QrCatalog;

class PublicRequestHistory
{
    public function __invoke(Request $request, string $token): View
    {
        abort_unless(QrCatalog::where('token', $token)->exists(), 404);
        $purchaseRequest = null;
        $requestCode = $request->query('request_code', session('last_request_code'));
        $customerEmail = $request->query('customer_email', session('last_customer_email'));
        if ($requestCode && $customerEmail) {
            $purchaseRequest = PurchaseRequest::query()->with(['items.product', 'assignee'])->where('request_code', $request->string('request_code'))->where('customer_email', $request->string('customer_email'))->first();
            if (! $purchaseRequest) {
                $purchaseRequest = PurchaseRequest::query()->with(['items.product', 'assignee'])->where('request_code', $requestCode)->where('customer_email', $customerEmail)->first();
            }
        }

        return view('livewire.layout.public', ['slot' => view('inventory::public.request-history', compact('token', 'purchaseRequest'))]);
    }
}

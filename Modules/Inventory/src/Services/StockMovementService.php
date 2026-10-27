<?php

namespace Modules\Inventory\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Modules\Inventory\Events\LowStockDetected;
use Modules\Inventory\Events\StockMovementRecorded;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\PurchaseRequest;
use Modules\Inventory\Models\PurchaseRequestItem;
use Modules\Inventory\Models\PurchaseRequestReturn;
use Modules\Inventory\Models\StockMovement;

class StockMovementService
{
    /**
     * Perform a stock-in operation.
     */
    private const MAX_STOCK_QUANTITY = 2_147_483_647;

    public function stockIn(int $productId, int $quantity, ?string $reason = null, ?int $userId = null): StockMovement
    {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be greater than zero.',
            ]);
        }

        $movement = DB::transaction(function () use ($productId, $quantity, $reason, $userId) {
            $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

            if ($quantity > self::MAX_STOCK_QUANTITY - $product->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'This stock increase exceeds the maximum supported quantity.',
                ]);
            }

            $product->quantity += $quantity;
            $product->save();

            return StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $userId ?? auth()->id(),
                'type' => 'in',
                'quantity' => $quantity,
                'reason' => $reason,
            ]);
        });

        $this->notifyAboutMovement($movement);
        return $movement;
    }

    /**
     * Perform a stock-out operation.
     */
    public function stockOut(int $productId, int $quantity, ?string $reason = null, ?int $userId = null): StockMovement
    {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be greater than zero.',
            ]);
        }

        $movement = DB::transaction(function () use ($productId, $quantity, $reason, $userId) {
            $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

            if ($product->quantity < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Insufficient stock. Available: {$product->quantity}",
                ]);
            }

            $product->quantity -= $quantity;
            $product->save();

            return StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $userId ?? auth()->id(),
                'type' => 'out',
                'quantity' => $quantity,
                'reason' => $reason,
            ]);
        });

        $this->notifyAboutMovement($movement);
        return $movement;
    }

    /**
     * Adjust the stock to a specific quantity.
     */

    public function adjustToQuantity(int $productId, int $actualQuantity, ?string $reason = null, ?int $userId = null): ?StockMovement
    {
        if ($actualQuantity < 0 || $actualQuantity > self::MAX_STOCK_QUANTITY) {
            throw ValidationException::withMessages([
                'actual_quantity' => 'The counted quantity must be between 0 and 2,147,483,647.',
            ]);
        }

        $movement = DB::transaction(function () use ($productId, $actualQuantity, $reason, $userId) {
            $product = Product::whereKey($productId)->lockForUpdate()->firstOrFail();
            $difference = $actualQuantity - $product->quantity;

            if ($difference === 0) {
                return null;
            }

            $product->quantity = $actualQuantity;
            $product->save();

            $direction = $difference > 0 ? 'in' : 'out';
            $adjustmentReason = "Manual adjustment to {$actualQuantity} units";

            if ($reason !== null && trim($reason) !== '') {
                $adjustmentReason .= ': ' . trim($reason);
            }
            $adjustmentReason = Str::limit($adjustmentReason, 255, '');

            return StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $userId ?? auth()->id(),
                'type' => $direction,
                'quantity' => abs($difference),
                'reason' => $adjustmentReason,
                'is_adjustment' => true,
            ]);
        });

        if ($movement !== null) {
            $this->notifyAboutMovement($movement);
        }

        return $movement;
    }

    public function approveRequest(PurchaseRequest $request, ?int $assignedTo, ?int $userId = null): PurchaseRequest
    {
        return DB::transaction(function () use ($request, $assignedTo, $userId) {
            $request = PurchaseRequest::query()->lockForUpdate()->findOrFail($request->id);
            if ($request->status !== 'requested') {
                throw ValidationException::withMessages(['request' => 'Only requested purchases can be approved.']);
            }

            $request->items()->update(['approved_quantity' => DB::raw('requested_quantity')]);
            $request->update([
                'assigned_to' => $assignedTo,
                'approved_by' => $userId ?? auth()->id(),
                'approved_at' => now(),
                'status' => 'approved',
            ]);

            return $request->fresh(['items.product', 'assignee']);
        });
    }

    // public function rejectRequest(PurchaseRequest $request, ?string $reason = null): PurchaseRequest
    // {
    //     return DB::transaction(function () use ($request, $reason) {
    //         $request = PurchaseRequest::query()->lockForUpdate()->findOrFail($request->id);
    //         if (! in_array($request->status, ['requested', 'approved'], true)) {
    //             throw ValidationException::withMessages(['request' => 'This request can no longer be rejected.']);
    //         }

    //         $request->update([
    //             'status' => 'rejected',
    //             'notes' => $reason ?: $request->notes,
    //             'rejected_at' => now(),
    //         ]);

    //         return $request;
    //     });
    // }

    public function issueRequest(PurchaseRequest $request, ?int $userId = null): PurchaseRequest
    {
        $movements = DB::transaction(function () use ($request, $userId) {
            $request = PurchaseRequest::query()->lockForUpdate()->findOrFail($request->id);
            if ($request->status !== 'approved') {
                throw ValidationException::withMessages(['request' => 'Only approved requests can be issued.']);
            }

            $movements = [];
            foreach ($request->items()->lockForUpdate()->get() as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item->product_id);
                $quantity = $item->approved_quantity;
                if ($quantity < 1 || $product->quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'request' => "Insufficient stock for {$product->name}.",
                    ]);
                }

                $product->decrement('quantity', $quantity);
                $item->update(['issued_quantity' => $quantity]);
                $movements[] = StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $userId ?? auth()->id(),
                    'type' => 'out',
                    'quantity' => $quantity,
                    'reason' => "Issued for purchase request {$request->request_code}",
                ]);
            }

            $request->update(['status' => 'issued']);
            return $movements;
        });

        foreach ($movements as $movement) {
            $this->notifyAboutMovement($movement);
        }

        return $request->fresh(['items.product', 'assignee']);
    }


    public function assignRequest(PurchaseRequest $request, ?int $userId = null): PurchaseRequest
    {
        $movements = DB::transaction(function () use ($request, $userId) {
            $request = PurchaseRequest::query()->lockForUpdate()->findOrFail($request->id);

            if ($request->status !== 'requested') {
                throw ValidationException::withMessages([
                    'request' => 'Only requested items can be assigned.',
                ]);
            }

            $movements = [];

            foreach ($request->items()->lockForUpdate()->get() as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item->product_id);
                $quantity = $item->requested_quantity;

                if ($quantity < 1 || $product->quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'request' => "Insufficient stock for {$product->name}.",
                    ]);
                }

                $product->decrement('quantity', $quantity);

                $item->update([
                    'issued_quantity' => $quantity,
                    'approved_quantity' => $quantity,
                ]);

                // Create stock movement record
                $movements[] = StockMovement::create([
                    'product_id' => $product->id,
                    'user_id'    => $userId ?? auth()->id(),
                    'type'       => 'out',
                    'quantity'   => $quantity,
                    'reason'     => "Assigned for request {$request->request_code}",
                ]);
            }

            // Update request status
            $request->update([
                'status'      => 'assigned',
                'assigned_to' => $userId ?? auth()->id(),
                'approved_by' => $userId ?? auth()->id(),
                'approved_at' => now(),
            ]);

            return $movements;
        });

        foreach ($movements as $movement) {
            $this->notifyAboutMovement($movement);
        }

        return $request->fresh(['items.product', 'assignee']);
    }


    public function processReturn(
        PurchaseRequestItem $item,
        int $quantity,
        string $condition,
        ?string $notes = null,
        ?int $userId = null,
    ): PurchaseRequestReturn {
        if ($quantity < 1 || ! in_array($condition, ['usable', 'damaged'], true)) {
            throw ValidationException::withMessages(['return' => 'Enter a valid return quantity and condition.']);
        }

        $movement = null;
        $return = DB::transaction(function () use ($item, $quantity, $condition, $notes, $userId, &$movement) {
            $item = PurchaseRequestItem::query()->lockForUpdate()->findOrFail($item->id);
            $available = $item->issued_quantity - $item->returned_quantity;
            // if ($item->request()->value('status') !== 'issued' || $quantity > $available) {
            //     throw ValidationException::withMessages(['return' => 'The return quantity exceeds the issued quantity.']);
            // }

            if (!in_array($item->request()->value('status'), ['issued', 'assigned'], true) || $quantity > $available) {
                throw ValidationException::withMessages(['return' => 'The return quantity exceeds the issued quantity.']);
            }

            if ($condition === 'usable') {
                $product = Product::query()->lockForUpdate()->findOrFail($item->product_id);
                if ($quantity > self::MAX_STOCK_QUANTITY - $product->quantity) {
                    throw ValidationException::withMessages(['return' => 'The returned quantity exceeds stock capacity.']);
                }
                $product->increment('quantity', $quantity);
                $movement = StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $userId ?? auth()->id(),
                    'type' => 'in',
                    'quantity' => $quantity,
                    'reason' => "Usable return for purchase request {$item->request->request_code}",
                ]);
            }

            $item->increment('returned_quantity', $quantity);
            $return = PurchaseRequestReturn::create([
                'purchase_request_item_id' => $item->id,
                'processed_by' => $userId ?? auth()->id(),
                'quantity' => $quantity,
                'condition' => $condition,
                'status' => $condition === 'usable' ? 'accepted' : 'rejected',
                'notes' => $notes,
                'processed_at' => now(),
            ]);

            if ($item->request->items()->whereColumn('returned_quantity', '<', 'issued_quantity')->doesntExist()) {
                $item->request->update(['status' => 'completed']);
            }

            return $return;
        });

        if ($movement !== null) {
            $this->notifyAboutMovement($movement);
        }

        return $return;
    }

    public function submitReturn(PurchaseRequestItem $item, int $quantity, ?string $notes = null): PurchaseRequestReturn
    {
        if ($quantity < 1) {
            throw ValidationException::withMessages(['return' => 'Enter a valid return quantity.']);
        }

        return DB::transaction(function () use ($item, $quantity, $notes) {
            $item = PurchaseRequestItem::query()->lockForUpdate()->findOrFail($item->id);
            $available = $item->issued_quantity - $item->returned_quantity - $item->returns()->where('status', 'pending')->sum('quantity');
            if ($item->request()->value('status') !== 'issued' || $quantity > $available) {
                throw ValidationException::withMessages(['return' => 'The return quantity exceeds the remaining issued quantity.']);
            }

            return PurchaseRequestReturn::create([
                'purchase_request_item_id' => $item->id,
                'processed_by' => null,
                'quantity' => $quantity,
                'condition' => 'pending',
                'status' => 'pending',
                'notes' => $notes,
                'processed_at' => null,
            ]);
        });
    }

    public function reviewReturn(
        PurchaseRequestReturn $return,
        string $condition,
        ?string $notes = null,
        ?int $userId = null,
    ): PurchaseRequestReturn {
        if (! in_array($condition, ['usable', 'damaged'], true)) {
            throw ValidationException::withMessages(['return' => 'Choose whether the returned item is usable or damaged.']);
        }

        $movement = null;
        $return = DB::transaction(function () use ($return, $condition, $notes, $userId, &$movement) {
            $return = PurchaseRequestReturn::query()->lockForUpdate()->findOrFail($return->id);
            if ($return->status !== 'pending') {
                throw ValidationException::withMessages(['return' => 'This return has already been reviewed.']);
            }

            $item = PurchaseRequestItem::query()->lockForUpdate()->findOrFail($return->purchase_request_item_id);
            if ($item->request()->value('status') !== 'issued') {
                throw ValidationException::withMessages(['return' => 'This request is not awaiting a return.']);
            }

            if ($condition === 'usable') {
                $product = Product::query()->lockForUpdate()->findOrFail($item->product_id);
                if ($return->quantity > self::MAX_STOCK_QUANTITY - $product->quantity) {
                    throw ValidationException::withMessages(['return' => 'The returned quantity exceeds stock capacity.']);
                }
                $product->increment('quantity', $return->quantity);
                $movement = StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $userId ?? auth()->id(),
                    'type' => 'in',
                    'quantity' => $return->quantity,
                    'reason' => "Usable return for purchase request {$item->request->request_code}",
                ]);
            }

            $item->increment('returned_quantity', $return->quantity);
            $return->update([
                'processed_by' => $userId ?? auth()->id(),
                'condition' => $condition,
                'status' => $condition === 'usable' ? 'accepted' : 'rejected',
                'notes' => $notes,
                'processed_at' => now(),
            ]);

            if ($item->request->items()->whereColumn('returned_quantity', '<', 'issued_quantity')->doesntExist()) {
                $item->request->update(['status' => 'completed']);
            }

            return $return;
        });

        if ($movement !== null) {
            $this->notifyAboutMovement($movement);
        }

        return $return;
    }

    private function notifyAboutMovement(StockMovement $movement): void
    {
        $movement->load(['product', 'user']);

        event(new StockMovementRecorded($movement));

        if ($movement->product && $movement->product->quantity <= $movement->product->reorder_level) {
            event(new LowStockDetected($movement->product, $movement));
        }
    }
}

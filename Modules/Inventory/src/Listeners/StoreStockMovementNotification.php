<?php

namespace Modules\Inventory\Listeners;

use App\Models\User;
use Modules\Inventory\Events\StockMovementRecorded;
use Modules\Inventory\Notifications\InventoryNotification;
use Modules\Inventory\Services\NotificationRecipients;

class StoreStockMovementNotification
{
    public function handle(StockMovementRecorded $event): void
    {
        $emails = app(NotificationRecipients::class)->all();

        $users = User::query()
            ->whereIn('email', $emails)
            ->get();

        $movement = $event->movement;
        $product = $movement->product;

        if (! $product) {
            return;
        }

        if ($movement->is_adjustment) {
            $title = 'Stock Adjusted';
            $message = "Stock for {$product->name} was adjusted by {$movement->quantity} units.";
            $type = 'stock_adjustment';
            $icon = 'sliders-horizontal';
        } elseif ($movement->type === 'in') {
            $title = 'Stock Added';
            $message = "{$movement->quantity} units were added to {$product->name}.";
            $type = 'stock_in';
            $icon = 'package-plus';
        } else {
            $title = 'Stock Removed';
            $message = "{$movement->quantity} units were removed from {$product->name}.";
            $type = 'stock_out';
            $icon = 'package-minus';
        }

        foreach ($users as $user) {
            $user->notify(new InventoryNotification(
                title: $title,
                message: $message,
                icon: $icon,
                extra: [
                    'product_id' => $product->id,
                    'movement_id' => $movement->id,
                    'type' => $type,
                ],
            ));
        }
    }
}

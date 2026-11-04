<?php

namespace Modules\Inventory\Listeners;

use App\Models\User;
use Modules\Inventory\Events\LowStockDetected;
use Modules\Inventory\Notifications\InventoryNotification;
use Modules\Inventory\Services\NotificationRecipients;

class StoreLowStockNotification
{
    public function handle(LowStockDetected $event): void
    {
        $emails = app(NotificationRecipients::class)->all();

        $users = User::query()
            ->whereIn('email', $emails)
            ->get();

        $product = $event->product;

        foreach ($users as $user) {
            $user->notify(new InventoryNotification(
                title: 'Low Stock Alert',
                message: "{$product->name} has only {$product->quantity} items remaining.",
                icon: 'alert-triangle',
                extra: [
                    'product_id' => $product->id,
                    'type' => 'low_stock',
                ],
            ));
        }
    }
}

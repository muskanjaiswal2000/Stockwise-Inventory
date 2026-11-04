<?php

namespace Modules\Inventory\Listeners;

use Modules\Inventory\Events\LowStockDetected;
use Modules\Inventory\Jobs\SendInventoryNotification;
use Modules\Inventory\Mail\LowStockAlertMail;
use Modules\Inventory\Services\NotificationRecipients;

class SendLowStockAlert
{
    public function handle(LowStockDetected $event): void
    {
        $product = (object) [
            'name' => $event->product->name,
            'sku' => $event->product->sku,
            'quantity' => $event->product->quantity,
            'unit' => $event->product->unit,
            'reorder_level' => $event->product->reorder_level,
        ];

        $movement = null;

        if ($event->movement) {
            $movement = (object) [
                'is_adjustment' => $event->movement->is_adjustment,
                'type' => $event->movement->type,
                'quantity' => $event->movement->quantity,
                'reason' => $event->movement->reason,
                'user' => (object) [
                    'name' => $event->movement->user?->name,
                ],
            ];
        }

        $recipients = app(NotificationRecipients::class);

        foreach ($recipients->all() as $recipient) {
            SendInventoryNotification::dispatch(
                $recipient,
                new LowStockAlertMail($product, $movement),
            );
        }
    }
}
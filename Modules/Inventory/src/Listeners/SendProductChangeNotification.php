<?php

namespace Modules\Inventory\Listeners;

use Modules\Inventory\Events\ProductCreatedOrUpdated;
use Modules\Inventory\Jobs\SendInventoryNotification;
use Modules\Inventory\Mail\ProductChangeMail;
use Modules\Inventory\Services\NotificationRecipients;

class SendProductChangeNotification
{
    public function handle(ProductCreatedOrUpdated $event): void
    {
        $product = (object) [
            'name' => $event->product->name,
            'sku' => $event->product->sku,
            'quantity' => $event->product->quantity,
            'unit' => $event->product->unit,
            'selling_price' => $event->product->selling_price,
        ];

        $recipients = app(NotificationRecipients::class);

        foreach ($recipients->all() as $recipient) {
            SendInventoryNotification::dispatch(
                $recipient,
                new ProductChangeMail($product, $event->action),
            );
        }
    }
}
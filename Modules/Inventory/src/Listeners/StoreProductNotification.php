<?php

namespace Modules\Inventory\Listeners;

use App\Models\User;
use Modules\Inventory\Events\ProductCreatedOrUpdated;
use Modules\Inventory\Notifications\InventoryNotification;
use Modules\Inventory\Services\NotificationRecipients;

class StoreProductNotification
{
    public function handle(ProductCreatedOrUpdated $event): void
    {
        $emails = app(NotificationRecipients::class)->all();

        $users = User::query()
            ->whereIn('email', $emails)
            ->get();

        $product = $event->product;

        $isCreated = str_contains(
            strtolower($event->action),
            'creat'
        );

        $title = $isCreated ? 'Product Created' : 'Product Updated';

        $message = $isCreated
            ? "New product {$product->name} was added."
            : "Product {$product->name} was updated.";

        foreach ($users as $user) {
            $user->notify(new InventoryNotification(
                title: $title,
                message: $message,
                icon: 'package',
                extra: [
                    'product_id' => $product->id,
                    'type' => $isCreated ? 'product_created' : 'product_updated',
                ],
            ));
        }
    }
}

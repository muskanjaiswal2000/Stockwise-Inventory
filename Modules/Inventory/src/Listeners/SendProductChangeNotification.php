<?php

namespace Modules\Inventory\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Inventory\Events\ProductCreatedOrUpdated;
use Modules\Inventory\Jobs\SendInventoryNotification;
use Modules\Inventory\Mail\ProductChangeMail;
use Modules\Inventory\Services\NotificationRecipients;

class SendProductChangeNotification implements ShouldQueue
{
    public string $queue;

    public function __construct()
    {
        $this->queue = config('inventory.notifications.queue', 'notifications');
    }

    public function handle(ProductCreatedOrUpdated $event): void
    {
        $recipients = app(NotificationRecipients::class);

        foreach ($recipients->all() as $recipient) {
            SendInventoryNotification::dispatch(
                $recipient,
                new ProductChangeMail($event->product, $event->action),
            );
        }
    }
}

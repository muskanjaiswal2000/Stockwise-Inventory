<?php

namespace Modules\Inventory\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Inventory\Events\StockMovementRecorded;
use Modules\Inventory\Jobs\SendInventoryNotification;
use Modules\Inventory\Mail\StockMovementMail;
use Modules\Inventory\Services\NotificationRecipients;

class SendStockMovementNotification implements ShouldQueue
{
    public string $queue;

    public function __construct()
    {
        $this->queue = config('inventory.notifications.queue', 'notifications');
    }

    public function handle(StockMovementRecorded $event): void
    {
        $recipients = app(NotificationRecipients::class);

        foreach ($recipients->all() as $recipient) {
            SendInventoryNotification::dispatch(
                $recipient,
                new StockMovementMail($event->movement),
            );
        }
    }
}

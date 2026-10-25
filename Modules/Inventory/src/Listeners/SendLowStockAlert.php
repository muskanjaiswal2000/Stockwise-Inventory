<?php

namespace Modules\Inventory\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Inventory\Events\LowStockDetected;
use Modules\Inventory\Jobs\SendInventoryNotification;
use Modules\Inventory\Mail\LowStockAlertMail;
use Modules\Inventory\Services\NotificationRecipients;

class SendLowStockAlert implements ShouldQueue
{
    public string $queue;

    public function __construct()
    {
        $this->queue = config('inventory.notifications.queue', 'notifications');
    }

    public function handle(LowStockDetected $event): void
    {
        $recipients = app(NotificationRecipients::class);

        foreach ($recipients->all() as $recipient) {
            SendInventoryNotification::dispatch(
                $recipient,
                new LowStockAlertMail($event->product, $event->movement),
            );
        }
    }
}

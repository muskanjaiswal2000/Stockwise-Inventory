<?php

namespace Modules\Inventory\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InventoryNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $icon = 'bell',
        public array $extra = [],
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return array_merge([
            'title' => $this->title,
            'message' => $this->message,
            'icon' => $this->icon,
        ], $this->extra);
    }
}

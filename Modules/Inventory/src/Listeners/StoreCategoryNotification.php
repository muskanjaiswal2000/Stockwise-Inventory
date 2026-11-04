<?php

namespace Modules\Inventory\Listeners;

use App\Models\User;
use Modules\Inventory\Events\CategoryCreated;
use Modules\Inventory\Notifications\InventoryNotification;
use Modules\Inventory\Services\NotificationRecipients;

class StoreCategoryNotification
{
    public function __construct(
        protected NotificationRecipients $recipients
    ) {}

    public function handle(CategoryCreated $event): void
    {
        $adminUsers = User::whereIn(
            'email',
            $this->recipients->all()
        )->get();

        foreach ($adminUsers as $admin) {
            $admin->notify(new InventoryNotification(
                title: 'New Category Created',
                message: "A new category '{$event->category->name}' has been created.",
                icon: 'folder-plus',
                extra: [
                    'type' => 'category_created',
                    'category_id' => $event->category->id,
                    'category_name' => $event->category->name,
                ]
            ));
        }
    }
}

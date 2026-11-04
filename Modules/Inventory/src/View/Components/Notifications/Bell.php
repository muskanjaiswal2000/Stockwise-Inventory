<?php

namespace Modules\Inventory\View\Components\Notifications;

use Illuminate\View\Component;
use Illuminate\View\View;

class Bell extends Component
{
    public $notifications;

    public int $unreadCount;

    public function __construct()
    {
        $user = auth()->user();

        $this->notifications = $user
            ? $user->notifications()->latest()->take(10)->get()
            : collect();

        $this->unreadCount = $user
            ? $user->unreadNotifications()->count()
            : 0;
    }

    public function render(): View|string
    {
        return view('inventory::components.notifications.bell');
    }
}
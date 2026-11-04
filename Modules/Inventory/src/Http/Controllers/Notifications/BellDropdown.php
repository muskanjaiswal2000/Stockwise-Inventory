<?php

namespace Modules\Inventory\Http\Controllers\Notifications;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BellDropdown extends Component
{
    public bool $open = false;

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = Auth::user()
            ->notifications()
            ->whereKey($notificationId)
            ->first();

        if ($notification && ! $notification->read_at) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        Auth::user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        $user = Auth::user();

        return view('inventory::livewire.notifications.bell-dropdown', [
            'notifications' => $user->notifications()
                ->latest()
                ->take(10)
                ->get(),

            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }
}
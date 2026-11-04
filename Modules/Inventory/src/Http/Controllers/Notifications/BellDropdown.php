<?php

namespace Modules\Inventory\Http\Controllers\Notifications;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BellDropdown extends Component
{
    public bool $open = false;

    public int $lastUnreadCount = 0;

    public function mount(): void
    {
        $this->lastUnreadCount = Auth::user()
            ->unreadNotifications()
            ->count();
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function checkForNewNotifications(): void
    {
        $currentCount = Auth::user()
            ->unreadNotifications()
            ->count();

        if ($currentCount > $this->lastUnreadCount) {
            $this->dispatch('new-notification');

            logger()->info('New notification event dispatched', [
                'previous_count' => $this->lastUnreadCount,
                'current_count' => $currentCount,
            ]);
        }

        $this->lastUnreadCount = $currentCount;
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
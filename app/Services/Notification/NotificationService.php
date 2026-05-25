<?php

namespace App\Services\Notification;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a notification to a user via all configured channels.
     */
    public function send(User $user, Notification $notification): void
    {
        try {
            $user->notify($notification);
        } catch (\Throwable $e) {
            Log::warning("NotificationService: failed to notify user #{$user->id}: {$e->getMessage()}");
        }
    }

    /**
     * Send a notification to multiple users.
     */
    public function sendToMany(iterable $users, Notification $notification): void
    {
        foreach ($users as $user) {
            $this->send($user, $notification);
        }
    }

    /**
     * Get unread notification count for a user.
     */
    public function unreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllRead(User $user): void
    {
        $user->unreadNotifications()->update(['read_at' => now()]);
    }

    /**
     * Get paginated notifications for a user.
     */
    public function paginate(User $user, int $perPage = 15)
    {
        return $user->notifications()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}

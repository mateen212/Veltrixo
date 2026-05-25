<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\SubscriptionPaused;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendSubscriptionPausedNotification implements ShouldQueue
{
    public string $queue = 'notifications';

    public function handle(SubscriptionPaused $event): void
    {
        $subscription = $event->subscription;
        $user         = $subscription->user;

        if (!$user) {
            return;
        }

        try {
            $message = 'Your subscription has been paused.';
            if ($subscription->pause_until) {
                $message .= ' It will automatically resume on ' . \Carbon\Carbon::parse($subscription->pause_until)->format('M j, Y') . '.';
            }

            $user->notify(
                \Illuminate\Support\Facades\Notification::route('mail', $user->email)
            );

            // Database notification
            $user->notifications()->create([
                'id'              => \Illuminate\Support\Str::uuid(),
                'type'            => 'subscription_paused',
                'data'            => json_encode([
                    'subscription_id' => $subscription->id,
                    'message'         => $message,
                    'pause_until'     => $subscription->pause_until,
                ]),
                'read_at'         => null,
            ]);
        } catch (\Throwable $e) {
            Log::warning("SubscriptionPausedNotification failed for subscription #{$subscription->id}: {$e->getMessage()}");
        }
    }
}

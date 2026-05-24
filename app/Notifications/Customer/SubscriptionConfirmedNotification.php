<?php

namespace App\Notifications\Customer;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Subscription Confirmed!')
            ->greeting("Hello {$notifiable->name},")
            ->line('Your subscription has been successfully created.')
            ->line("Frequency: {$this->subscription->frequency}")
            ->line("Starting: {$this->subscription->starts_at->format('M d, Y')}")
            ->action('View Subscription', url('/subscriptions'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'            => 'subscription_confirmed',
            'subscription_id' => $this->subscription->id,
            'message'         => 'Your subscription has been confirmed.',
        ];
    }
}

<?php

namespace App\Notifications\Customer;

use App\Models\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DeliveryCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Delivery $delivery) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your delivery has been completed!')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your delivery scheduled for {$this->delivery->delivery_date->format('M d, Y')} has been completed.")
            ->line('Thank you for your continued trust!')
            ->action('View Delivery', url('/dashboard'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'delivery_completed',
            'delivery_id'   => $this->delivery->id,
            'delivery_date' => $this->delivery->delivery_date->toDateString(),
            'message'       => 'Your delivery has been completed.',
        ];
    }
}

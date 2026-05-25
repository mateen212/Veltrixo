<?php

namespace App\Notifications\Customer;

use App\Models\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliveryMissedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Delivery $delivery) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your delivery could not be completed')
            ->greeting("Hello {$notifiable->name},")
            ->line("Unfortunately, your delivery scheduled for {$this->delivery->delivery_date} could not be completed.")
            ->line("Reason: " . ($this->delivery->missed_reason ?? 'Unable to reach address'))
            ->line("Our support team will contact you to reschedule.")
            ->action('View Delivery', url("/customer/deliveries/{$this->delivery->id}"))
            ->line('We apologise for the inconvenience.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'delivery_missed',
            'delivery_id' => $this->delivery->id,
            'date'        => $this->delivery->delivery_date,
            'reason'      => $this->delivery->missed_reason,
            'message'     => "Delivery on {$this->delivery->delivery_date} was missed.",
        ];
    }
}

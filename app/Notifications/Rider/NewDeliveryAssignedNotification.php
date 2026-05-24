<?php

namespace App\Notifications\Rider;

use App\Models\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewDeliveryAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Delivery $delivery) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'delivery_assigned',
            'delivery_id'   => $this->delivery->id,
            'delivery_date' => $this->delivery->delivery_date->toDateString(),
            'address'       => $this->delivery->address?->full_address,
            'message'       => 'You have been assigned a new delivery.',
        ];
    }
}

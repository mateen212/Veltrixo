<?php

namespace App\Notifications\Business;

use App\Models\Tenant;
use App\Support\TenantUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Tenant $tenant,
        public readonly string $reason,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Business Registration Update — ' . $this->tenant->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Unfortunately your business registration for **' . $this->tenant->name . '** was not approved.')
            ->line('**Reason:** ' . $this->reason)
            ->line('If you believe this is an error or would like to address the concerns, please contact our support team.')
            ->action('Contact Support', url('/support'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'business_rejected',
            'tenant_id'   => $this->tenant->id,
            'tenant_name' => $this->tenant->name,
            'reason'      => $this->reason,
            'message'     => 'Your business registration was not approved: ' . $this->reason,
        ];
    }
}

<?php

namespace App\Notifications\Business;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessSignupReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Tenant $tenant) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Business Registration Received — ' . $this->tenant->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We have received your business registration for **' . $this->tenant->name . '**.')
            ->line('Our team will review your application and notify you within 24–48 hours.')
            ->line('You will receive an email when your business is approved and ready to go live.')
            ->salutation('The Veltrixo Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'business_signup_received',
            'tenant_id'   => $this->tenant->id,
            'tenant_name' => $this->tenant->name,
            'message'     => 'Your business registration is under review.',
        ];
    }
}

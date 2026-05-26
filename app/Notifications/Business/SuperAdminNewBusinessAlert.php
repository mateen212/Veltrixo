<?php

namespace App\Notifications\Business;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuperAdminNewBusinessAlert extends Notification implements ShouldQueue
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
            ->subject('New Business Signup — ' . $this->tenant->name)
            ->line('A new business has signed up and is pending verification.')
            ->line('**Business:** ' . $this->tenant->name)
            ->line('**Owner:** ' . $this->tenant->owner?->name . ' (' . $this->tenant->email . ')')
            ->action('Review Business', url('/super-admin/business-verifications'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'new_business_signup',
            'tenant_id'   => $this->tenant->id,
            'tenant_name' => $this->tenant->name,
            'message'     => 'New business signup: ' . $this->tenant->name . ' — pending review.',
        ];
    }
}

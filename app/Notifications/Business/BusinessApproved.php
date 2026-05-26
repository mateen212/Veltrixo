<?php

namespace App\Notifications\Business;

use App\Models\Tenant;
use App\Models\TenantPlan;
use App\Support\TenantUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Tenant $tenant,
        public readonly TenantPlan $plan,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Your Business is Approved — ' . $this->tenant->name)
            ->greeting('Congratulations, ' . $notifiable->name . '!')
            ->line('Your business **' . $this->tenant->name . '** has been approved.')
            ->line('You are now on the **' . $this->plan->name . '** plan with a 30-day free trial.')
            ->action('Go to Dashboard', TenantUrl::to('/admin/dashboard', $this->tenant))
            ->line('Start setting up your products, riders, and delivery schedule!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'business_approved',
            'tenant_id'   => $this->tenant->id,
            'tenant_name' => $this->tenant->name,
            'plan_name'   => $this->plan->name,
            'message'     => 'Your business has been approved! You are on the ' . $this->plan->name . ' plan.',
        ];
    }
}

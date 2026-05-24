<?php

namespace App\Notifications\Customer;

use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LowBalanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Wallet $wallet) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Wallet Balance Alert')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your wallet balance is low: **{$this->wallet->balance}**")
            ->line('Please recharge to continue uninterrupted deliveries.')
            ->action('Recharge Wallet', url('/wallet/recharge'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'low_balance',
            'balance' => $this->wallet->balance,
            'message' => 'Your wallet balance is low.',
        ];
    }
}

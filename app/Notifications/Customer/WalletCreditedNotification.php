<?php

namespace App\Notifications\Customer;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletCreditedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Wallet            $wallet,
        public readonly WalletTransaction $transaction
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount  = number_format($this->transaction->amount, 2);
        $balance = number_format($this->wallet->balance, 2);

        return (new MailMessage)
            ->subject("Wallet Credited — Rs {$amount}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your wallet has been credited with **Rs {$amount}**.")
            ->line("{$this->transaction->description}")
            ->line("Your new balance is **Rs {$balance}**.")
            ->action('View Wallet', url('/customer/wallet'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'            => 'wallet_credited',
            'amount'          => $this->transaction->amount,
            'balance'         => $this->wallet->balance,
            'description'     => $this->transaction->description,
            'transaction_id'  => $this->transaction->id,
            'message'         => "Rs " . number_format($this->transaction->amount, 2) . " credited to your wallet.",
        ];
    }
}

<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class NotificationSettings extends Settings
{
    public bool $email_notifications_enabled;
    public bool $sms_notifications_enabled;
    public bool $push_notifications_enabled;
    public bool $delivery_reminder_enabled;
    public int $delivery_reminder_hours_before;
    public bool $payment_receipt_enabled;
    public bool $low_balance_alert_enabled;
    public bool $subscription_renewal_reminder_enabled;
    public int $subscription_renewal_reminder_days_before;

    public static function group(): string
    {
        return 'notification';
    }
}

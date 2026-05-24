<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class DeliverySettings extends Settings
{
    public string $default_delivery_time;
    public int $max_skip_days_per_month;
    public bool $allow_delivery_reschedule;
    public bool $otp_verification_required;
    public bool $proof_image_required;
    public int $advance_booking_days;
    public bool $allow_partial_delivery;
    public bool $auto_assign_riders;

    public static function group(): string
    {
        return 'delivery';
    }
}

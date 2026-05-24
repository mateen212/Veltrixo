<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;
    public string $site_tagline;
    public string $support_email;
    public string $support_phone;
    public string $currency;
    public string $currency_symbol;
    public string $timezone;
    public string $locale;
    public bool $maintenance_mode;
    public bool $user_registration_enabled;
    public bool $referral_program_enabled;

    public static function group(): string
    {
        return 'general';
    }
}

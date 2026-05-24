<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class WalletSettings extends Settings
{
    public bool $wallet_enabled;
    public float $minimum_recharge_amount;
    public float $maximum_recharge_amount;
    public float $low_balance_threshold;
    public bool $auto_deduct_on_delivery;
    public bool $allow_credit_limit;
    public float $default_credit_limit;
    public bool $loyalty_points_enabled;
    public int $loyalty_points_per_order;
    public float $loyalty_points_value;

    public static function group(): string
    {
        return 'wallet';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TenantPlan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price_monthly', 'price_yearly',
        'max_customers', 'max_riders', 'max_products', 'max_orders_per_month',
        'features', 'is_active', 'is_public', 'sort_order',
    ];

    protected $casts = [
        'price_monthly'         => 'decimal:2',
        'price_yearly'          => 'decimal:2',
        'features'              => 'array',
        'is_active'             => 'boolean',
        'is_public'             => 'boolean',
    ];

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TenantSubscription::class, 'plan_id');
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }
}

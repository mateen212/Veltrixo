<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'tenant_id', 'user_id', 'address_id', 'frequency',
        'delivery_days', 'preferred_delivery_time', 'starts_at', 'ends_at',
        'next_delivery_date', 'status', 'paused_at', 'pause_until',
        'cancelled_at', 'cancellation_reason', 'total_amount', 'notes', 'meta',
    ];

    protected $casts = [
        'delivery_days'  => 'array',
        'starts_at'      => 'date',
        'ends_at'        => 'date',
        'next_delivery_date' => 'date',
        'pause_until'    => 'date',
        'paused_at'      => 'datetime',
        'cancelled_at'   => 'datetime',
        'total_amount'   => 'decimal:2',
        'meta'           => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->uuid ??= \Str::uuid()->toString();
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    public function activeItems(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class)->where('is_active', true);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function skips(): HasMany
    {
        return $this->hasMany(SubscriptionSkip::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isSkippedOn(\Carbon\Carbon $date): bool
    {
        return $this->skips()->where('skip_date', $date->toDateString())->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}

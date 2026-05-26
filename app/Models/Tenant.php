<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'domain', 'subdomain', 'email', 'phone',
        'logo', 'favicon', 'branding', 'address', 'currency',
        'currency_symbol', 'timezone', 'locale', 'status',
        'trial_ends_at', 'features', 'limits', 'meta', 'owner_id',
        // geo + verification
        'latitude', 'longitude', 'delivery_radius_km',
        'verification_status', 'verified_at', 'verified_by', 'rejection_reason',
    ];

    protected $casts = [
        'branding'             => 'array',
        'address'              => 'array',
        'features'             => 'array',
        'limits'               => 'array',
        'meta'                 => 'array',
        'trial_ends_at'        => 'datetime',
        'verified_at'          => 'datetime',
        'latitude'             => 'decimal:7',
        'longitude'            => 'decimal:7',
        'delivery_radius_km'   => 'integer',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function riders(): HasMany
    {
        return $this->hasMany(Rider::class);
    }

    public function tenantSubscription(): HasOne
    {
        return $this->hasOne(TenantSubscription::class)->latestOfMany();
    }

    public function verifiedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->verification_status === 'approved';
    }

    /** Generate a URL on this tenant's subdomain */
    public function url(string $path = ''): string
    {
        return \App\Support\TenantUrl::to($path, $this);
    }

    public function isPendingVerification(): bool
    {
        return $this->verification_status === 'pending_verification';
    }

    public function isApproved(): bool
    {
        return $this->verification_status === 'approved';
    }

    /** Haversine distance in km from tenant location to a given lat/lng */
    public function distanceTo(float $lat, float $lng): float
    {
        if (!$this->latitude || !$this->longitude) {
            return 0.0;
        }
        $earthRadius = 6371;
        $dLat = deg2rad($lat - (float) $this->latitude);
        $dLon = deg2rad($lng - (float) $this->longitude);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad((float) $this->latitude)) * cos(deg2rad($lat)) * sin($dLon / 2) ** 2;
        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public function isWithinRadius(float $lat, float $lng): bool
    {
        return $this->distanceTo($lat, $lng) <= ($this->delivery_radius_km ?: 5);
    }

    public function isOnTrial(): bool
    {
        return $this->status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    public function getLimit(string $key, int $default = 0): int
    {
        return $this->limits[$key] ?? $default;
    }
}

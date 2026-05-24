<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Delivery extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'uuid', 'tenant_id', 'subscription_id', 'user_id', 'rider_id',
        'route_id', 'address_id', 'invoice_id', 'delivery_date',
        'scheduled_time', 'assigned_at', 'started_at', 'arrived_at',
        'delivered_at', 'status', 'delivery_otp', 'qr_code_token',
        'otp_verified', 'proof_image', 'delivery_latitude', 'delivery_longitude',
        'rider_notes', 'customer_notes', 'missed_reason', 'attempt_number',
        'total_amount', 'is_paid', 'meta',
    ];

    protected $casts = [
        'delivery_date'       => 'date',
        'assigned_at'         => 'datetime',
        'started_at'          => 'datetime',
        'arrived_at'          => 'datetime',
        'delivered_at'        => 'datetime',
        'otp_verified'        => 'boolean',
        'delivery_latitude'   => 'float',
        'delivery_longitude'  => 'float',
        'total_amount'        => 'decimal:2',
        'is_paid'             => 'boolean',
        'meta'                => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(fn (self $m) => $m->uuid ??= \Str::uuid()->toString());
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function subscription(): BelongsTo { return $this->belongsTo(Subscription::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function rider(): BelongsTo { return $this->belongsTo(Rider::class); }
    public function route(): BelongsTo { return $this->belongsTo(Route::class); }
    public function address(): BelongsTo { return $this->belongsTo(Address::class); }
    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function items(): HasMany { return $this->hasMany(DeliveryItem::class); }

    public function isDelivered(): bool { return $this->status === 'delivered'; }
    public function isMissed(): bool { return $this->status === 'missed'; }
    public function isAssigned(): bool { return $this->status === 'assigned'; }
    public function isPending(): bool { return in_array($this->status, ['scheduled', 'pending']); }

    public function scopeForDate($query, string $date)
    {
        return $query->where('delivery_date', $date);
    }

    public function scopeForRider($query, int $riderId)
    {
        return $query->where('rider_id', $riderId);
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('proof')->singleFile();
    }
}

<?php

namespace App\Models;
use App\Traits\HasTenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WalletRechargeRequest extends Model implements HasMedia
{
    use HasTenant;
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'uuid', 'tenant_id', 'user_id', 'wallet_id', 'amount',
        'payment_method', 'payment_reference', 'receipt_image',
        'status', 'reviewed_by', 'reviewed_at', 'rejection_reason', 'notes', 'meta',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'reviewed_at' => 'datetime',
        'meta'        => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(fn (self $m) => $m->uuid ??= \Str::uuid()->toString());
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function wallet(): BelongsTo { return $this->belongsTo(Wallet::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function isPending(): bool { return $this->status === 'pending'; }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('receipt')->singleFile();
    }
}

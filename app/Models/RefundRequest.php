<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'tenant_id', 'user_id', 'delivery_id', 'invoice_id',
        'requested_amount', 'approved_amount', 'reason', 'description',
        'evidence_images', 'status', 'reviewed_by', 'reviewed_at', 'admin_notes',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'approved_amount'  => 'decimal:2',
        'evidence_images'  => 'array',
        'reviewed_at'      => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(fn (self $m) => $m->uuid ??= \Str::uuid()->toString());
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function delivery(): BelongsTo { return $this->belongsTo(Delivery::class); }
    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function isPending(): bool { return $this->status === 'pending'; }
}

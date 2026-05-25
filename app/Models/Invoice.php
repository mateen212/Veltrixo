<?php

namespace App\Models;
use App\Traits\HasTenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasTenant;
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'tenant_id', 'user_id', 'subscription_id', 'invoice_number',
        'subtotal', 'tax_amount', 'discount_amount', 'total', 'paid_amount',
        'due_amount', 'status', 'payment_method', 'issue_date', 'due_date',
        'paid_at', 'notes', 'meta',
    ];

    protected $casts = [
        'subtotal'          => 'decimal:2',
        'tax_amount'        => 'decimal:2',
        'discount_amount'   => 'decimal:2',
        'total'             => 'decimal:2',
        'paid_amount'       => 'decimal:2',
        'due_amount'        => 'decimal:2',
        'issue_date'        => 'date',
        'due_date'          => 'date',
        'paid_at'           => 'datetime',
        'meta'              => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(fn (self $m) => $m->uuid ??= \Str::uuid()->toString());
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function subscription(): BelongsTo { return $this->belongsTo(Subscription::class); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class); }
    public function deliveries(): HasMany { return $this->hasMany(Delivery::class); }

    public function isPaid(): bool { return $this->status === 'paid'; }
    public function isOverdue(): bool { return $this->status === 'overdue'; }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}

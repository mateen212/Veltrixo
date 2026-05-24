<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $fillable = [
        'uuid', 'wallet_id', 'tenant_id', 'user_id', 'type', 'category',
        'amount', 'balance_before', 'balance_after', 'description',
        'reference_type', 'reference_id', 'status', 'meta',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after'  => 'decimal:2',
        'meta'           => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->uuid ??= \Str::uuid()->toString();
        });
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function reference()
    {
        return $this->morphTo('reference');
    }

    public function isCredit(): bool
    {
        return $this->type === 'credit';
    }

    public function isDebit(): bool
    {
        return $this->type === 'debit';
    }
}

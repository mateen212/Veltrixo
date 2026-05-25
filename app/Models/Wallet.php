<?php

namespace App\Models;
use App\Traits\HasTenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasTenant;
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'user_id', 'balance', 'total_credited', 'total_debited',
        'credit_limit', 'status', 'low_balance_threshold', 'low_balance_notified',
    ];

    protected $casts = [
        'balance'               => 'decimal:2',
        'total_credited'        => 'decimal:2',
        'total_debited'         => 'decimal:2',
        'credit_limit'          => 'decimal:2',
        'low_balance_threshold' => 'decimal:2',
        'low_balance_notified'  => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function rechargeRequests(): HasMany
    {
        return $this->hasMany(WalletRechargeRequest::class);
    }

    public function hasSufficientBalance(float $amount): bool
    {
        return (float) $this->balance >= $amount;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isBelowThreshold(): bool
    {
        return $this->low_balance_threshold > 0 && $this->balance <= $this->low_balance_threshold;
    }
}

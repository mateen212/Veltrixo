<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $fillable = [
        'tenant_id', 'referrer_id', 'referred_id',
        'referrer_bonus', 'referred_bonus', 'status', 'rewarded_at',
    ];

    protected $casts = [
        'referrer_bonus' => 'decimal:2',
        'referred_bonus' => 'decimal:2',
        'rewarded_at'    => 'datetime',
    ];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function referrer(): BelongsTo { return $this->belongsTo(User::class, 'referrer_id'); }
    public function referred(): BelongsTo { return $this->belongsTo(User::class, 'referred_id'); }
}

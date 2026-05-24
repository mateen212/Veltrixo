<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantSubscription extends Model
{
    protected $fillable = [
        'tenant_id', 'plan_id', 'billing_cycle', 'status',
        'starts_at', 'ends_at', 'trial_ends_at', 'cancelled_at', 'amount', 'meta',
    ];

    protected $casts = [
        'starts_at'     => 'datetime',
        'ends_at'       => 'datetime',
        'trial_ends_at' => 'datetime',
        'cancelled_at'  => 'datetime',
        'amount'        => 'decimal:2',
        'meta'          => 'array',
    ];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function plan(): BelongsTo { return $this->belongsTo(TenantPlan::class, 'plan_id'); }

    public function isActive(): bool { return $this->status === 'active'; }
}

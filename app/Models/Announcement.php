<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'tenant_id', 'created_by', 'title', 'message', 'type',
        'target_roles', 'is_active', 'publish_at', 'expires_at',
    ];

    protected $casts = [
        'target_roles' => 'array',
        'is_active'    => 'boolean',
        'publish_at'   => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function isPublished(): bool
    {
        return $this->is_active && (!$this->publish_at || $this->publish_at->isPast())
            && (!$this->expires_at || $this->expires_at->isFuture());
    }
}

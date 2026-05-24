<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceSession extends Model
{
    protected $fillable = [
        'user_id', 'session_token', 'device_name', 'device_type',
        'platform', 'browser', 'ip_address', 'is_current',
        'last_activity_at', 'expires_at',
    ];

    protected $casts = [
        'is_current'       => 'boolean',
        'last_activity_at' => 'datetime',
        'expires_at'       => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}

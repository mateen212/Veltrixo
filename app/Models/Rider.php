<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rider extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'user_id', 'employee_id', 'vehicle_type', 'vehicle_number',
        'license_number', 'base_salary', 'per_delivery_rate', 'total_earnings',
        'total_deliveries', 'successful_deliveries', 'rating',
        'current_latitude', 'current_longitude', 'location_updated_at',
        'availability_status', 'status', 'documents', 'working_hours',
    ];

    protected $casts = [
        'base_salary'           => 'decimal:2',
        'per_delivery_rate'     => 'decimal:2',
        'total_earnings'        => 'decimal:2',
        'rating'                => 'decimal:2',
        'current_latitude'      => 'float',
        'current_longitude'     => 'float',
        'location_updated_at'   => 'datetime',
        'documents'             => 'array',
        'working_hours'         => 'array',
    ];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function deliveries(): HasMany { return $this->hasMany(Delivery::class); }
    public function routes(): HasMany { return $this->hasMany(Route::class); }

    public function isAvailable(): bool { return $this->availability_status === 'available'; }
    public function getSuccessRateAttribute(): float
    {
        if ($this->total_deliveries === 0) return 0;
        return round(($this->successful_deliveries / $this->total_deliveries) * 100, 1);
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability_status', 'available')->where('status', 'active');
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}

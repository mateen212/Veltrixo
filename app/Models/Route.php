<?php

namespace App\Models;
use App\Traits\HasTenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    use HasTenant;
    use HasFactory;

    protected $table = 'routes';

    protected $fillable = [
        'tenant_id', 'name', 'code', 'description', 'rider_id',
        'area_polygon', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'area_polygon' => 'array',
        'is_active'    => 'boolean',
    ];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function rider(): BelongsTo { return $this->belongsTo(Rider::class); }
    public function stops(): HasMany { return $this->hasMany(RouteStop::class)->orderBy('sort_order'); }
    public function deliveries(): HasMany { return $this->hasMany(Delivery::class); }
}

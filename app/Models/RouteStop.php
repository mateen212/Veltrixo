<?php

namespace App\Models;
use App\Traits\HasTenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteStop extends Model
{
    use HasTenant;
    protected $fillable = [
        'route_id', 'address_id', 'label', 'latitude', 'longitude',
        'sort_order', 'estimated_minutes',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public function route(): BelongsTo { return $this->belongsTo(Route::class); }
    public function address(): BelongsTo { return $this->belongsTo(Address::class); }
}

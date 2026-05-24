<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'label', 'contact_name', 'contact_phone', 'line1', 'line2',
        'city', 'state', 'postal_code', 'country', 'latitude', 'longitude',
        'delivery_instructions', 'is_default', 'is_active',
    ];

    protected $casts = [
        'latitude'   => 'float',
        'longitude'  => 'float',
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->line1, $this->line2, $this->city, $this->state, $this->postal_code, $this->country,
        ]));
    }
}

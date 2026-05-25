<?php

namespace App\Models;
use App\Traits\HasTenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryItem extends Model
{
    use HasTenant;
    protected $fillable = [
        'delivery_id', 'product_id', 'variant_id', 'product_name',
        'quantity', 'unit_price', 'subtotal', 'is_delivered',
    ];

    protected $casts = [
        'unit_price'   => 'decimal:2',
        'subtotal'     => 'decimal:2',
        'is_delivered' => 'boolean',
    ];

    public function delivery(): BelongsTo { return $this->belongsTo(Delivery::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function variant(): BelongsTo { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
}

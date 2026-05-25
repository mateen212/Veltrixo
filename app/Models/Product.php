<?php

namespace App\Models;
use App\Traits\HasTenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasTenant;
    use HasFactory, SoftDeletes, Searchable, InteractsWithMedia;

    protected $fillable = [
        'tenant_id', 'category_id', 'name', 'slug', 'description', 'sku',
        'unit', 'price', 'sale_price', 'cost_price', 'stock_quantity',
        'min_stock_alert', 'track_inventory', 'is_active',
        'is_subscription_product', 'is_featured', 'availability_schedule',
        'meta', 'sort_order',
    ];

    protected $casts = [
        'price'                   => 'decimal:2',
        'sale_price'              => 'decimal:2',
        'cost_price'              => 'decimal:2',
        'track_inventory'         => 'boolean',
        'is_active'               => 'boolean',
        'is_subscription_product' => 'boolean',
        'is_featured'             => 'boolean',
        'availability_schedule'   => 'array',
        'meta'                    => 'array',
    ];

    public function toSearchableArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'description' => $this->description, 'sku' => $this->sku];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function subscriptionItems(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    public function isInStock(): bool
    {
        return !$this->track_inventory || $this->stock_quantity > 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
        $this->addMediaCollection('thumbnail')->singleFile();
    }
}

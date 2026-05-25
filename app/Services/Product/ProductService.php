<?php

namespace App\Services\Product;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Get all active products for a tenant, grouped by category.
     */
    public function catalogForTenant(int $tenantId): Collection
    {
        return Product::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->with(['category', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Create a new product with optional variants.
     */
    public function create(array $data, int $tenantId): Product
    {
        $product = Product::create([
            'tenant_id'      => $tenantId,
            'category_id'    => $data['category_id'],
            'name'           => $data['name'],
            'slug'           => Str::slug($data['name']) . '-' . Str::random(4),
            'description'    => $data['description'] ?? null,
            'sku'            => $data['sku'],
            'unit'           => $data['unit'],
            'price'          => $data['price'],
            'stock_quantity' => $data['stock_quantity'] ?? null,
            'is_active'      => $data['is_active'] ?? true,
            'sort_order'     => $data['sort_order'] ?? 0,
        ]);

        if (!empty($data['variants'])) {
            foreach ($data['variants'] as $variant) {
                $product->variants()->create([
                    'name'      => $variant['name'],
                    'sku'       => $variant['sku'] ?? $product->sku . '-' . Str::random(3),
                    'price'     => $variant['price'],
                    'unit'      => $variant['unit'] ?? $product->unit,
                    'is_active' => $variant['is_active'] ?? true,
                ]);
            }
        }

        return $product->load(['category', 'variants']);
    }

    /**
     * Update a product.
     */
    public function update(Product $product, array $data): Product
    {
        $product->update(array_filter($data, fn ($v) => $v !== null));
        return $product->refresh()->load(['category', 'variants']);
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(Product $product): Product
    {
        $product->update(['is_active' => !$product->is_active]);
        return $product->refresh();
    }

    /**
     * Check if a product is in stock.
     */
    public function isInStock(Product $product, int $quantity = 1): bool
    {
        if ($product->stock_quantity === null) {
            return true; // Unlimited stock
        }
        return $product->stock_quantity >= $quantity;
    }

    /**
     * Decrement stock after a delivery is created.
     */
    public function decrementStock(int $productId, int $quantity): void
    {
        Product::where('id', $productId)
            ->whereNotNull('stock_quantity')
            ->decrement('stock_quantity', $quantity);
    }
}

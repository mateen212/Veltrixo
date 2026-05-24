<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'name'                    => $this->name,
            'slug'                    => $this->slug,
            'description'             => $this->description,
            'sku'                     => $this->sku,
            'unit'                    => $this->unit,
            'price'                   => (float) $this->price,
            'sale_price'              => $this->sale_price ? (float) $this->sale_price : null,
            'effective_price'         => (float) $this->effective_price,
            'is_active'               => $this->is_active,
            'is_subscription_product' => $this->is_subscription_product,
            'in_stock'                => $this->isInStock(),
            'category'                => new CategoryResource($this->whenLoaded('category')),
            'variants'                => ProductVariantResource::collection($this->whenLoaded('variants')),
            'thumbnail'               => $this->getFirstMediaUrl('thumbnail'),
            'images'                  => $this->getMedia('images')->map->getUrl(),
        ];
    }
}

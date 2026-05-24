<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'sku'             => $this->sku,
            'price'           => (float) $this->price,
            'sale_price'      => $this->sale_price ? (float) $this->sale_price : null,
            'effective_price' => (float) $this->effective_price,
            'stock_quantity'  => $this->stock_quantity,
            'is_active'       => $this->is_active,
            'attributes'      => $this->attributes,
        ];
    }
}

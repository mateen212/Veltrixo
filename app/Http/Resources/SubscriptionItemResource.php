<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'quantity'   => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'subtotal'   => (float) $this->subtotal,
            'is_active'  => $this->is_active,
            'product'    => new ProductResource($this->whenLoaded('product')),
        ];
    }
}

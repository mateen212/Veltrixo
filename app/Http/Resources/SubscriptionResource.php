<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'uuid'                    => $this->uuid,
            'frequency'               => $this->frequency,
            'delivery_days'           => $this->delivery_days,
            'preferred_delivery_time' => $this->preferred_delivery_time,
            'starts_at'               => $this->starts_at?->toDateString(),
            'ends_at'                 => $this->ends_at?->toDateString(),
            'next_delivery_date'      => $this->next_delivery_date?->toDateString(),
            'status'                  => $this->status,
            'total_amount'            => (float) $this->total_amount,
            'notes'                   => $this->notes,
            'created_at'              => $this->created_at?->toIso8601String(),
            'items'                   => SubscriptionItemResource::collection($this->whenLoaded('items')),
            'address'                 => new AddressResource($this->whenLoaded('address')),
            'user'                    => new UserResource($this->whenLoaded('user')),
        ];
    }
}

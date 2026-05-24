<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'uuid'          => $this->uuid,
            'delivery_date' => $this->delivery_date?->toDateString(),
            'scheduled_time' => $this->scheduled_time,
            'status'        => $this->status,
            'total_amount'  => (float) $this->total_amount,
            'is_paid'       => $this->is_paid,
            'otp_verified'  => $this->otp_verified,
            'rider_notes'   => $this->rider_notes,
            'customer_notes' => $this->customer_notes,
            'delivered_at'  => $this->delivered_at?->toIso8601String(),
            'created_at'    => $this->created_at?->toIso8601String(),
            'user'          => new UserResource($this->whenLoaded('user')),
            'rider'         => new RiderResource($this->whenLoaded('rider')),
            'address'       => new AddressResource($this->whenLoaded('address')),
            'items'         => DeliveryItemResource::collection($this->whenLoaded('items')),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'label'                 => $this->label,
            'contact_name'          => $this->contact_name,
            'contact_phone'         => $this->contact_phone,
            'line1'                 => $this->line1,
            'line2'                 => $this->line2,
            'city'                  => $this->city,
            'state'                 => $this->state,
            'postal_code'           => $this->postal_code,
            'country'               => $this->country,
            'latitude'              => $this->latitude,
            'longitude'             => $this->longitude,
            'delivery_instructions' => $this->delivery_instructions,
            'is_default'            => $this->is_default,
            'full_address'          => $this->full_address,
        ];
    }
}

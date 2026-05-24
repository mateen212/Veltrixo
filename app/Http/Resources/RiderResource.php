<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'employee_id'         => $this->employee_id,
            'vehicle_type'        => $this->vehicle_type,
            'vehicle_number'      => $this->vehicle_number,
            'rating'              => (float) $this->rating,
            'availability_status' => $this->availability_status,
            'status'              => $this->status,
            'success_rate'        => $this->success_rate,
            'total_deliveries'    => $this->total_deliveries,
            'user'                => new UserResource($this->whenLoaded('user')),
        ];
    }
}

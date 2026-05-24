<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'balance'               => (float) $this->balance,
            'total_credited'        => (float) $this->total_credited,
            'total_debited'         => (float) $this->total_debited,
            'status'                => $this->status,
            'low_balance_threshold' => (float) $this->low_balance_threshold,
            'is_below_threshold'    => $this->isBelowThreshold(),
        ];
    }
}

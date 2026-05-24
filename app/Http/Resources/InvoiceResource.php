<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'uuid'           => $this->uuid,
            'invoice_number' => $this->invoice_number,
            'subtotal'       => (float) $this->subtotal,
            'tax_amount'     => (float) $this->tax_amount,
            'discount_amount' => (float) $this->discount_amount,
            'total'          => (float) $this->total,
            'paid_amount'    => (float) $this->paid_amount,
            'due_amount'     => (float) $this->due_amount,
            'status'         => $this->status,
            'payment_method' => $this->payment_method,
            'issue_date'     => $this->issue_date?->toDateString(),
            'due_date'       => $this->due_date?->toDateString(),
            'paid_at'        => $this->paid_at?->toIso8601String(),
            'created_at'     => $this->created_at?->toIso8601String(),
            'items'          => InvoiceItemResource::collection($this->whenLoaded('items')),
        ];
    }
}

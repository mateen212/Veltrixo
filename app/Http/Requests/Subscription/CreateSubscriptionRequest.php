<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'customer']);
    }

    public function rules(): array
    {
        return [
            'address_id'               => ['required', 'integer', 'exists:addresses,id'],
            'frequency'                => ['required', 'string', 'in:daily,weekly,biweekly,monthly,custom'],
            'delivery_days'            => ['nullable', 'array'],
            'delivery_days.*'          => ['integer', 'between:1,7'],
            'preferred_delivery_time'  => ['nullable', 'date_format:H:i'],
            'starts_at'                => ['required', 'date', 'after_or_equal:today'],
            'ends_at'                  => ['nullable', 'date', 'after:starts_at'],
            'items'                    => ['required', 'array', 'min:1'],
            'items.*.product_id'       => ['required', 'integer', 'exists:products,id'],
            'items.*.variant_id'       => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.quantity'         => ['required', 'integer', 'min:1'],
            'notes'                    => ['nullable', 'string', 'max:500'],
        ];
    }
}

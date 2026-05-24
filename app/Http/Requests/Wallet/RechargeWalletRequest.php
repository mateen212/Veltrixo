<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class RechargeWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'customer']);
    }

    public function rules(): array
    {
        return [
            'amount'            => ['required', 'numeric', 'min:1', 'max:100000'],
            'payment_method'    => ['required', 'string', 'in:bank_transfer,cash,upi,card'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'receipt_image'     => ['nullable', 'image', 'max:5120'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ];
    }
}

<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class BusinessRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // public route
    }

    public function rules(): array
    {
        return [
            'business_name'      => ['required', 'string', 'max:100'],
            'business_address'   => ['nullable', 'string', 'max:255'],
            'city'               => ['nullable', 'string', 'max:100'],
            'area'               => ['nullable', 'string', 'max:100'],
            'owner_name'         => ['required', 'string', 'max:100'],
            'owner_email'        => ['required', 'email', 'unique:users,email'],
            'owner_phone'        => ['required', 'string', 'max:20'],
            'password'           => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'latitude'           => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'          => ['nullable', 'numeric', 'between:-180,180'],
            'delivery_radius_km' => ['nullable', 'integer', 'min:1', 'max:200'],
            'selected_plan'      => ['nullable', 'string', 'exists:tenant_plans,slug'],
            'logo'               => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'owner_email.unique'    => 'An account with this email already exists.',
            'selected_plan.exists'  => 'The selected plan is not available.',
        ];
    }
}

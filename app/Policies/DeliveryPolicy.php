<?php

namespace App\Policies;

use App\Models\Delivery;
use App\Models\User;

class DeliveryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'rider', 'customer']);
    }

    public function view(User $user, Delivery $delivery): bool
    {
        if ($user->hasRole('admin')) return $user->tenant_id === $delivery->tenant_id;
        if ($user->hasRole('rider')) return $user->rider?->id === $delivery->rider_id;
        return $user->id === $delivery->user_id;
    }

    public function update(User $user, Delivery $delivery): bool
    {
        if ($user->hasRole('admin')) return $user->tenant_id === $delivery->tenant_id;
        if ($user->hasRole('rider')) return $user->rider?->id === $delivery->rider_id;
        return false;
    }

    public function complete(User $user, Delivery $delivery): bool
    {
        return $user->hasRole('rider') && $user->rider?->id === $delivery->rider_id
            || $user->hasRole('admin') && $user->tenant_id === $delivery->tenant_id;
    }
}

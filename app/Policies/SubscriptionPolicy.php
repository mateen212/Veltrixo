<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;

class SubscriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'customer']);
    }

    public function view(User $user, Subscription $subscription): bool
    {
        return $user->hasRole('admin') && $user->tenant_id === $subscription->tenant_id
            || $user->id === $subscription->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'customer']);
    }

    public function update(User $user, Subscription $subscription): bool
    {
        return ($user->hasRole('admin') && $user->tenant_id === $subscription->tenant_id)
            || $user->id === $subscription->user_id;
    }

    public function delete(User $user, Subscription $subscription): bool
    {
        return $user->hasRole('admin') && $user->tenant_id === $subscription->tenant_id;
    }
}

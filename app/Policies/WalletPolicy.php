<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wallet;

class WalletPolicy
{
    public function view(User $user, Wallet $wallet): bool
    {
        return $user->id === $wallet->user_id
            || ($user->hasRole('admin') && $user->tenant_id === $wallet->tenant_id);
    }

    public function credit(User $user, Wallet $wallet): bool
    {
        return $user->hasRole('admin') && $user->tenant_id === $wallet->tenant_id;
    }
}

<?php

namespace App\Policies;

use App\Models\User;

class RolePolicy
{
    public function isOwner(User $user): bool
    {
        return $user->role === 'owner';
    }

    public function isAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function isCashier(User $user): bool
    {
        return $user->role === 'cashier';
    }

    public function isKitchen(User $user): bool
    {
        return $user->role === 'kitchen';
    }
}

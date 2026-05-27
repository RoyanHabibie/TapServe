<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['owner', 'admin']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['owner', 'admin']);
    }

    public function update(User $user, Category $category): bool
    {
        return $user->shop_id === $category->shop_id && in_array($user->role, ['owner', 'admin']);
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->shop_id === $category->shop_id && in_array($user->role, ['owner', 'admin']);
    }
}

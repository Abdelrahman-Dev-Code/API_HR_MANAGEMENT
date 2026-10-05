<?php

namespace App\Policies;

use App\Models\User;

abstract class BaseResourcePolicy
{
    protected function hasPermission(User $user, string $permission): bool
    {
        return $user->is_active && $user->hasPermission($permission);
    }

    protected function canView(User $user, string $resource): bool
    {
        return $this->hasPermission($user, "{$resource}.view") || $this->hasPermission($user, "{$resource}.manage");
    }
}

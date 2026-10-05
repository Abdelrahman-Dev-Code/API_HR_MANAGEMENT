<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;

class PositionPolicy extends BaseResourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canView($user, 'positions');
    }

    public function view(User $user, Position $position): bool
    {
        return $this->canView($user, 'positions');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'positions.manage');
    }

    public function update(User $user, Position $position): bool
    {
        return $this->hasPermission($user, 'positions.manage');
    }

    public function delete(User $user, Position $position): bool
    {
        return $this->hasPermission($user, 'positions.manage');
    }
}

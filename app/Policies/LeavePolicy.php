<?php

namespace App\Policies;

use App\Models\Leave;
use App\Models\User;

class LeavePolicy extends BaseResourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canView($user, 'leaves');
    }

    public function view(User $user, Leave $leave): bool
    {
        return $this->canView($user, 'leaves');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'leaves.manage');
    }

    public function update(User $user, Leave $leave): bool
    {
        return $this->hasPermission($user, 'leaves.manage');
    }

    public function delete(User $user, Leave $leave): bool
    {
        return $this->hasPermission($user, 'leaves.manage');
    }

    public function decide(User $user, Leave $leave): bool
    {
        return $this->hasPermission($user, 'leaves.approve');
    }
}

<?php

namespace App\Policies;

use App\Models\LeaveBalance;
use App\Models\User;

class LeaveBalancePolicy extends BaseResourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canView($user, 'leave-balances');
    }

    public function view(User $user, LeaveBalance $leaveBalance): bool
    {
        return $this->canView($user, 'leave-balances');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'leave-balances.manage');
    }

    public function update(User $user, LeaveBalance $leaveBalance): bool
    {
        return $this->hasPermission($user, 'leave-balances.manage');
    }

    public function delete(User $user, LeaveBalance $leaveBalance): bool
    {
        return $this->hasPermission($user, 'leave-balances.manage');
    }
}

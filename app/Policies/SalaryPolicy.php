<?php

namespace App\Policies;

use App\Models\Salary;
use App\Models\User;

class SalaryPolicy extends BaseResourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canView($user, 'payroll');
    }

    public function view(User $user, Salary $salary): bool
    {
        return $this->canView($user, 'payroll');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'payroll.manage');
    }

    public function update(User $user, Salary $salary): bool
    {
        return $this->hasPermission($user, 'payroll.manage');
    }

    public function delete(User $user, Salary $salary): bool
    {
        return $this->hasPermission($user, 'payroll.manage');
    }

    public function decide(User $user, Salary $salary): bool
    {
        return $this->hasPermission($user, 'payroll.approve');
    }

    public function pay(User $user, Salary $salary): bool
    {
        return $this->hasPermission($user, 'payroll.pay');
    }
}

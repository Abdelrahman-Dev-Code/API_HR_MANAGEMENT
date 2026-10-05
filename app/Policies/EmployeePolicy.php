<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy extends BaseResourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canView($user, 'employees');
    }

    public function view(User $user, Employee $employee): bool
    {
        return $this->canView($user, 'employees');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'employees.manage');
    }

    public function update(User $user, Employee $employee): bool
    {
        return $this->hasPermission($user, 'employees.manage');
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $this->hasPermission($user, 'employees.manage');
    }
}

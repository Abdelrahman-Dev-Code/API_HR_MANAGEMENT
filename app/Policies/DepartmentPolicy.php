<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy extends BaseResourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canView($user, 'departments');
    }

    public function view(User $user, Department $department): bool
    {
        return $this->canView($user, 'departments');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'departments.manage');
    }

    public function update(User $user, Department $department): bool
    {
        return $this->hasPermission($user, 'departments.manage');
    }

    public function delete(User $user, Department $department): bool
    {
        return $this->hasPermission($user, 'departments.manage');
    }
}

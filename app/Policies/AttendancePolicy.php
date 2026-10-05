<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy extends BaseResourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canView($user, 'attendance');
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return $this->canView($user, 'attendance');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'attendance.manage');
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $this->hasPermission($user, 'attendance.manage');
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $this->hasPermission($user, 'attendance.manage');
    }
}

<?php

namespace App\Services\Authorization;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class DataVisibility
{
    public const SCOPED_RESOURCES = [
        'employees',
        'documents',
        'attendance',
        'leaves',
        'leave-balances',
        'payroll',
    ];

    public function canViewResource(User $user, string $resource): bool
    {
        if (! $user->is_active) {
            return false;
        }

        $hasPermission = $user->hasAnyPermission(["{$resource}.view", "{$resource}.manage"]);

        if ($this->hasActiveRole($user, 'department_manager')) {
            return $hasPermission;
        }

        return $hasPermission || (in_array($resource, self::SCOPED_RESOURCES, true) && $user->employee()->exists());
    }

    public function scope(Builder $query, User $user, string $resource): Builder
    {
        if (! $user->is_active) {
            return $query->whereRaw('1 = 0');
        }

        if ($this->hasActiveRole($user, 'hr_admin')) {
            return $query;
        }

        $isDepartmentManager = $this->hasActiveRole($user, 'department_manager');

        if (! $isDepartmentManager && $user->hasAnyPermission([
            "{$resource}.manage",
            "{$resource}.approve",
            "{$resource}.pay",
        ])) {
            return $query;
        }

        if ($isDepartmentManager && ! $this->hasAnyResourcePermission($user, $resource)) {
            return $query->whereRaw('1 = 0');
        }

        $employee = $user->employee()->first();

        if (! $employee) {
            return $query->whereRaw('1 = 0');
        }

        if ($isDepartmentManager && $employee->department_id !== null) {
            if ($resource === 'employees') {
                return $query->where('department_id', $employee->department_id);
            }

            return $query->whereHas('employee', fn (Builder $employeeQuery) => $employeeQuery
                ->where('department_id', $employee->department_id));
        }

        if ($resource === 'employees') {
            return $query->whereKey($employee->getKey());
        }

        return $query->where('employee_id', $employee->getKey());
    }

    public function assertEmployeeInScope(User $user, int $employeeId, string $resource): void
    {
        $isDepartmentManager = $this->hasActiveRole($user, 'department_manager');

        if (! $user->is_active || $this->hasActiveRole($user, 'hr_admin') || (! $isDepartmentManager && $user->hasPermission("{$resource}.manage"))) {
            abort_unless($user->is_active, 403);

            return;
        }

        $actor = $user->employee()->first();
        $target = Employee::query()->find($employeeId);

        abort_unless($actor && $target, 404);

        $isInScope = $isDepartmentManager
            ? $actor->department_id !== null && $target->department_id === $actor->department_id
            : $target->is($actor);

        abort_unless($isInScope, 404);
    }

    public function assertDepartmentInScope(User $user, int $departmentId, string $resource): void
    {
        $isDepartmentManager = $this->hasActiveRole($user, 'department_manager');

        if (! $user->is_active || $this->hasActiveRole($user, 'hr_admin') || (! $isDepartmentManager && $user->hasPermission("{$resource}.manage"))) {
            abort_unless($user->is_active, 403);

            return;
        }

        $employee = $user->employee()->first();

        abort_unless($employee && $employee->department_id === $departmentId, 404);
    }

    private function hasActiveRole(User $user, string $roleName): bool
    {
        return $user->roles()
            ->where('roles.role_name', $roleName)
            ->where('roles.is_active', true)
            ->exists();
    }

    private function hasAnyResourcePermission(User $user, string $resource): bool
    {
        return $user->hasAnyPermission([
            "{$resource}.view",
            "{$resource}.manage",
            "{$resource}.approve",
            "{$resource}.pay",
        ]);
    }
}

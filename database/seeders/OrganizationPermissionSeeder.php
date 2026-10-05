<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrganizationPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'employees.view' => ['View employees', 'employees'],
            'employees.manage' => ['Manage employees', 'employees'],
            'departments.view' => ['View departments', 'departments'],
            'departments.manage' => ['Manage departments', 'departments'],
            'positions.view' => ['View positions', 'positions'],
            'positions.manage' => ['Manage positions', 'positions'],
            'documents.view' => ['View employee documents', 'documents'],
            'documents.manage' => ['Manage employee documents', 'documents'],
            'holidays.view' => ['View holidays', 'holidays'],
            'holidays.manage' => ['Manage holidays', 'holidays'],
            'attendance.view' => ['View attendance', 'attendance'],
            'attendance.manage' => ['Manage attendance', 'attendance'],
            'leaves.view' => ['View leave requests', 'leaves'],
            'leaves.manage' => ['Manage leave requests', 'leaves'],
            'leaves.approve' => ['Approve leave requests', 'leaves'],
            'leave-balances.view' => ['View leave balances', 'leave-balances'],
            'leave-balances.manage' => ['Manage leave balances', 'leave-balances'],
            'payroll.view' => ['View payroll', 'payroll'],
            'payroll.manage' => ['Manage payroll', 'payroll'],
            'payroll.approve' => ['Approve payroll', 'payroll'],
            'payroll.pay' => ['Pay approved payroll', 'payroll'],
            'roles.manage' => ['Manage roles', 'authorization'],
            'permissions.manage' => ['Manage permissions', 'authorization'],
            'users.manage' => ['Manage user accounts', 'authorization'],
            'organization.manage' => ['Manage organization settings', 'organization'],
        ];

        $permissionIds = [];

        foreach ($permissions as $name => [$displayName, $group]) {
            $permissionIds[] = Permission::query()->firstOrCreate(
                ['permission_name' => $name],
                ['display_name' => $displayName, 'group' => $group],
            )->id;
        }

        $adminRole = Role::query()->where('role_name', 'hr_admin')->first();

        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching($permissionIds);

            $admin = User::query()->whereHas('roles', fn ($query) => $query->whereKey($adminRole->id))->first();
            $departmentId = Department::query()->value('id');
            $positionId = Position::query()->value('id');

            if ($admin && ! $admin->employee()->exists() && $departmentId && $positionId) {
                Employee::query()->firstOrCreate(
                    ['user_id' => $admin->id],
                    [
                        'department_id' => $departmentId,
                        'position_id' => $positionId,
                        'employee_code' => 'EMP-HR-ADMIN',
                        'name' => $admin->name,
                        'email' => $admin->email,
                        'work_shift' => 'day',
                    ],
                );
            }
        }

        $departmentManagerRole = Role::query()->firstOrCreate(
            ['role_name' => 'department_manager'],
            [
                'display_name' => 'Department Manager',
                'description' => 'Access limited to the manager employee department',
                'is_active' => true,
            ],
        );

        $departmentViewPermissionIds = Permission::query()
            ->whereIn('permission_name', [
                'employees.view',
                'documents.view',
                'attendance.view',
                'leaves.view',
                'leave-balances.view',
                'payroll.view',
            ])
            ->pluck('id');

        $departmentManagerRole->permissions()->syncWithoutDetaching($departmentViewPermissionIds);
    }
}

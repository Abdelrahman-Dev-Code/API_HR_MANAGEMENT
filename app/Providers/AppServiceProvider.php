<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Document;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\Position;
use App\Models\Salary;
use App\Models\User;
use App\Policies\AttendancePolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\LeaveBalancePolicy;
use App\Policies\LeavePolicy;
use App\Policies\PositionPolicy;
use App\Policies\SalaryPolicy;
use App\Repositories\Attendance\AttendanceRepository;
use App\Repositories\Attendance\AttendanceRepositoryInterface;
use App\Repositories\Department\DepartmentRepository;
use App\Repositories\Department\DepartmentRepositoryInterface;
use App\Repositories\Document\DocumentRepository;
use App\Repositories\Document\DocumentRepositoryInterface;
use App\Repositories\Employee\EmployeeRepository;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use App\Repositories\Holiday\HolidayRepository;
use App\Repositories\Holiday\HolidayRepositoryInterface;
use App\Repositories\Leave\LeaveRepository;
use App\Repositories\Leave\LeaveRepositoryInterface;
use App\Repositories\LeaveBalance\LeaveBalanceRepository;
use App\Repositories\LeaveBalance\LeaveBalanceRepositoryInterface;
use App\Repositories\Permission\PermissionRepository;
use App\Repositories\Permission\PermissionRepositoryInterface;
use App\Repositories\Position\PositionRepository;
use App\Repositories\Position\PositionRepositoryInterface;
use App\Repositories\Role\RoleRepository;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\Salary\SalaryRepository;
use App\Repositories\Salary\SalaryRepositoryInterface;
use App\Repositories\Setting\SettingRepository;
use App\Repositories\Setting\SettingRepositoryInterface;
use App\Services\Authorization\DataVisibility;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(AttendanceRepositoryInterface::class, AttendanceRepository::class);
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->bind(DocumentRepositoryInterface::class, DocumentRepository::class);
        $this->app->bind(HolidayRepositoryInterface::class, HolidayRepository::class);
        $this->app->bind(LeaveRepositoryInterface::class, LeaveRepository::class);
        $this->app->bind(LeaveBalanceRepositoryInterface::class, LeaveBalanceRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(PositionRepositoryInterface::class, PositionRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(SalaryRepositoryInterface::class, SalaryRepository::class);
        $this->app->bind(SettingRepositoryInterface::class, SettingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Position::class, PositionPolicy::class);
        Gate::policy(Document::class, DocumentPolicy::class);
        Gate::policy(Leave::class, LeavePolicy::class);
        Gate::policy(LeaveBalance::class, LeaveBalancePolicy::class);
        Gate::policy(Attendance::class, AttendancePolicy::class);
        Gate::policy(Salary::class, SalaryPolicy::class);

        foreach ([
            'employees.manage', 'departments.manage', 'positions.manage',
            'documents.manage', 'holidays.manage', 'attendance.manage',
            'leaves.manage', 'leaves.approve', 'leave-balances.manage',
            'payroll.manage', 'payroll.approve', 'payroll.pay',
            'roles.manage', 'permissions.manage', 'users.manage',
            'organization.manage',
        ] as $permission) {
            Gate::define($permission, fn (User $user): bool => $user->is_active
                && ($user->hasPermission($permission) || ($permission === 'users.manage' && $user->hasPermission('roles.manage'))));
        }

        foreach ([
            'employees', 'departments', 'positions', 'documents', 'holidays',
            'attendance', 'leaves', 'leave-balances', 'payroll',
        ] as $resource) {
            Gate::define("{$resource}.view", fn (User $user): bool => app(DataVisibility::class)
                ->canViewResource($user, $resource));
        }

        Gate::define('manage-organization', fn (User $user): bool => $user->can('organization.manage'));

        $this->loadMigrationsFrom([
            database_path('migrations/01-foundation'),
            database_path('migrations/02-authorization'),
            database_path('migrations/03-company-structure'),
            database_path('migrations/04-employee-records'),
            database_path('migrations/05-leave-management'),
            database_path('migrations/06-payroll-notifications'),
        ]);
    }
}

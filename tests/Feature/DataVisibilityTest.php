<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Salary;
use App\Models\User;
use Database\Seeders\OrganizationPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createVisibilityEmployee(Department $department): Employee
{
    return Employee::factory()->create(['department_id' => $department->id]);
}

function createVisibilitySalary(Employee $employee): Salary
{
    return Salary::query()->create([
        'employee_id' => $employee->id,
        'year' => 2026,
        'month' => 9,
    ]);
}

test('department managers can only view employees and payroll in their department', function () {
    $department = Department::factory()->create();
    $otherDepartment = Department::factory()->create();
    $managerEmployee = createVisibilityEmployee($department);
    $colleague = createVisibilityEmployee($department);
    $outsideEmployee = createVisibilityEmployee($otherDepartment);
    $manager = $managerEmployee->user;
    $managerRole = Role::factory()->create(['role_name' => 'department_manager']);
    $manager->roles()->attach($managerRole);
    $managerRole->permissions()->sync(collect(['employees.view', 'payroll.view', 'leaves.manage'])->map(fn (string $name) => Permission::query()->create([
        'permission_name' => $name,
        'display_name' => $name,
        'group' => 'test',
    ]))->pluck('id')->all());
    $colleagueSalary = createVisibilitySalary($colleague);
    $outsideSalary = createVisibilitySalary($outsideEmployee);

    $employeesResponse = $this->actingAs($manager, 'sanctum')->getJson('/api/employees');
    $employeesResponse->assertOk()->assertJsonCount(2, 'data');

    expect(collect($employeesResponse->json('data'))->pluck('id')->sort()->values()->all())
        ->toBe([$managerEmployee->id, $colleague->id]);

    $this->getJson('/api/employees?per_page=0')->assertJsonPath('meta.per_page', 1);
    $this->getJson('/api/employees?per_page=1000')->assertJsonPath('meta.per_page', 100);

    $this->getJson("/api/employees/{$outsideEmployee->id}")
        ->assertNotFound()
        ->assertJsonPath('message', 'المورد غير موجود أو خارج نطاق صلاحيتك.');

    $this->getJson('/api/salaries')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $colleagueSalary->id]);

    $this->getJson("/api/salaries/{$outsideSalary->id}")->assertNotFound();

    $this->getJson('/api/documents')->assertForbidden();

    $this->postJson('/api/leaves', [
        'employee_id' => $outsideEmployee->id,
        'leave_type' => 'earned',
        'start_date' => '2026-10-05',
        'end_date' => '2026-10-06',
    ])->assertNotFound();
});

test('employees can only view their own employee record and payroll', function () {
    $department = Department::factory()->create();
    $employee = createVisibilityEmployee($department);
    $colleague = createVisibilityEmployee($department);
    $salary = createVisibilitySalary($employee);
    $colleagueSalary = createVisibilitySalary($colleague);
    $user = User::query()->findOrFail($employee->user_id);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/employees')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $employee->id);

    $this->getJson("/api/employees/{$colleague->id}")->assertNotFound();

    $this->getJson('/api/salaries')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $salary->id]);
});

test('department managers cannot create or update records outside their department', function () {
    $department = Department::factory()->create();
    $otherDepartment = Department::factory()->create();
    $managerEmployee = createVisibilityEmployee($department);
    $outsideEmployee = createVisibilityEmployee($otherDepartment);
    $outsideSalary = createVisibilitySalary($outsideEmployee);
    $managerRole = Role::factory()->create(['role_name' => 'department_manager']);
    $permissions = collect(['employees.manage', 'payroll.manage'])->map(fn (string $name) => Permission::query()->create([
        'permission_name' => $name,
        'display_name' => $name,
        'group' => 'test',
    ]));
    $managerRole->permissions()->sync($permissions->pluck('id')->all());
    $managerEmployee->user->roles()->attach($managerRole);

    $this->actingAs($managerEmployee->user, 'sanctum')
        ->postJson('/api/salaries', [
            'employee_id' => $outsideEmployee->id,
            'year' => 2026,
            'month' => 10,
            'basic_amount' => 100,
        ])
        ->assertNotFound();

    $this->patchJson("/api/salaries/{$outsideSalary->id}", ['bonus' => 50])
        ->assertNotFound();

    $this->postJson('/api/employees', [
        'user_id' => User::factory()->create()->id,
        'department_id' => $otherDepartment->id,
        'position_id' => \App\Models\Position::factory()->create()->id,
        'employee_code' => 'EMP-OUTSIDE-1',
        'name' => 'Outside Employee',
        'email' => 'outside.employee@example.test',
    ])->assertNotFound();
});

test('sanctum tokens have a finite default lifetime', function () {
    expect(config('sanctum.expiration'))->toBeGreaterThan(0);
});

test('department manager seeder grants scoped resource reads without management rights', function () {
    $this->seed(OrganizationPermissionSeeder::class);

    $managerRole = Role::query()->where('role_name', 'department_manager')->firstOrFail();
    $permissionNames = $managerRole->permissions()->pluck('permission_name')->all();

    expect($permissionNames)->toContain(
        'employees.view',
        'documents.view',
        'attendance.view',
        'leaves.view',
        'leave-balances.view',
        'payroll.view',
    )->not->toContain('payroll.manage', 'payroll.approve', 'payroll.pay');
});

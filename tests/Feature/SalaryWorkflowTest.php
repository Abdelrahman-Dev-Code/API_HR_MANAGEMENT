<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Position;
use App\Models\Role;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createSalaryWorkflowUser(array $permissions): User
{
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permissionIds = [];

    foreach ($permissions as $name) {
        $permissionIds[] = Permission::query()->create([
            'permission_name' => $name,
            'display_name' => $name,
            'group' => 'payroll',
        ])->id;
    }

    $user->roles()->attach($role);
    $role->permissions()->sync($permissionIds);

    return $user;
}

test('salary net amount is calculated by the server and approved salary cannot be edited', function () {
    $user = createSalaryWorkflowUser(['payroll.manage', 'payroll.approve', 'payroll.pay']);
    $employee = Employee::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/salaries', [
        'employee_id' => $employee->id,
        'year' => 2026,
        'month' => 10,
        'basic_amount' => '1000.01',
        'bonus' => '100.25',
        'overtime_amount' => '20.00',
        'deductions' => '50.10',
        'net_amount' => 1,
        'status' => 'approved',
        'paid_at' => now()->toISOString(),
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.net_amount', '1070.16')
        ->assertJsonPath('data.status', 'pending')
        ->assertJsonPath('data.paid_at', null);

    $salaryId = $response->json('data.id');

    $this->postJson('/api/salaries', [
        'employee_id' => $employee->id,
        'year' => 2026,
        'month' => 10,
        'basic_amount' => 1000,
    ])->assertUnprocessable()->assertJsonValidationErrors('employee_id');

    Salary::query()->create([
        'employee_id' => $employee->id,
        'year' => 2026,
        'month' => 11,
        'basic_amount' => 100,
        'net_amount' => 100,
    ]);

    $this->patchJson("/api/salaries/{$salaryId}", ['month' => 11])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('month');

    $this->postJson("/api/salaries/{$salaryId}/decision", ['decision' => 'approved'])
        ->assertOk()
        ->assertJsonPath('data.status', 'approved')
        ->assertJsonPath('data.reviewed_by_user_id', $user->id);

    $this->patchJson("/api/salaries/{$salaryId}", ['bonus' => 500])
        ->assertUnprocessable();

    $paidResponse = $this->postJson("/api/salaries/{$salaryId}/pay")
        ->assertOk()
        ->assertJsonPath('data.status', 'approved')
        ->assertJsonPath('data.paid_by_user_id', $user->id);

    expect($paidResponse->json('data.paid_at'))->not->toBeNull();
});

test('salary deductions cannot produce a negative net amount', function () {
    $user = createSalaryWorkflowUser(['payroll.manage']);
    $employee = Employee::factory()->create();

    $this->actingAs($user, 'sanctum')->postJson('/api/salaries', [
        'employee_id' => $employee->id,
        'year' => 2026,
        'month' => 10,
        'basic_amount' => 100,
        'deductions' => 101,
    ])->assertUnprocessable()->assertJsonValidationErrors('deductions');
});

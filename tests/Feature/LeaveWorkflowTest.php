<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\Permission;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createLeaveWorkflowActor(array $permissions): array
{
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $department = Department::factory()->create();
    $position = Position::factory()->create();
    $employee = Employee::query()->create([
        'user_id' => $user->id,
        'department_id' => $department->id,
        'position_id' => $position->id,
        'employee_code' => fake()->unique()->bothify('EMP-####'),
        'name' => $user->name,
        'email' => $user->email,
    ]);
    $permissionIds = [];

    foreach ($permissions as $permissionName) {
        $permissionIds[] = Permission::query()->create([
            'permission_name' => $permissionName,
            'display_name' => $permissionName,
            'group' => 'leaves',
        ])->id;
    }

    $user->roles()->attach($role);
    $role->permissions()->sync($permissionIds);

    return [$user, $employee];
}

test('leave submission calculates workdays and ignores client approval fields', function () {
    [$manager, $managerEmployee] = createLeaveWorkflowActor(['leaves.manage']);
    $target = Employee::factory()->create();

    $this->actingAs($manager, 'sanctum')
        ->postJson('/api/leaves', [
            'employee_id' => $target->id,
            'leave_type' => 'earned',
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-06',
            'status' => 'approved',
            'approved_by' => $managerEmployee->id,
            'total_days' => 99,
        ])
        ->assertCreated()
        ->assertJsonPath('data.status', 'pending')
        ->assertJsonPath('data.total_days', 2)
        ->assertJsonPath('data.approved_by', null);
});

test('leave submission excludes official holidays for every leave type', function (string $leaveType) {
    [$manager] = createLeaveWorkflowActor(['leaves.manage']);
    $employee = Employee::factory()->create();
    Holiday::query()->create([
        'holiday_name' => 'Company holiday',
        'holiday_date' => '2026-10-06',
        'is_recurring' => false,
    ]);

    $this->actingAs($manager, 'sanctum')
        ->postJson('/api/leaves', [
            'employee_id' => $employee->id,
            'leave_type' => $leaveType,
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-07',
        ])
        ->assertCreated()
        ->assertJsonPath('data.total_days', 2);
})->with(['earned', 'sick', 'casual', 'maternity', 'paternity', 'bereavement', 'unpaid']);

test('leave update validates end date against the stored start date when start date is omitted', function () {
    [$manager] = createLeaveWorkflowActor(['leaves.manage']);
    $employee = Employee::factory()->create();
    $leave = Leave::query()->create([
        'employee_id' => $employee->id,
        'leave_type' => 'earned',
        'start_date' => '2026-10-05',
        'end_date' => '2026-10-07',
        'total_days' => 3,
        'status' => 'pending',
    ]);

    $this->actingAs($manager, 'sanctum')
        ->patchJson("/api/leaves/{$leave->id}", ['end_date' => '2026-10-06'])
        ->assertOk()
        ->assertJsonPath('data.end_date', '2026-10-06T00:00:00.000000Z')
        ->assertJsonPath('data.total_days', 2);

    $this->patchJson("/api/leaves/{$leave->id}", ['end_date' => '2026-10-04'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('end_date');
});

test('leave balance updates preserve used days and unique employee year type combinations', function () {
    [$manager] = createLeaveWorkflowActor(['leave-balances.manage']);
    $employee = Employee::factory()->create();
    $balance = LeaveBalance::query()->create([
        'employee_id' => $employee->id,
        'year' => 2026,
        'leave_type' => 'earned',
        'allocated_days' => 5,
        'used_days' => 3,
    ]);
    LeaveBalance::query()->create([
        'employee_id' => $employee->id,
        'year' => 2027,
        'leave_type' => 'earned',
        'allocated_days' => 5,
        'used_days' => 0,
    ]);

    $this->actingAs($manager, 'sanctum')
        ->patchJson("/api/leave-balances/{$balance->id}", ['allocated_days' => 2])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('allocated_days');

    $this->patchJson("/api/leave-balances/{$balance->id}", ['year' => 2027])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('leave_type');
});

test('leave balance creation rejects duplicate employee year and type combinations', function () {
    [$manager] = createLeaveWorkflowActor(['leave-balances.manage']);
    $employee = Employee::factory()->create();
    LeaveBalance::query()->create([
        'employee_id' => $employee->id,
        'year' => 2026,
        'leave_type' => 'earned',
        'allocated_days' => 10,
        'used_days' => 0,
    ]);

    $this->actingAs($manager, 'sanctum')
        ->postJson('/api/leave-balances', [
            'employee_id' => $employee->id,
            'year' => 2026,
            'leave_type' => 'earned',
            'allocated_days' => 10,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('employee_id');
});

test('leave approval reserves the yearly balance atomically', function () {
    [$approver] = createLeaveWorkflowActor(['leaves.manage', 'leaves.approve']);
    $employee = Employee::factory()->create();
    $balance = LeaveBalance::query()->create([
        'employee_id' => $employee->id,
        'year' => 2026,
        'leave_type' => 'earned',
        'allocated_days' => 2,
        'used_days' => 0,
    ]);
    $leave = Leave::query()->create([
        'employee_id' => $employee->id,
        'leave_type' => 'earned',
        'start_date' => '2026-10-05',
        'end_date' => '2026-10-06',
        'total_days' => 2,
        'status' => 'pending',
    ]);

    $this->actingAs($approver, 'sanctum')
        ->postJson("/api/leaves/{$leave->id}/decision", ['decision' => 'approved'])
        ->assertOk()
        ->assertJsonPath('data.status', 'approved');

    expect($balance->fresh()->used_days)->toBe(2)
        ->and($leave->fresh()->approved_by)->toBe($approver->employee->id);
});

test('leave approval fails when the yearly balance is insufficient', function () {
    [$approver] = createLeaveWorkflowActor(['leaves.approve']);
    $employee = Employee::factory()->create();
    LeaveBalance::query()->create([
        'employee_id' => $employee->id,
        'year' => 2026,
        'leave_type' => 'earned',
        'allocated_days' => 1,
        'used_days' => 0,
    ]);
    $leave = Leave::query()->create([
        'employee_id' => $employee->id,
        'leave_type' => 'earned',
        'start_date' => '2026-10-05',
        'end_date' => '2026-10-06',
        'total_days' => 2,
        'status' => 'pending',
    ]);

    $this->actingAs($approver, 'sanctum')
        ->postJson("/api/leaves/{$leave->id}/decision", ['decision' => 'approved'])
        ->assertUnprocessable();

    expect($leave->fresh()->status)->toBe('pending');
});

test('leave balance does not roll over into the next calendar year', function () {
    [$approver] = createLeaveWorkflowActor(['leaves.approve']);
    $employee = Employee::factory()->create();
    $currentYearBalance = LeaveBalance::query()->create([
        'employee_id' => $employee->id,
        'year' => 2026,
        'leave_type' => 'earned',
        'allocated_days' => 10,
        'used_days' => 0,
    ]);
    $leave = Leave::query()->create([
        'employee_id' => $employee->id,
        'leave_type' => 'earned',
        'start_date' => '2026-12-31',
        'end_date' => '2027-01-04',
        'total_days' => 3,
        'status' => 'pending',
    ]);

    $this->actingAs($approver, 'sanctum')
        ->postJson("/api/leaves/{$leave->id}/decision", ['decision' => 'approved'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('leave_balance');

    expect($leave->fresh()->status)->toBe('pending')
        ->and($currentYearBalance->fresh()->used_days)->toBe(0);
});

test('leave approval rejects a date range overlapping another approved leave', function () {
    [$approver] = createLeaveWorkflowActor(['leaves.approve']);
    $employee = Employee::factory()->create();
    LeaveBalance::query()->create([
        'employee_id' => $employee->id,
        'year' => 2026,
        'leave_type' => 'earned',
        'allocated_days' => 5,
        'used_days' => 0,
    ]);
    Leave::query()->create([
        'employee_id' => $employee->id,
        'leave_type' => 'earned',
        'start_date' => '2026-10-05',
        'end_date' => '2026-10-06',
        'total_days' => 2,
        'status' => 'approved',
    ]);
    $pendingLeave = Leave::query()->create([
        'employee_id' => $employee->id,
        'leave_type' => 'earned',
        'start_date' => '2026-10-06',
        'end_date' => '2026-10-07',
        'total_days' => 2,
        'status' => 'pending',
    ]);

    $this->actingAs($approver, 'sanctum')
        ->postJson("/api/leaves/{$pendingLeave->id}/decision", ['decision' => 'approved'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('start_date');

    expect($pendingLeave->fresh()->status)->toBe('pending')
        ->and(LeaveBalance::query()->where('employee_id', $employee->id)->value('used_days'))->toBe(0);
});

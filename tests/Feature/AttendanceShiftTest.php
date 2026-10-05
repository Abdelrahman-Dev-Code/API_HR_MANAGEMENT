<?php

use App\Models\Attendance;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('attendance shift must match employee shift and check-in date', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permission = Permission::query()->create([
        'permission_name' => 'attendance.manage',
        'display_name' => 'Manage attendance',
        'group' => 'attendance',
    ]);
    $user->roles()->attach($role);
    $role->permissions()->attach($permission);
    $employee = Employee::factory()->create(['work_shift' => 'day']);

    $payload = [
        'employee_id' => $employee->id,
        'attendance_date' => '2026-10-05',
        'shift' => 'night',
        'check_in_at' => '2026-10-05 08:00:00',
        'check_out_at' => '2026-10-05 17:00:00',
        'status' => 'present',
    ];

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/attendances', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('shift');

    $payload['shift'] = 'day';
    $payload['check_in_at'] = '2026-10-06 08:00:00';
    $payload['check_out_at'] = null;

    $this->postJson('/api/attendances', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('check_in_at');

    $payload['check_in_at'] = null;
    $payload['check_out_at'] = '2026-10-05 17:00:00';

    $this->postJson('/api/attendances', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('check_in_at');

    $payload['check_in_at'] = '2026-10-05 08:00:00';
    $payload['check_out_at'] = '2026-10-05 17:00:00';

    $this->postJson('/api/attendances', $payload)
        ->assertCreated()
        ->assertJsonPath('data.shift', 'day');

    $this->postJson('/api/attendances', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('attendance_date');

    $nightEmployee = Employee::factory()->create(['work_shift' => 'night']);

    $this->postJson('/api/attendances', [
        'employee_id' => $nightEmployee->id,
        'attendance_date' => '2026-10-05',
        'shift' => 'night',
        'check_in_at' => '2026-10-05 22:00:00',
        'check_out_at' => '2026-10-06 06:00:00',
        'status' => 'present',
    ])->assertCreated();

    expect(Attendance::query()->count())->toBe(2);
});

test('attendance updates reject duplicate shifts and check-out without check-in', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permission = Permission::query()->create([
        'permission_name' => 'attendance.manage',
        'display_name' => 'Manage attendance',
        'group' => 'attendance',
    ]);
    $user->roles()->attach($role);
    $role->permissions()->attach($permission);
    $employee = Employee::factory()->create(['work_shift' => 'day']);
    $firstAttendance = Attendance::query()->create([
        'employee_id' => $employee->id,
        'attendance_date' => '2026-10-05',
        'shift' => 'day',
    ]);
    Attendance::query()->create([
        'employee_id' => $employee->id,
        'attendance_date' => '2026-10-06',
        'shift' => 'day',
    ]);

    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/attendances/{$firstAttendance->id}", [
            'check_out_at' => '2026-10-05 17:00:00',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('check_in_at');

    $this->patchJson("/api/attendances/{$firstAttendance->id}", [
        'attendance_date' => '2026-10-06',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('attendance_date');
});

test('attendance records cannot be deleted through the API', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permission = Permission::query()->create([
        'permission_name' => 'attendance.manage',
        'display_name' => 'Manage attendance',
        'group' => 'attendance',
    ]);
    $user->roles()->attach($role);
    $role->permissions()->attach($permission);
    $employee = Employee::factory()->create(['work_shift' => 'day']);
    $attendance = Attendance::query()->create([
        'employee_id' => $employee->id,
        'attendance_date' => '2026-10-05',
        'shift' => 'day',
    ]);

    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/attendances/{$attendance->id}")
        ->assertMethodNotAllowed();

    expect($attendance->fresh())->not->toBeNull();
});

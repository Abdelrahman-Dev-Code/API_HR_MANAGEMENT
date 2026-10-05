<?php

use App\Models\Permission;
use App\Models\Holiday;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('position create and update return clear Arabic duplicate-value errors', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permission = Permission::query()->create([
        'permission_name' => 'positions.manage',
        'display_name' => 'Manage positions',
        'group' => 'positions',
    ]);
    $user->roles()->attach($role);
    $role->permissions()->attach($permission);

    $firstPosition = Position::factory()->create([
        'position_name' => 'Position Alpha',
        'code' => 'POS-ALPHA',
    ]);
    $secondPosition = Position::factory()->create([
        'position_name' => 'Position Beta',
        'code' => 'POS-BETA',
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/positions', [
            'position_name' => $firstPosition->position_name,
            'code' => 'POS-GAMMA',
        ])
        ->assertUnprocessable()
        ->assertJsonPath('errors.position_name.0', 'اسم المسمى الوظيفي مستخدم بالفعل.');

    $this->patchJson("/api/positions/{$secondPosition->id}", [
        'code' => $firstPosition->code,
    ])
        ->assertUnprocessable()
        ->assertJsonPath('errors.code.0', 'رمز المسمى الوظيفي مستخدم بالفعل.');
});

test('holiday create and update return a clear Arabic duplicate-date error', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permission = Permission::query()->create([
        'permission_name' => 'holidays.manage',
        'display_name' => 'Manage holidays',
        'group' => 'holidays',
    ]);
    $user->roles()->attach($role);
    $role->permissions()->attach($permission);

    $firstHoliday = Holiday::query()->create([
        'holiday_name' => 'Holiday Alpha',
        'holiday_date' => '2026-12-25',
        'is_recurring' => false,
    ]);
    $secondHoliday = Holiday::query()->create([
        'holiday_name' => 'Holiday Beta',
        'holiday_date' => '2026-12-31',
        'is_recurring' => false,
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/holidays', [
            'holiday_name' => 'Duplicate holiday',
            'holiday_date' => $firstHoliday->holiday_date->toDateString(),
        ])
        ->assertUnprocessable()
        ->assertJsonPath('errors.holiday_date.0', 'يوجد تسجيل لعطلة في هذا التاريخ بالفعل.');

    $this->patchJson("/api/holidays/{$secondHoliday->id}", [
        'holiday_date' => $firstHoliday->holiday_date->toDateString(),
    ])
        ->assertUnprocessable()
        ->assertJsonPath('errors.holiday_date.0', 'يوجد تسجيل لعطلة في هذا التاريخ بالفعل.');
});

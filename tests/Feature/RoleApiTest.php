<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('role validation returns clear Arabic errors for duplicate names and unknown permissions', function () {
    $user = User::factory()->create();
    $managerRole = Role::factory()->create();
    $rolesManage = Permission::query()->create([
        'permission_name' => 'roles.manage',
        'display_name' => 'Manage roles',
        'group' => 'authorization',
    ]);
    $user->roles()->attach($managerRole);
    $managerRole->permissions()->attach($rolesManage);
    Role::factory()->create(['role_name' => 'existing-role']);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/roles', [
            'role_name' => 'existing-role',
            'display_name' => 'Existing role',
        ])
        ->assertUnprocessable()
        ->assertJsonPath('errors.role_name.0', 'اسم الدور مستخدم بالفعل.');

    $roleToUpdate = Role::factory()->create(['role_name' => 'another-role']);

    $this->patchJson("/api/roles/{$roleToUpdate->id}", ['role_name' => 'existing-role'])
        ->assertUnprocessable()
        ->assertJsonPath('errors.role_name.0', 'اسم الدور مستخدم بالفعل.');

    $response = $this->postJson('/api/roles', [
        'role_name' => 'new-role',
        'display_name' => 'New role',
        'permission_ids' => [999999],
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors('permission_ids.0');

    expect($response->json('errors')['permission_ids.0'][0])
        ->toBe('أحد معرفات الأذونات المحددة غير موجودة في قاعدة البيانات.');
});

test('permission creation and update return a clear Arabic duplicate-name error', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $managePermissions = Permission::query()->create([
        'permission_name' => 'permissions.manage',
        'display_name' => 'Manage permissions',
        'group' => 'authorization',
    ]);
    $user->roles()->attach($role);
    $role->permissions()->attach($managePermissions);

    $firstPermission = Permission::query()->create([
        'permission_name' => 'staff.read',
        'display_name' => 'Read staff',
        'group' => 'staff',
    ]);
    $secondPermission = Permission::query()->create([
        'permission_name' => 'staff.update',
        'display_name' => 'Update staff',
        'group' => 'staff',
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/permissions', [
            'permission_name' => $firstPermission->permission_name,
            'display_name' => 'Duplicate permission',
            'group' => 'staff',
        ])
        ->assertUnprocessable()
        ->assertJsonPath('errors.permission_name.0', 'اسم الصلاحية مستخدم بالفعل.');

    $this->patchJson("/api/permissions/{$secondPermission->id}", [
        'permission_name' => $firstPermission->permission_name,
    ])
        ->assertUnprocessable()
        ->assertJsonPath('errors.permission_name.0', 'اسم الصلاحية مستخدم بالفعل.');
});

test('authorized administrators can create and update user accounts without exposing passwords', function () {
    $admin = User::factory()->create();
    $adminRole = Role::factory()->create();
    $usersManage = Permission::query()->create([
        'permission_name' => 'users.manage',
        'display_name' => 'Manage user accounts',
        'group' => 'authorization',
    ]);
    $admin->roles()->attach($adminRole);
    $adminRole->permissions()->attach($usersManage);

    $assignedRole = Role::factory()->create();
    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/users', [
            'name' => 'New Staff Account',
            'email' => 'new-staff@example.com',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
            'is_active' => true,
            'role_ids' => [$assignedRole->id],
        ])
        ->assertCreated()
        ->assertJsonPath('data.email', 'new-staff@example.com')
        ->assertJsonPath('data.roles.0.id', $assignedRole->id);

    expect($response->json('data'))->not->toHaveKey('password');

    $createdUser = User::query()->where('email', 'new-staff@example.com')->firstOrFail();
    expect($createdUser->roles->modelKeys())->toBe([$assignedRole->id]);

    $this->patchJson("/api/users/{$createdUser->id}", ['is_active' => false])
        ->assertSuccessful()
        ->assertJsonPath('data.is_active', false);
});

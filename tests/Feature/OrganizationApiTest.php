<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('organization api denies authenticated users without management permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/organization')
        ->assertForbidden()
        ->assertJsonPath('message', 'لا تملك الصلاحية المطلوبة لتنفيذ هذا الطلب.');
});

test('unauthenticated API requests return a clear Arabic error', function () {
    $this->getJson('/api/auth/me')
        ->assertUnauthorized()
        ->assertJsonPath('message', 'يجب تسجيل الدخول للوصول إلى هذا المسار.');
});

test('organization manager can read and update the single organization record', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permission = Permission::query()->create([
        'permission_name' => 'organization.manage',
        'display_name' => 'Manage organization settings',
        'group' => 'organization',
    ]);

    $user->roles()->attach($role);
    $role->permissions()->attach($permission);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/organization')
        ->assertOk()
        ->assertJsonPath('data.name', 'My Company')
        ->assertJsonPath('data.currency_code', 'SAR');

    $this->patchJson('/api/organization', [
        'company_name' => '<b>Example Company</b>',
        'currency_code' => 'AED',
        'weekend_days' => ['Friday', 'Saturday'],
    ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Example Company')
        ->assertJsonPath('data.currency_code', 'AED');

    $this->patchJson('/api/organization', ['currency_code' => 'US'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('currency_code');

    expect(Setting::query()->where('setting_key', 'company')->count())->toBe(1);
});

test('login sanitizer preserves password characters', function () {
    $password = '<b>secret</b>';
    $user = User::factory()->create(['password' => $password]);

    $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => $password,
        'device_name' => 'test-client',
    ])->assertOk()->assertJsonPath('token_type', 'Bearer');
});

test('resource view permission does not grant write access', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permission = Permission::query()->create([
        'permission_name' => 'employees.view',
        'display_name' => 'View employees',
        'group' => 'employees',
    ]);

    $user->roles()->attach($role);
    $role->permissions()->attach($permission);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/employees')
        ->assertOk();

    $this->postJson('/api/employees', [])
        ->assertForbidden();
});

test('API validation messages are Arabic by default and follow the requested language', function () {
    $this->withHeader('Accept-Language', '')
        ->postJson('/api/auth/login', [])
        ->assertUnprocessable()
        ->assertJsonPath('errors.email.0', 'حقل البريد الإلكتروني مطلوب.');

    $this->withHeader('Accept-Language', 'en')
        ->postJson('/api/auth/login', [])
        ->assertUnprocessable()
        ->assertJsonPath('errors.email.0', 'The email field is required.');
});

<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class HrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'HR Administrator', 'password' => 'password', 'is_active' => true],
        );

        $adminRole = Role::query()->firstOrCreate(
            ['role_name' => 'hr_admin'],
            ['display_name' => 'HR Administrator', 'description' => 'Full HR system access', 'is_active' => true],
        );

        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $departments = Department::factory(3)->create();
        $positions = Position::factory(5)->create();

        $this->call(OrganizationPermissionSeeder::class);

        Employee::factory(10)->create([
            'department_id' => fn () => $departments->random()->id,
            'position_id' => fn () => $positions->random()->id,
        ]);
    }
}

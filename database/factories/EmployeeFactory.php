<?php

/**
 * File Name: EmployeeFactory.php
 * Description:dkh3
 * Developer: Abdelrahman-Dev-Code
 * Created Date: 2026-09-27
 * Last Modified: 2026-09-27
 */

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'position_id' => Position::factory(),
            'employee_code' => fake()->unique()->bothify('EMP-####'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'basic_salary' => fake()->randomFloat(2, 3000, 15000),
            'hire_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'birth_date' => fake()->dateTimeBetween('-55 years', '-20 years')->format('Y-m-d'),
            'address' => fake()->address(),
            'gender' => fake()->randomElement(['male', 'female']),
            'profile_photo' => null,
            'status' => 'active',
            'work_shift' => fake()->randomElement(['day', 'night']),
        ];
    }
}

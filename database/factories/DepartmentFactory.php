<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_name' => fake()->unique()->company(),
            'description' => fake()->sentence(),
            'parent_department_id' => null,
            'is_active' => true,
            'code' => fake()->unique()->bothify('DEP-###'),
            'location' => fake()->city(),
        ];
    }
}

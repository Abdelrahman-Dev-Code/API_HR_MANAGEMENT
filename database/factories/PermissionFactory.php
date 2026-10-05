<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'permission_name' => fake()->unique()->slug(2),
            'display_name' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'group' => fake()->randomElement(['employees', 'attendance', 'leaves', 'payroll']),
        ];
    }
}

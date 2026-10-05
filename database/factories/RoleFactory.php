<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_name' => fake()->unique()->slug(2),
            'display_name' => fake()->jobTitle(),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}

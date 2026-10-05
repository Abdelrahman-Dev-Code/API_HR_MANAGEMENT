<?php

namespace Database\Factories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'position_name' => fake()->unique()->jobTitle(),
            'description' => fake()->sentence(),
            'code' => fake()->unique()->bothify('POS-###'),
            'is_active' => true,
        ];
    }
}

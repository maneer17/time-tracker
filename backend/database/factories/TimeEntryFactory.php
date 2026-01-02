<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "label" => fake()->name,
            "start_time" => fake()->time(),
            "end_time" => fake()->time(),
            "user_id" => 2,
            "created_at" => today()
            //
        ];
    }
}

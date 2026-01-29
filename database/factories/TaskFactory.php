<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
$completed = fake()->boolean(35);
    $due = fake()->dateTimeBetween('-5 days', '+15 days');

    return [
        'title' => fake()->sentence(4),
        'description' => fake()->optional(0.4)->paragraph(),
        'due_date' => $due,
        'completed_at' => $completed ? now() : null,
        'user_id' => User::factory(),
        'category_id' => Category::factory(),
        ];
    }
}

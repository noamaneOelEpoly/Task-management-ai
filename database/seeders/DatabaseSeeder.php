<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@root.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Test user with visible data
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        // Create many categories for test user (for pagination testing)
        $categories = Category::factory(15)->for($testUser)->create();

        // Create many tasks for test user (for pagination testing)
        $categories->each(function (Category $category) use ($testUser) {
            Task::factory(5)
                ->for($testUser)
                ->for($category)
                ->create();
        });

        // 10 more users, each with random categories and tasks
        User::factory(10)->create()->each(function (User $user) {
            $categories = Category::factory(rand(2, 5))->for($user)->create();

            $categories->each(function (Category $category) use ($user) {
                Task::factory(rand(3, 10))
                    ->for($user)
                    ->for($category)
                    ->create();
            });
        });
    }
}

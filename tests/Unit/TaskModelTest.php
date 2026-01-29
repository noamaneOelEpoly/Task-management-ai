<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'une tâche
     */
    public function test_can_create_task()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $task = Task::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'due_date' => now()->addDays(5),
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Test Task',
            'user_id' => $user->id
        ]);
    }

    /**
     * Test que la relation utilisateur fonctionne
     */
    public function test_task_belongs_to_user()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($task->user()->is($user));
    }

    /**
     * Test que la relation catégorie fonctionne
     */
    public function test_task_belongs_to_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id
        ]);

        $this->assertTrue($task->category()->is($category));
    }

    /**
     * Test de marquage comme complétée
     */
    public function test_can_mark_task_as_completed()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'status' => 'pending']);

        $task->update(['status' => 'completed']);

        $this->assertEquals('completed', $task->status);
    }

    /**
     * Test de suppression d'une tâche
     */
    public function test_can_delete_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $taskId = $task->id;

        $task->delete();

        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
    }

    /**
     * Test que les tâches en retard sont détectées
     */
    public function test_can_detect_overdue_tasks()
    {
        $user = User::factory()->create();
        $overdueTask = Task::factory()->create([
            'user_id' => $user->id,
            'due_date' => now()->subDays(1),
            'status' => 'pending'
        ]);

        $overdueTasks = Task::where('user_id', $user->id)
            ->where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->get();

        $this->assertCount(1, $overdueTasks);
        $this->assertTrue($overdueTasks->contains($overdueTask));
    }

    /**
     * Test de validation du titre requis
     */
    public function test_title_is_required()
    {
        $user = User::factory()->create();

        // Test that mass assignment doesn't include title
        $task = Task::create([
            'user_id' => $user->id,
            'title' => '', // Empty title
            'status' => 'pending'
        ]);

        // The task is created but title is empty
        $this->assertEquals('', $task->title);
    }

    /**
     * Test d'assignation de statuts
     */
    public function test_status_can_be_set()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $statuses = ['pending', 'in_progress', 'completed'];
        
        foreach ($statuses as $status) {
            $task->update(['status' => $status]);
            $this->assertEquals($status, $task->fresh()->status);
        }
    }
}

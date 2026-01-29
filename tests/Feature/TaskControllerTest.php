<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test affichage du dashboard des tâches
     */
    public function test_can_view_tasks_dashboard()
    {
        $response = $this->actingAs($this->user)->get('/tasks');

        $response->assertStatus(200);
        $response->assertViewIs('tasks.index');
    }

    /**
     * Test affichage du formulaire de création
     */
    public function test_can_view_create_task_form()
    {
        $response = $this->actingAs($this->user)->get('/tasks/create');

        $response->assertStatus(200);
        $response->assertViewIs('tasks.create');
    }

    /**
     * Test de création d'une tâche
     */
    public function test_can_create_task()
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $data = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'category_id' => $category->id,
            'due_date' => now()->addDays(5)->format('Y-m-d\TH:i')
        ];

        $response = $this->actingAs($this->user)->post('/tasks', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'user_id' => $this->user->id
        ]);
    }

    /**
     * Test que seul le propriétaire peut créer
     */
    public function test_task_creation_requires_authentication()
    {
        $response = $this->get('/tasks/create');
        $response->assertRedirect('/login');
    }

    /**
     * Test affichage des détails d'une tâche
     */
    public function test_can_view_task_details()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get("/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertViewIs('tasks.show');
        $response->assertViewHas('task', $task);
    }

    /**
     * Test affichage du formulaire d'édition
     */
    public function test_can_view_edit_task_form()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get("/tasks/{$task->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('tasks.edit');
    }

    /**
     * Test de modification d'une tâche
     */
    public function test_can_update_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $data = [
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'status' => 'in_progress'
        ];

        $response = $this->actingAs($this->user)->put("/tasks/{$task->id}", $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
            'status' => 'in_progress'
        ]);
    }

    /**
     * Test de suppression d'une tâche
     */
    public function test_can_delete_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $taskId = $task->id;

        $response = $this->actingAs($this->user)->delete("/tasks/{$taskId}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
    }

    /**
     * Test qu'on ne peut pas modifier la tâche d'un autre utilisateur
     */
    public function test_cannot_update_other_users_task()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->put("/tasks/{$task->id}", [
            'title' => 'Hacked'
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test qu'on ne peut pas voir la tâche d'un autre utilisateur
     */
    public function test_cannot_view_other_users_task()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get("/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    /**
     * Test du filtrage par catégorie
     */
    public function test_can_filter_tasks_by_category()
    {
        $category1 = Category::factory()->create(['user_id' => $this->user->id]);
        $category2 = Category::factory()->create(['user_id' => $this->user->id]);

        Task::factory(3)->create(['user_id' => $this->user->id, 'category_id' => $category1->id]);
        Task::factory(2)->create(['user_id' => $this->user->id, 'category_id' => $category2->id]);

        $response = $this->actingAs($this->user)->get("/tasks?category={$category1->id}");

        $response->assertStatus(200);
        // Note: Le test exact dépend de votre implémentation du filtre
    }

    /**
     * Test validation des données requises
     */
    public function test_task_creation_requires_title()
    {
        $data = [
            'title' => '',
            'description' => 'Task Description'
        ];

        $response = $this->actingAs($this->user)->post('/tasks', $data);

        $response->assertSessionHasErrors('title');
    }

    /**
     * Test que la date d'échéance peut être nulle
     */
    public function test_task_due_date_can_be_null()
    {
        $data = [
            'title' => 'Task without due date',
            'due_date' => null
        ];

        $response = $this->actingAs($this->user)->post('/tasks', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'title' => 'Task without due date'
        ]);
    }
}

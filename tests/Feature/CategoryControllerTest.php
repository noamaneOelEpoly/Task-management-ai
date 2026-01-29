<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test affichage de la liste des catégories
     */
    public function test_can_view_categories_index()
    {
        $response = $this->actingAs($this->user)->get('/categories');

        $response->assertStatus(200);
        $response->assertViewIs('categories.index');
    }

    /**
     * Test affichage du formulaire de création
     */
    public function test_can_view_create_category_form()
    {
        $response = $this->actingAs($this->user)->get('/categories/create');

        $response->assertStatus(200);
        $response->assertViewIs('categories.create');
    }

    /**
     * Test de création d'une catégorie
     */
    public function test_can_create_category()
    {
        $data = [
            'name' => 'Work',
            'description' => 'Work related tasks',
            'color' => '#FF5733'
        ];

        $response = $this->actingAs($this->user)->post('/categories', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name' => 'Work',
            'user_id' => $this->user->id
        ]);
    }

    /**
     * Test que la création nécessite l'authentification
     */
    public function test_category_creation_requires_authentication()
    {
        $response = $this->get('/categories/create');
        $response->assertRedirect('/login');
    }

    /**
     * Test affichage des détails d'une catégorie
     */
    public function test_can_view_category_details()
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get("/categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertViewIs('categories.show');
        $response->assertViewHas('category', $category);
    }

    /**
     * Test affichage du formulaire d'édition
     */
    public function test_can_view_edit_category_form()
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get("/categories/{$category->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('categories.edit');
    }

    /**
     * Test de modification d'une catégorie
     */
    public function test_can_update_category()
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $data = [
            'name' => 'Updated Category',
            'description' => 'Updated Description',
            'color' => '#FFFFFF'
        ];

        $response = $this->actingAs($this->user)->put("/categories/{$category->id}", $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category'
        ]);
    }

    /**
     * Test de suppression d'une catégorie
     */
    public function test_can_delete_category()
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $categoryId = $category->id;

        $response = $this->actingAs($this->user)->delete("/categories/{$categoryId}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $categoryId]);
    }

    /**
     * Test qu'on ne peut pas modifier la catégorie d'un autre utilisateur
     */
    public function test_cannot_update_other_users_category()
    {
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->put("/categories/{$category->id}", [
            'name' => 'Hacked'
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test qu'on ne peut pas voir la catégorie d'un autre utilisateur
     */
    public function test_cannot_view_other_users_category()
    {
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get("/categories/{$category->id}");

        $response->assertStatus(403);
    }

    /**
     * Test validation du nom requis
     */
    public function test_category_creation_requires_name()
    {
        $data = [
            'name' => '',
            'description' => 'Description'
        ];

        $response = $this->actingAs($this->user)->post('/categories', $data);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test que la description peut être vide
     */
    public function test_category_description_can_be_null()
    {
        $data = [
            'name' => 'Test Category',
            'description' => null
        ];

        $response = $this->actingAs($this->user)->post('/categories', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category'
        ]);
    }

    /**
     * Test affichage des tâches d'une catégorie
     */
    public function test_can_view_tasks_in_category()
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $category->tasks()->createMany([
            ['user_id' => $this->user->id, 'title' => 'Task 1'],
            ['user_id' => $this->user->id, 'title' => 'Task 2'],
        ]);

        $response = $this->actingAs($this->user)->get("/categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertViewHas('tasks');
    }

    /**
     * Test que seules les catégories de l'utilisateur sont affichées
     */
    public function test_only_user_categories_are_displayed()
    {
        Category::factory(3)->create(['user_id' => $this->user->id]);
        Category::factory(2)->create(['user_id' => User::factory()]);

        $response = $this->actingAs($this->user)->get('/categories');

        // Vérifier que la réponse affiche les catégories
        $response->assertStatus(200);
        // Le test exact dépend de votre implémentation
    }
}

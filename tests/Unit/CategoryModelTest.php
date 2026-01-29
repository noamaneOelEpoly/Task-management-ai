<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'une catégorie
     */
    public function test_can_create_category()
    {
        $user = User::factory()->create();

        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Work',
            'description' => 'Work related tasks',
            'color' => '#FF5733'
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Work',
            'user_id' => $user->id
        ]);
    }

    /**
     * Test que la relation utilisateur fonctionne
     */
    public function test_category_belongs_to_user()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($category->user()->is($user));
    }

    /**
     * Test que la relation tâches fonctionne
     */
    public function test_category_has_many_tasks()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $category->tasks()->createMany([
            ['user_id' => $user->id, 'title' => 'Task 1'],
            ['user_id' => $user->id, 'title' => 'Task 2'],
        ]);

        $this->assertCount(2, $category->tasks);
    }

    /**
     * Test de suppression d'une catégorie
     */
    public function test_can_delete_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $categoryId = $category->id;

        $category->delete();

        $this->assertDatabaseMissing('categories', ['id' => $categoryId]);
    }

    /**
     * Test de mise à jour d'une catégorie
     */
    public function test_can_update_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $category->update([
            'name' => 'Updated Name',
            'color' => '#FFFFFF'
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'color' => '#FFFFFF'
        ]);
    }

    /**
     * Test que la couleur par défaut est assignée
     */
    public function test_default_color_is_assigned()
    {
        $user = User::factory()->create();

        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Test Category'
        ]);

        $this->assertNotNull($category->color);
    }

    /**
     * Test que le nom est requis
     */
    public function test_name_is_required()
    {
        $user = User::factory()->create();

        // Test that attempting to create a category without name throws exception
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Category::create([
            'user_id' => $user->id,
            'description' => 'Category without name'
        ]);
    }

    /**
     * Test de comptage des tâches par catégorie
     */
    public function test_count_tasks_in_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        // Créer 3 tâches
        $category->tasks()->createMany([
            ['user_id' => $user->id, 'title' => 'Task 1'],
            ['user_id' => $user->id, 'title' => 'Task 2'],
            ['user_id' => $user->id, 'title' => 'Task 3'],
        ]);

        $this->assertEquals(3, $category->tasks()->count());
    }
}

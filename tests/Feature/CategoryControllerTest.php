<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // On crée un utilisateur pour chaque test
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_view_their_categories()
    {
        /*
        Manual seeding of categories
        */
        Category::create([
            'name' => 'Travail',
            'color' => '#FF5733',
            'user_id' => $this->user->id
        ]);

        Category::create([
            'name' => 'Perso',
            'color' => '#3357FF',
            'user_id' => $this->user->id
        ]);

        /*
        *Other user categories
        */
        $otherUser = User::factory()->create();
        Category::create([
            'name' => 'Secret',
            'color' => '#000000',
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)->get('/categories');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Categories/Index')
                ->has('categories', 2) //Verify that only the user's 2 categories are returned
            );
    }

    /** @test */
    public function user_can_create_a_category()
    {
        $response = $this->actingAs($this->user)->post('/categories', [
            'name' => 'Shopping',
            'color' => '#33FF57',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name' => 'Shopping',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function user_can_update_their_own_category()
    {
        $category = Category::create([
            'name' => 'Ancien Nom',
            'color' => '#000000',
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)->put("/categories/{$category->id}", [
            'name' => 'Nom Modifié',
            'color' => '#FFFFFF',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Nom Modifié',
        ]);
    }

    /** @test */
    public function user_cannot_update_others_category()
    {
        $otherUser = User::factory()->create();
        $category = Category::create([
            'name' => 'Catégorie Autre',
            'color' => '#000000',
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)->put("/categories/{$category->id}", [
            'name' => 'Tentative Hack',
            'color' => '#FF0000',
        ]);

        $response->assertStatus(403); // Lock 403 Forbidden due to policy
    }

    /** @test */
    public function user_can_delete_their_own_category()
    {
        $category = Category::create([
            'name' => 'A supprimer',
            'color' => '#FF0000',
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)->delete("/categories/{$category->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}

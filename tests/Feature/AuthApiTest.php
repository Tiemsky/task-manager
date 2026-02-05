<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'P@ssword1234',
            'password_confirmation' => 'P@ssword1234',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'token']);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    /** @test */
    public function an_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'user@gmail.com',
            'password' => bcrypt('P@ssword1234'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'user@gmail.com',
            'password' => 'P@ssword1234',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token', 'message']);
    }

    /** @test */
    public function protected_route_requires_authentication()
    {

        $response = $this->getJson('/api/profile');
        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_access_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/profile');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'user' => [
                         'email' => $user->email
                     ]
                 ]);
    }
}

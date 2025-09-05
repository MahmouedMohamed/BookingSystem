<?php

namespace Tests\Feature;

use App\Modules\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthModuleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function test_user_can_register_as_customer(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'password' => 'password123',
            'role' => 'customer',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'items' => [
                    'user' => ['id', 'name', 'email', 'timezone'],
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'customer@test.com',
            'name' => 'Test Customer',
        ]);
    }

    #[Test]
    public function test_user_can_register_as_provder(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test Provider',
            'email' => 'provider@test.com',
            'password' => 'password123',
            'role' => 'provider',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'items' => [
                    'user' => ['id', 'name', 'email', 'timezone'],
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'provider@test.com',
            'name' => 'Test Provider',
        ]);
    }

    #[Test]
    public function test_user_can_login(): void
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'item' => [
                    'user' => ['id', 'name', 'email', 'timezone'],
                    'token',
                ],
            ]);
    }

    #[Test]
    public function test_login_fails_with_invalid_credentials(): void
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ])->assertStatus(404);

        $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ])->assertStatus(401);
    }
}

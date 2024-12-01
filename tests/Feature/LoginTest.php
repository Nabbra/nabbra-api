<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user can login with email and password.
     */
    public function test_user_can_login_using_email_password(): void
    {
        User::factory()->createOne([
            'email' => $email = 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson(route('api.auth.login', [
            'apiVersion' => '1',
        ]), ['email' => $email, 'password' => 'password']);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'name',
                'email',
                'email_verified_at',
                'image',
                'last_activity_at',
                'last_activity_at_formatted',
                'created_at',
                'created_at_formatted',
            ],
            'token',
        ]);
    }

    /**
     * Test email, and password login method validation.
     */
    public function test_user_email_password_login_validation(): void
    {
        $response = $this->postJson(route('api.auth.login', [
            'apiVersion' => '1',
        ]), ['email' => 'not-existing@email.com', 'password' => 'password']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('email');

        User::factory()->createOne([
            'email' => $email = 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson(route('api.auth.login', [
            'apiVersion' => '1',
        ]), ['email' => $email, 'password' => 'wrong-password']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('email');
    }
}

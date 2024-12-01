<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user can register a new account.
     */
    public function test_user_can_register_new_account(): void
    {
        $this->assertDatabaseCount('users', 0);

        $response = $this->postJson(route('api.auth.register', [
            'apiVersion' => '1',
        ]), [
            'name' => 'Abdallah Mohammed',
            'email' => 'abdallah@demo.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSuccessful();

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

        $this->assertDatabaseCount('users', 1);
    }

    /**
     * Test all register form validation conditions.
     */
    public function test_register_form_validation(): void
    {
        $this->assertDatabaseCount('users', 0);

        $response = $this->postJson(route('api.auth.register', [
            'apiVersion' => '1',
        ]), [
            'name' => 'Abdallah Mohammed',
            'email' => 'abdallah@demo.com',
            'password' => 'password',
            'password_confirmation' => 'password_mismatch',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('password');

        $this->assertDatabaseCount('users', 0);

        User::factory()->createOne([
            'email' => $email = 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson(route('api.auth.register', [
            'apiVersion' => '1',
        ]), [
            'name' => 'Abdallah Mohammed',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('email');

        $this->assertDatabaseCount('users', 1);
    }
}

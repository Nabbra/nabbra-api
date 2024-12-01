<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VerificationToken;
use App\Notifications\PasswordlessLinkNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordlessAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that invalid email will show wrong response.
     *
     * @return void
     */
    public function test_passwordless_link_wont_be_send_when_email_is_invalid(): void
    {
        Notification::fake();

        $response = $this->postJson(
            route('auth.passwordless.token', ['apiVersion' => '1']),
            ['email' => 'abdallah'],
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('email');

        Notification::assertSentOnDemandTimes(PasswordlessLinkNotification::class, 0);
    }

    /**
     * Test sending a passwordless link to email.
     *
     * @return void
     */
    public function test_send_passwordless_link_through_email(): void
    {
        Notification::fake();

        $this->assertDatabaseCount('verification_tokens', 0);

        $response = $this->postJson(
            route('auth.passwordless.token', ['apiVersion' => '1']),
            ['email' => $email = 'abdallah.r660@gmail.com'],
        );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => trans('auth.passwordless.success'),
        ]);

        $this->assertDatabaseCount('verification_tokens', 1);

        Notification::assertSentOnDemand(PasswordlessLinkNotification::class, function ($notification, $channels, $notifiable) use ($email) {
            return in_array('mail', $channels) && $notifiable->routes['mail'] == $email;
        });
    }

    /**
     * Test that valid passwordless token for non-registered user, creates a new user.
     *
     * @return void
     */
    public function test_valid_token_for_new_user(): void
    {
        $token = VerificationToken::factory()
            ->expires()
            ->create(['payload' => base64_encode('email@example.com')]);

        $this->assertDatabaseCount('users', 0);

        $response = $this->postJson(
            route('auth.passwordless.verify', ['apiVersion' => '1']),
            ['token' => $token->token],
        );

        $response->assertStatus(201);
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
        $this->assertDatabaseHas('users', [
            'email' => 'email@example.com',
        ]);
    }

    /**
     * Test that valid passwordless token for already-registered user, return same user.
     *
     * @return void
     */
    public function test_valid_token_for_exists_user(): void
    {
        $token = VerificationToken::factory()
            ->expires()
            ->create(['payload' => base64_encode($email = 'email@example.com')]);

        $user = User::factory()->create([
            'email' => $email,
        ]);

        $this->assertDatabaseCount('users', 1);

        $response = $this->postJson(
            route('auth.passwordless.verify', ['apiVersion' => '1']),
            ['token' => $token->token],
        );

        $response->assertStatus(200);
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
        $response->assertJson([
            'data' => [
                'email' => $user->email,
            ],
        ]);

        $this->assertDatabaseCount('users', 1);
    }

    /**
     * Test expired verification token.
     *
     * @return void
     */
    public function test_expired_token_return_error(): void
    {
        $token = VerificationToken::factory()
            ->expired()
            ->create(['payload' => base64_encode('email@example.com')]);

        $response = $this->postJson(
            route('auth.passwordless.verify', ['apiVersion' => '1']),
            ['token' => $token->token],
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('token');

        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Test invalid token.
     *
     * @return void
     */
    public function test_non_existance_token_return_error(): void
    {
        $response = $this->postJson(
            route('auth.passwordless.verify', ['apiVersion' => '1']),
            ['token' => 'mocked-token'],
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('token');

        $this->assertDatabaseCount('users', 0);
    }
}

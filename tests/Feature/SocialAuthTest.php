<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use App\Models\User;
use Laravel\Socialite\Two\User as SocialiteUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    protected $providerUser;

    protected $accountId;
    protected $userName;
    protected $userEmail;
    protected $userAvatar;
    protected $accessToken;

    /**
     * Set up the common mock for Socialite and providerUser before each test.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->providerUser = Mockery::mock(SocialiteUser::class);

        $this->mockSocaliteUser();
    }

    /**
     * Test social login for a not-supported provider.
     *
     * @return void
     */
    public function test_non_supported_provider_return_error(): void
    {
        $response = $this->postJson(
            route('api.auth.social.callback', [
                'provider' => 'mocked-provider',
                'apiVersion' => '1',
            ]),
            [
                'token' => $this->accessToken,
                'device' => 'mock-device-id',
            ]
        );

        $response->assertNotFound();
    }

    /**
     * Test social login for existing user.
     *
     * @return void
     */
    public function test_social_login_for_existing_user(): void
    {
        $this->createUser();

        $response = $this->postJson(
            route('api.auth.social.callback', [
                'provider' => 'google',
                'apiVersion' => '1',
            ]),
            [
                'token' => $this->accessToken,
                'device' => 'mock-device-id',
            ]
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

        $this->assertDatabaseCount('accounts', 1);
        $this->assertDatabaseCount('users', 1);
    }

    /**
     * Test social login for new user.
     *
     * @return void
     */
    public function test_social_login_for_new_user(): void
    {
        $response = $this->postJson(
            route('api.auth.social.callback', [
                'provider' => 'google',
                'apiVersion' => '1',
            ]),
            [
                'token' => $this->accessToken,
                'device' => 'mock-device-id',
            ]
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

        $this->assertDatabaseHas('users', [
            'email' => $this->userEmail,
        ]);

        $this->assertDatabaseHas('accounts', [
            'provider_account_id' => $this->accountId,
        ]);
    }

    /**
     * Test social login for new user.
     *
     * @return void
     */
    public function test_social_loign_same_user_different_provider(): void
    {
        $user = $this->createUser();

        $accessToken = $this->accessToken;

        $this->mockSocaliteUser('Mohammed Rezk');
        $this->assertNotEquals($accessToken, $this->accessToken);

        $response = $this->postJson(
            route('api.auth.social.callback', [
                'provider' => 'github',
                'apiVersion' => '1',
            ]),
            [
                'token' => $accessToken,
                'device' => 'mock-device-id',
            ]
        );

        $response->assertStatus(200);

        $this->assertDatabaseCount('users', 1);
        $this->assertEquals($user->accounts()->count(), 2);
    }

    /**
     * Mock socialite user.
     *
     * @return void
     */
    private function mockSocaliteUser($userName = 'Abdallah Mohammed', $userEmail = 'abdallah.r660@gmail.com'): void
    {
        $this->providerUser
            ->shouldReceive('getId')->andReturn($this->accountId = rand(9, 9999))
            ->shouldReceive('getName')->andReturn($this->userName = $userName)
            ->shouldReceive('getEmail')->andReturn($this->userEmail = $userEmail)
            ->shouldReceive('getAvatar')->andReturn($this->userAvatar = 'https://en.gravatar.com' . $this->userEmail)
            ->shouldReceive('expiresIn')->andReturn(3600)
            ->shouldReceive('token')->andReturn($this->accessToken = Str::random())
            ->shouldReceive('refreshToken')->andReturn('mock-refresh-token')
            ->shouldReceive('tokenSecret')->andReturn('mock-token-secret')
            ->shouldReceive('approvedScopes')->andReturn(['scope1', 'scope2']);

        Socialite::shouldReceive('driver->stateless->userFromToken')->andReturn($this->providerUser);
    }

    /**
     * Create a social user with mocked socialite user.
     *
     * @return User
     */
    private function createUser(): User
    {
        $user = User::factory()->create([
            'name' => $this->userName,
            'email' => $this->userEmail,
            'image' => $this->userAvatar,
        ]);

        $user->createSocialAccount([
            'provider' => 'google',
            'provider_account_id' => $this->accountId,
            'refresh_token' => 'mock-refresh-token',
            'access_token' => $this->accessToken,
            'scope' => 'scope1,scope2',
            'oauth_token_secret' => 'mock-token-secret',
            'oauth_token' => 'mock-oauth-token',
            'refresh_token_expires_in' => 3600,
            'expires_at' => 3600,
        ]);

        return $user;
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that we cannot pass an empty name to update request.
     */
    public function test_profile_update_validation(): void
    {
        $response = $this->actingAs(User::factory()->createOne())
            ->putJson(route('api.profile.update'), [
                'name' => '',
            ]);

        $response->assertJsonValidationErrors('name');
    }

    /**
     * A basic feature test example.
     */
    public function test_user_can_update_profile_name(): void
    {
        $response = $this->actingAs($user = User::factory()->createOne())
            ->putJson(route('api.profile.update'), [
                'name' => 'Mustafa Mahmoud'
            ]);

        $response->assertJson([
            'success' => true,
        ]);

        $user->refresh();

        $this->assertEquals($user->name, 'Mustafa Mahmoud');
    }
}

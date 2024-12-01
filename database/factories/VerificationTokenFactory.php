<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VerificationToken>
 */
class VerificationTokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payload' => base64_encode(fake()->safeEmail()),
            'token' => Str::random(32),
            'ip_address' => '127.0.0.1',
        ];
    }

    /**
     * Indicate that the model's expires at.
     */
    public function expires(int $minutes = 15): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Indicate that the model's expired.
     */
    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'expires_at' => now()->subMinute(),
        ]);
    }
}
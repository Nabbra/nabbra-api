<?php

namespace App\Models\Helpers;

use App\Models\User;
use Illuminate\Support\Str;

trait VerificationTokenHelpers
{
    /**
     * Store or generate a new token for a specific email.
     *
     * @return self
     */
    public static function createForEmail(string $email, string $ip_address, string $token = null): self
    {
        return self::create([
            'ip_address' => $ip_address,
            'payload' => base64_encode($email),
            'token' => $token ?? Str::uuid(),
            'expires_at' => now()->addMinutes(10),
        ]);
    }

    /**
     * Get verification token.
     *
     * @return self|null
     */
    public static function getTokenBy(string $token, string $ip_address = null): ?self
    {
        return self::where('token', $token)
            ->where('ip_address', $ip_address)
            ->first();
    }

    /**
     * Decode email.
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return base64_decode($this->payload);
    }

    /**
     * Generate a passwordless link to be redirected to.
     *
     * @return string
     */
    public function generateLink(): string
    {
        return env('FRONTEND_URL').'/auth/login?token='.$this->token;
    }

    /**
     * Determine whether the token is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return !!$this->expires_at->isPast();
    }

    /**
     * Reterive user based on email.
     *
     * @return User|null
     */
    public function getUserByEmail(): ?User
    {
        return User::where('email', $this->getEmail())->first();
    }
}
<?php

namespace App\Models\Helpers;

use App\Models\Audiogram;
use Illuminate\Support\Arr;

trait UserHelpers
{
    /**
     * Attach social account to current user.
     *
     * @return void
     */
    public function createSocialAccount(array $data): void
    {
        $data = Arr::only($data, [
            'provider',
            'provider_account_id',
            'refresh_token',
            'access_token',
            'scope',
            'oauth_token_secret',
            'oauth_token',
            'refresh_token_expires_in',
            'expires_at',
        ]);

        $this->accounts()->create(array_merge($data, [
            'type' => 'oauth',
            'token_type' => 'Bearer',
        ]));
    }

    /**
     * Determine whether the user has social account.
     *
     * @return bool
     */
    public function hasSocialAccount($provider, $providerAccountId): bool
    {
        return $this->accounts()
            ->where('provider', $provider)
            ->where('provider_account_id', $providerAccountId)
            ->exists();
    }

    /**
     * Get the access token currently associated with the user. Create a new.
     *
     * @param string|null $device
     * @return string
     */
    public function createTokenForDevice($device = null)
    {
        $device = $device ?: 'Unknown Device';

        $this->tokens()->where('name', $device)->delete();

        return $this->createToken($device)->plainTextToken;
    }

    /**
     * Determine whether the user's name is default username.
     *
     * @return bool
     */
    public function isDefaultName(): bool
    {
        return $this->name === env('APP_NAME').' User';
    }

    /**
     * Create or get audiogram values for specific ear type.
     *
     * @return \App\Models\Audiogram
     */
    public function createOrGetAudiogramForEar(string $type, array $data): Audiogram
    {
        return $this->audiograms()->firstOrCreate(
            ['type' => $type],
            ['type' => $type, 'freqs' => $data]
        );
    }

    /**
     * @return \App\Models\Audiogram
     */
    public function createOrGetAudiogramForRightEar(array $data): Audiogram
    {
        return $this->createOrGetAudiogramForEar(Audiogram::RIGHT, $data);
    }

    /**
     * @return \App\Models\Audiogram
     */
    public function createOrGetAudiogramForLeftEar(array $data): Audiogram
    {
        return $this->createOrGetAudiogramForEar(Audiogram::LEFT, $data);
    }
}

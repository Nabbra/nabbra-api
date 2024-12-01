<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SocialLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Authenticate a social auth user.
     */
    public function __invoke(SocialLoginRequest $request, string $provider)
    {
        $providerUser = Socialite::driver($provider)
            ->stateless()
            ->userFromToken($request->input('token'));

        $user = User::query()
            ->where('email', $providerUser->getEmail())
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'image' => $providerUser->getAvatar(),
                'email_verified_at' => now(),
                'last_activity_at' => now(),
            ]);
        }

        if (! $user->hasSocialAccount($provider, $providerUser->getId())) {
            $user->createSocialAccount([
                'provider' => $provider,
                'provider_account_id' => $providerUser->getId(),
                'refresh_token' => $providerUser->refreshToken,
                'access_token' => $providerUser->token,
                'refresh_token_expires_in' => $providerUser->expiresIn,
                'scope' => implode(',', $providerUser->approvedScopes ?? []),
                'oauth_token_secret' => $providerUser->tokenSecret,
                'oauth_token' => $providerUser->token,
                'expires_at' => $providerUser->expiresIn,
            ]);
        }

        return (new UserResource($user))->additional([
            'token' => $user->createTokenForDevice($request->input('device')),
        ]);
    }
}

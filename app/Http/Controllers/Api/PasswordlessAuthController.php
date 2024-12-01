<?php

namespace App\Http\Controllers\Api;

use App\Events\VerificationTokenCreatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordlessLoginRequest;
use App\Http\Requests\Auth\PasswordlessVerifyRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Validation\ValidationException;

class PasswordlessAuthController extends Controller
{
    /**
     * Generate and save token for passwordless authentication.
     */
    public function sendToken(PasswordlessLoginRequest $request)
    {
        $token = VerificationToken::createForEmail(
            $request->input('email'),
            $request->ip()
        );

        VerificationTokenCreatedEvent::dispatch($token);

        return response()->json([
            'success' => true,
            'message' => trans('auth.passwordless.success'),
        ]);
    }

    /**
     * Verify passwordless token and authenticate.
     */
    public function verifyToken(PasswordlessVerifyRequest $request)
    {
        $token = VerificationToken::getTokenBy($request->input('token'), $request->ip());

        if (! $token || $token->isExpired()) {
            throw ValidationException::withMessages([
                'token' => [trans('auth.passwordless.token_invalid')],
            ]);
        }

        $user = $token->getUserByEmail();

        if (!$user) {
            $user = User::create([
                'name' => env('APP_NAME').' User',
                'email' => $email = $token->getEmail(),
                'email_verified_at' => now(),
                'image' => 'https://gravatar.com/'.md5($email),
                'last_activity_at' => now(),
            ]);
        }

        $token->delete();

        return (new UserResource($user))->additional([
            'token' => $user->createTokenForDevice($request->input('device')),
        ]);
    }
}

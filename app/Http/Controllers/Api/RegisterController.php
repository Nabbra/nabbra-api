<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Create a new user and save it.
     */
    public function __invoke(RegisterRequest $request)
    {
        $user = User::create(array_merge($request->validated(), [
            'last_activity_at' => now(),
            'email_verified_at' => now(),
        ]));

        return (new UserResource($user))->additional([
            'token' => $user->createTokenForDevice($request->input('device')),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;

class LoginController extends Controller
{
    /**
     * Login user using email and password.
     */
    public function __invoke(LoginRequest $request)
    {
        $request->authenticate();

        return (new UserResource($user = $request->user()))->additional([
            'token' => $user->createTokenForDevice($request->input('device')),
        ]);
    }
}

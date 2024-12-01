<?php

# Innovation creativity (30%)
# Implementation (25%)
# UX (20%)

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Show current profile data.
     */
    public function show()
    {
        return new UserResource(auth()->user());
    }

    /**
     * Update current user profile.
     */
    public function update(ProfileRequest $request)
    {
        auth()->user()->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('users.messages.updated'),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        return UserResource::make($user);

    }

    public function update(UpdateUserRequest $request)
    {
        /** @var User $user */
        $user = $request->user();
        $user->update($request->validated());

        return UserResource::make($user);
    }
}

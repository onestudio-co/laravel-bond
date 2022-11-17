<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function anonymous()
    {
        $user = User::query()->create([
            'is_anonymous' => true,
            'password'     => Str::random(),
        ]);
        return UserResource::make($user)->withToken();
    }

    public function login(LoginRequest $request)
    {
        $user = User::query()->where('email', $request->email)
            ->firstOrFail();

        if (Hash::check($request->password, $user->password)) {
            return UserResource::make($user)->withToken();
        }
        return response()->json([
            'code'    => 'failed_login',
            'message' => __('auth.failed'),
        ], 403);
    }
}
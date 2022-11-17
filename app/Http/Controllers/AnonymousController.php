<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Str;

class AnonymousController extends Controller
{
    public function __invoke()
    {
        $user = User::query()->create([
            'is_anonymous' => true,
            'password' => Str::random(),
        ]);
        return UserResource::make($user)
            ->additional(['meta' => [
                'token' => $user->createToken('test')->plainTextToken,
            ]]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function store(UserRegisterRequest $request)
    {
        $user = User::create($request->validated());

        return UserResource::make($user)->withToken();
    }
}
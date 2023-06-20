<?php

namespace App\Http\Controllers;

use App\Models\SocialLogin;
use App\Http\Requests\SocialLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Laravel\Socialite\AbstractUser as SocialUser;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function __invoke(SocialLoginRequest $request)
    {
        $provider = $request->input('provider');
        $token = $request->input('token');

        /** @var SocialUser $socialUser */
        $socialUser = Socialite::driver($provider)->userFromToken($token);

        abort_unless($socialUser !== null, 401, __('auth.failed'));

        $user = User::query()->firstOrCreate([
            'email' => $socialUser->getEmail(),
        ], [
            'name' => $socialUser->getName(),
            'email_verified_at' => now(),
        ]);

        SocialLogin::query()->updateOrCreate([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
        ], [
            'last_token' => $token,
            'email' => $socialUser->email,
            'user_id' => $user->id,
        ]);

        return UserResource::make($user)->withToken('social');
    }
}

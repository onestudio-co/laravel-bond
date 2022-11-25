<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\AbstractUser as SocialUser;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'provider' => 'required|string',
            'token' => 'required|string',
        ]);

        $provider = $request->input('provider');
        $token = $request->input('token');

        /**@var SocialUser $socialUser */
        $socialUser = Socialite::driver($provider)->userFromToken($token);

        abort_unless((bool)$socialUser, 401, __('auth.failed'));

        $user = User::firstOrCreate([
            'email' => $socialUser->getEmail(),
        ], [
            'name' => $socialUser->getName(),
            'email_verified_at' => now(),
        ]);

        $user->socialLogins()->updateOrCreate([
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

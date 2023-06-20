<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialUser;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_social()
    {
        $socialUser = app(SocialUser::class)->map([
            'id' => 1,
            'email' => 'test@test.com',
            'name' => 'Mohamed Wh',
            'token' => 'fake_token',
        ]);

        Socialite::shouldReceive('driver->userFromToken')->andReturn($socialUser);

        $this->postJson('api/users/social-login', [
            'provider' => 'google_jwt',
            'token' => $socialUser->token,
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'email',
                    'name',
                    'is_anonymous',
                ],
                'meta' => [
                    'token',
                ],
            ])
            ->assertJson([
                'data' => [
                    'email' => 'test@test.com',
                    'name' => 'Mohamed Wh',
                    'is_anonymous' => false,
                ],
            ]);
        $this->assertDatabaseHas('social_logins', [
            'user_id' => User::firstWhere('email', 'test@test.com')->id,
            'provider' => 'google_jwt',
            'provider_id' => 1,
            'email' => 'test@test.com',
            'last_token' => 'fake_token',
        ]);
    }
}

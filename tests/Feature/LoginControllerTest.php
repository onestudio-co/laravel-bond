<?php

namespace Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_anonymosly()
    {
        $this->postJson('api/users/anonymous-login')
            ->assertSuccessful()
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'is_anonymous',
                    ],
                    'meta' => [
                        'token',
                    ],
                ]
            )
            ->assertJson([
                'data' => [
                    'id'           => 1,
                    'name'         => null,
                    'is_anonymous' => true,
                ],
            ]);
    }

    public function test_authenticated_user_cannot_login_anonymosly()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user)
            ->postJson('api/users/anonymous-login')
            ->assertForbidden();
    }

    public function test_user_can_login()
    {
        UserFactory::new()
            ->create([
                'email'    => 'test@test.com',
                'password' => '123456',
            ]);

        $this->post('api/users/login', [
            'email'    => 'test@test.com',
            'password' => '123456',
        ])
            ->assertSuccessful()
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'is_anonymous',
                    ],
                    'meta' => [
                        'token',
                    ],
                ]
            );
    }

    public function test_user_can_not_login_with_worng_password()
    {
        UserFactory::new()
            ->create([
                'email'    => 'test@test.com',
                'password' => '123456',
            ]);

        $this->post('api/users/login', [
            'email'    => 'test@test.com',
            'password' => 'wrong_password',
        ])->assertForbidden()
            ->assertJson([
                'code' => 'failed_login',
            ]);
    }
}
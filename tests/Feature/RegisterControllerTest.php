<?php

namespace Tests\Feature;

use App\Http\Requests\UserRegisterRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\Rule;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_authonticated_user_can_register()
    {
        $this->postJson('api/users/register', [
            'name' => 'salah',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSuccessful()
            ->assertJsonStructure([
                'meta' => [
                    'token',
                ],
            ])
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'name' => 'salah',
                    'email' => 'test@test.com',
                    'is_anonymous' => false,
                ],
            ]);
    }

    public function test_password_confirmation_register()
    {
        $this->postJson('api/users/register', [
            'name' => 'salah',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'wrong_password',
        ])->assertJsonValidationErrorFor('password');
    }

    public function test_registration_validation_roles()
    {
        $this->assertEquals(
            [
                'name' => [
                    'nullable',
                    'string',
                ],
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email'),
                ],
                'password' => [
                    'required',
                    'confirmed',
                    'min:6',
                ],
            ], (new UserRegisterRequest)->rules());
    }
}

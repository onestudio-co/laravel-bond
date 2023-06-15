<?php

namespace Tests\Feature;

use App\Http\Requests\UserRegisterRequest;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function test_cannot_register_with_existing_email_of_active_user()
    {
        UserFactory::new()->create([
            'email' => 'existing@test.com',
        ]);

        $response = $this->postJson('api/users/register', [
            'name' => 'New User',
            'email' => 'existing@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $this->assertEquals(
            __('validation.active_user', [
                'attribute' => 'email',
            ]),
            $response['errors']['email'][0]
        );
    }

    public function test_can_register_with_email_of_soft_deleted_user()
    {
        $this->markTestIncomplete('The way the soft-delete is made is not correct and it will be reprogrammed');
        UserFactory::new()->create([
            'name' => 'Deleted User',
            'email' => 'deleted@test.com',
            'password' => Hash::make('password'),
        ])->delete();

        $this->postJson('api/users/register', [
            'name' => 'New User',
            'email' => 'deleted@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta' => [
                    'token',
                ],
            ])
            ->assertJson([
                'data' => [
                    'id' => 2,  // The new user ID will be 2.
                    'name' => 'New User',
                    'email' => 'deleted@test.com',
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

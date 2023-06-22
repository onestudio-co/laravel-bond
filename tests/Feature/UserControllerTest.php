<?php

namespace Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_show_profile()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user)
            ->getJson("api/user/1")
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                ]
            ]);
    }

    public function test_user_can_update_profile()
    {
        $user = UserFactory::new()->create([
            'name' => 'Hala Abu Salim',
            'email' => 'hala@test.com'
        ]);

        $this->actingAs($user)
            ->putJson('api/user/update-profile', [
                'name' => 'hala salim',
                'email' => 'test@test.com',
            ])
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'name' => 'hala salim',
                    'email' => 'test@test.com'
                ],
            ]);
    }
}

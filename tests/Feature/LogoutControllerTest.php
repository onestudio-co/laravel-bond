<?php

namespace Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_user_can_logout()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user)
            ->postJson('api/users/logout')
            ->assertSuccessful();
    }
}

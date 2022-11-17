<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnonymousControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_anonymosly()
    {
        $this->post('api/users/anonymous-login')
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
}
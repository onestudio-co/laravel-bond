<?php

namespace Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $token = $user->createToken('user')->plainTextToken;

        $this->actingAs($user)
            ->postJson('api/logout', [], ['Authorization' => 'Bearer '.$token])
            ->assertSuccessful();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => 'App\Models\User',
            'name' => 'user',
            'token' => hash('sha256', $token),
        ]);
    }

    public function  test_user_can_delete_his_account(){

        $user = UserFactory::new()->create([
            'name' => 'test user',
            'email' => 'test@user.com',
        ]);

        $this->actingAs($user)
            ->post('api/delete-account')
            ->assertSuccessful()
            ->assertJson([
                'message' => __('account deleted successfully')
            ]);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }
}

<?php

namespace Tests\Feature\Profile;

use Tests\Feature\Components\AuthCase;
use Tests\TestCase;

class UpdateUserCanLoginWithNewPasswordTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function superadmin_update_their_password()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.profile.password'), [
            'password' => '54321',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function superadmin_can_not_login_with_previous_password()
    {
        $response = $this->postJson(route('api.auth.login'), [
            'email' => 'superadmin@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function superadmin_can_login_with_new_password()
    {
        $response = $this->postJson(route('api.auth.login'), [
            'email' => 'superadmin@mailinator.com',
            'password' => '54321',
        ]);

        $response->assertOk();
    }
}

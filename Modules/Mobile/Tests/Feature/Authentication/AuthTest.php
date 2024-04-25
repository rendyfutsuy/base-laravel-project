<?php

namespace Modules\Mobile\Tests\Feature\Authentication;

use Tests\TestCase;

class AuthTest extends TestCase
{
    private function credential($email, $pass): array
    {
        return [
            'email' => $email,
            'password' => $pass,
        ];
    }

    /**
     * A basic test example.
     *
     * @return void
     */

    /** @test */
    public function user_can_login()
    {
        // dd(route('api.authentication.mobile.login'));
        $response = $this->postJson(route('api.authentication.mobile.login'),
            $this->credential('rendy@mailinator.com', '12345'));

        $response->assertStatus(200);
    }

    /** @test */
    public function user_cannot_login_with_un_validated_email()
    {
        $response = $this->postJson(route('api.authentication.mobile.login'),
            $this->credential('invalid.email@mailinator.com', '12345'));

        $response->assertStatus(400);
    }

    /** @test */
    public function user_cannot_login_with_if_inactive()
    {
        $response = $this->postJson(route('api.authentication.mobile.login'),
            $this->credential('non-active@mailinator.com', '12345'));

        $response->assertStatus(400);
    }

    /** @test */
    public function user_cannot_login_with_invalid_email()
    {
        $response = $this->postJson(route('api.authentication.mobile.login'),
            $this->credential('bukan@mailinator.com', '12345'));

        $response->assertStatus(400);
    }

    /** @test */
    public function user_cannot_login_with_invalid_pin()
    {
        $response = $this->postJson(route('api.authentication.mobile.login'),
            $this->credential('rendy@mailinator.com', '11111'));

        $response->assertStatus(400);
    }

    /** @test */
    public function email_required()
    {
        $response = $this->postJson(route('api.authentication.mobile.login'),
            $this->credential(null, '12345'));

        $response->assertStatus(400);
    }

    /** @test */
    public function pin_required()
    {
        $response = $this->postJson(route('api.authentication.mobile.login'),
            $this->credential('rendy@mailinator.com', null));

        $response->assertStatus(400);
    }

    /** @test */
    public function unauthenticated_user_cannot_login()
    {
        $this->postJson(route('api.authentication.mobile.login'), [
            'email' => 'unauth@mailinator.com',
            'password' => '22222',
        ])->assertStatus(400);
    }

    /** @test */
    public function user_can_logout()
    {
        $authenticatedUser = $this->postJson(route('api.authentication.mobile.login'),
            $this->credential('rendy@mailinator.com', '12345'));
        $userToken = $authenticatedUser->json()['data']['access_token'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$userToken,
        ])->postJson(route('api.authentication.mobile.logout'));

        $response->assertStatus(200);
    }
}

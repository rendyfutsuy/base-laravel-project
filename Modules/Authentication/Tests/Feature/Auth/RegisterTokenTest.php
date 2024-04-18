<?php

namespace Modules\Authentication\Tests\Feature\Auth;

use Tests\TestCase;
use Modules\Authentication\Tests\Feature\Helpers\AuthToken;

class RegisterTokenTest extends TestCase
{
    use AuthToken;

    /** @test */
    public function user_can_register_with_otp_email_sent()
    {
        $this->getRegisterOTPToken('test.user.register@mailinator.com', 'userApp123!');
    }

    /** @test */
    public function user_cant_register_because_email_has_already_been_taken()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.register'), [
            'name' => 'random name',
            'email' => 'test.user.register@mailinator.com',
            'password' => 'userApp123!',
            'password_confirmation' => 'userApp123!',
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function user_cant_register_because_password_not_same_confirm_password()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.register'), [
            'name' => 'random name',
            'email' => 'test.user.register@mailinator.com',
            'password' => 'userApp123!',
            'password_confirmation' => 'userApp123x',
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function user_cant_register_because_email_is_required()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.register'), [
            'email' => null,
            'password' => 'userApp123!',
            'password_confirmation' => 'userApp123!',
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function user_cant_register_because_password_is_required()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.register'), [
            'name' => 'random name',
            'email' => 'test.user.register@mailinator.com',
            'password' => null,
            'password_confirmation' => null,
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function register_must_use_post_method()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->getJson(route('api.authentication.register'), [
            'name' => 'random name',
            'email' => 'test.user.register@mailinator.com',
            'password' => 'userApp123!',
        ]);

        // Method Not Allowed
        $response->assertStatus(405);
    }
}

<?php

namespace Modules\Mobile\Tests\Feature\Authentication\Helpers;

use Illuminate\Support\Facades\Mail;
use Modules\Authentication\Mail\MailOTP;

trait AuthToken
{
    private function getToken()
    {
        // Account User
        // username: 'staff@mailinator.com'
        // password: '12345'

        $authentication = $this->post(route('api.authentication.mobile.login'), [
            'email' => 'staff@mailinator.com',
            'password' => '12345',
        ]);

        $authentication->assertOk();
        $response = $authentication->json();

        return $response['data'];
    }

    private function getRegisterOTPToken($email, $pass)
    {
        Mail::fake();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.mobile.register'), [
            'name' => 'Random Name',
            'email' => $email,
            'password' => $pass,
            'password_confirmation' => $pass,
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'otp_token',
            ],
        ]);

        Mail::assertSent(MailOTP::class);

        return $response->original['data'];
    }
}

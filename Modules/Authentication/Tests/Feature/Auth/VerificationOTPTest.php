<?php

namespace Modules\Authentication\Tests\Feature\Auth;

use Tests\TestCase;
use Modules\Authentication\Models\OTP;
use Modules\Authentication\Tests\Feature\Helpers\AuthToken;

class VerificationOTPTest extends TestCase
{
    use AuthToken;

    /** @test */
    public function user_can_access_verification_otp_code_with_otp_token()
    {
        $authentication = $this->getRegisterOTPToken('test.user.otp@mailinator.com', 'userApp123!');

        $otp = OTP::where('otp_token', $authentication['otp_token'])->first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$authentication['otp_token'],
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.verification.otp'), [
            'code' => $otp['code'],
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token',
                'user',
            ],
        ]);
    }

    /** @test */
    public function verification_otp_code_fail_because_otp_code_not_registered()
    {
        $authentication = $this->getRegisterOTPToken('test.user.otp.false@mailinator.com', 'userApp123!');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$authentication['otp_token'],
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.verification.otp'), [
            'code' => '1234',
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function user_cant_access_verification_otp_without_otp_token()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.verification.otp'), [
            'code' => rand(1000, 9999),
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_access_resend_email_otp_for_refresh_otp_code()
    {
        $authentication = $this->getRegisterOTPToken('test.user.otp.resend.mail@mailinator.com', 'userApp123!');

        $otp = OTP::where('otp_token', $authentication['otp_token'])->first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$authentication['otp_token'],
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.verification.resend.email.otp'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'otp_token',
            ],
        ]);
    }
}

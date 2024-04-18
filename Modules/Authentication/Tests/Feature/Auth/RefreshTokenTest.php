<?php

namespace Modules\Authentication\Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use Modules\Authentication\Tests\Feature\Helpers\AuthToken;
use Modules\Authentication\Http\Services\Traits\RefreshTokenTrait;

class RefreshTokenTest extends TestCase
{
    use AuthToken, RefreshTokenTrait;

    /** @test */
    public function user_can_get_refresh_token_with_valid_token()
    {
        $authentication = $this->getToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$authentication['refresh_token'],
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.refresh.token'));

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
    public function user_can_get_refresh_token_with_valid_token_and_token_from_other_device_will_not_be_revoked()
    {
        $authentication = $this->getToken();

        $otherDeviceOauth = User::where('email', 'staff@mailinator.com')->first()
            ->createToken(config('passport.token'));

        $otherDeviceRefreshTokenID = $this->refreshToken($otherDeviceOauth->token->id);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$authentication['refresh_token'],
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.refresh.token'));

        $response->assertOk();
        $otherDeviceRefreshToken = Passport::refreshToken()->find($otherDeviceRefreshTokenID);
        $otherDeviceOauth = Passport::token()->find($otherDeviceOauth->token->id);

        $this->assertEquals(false, $otherDeviceRefreshToken->revoked);
        $this->assertEquals(false, $otherDeviceOauth->revoked);

        $myDeviceRefreshToken = Passport::refreshToken()->find($authentication['refresh_token']);
        $myDeviceOauth = Passport::token()->find($myDeviceRefreshToken->access_token_id);

        $this->assertEquals(true, $myDeviceRefreshToken->revoked);
        $this->assertEquals(true, $myDeviceOauth->revoked);
    }

    /** @test */
    public function user_can_refresh_token_after_user_revoke_access_token()
    {
        $authentication = $this->getToken();

        $userLogin = auth()->loginUsingId($authentication['id']);

        $tokens = User::find($authentication['id'])->tokens;
        foreach ($tokens as $token) {
            $token->revoke();
        }

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$authentication['refresh_token'],
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.refresh.token'));

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
    public function user_cant_refresh_token_if_token_invalid()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.'thisIsIdTokenFromAccessToken',
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.refresh.token'));

        $response->assertStatus(400);
    }
}

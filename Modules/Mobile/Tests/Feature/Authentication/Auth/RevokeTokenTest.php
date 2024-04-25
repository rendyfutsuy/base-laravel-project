<?php

namespace Modules\Mobile\Tests\Feature\Authentication\Auth;

use Tests\TestCase;
use Tests\Feature\Components\AuthCase;

class RevokeTokenTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function user_can_revoke_token_with_send_access_token()
    {
        $authentication = $this->getAuthenticate('staff@mailinator.com', 'userApp123!');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$authentication['access_token'],
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.mobile.revoke.token'));

        $response->assertOk();
    }

    /** @test */
    public function user_cant_revoke_token_without_access_token()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.mobile.revoke.token'));

        $response->assertStatus(401);
    }
}

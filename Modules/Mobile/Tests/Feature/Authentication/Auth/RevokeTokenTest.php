<?php

namespace Modules\Mobile\Tests\Feature\Authentication\Auth;

use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Tests\Feature\Components\MockAuthHelper;

class RevokeTokenTest extends TestCase
{
    use MockAuthHelper;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function user_can_revoke_token_with_send_access_token()
    {
        // Mock user authentication
        $userMock = $this->mockUser('staff@mailinator.com', 'Staff User', 'user-id-123');

        // Mock Passport token
        $passportTokenMock = Mockery::mock();
        $passportTokenMock->id = 'token-id-123';
        $passportTokenMock->shouldReceive('revoke')->once()->andReturn(true);

        // Mock user tokens collection
        $tokensCollection = collect([$passportTokenMock]);
        $userMock->shouldReceive('getAttribute')
            ->with('tokens')
            ->andReturn($tokensCollection);

        $userMock->tokens = $tokensCollection;

        // Use Passport::actingAs for authentication (this creates a valid token)
        Passport::actingAs($userMock, ['*'], 'api');

        // Mock Passport::refreshToken() query builder
        $passportRefreshTokenQuery = Mockery::mock();
        $passportRefreshTokenQuery->shouldReceive('where')
            ->with('access_token_id', Mockery::any())
            ->andReturnSelf();

        $passportRefreshTokenQuery->shouldReceive('update')
            ->with(['revoked' => true])
            ->andReturn(true);

        // Mock Passport::refreshToken() static method using instance
        $this->app->instance('passport.refreshToken', $passportRefreshTokenQuery);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.mobile.revoke.token'));

        $response->assertOk();
    }

    #[Test]
    public function user_cant_revoke_token_without_access_token()
    {
        // No authentication - should return 401
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.mobile.revoke.token'));

        $response->assertStatus(401);
    }
}

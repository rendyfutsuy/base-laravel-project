<?php

namespace Modules\Mobile\Tests\Feature\Authentication;

use Mockery;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Tests\Feature\Components\MockAuthHelper;
use Modules\Authentication\Http\Services\AuthenticationService;

class UpdateUserCanLoginWithNewPasswordTest extends TestCase
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

    /** @test */
    public function superadmin_update_their_password()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.authentication.mobile.profile.password']
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.authentication.mobile.profile.password'), [
            'password' => '54321',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function superadmin_can_not_login_with_previous_password()
    {
        // Mock AuthenticationService to throw error for old password
        $authServiceMock = Mockery::mock(AuthenticationService::class);

        $authServiceMock->shouldReceive('login')
            ->once()
            ->andThrow(new \Exception('Email or PIN is Wrong'));

        $this->app->instance(AuthenticationService::class, $authServiceMock);

        $response = $this->postJson(route('api.authentication.mobile.login'), [
            'email' => 'superadmin@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function superadmin_can_login_with_new_password()
    {
        // Mock AuthenticationService for successful login with new password
        $authServiceMock = Mockery::mock(AuthenticationService::class);
        $userMock = $this->mockUser('superadmin@mailinator.com', 'Superadmin', 'user-id-123');

        $authServiceMock->shouldReceive('login')
            ->once()
            ->andReturn($userMock);

        $this->app->instance(AuthenticationService::class, $authServiceMock);

        $response = $this->postJson(route('api.authentication.mobile.login'), [
            'email' => 'superadmin@mailinator.com',
            'password' => '54321',
        ]);

        $response->assertOk();
    }
}

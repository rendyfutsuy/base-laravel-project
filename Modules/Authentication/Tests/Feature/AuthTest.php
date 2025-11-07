<?php

namespace Modules\Authentication\Tests\Feature;

use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use Tests\Feature\Components\MockAuthHelper;
use Modules\Authentication\Http\Services\AuthenticationService;

class AuthTest extends TestCase
{
    use MockAuthHelper;

    private function credential($email, $pass): array
    {
        return [
            'email' => $email,
            'password' => $pass,
        ];
    }

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
    public function user_can_login()
    {
        // Mock AuthenticationService
        $authServiceMock = Mockery::mock(AuthenticationService::class);
        $userMock = $this->mockUser('rendy@mailinator.com', 'Rendy', 'user-id-123');

        $authServiceMock->shouldReceive('login')
            ->once()
            ->with(Mockery::on(function ($params) {
                return $params['email'] === 'rendy@mailinator.com'
                    && $params['password'] === '12345';
            }))
            ->andReturn($userMock);

        $this->app->instance(AuthenticationService::class, $authServiceMock);

        $response = $this->postJson(route('api.authentication.login'),
            $this->credential('rendy@mailinator.com', '12345'));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_cannot_login_with_un_validated_email()
    {
        // Mock AuthenticationService to throw error
        $authServiceMock = Mockery::mock(AuthenticationService::class);

        $authServiceMock->shouldReceive('login')
            ->once()
            ->andThrow(new \Exception('Email or PIN is Wrong'));

        $this->app->instance(AuthenticationService::class, $authServiceMock);

        $response = $this->postJson(route('api.authentication.login'),
            $this->credential('invalid.email@mailinator.com', '12345'));

        $response->assertStatus(400);
    }

    #[Test]
    public function user_cannot_login_with_if_inactive()
    {
        // Mock AuthenticationService to throw error
        $authServiceMock = Mockery::mock(AuthenticationService::class);

        $authServiceMock->shouldReceive('login')
            ->once()
            ->andThrow(new \Exception('Your account is not activated. Please contact Customer Service for more detail.'));

        $this->app->instance(AuthenticationService::class, $authServiceMock);

        $response = $this->postJson(route('api.authentication.login'),
            $this->credential('non-active@mailinator.com', '12345'));

        $response->assertStatus(400);
    }

    #[Test]
    public function user_cannot_login_with_invalid_email()
    {
        // Mock AuthenticationService to throw error
        $authServiceMock = Mockery::mock(AuthenticationService::class);

        $authServiceMock->shouldReceive('login')
            ->once()
            ->andThrow(new \Exception('Email or PIN is Wrong'));

        $this->app->instance(AuthenticationService::class, $authServiceMock);

        $response = $this->postJson(route('api.authentication.login'),
            $this->credential('bukan@mailinator.com', '12345'));

        $response->assertStatus(400);
    }

    #[Test]
    public function user_cannot_login_with_invalid_pin()
    {
        // Mock AuthenticationService to throw error
        $authServiceMock = Mockery::mock(AuthenticationService::class);

        $authServiceMock->shouldReceive('login')
            ->once()
            ->andThrow(new \Exception('Email or PIN is Wrong'));

        $this->app->instance(AuthenticationService::class, $authServiceMock);

        $response = $this->postJson(route('api.authentication.login'),
            $this->credential('rendy@mailinator.com', '11111'));

        $response->assertStatus(400);
    }

    #[Test]
    public function email_required()
    {
        // Validation error - no need to mock service
        $response = $this->postJson(route('api.authentication.login'),
            $this->credential(null, '12345'));

        $response->assertStatus(400);
    }

    #[Test]
    public function pin_required()
    {
        // Validation error - no need to mock service
        $response = $this->postJson(route('api.authentication.login'),
            $this->credential('rendy@mailinator.com', null));

        $response->assertStatus(400);
    }

    #[Test]
    public function unauthenticated_user_cannot_login()
    {
        // Mock AuthenticationService to throw error
        $authServiceMock = Mockery::mock(AuthenticationService::class);

        $authServiceMock->shouldReceive('login')
            ->once()
            ->andThrow(new \Exception('Email or PIN is Wrong'));

        $this->app->instance(AuthenticationService::class, $authServiceMock);

        $this->postJson(route('api.authentication.login'), [
            'email' => 'unauth@mailinator.com',
            'password' => '22222',
        ])->assertStatus(400);
    }

    #[Test]
    public function user_can_logout()
    {
        // Mock AuthenticationService for logout
        $authServiceMock = Mockery::mock(AuthenticationService::class);

        $authServiceMock->shouldReceive('logout')
            ->once();

        $this->app->instance(AuthenticationService::class, $authServiceMock);

        // Mock user authentication
        $userMock = $this->mockUser('rendy@mailinator.com', 'Rendy', 'user-id-123');
        $this->actingAs($userMock, 'api');

        $response = $this->postJson(route('api.authentication.logout'));

        $response->assertStatus(200);
    }
}

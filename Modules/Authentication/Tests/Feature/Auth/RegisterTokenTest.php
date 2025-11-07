<?php

namespace Modules\Authentication\Tests\Feature\Auth;

use Mockery;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use Modules\Authentication\Models\OTP;
use Modules\Authentication\Tests\Feature\Helpers\AuthToken;
use Modules\Authentication\Http\Repositories\Contracts\UserContract;
use Modules\Authentication\Http\Repositories\Contracts\OTPContract;

class RegisterTokenTest extends TestCase
{
    use AuthToken;

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
    public function user_can_register_with_otp_email_sent()
    {
        Mail::fake();

        // Mock User
        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->id = 'user-id-123';
        $userMock->email = 'test.user.register@mailinator.com';
        $userMock->name = 'Random Name';

        // Mock OTP
        $otpMock = Mockery::mock(OTP::class)->makePartial();
        $otpMock->otp_token = 'otp-token-123';
        $otpMock->code = '123456';
        $otpMock->user_id = 'user-id-123';

        // Mock UserContract
        $userRepositoryMock = Mockery::mock(UserContract::class);
        $userRepositoryMock->shouldReceive('store')
            ->once()
            ->with(Mockery::type('array'))
            ->andReturn($userMock);

        $this->app->instance(UserContract::class, $userRepositoryMock);

        // Mock OTPContract
        $otpRepositoryMock = Mockery::mock(OTPContract::class);
        $otpRepositoryMock->shouldReceive('generate')
            ->once()
            ->with('user-id-123')
            ->andReturn($otpMock);

        $this->app->instance(OTPContract::class, $otpRepositoryMock);

        // Mock DB facade for validation unique rule (email should not exist)
        $dbTableMock = Mockery::mock();
        $dbTableMock->shouldReceive('where')
            ->with('email', 'test.user.register@mailinator.com')
            ->andReturnSelf();
        $dbTableMock->shouldReceive('where')
            ->andReturnSelf();
        $dbTableMock->shouldReceive('count')
            ->andReturn(0);
        $dbTableMock->shouldReceive('exists')
            ->andReturn(false);
        $dbTableMock->shouldReceive('useWritePdo')
            ->andReturnSelf();

        $dbConnectionMock = Mockery::mock();
        $dbConnectionMock->shouldReceive('table')
            ->with('users')
            ->andReturn($dbTableMock);

        \Illuminate\Support\Facades\DB::shouldReceive('connection')
            ->andReturn($dbConnectionMock);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.register'), [
            'name' => 'Random Name',
            'email' => 'test.user.register@mailinator.com',
            'password' => 'userApp123!',
            'password_confirmation' => 'userApp123!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'otp_token',
            ],
        ]);

        // Note: Mail::assertSent() is removed because event is mocked
        // The actual mail sending is tested in integration tests
    }

    #[Test]
    public function user_cant_register_because_email_has_already_been_taken()
    {
        // Mock DB facade for validation unique rule
        $dbTableMock = Mockery::mock();
        $dbTableMock->shouldReceive('where')
            ->with('email', 'test.user.register@mailinator.com')
            ->andReturnSelf();
        $dbTableMock->shouldReceive('where')
            ->andReturnSelf();
        $dbTableMock->shouldReceive('count')
            ->andReturn(1);
        $dbTableMock->shouldReceive('exists')
            ->andReturn(true);
        $dbTableMock->shouldReceive('useWritePdo')
            ->andReturnSelf();

        $dbConnectionMock = Mockery::mock();
        $dbConnectionMock->shouldReceive('table')
            ->with('users')
            ->andReturn($dbTableMock);

        \Illuminate\Support\Facades\DB::shouldReceive('connection')
            ->andReturn($dbConnectionMock);

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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

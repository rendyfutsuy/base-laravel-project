<?php

namespace Modules\Authentication\Tests\Feature\Auth;

use Mockery;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Auth;
use Modules\Authentication\Models\OTP;
use Modules\Authentication\Tests\Feature\Helpers\AuthToken;
use Modules\Authentication\Http\Repositories\Contracts\OTPContract;
use Modules\Authentication\Http\Repositories\Contracts\UserContract;
use Modules\Authentication\Http\Services\OTPServices;

class VerificationOTPTest extends TestCase
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
    public function user_can_access_verification_otp_code_with_otp_token()
    {
        // Mock User
        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->id = 'user-id-123';
        $userMock->email = 'test.user.otp@mailinator.com';
        $userMock->name = 'Test User';

        // Mock OTP for middleware (OTPTrait uses OTP::where()->where()->first())
        $otpMock = Mockery::mock(OTP::class)->makePartial();
        $otpMock->otp_token = 'otp-token-123';
        $otpMock->code = '123456';
        $otpMock->user_id = 'user-id-123';
        $otpMock->is_active = true;
        $otpMock->expired_at = Carbon::now()->addMinutes(15);

        // Mock OTP for controller
        $otpForController = Mockery::mock(OTP::class)->makePartial();
        $otpForController->code = '123456';
        $otpForController->user_id = 'user-id-123';

        // Mock Passport token
        $passportTokenMock = Mockery::mock();
        $passportTokenMock->id = 'token-id-123';
        $passportTokenMock->expires_at = Carbon::now()->addHours(1);
        $passportTokenMock->shouldReceive('getAttribute')->with('expires_at')->andReturn(Carbon::now()->addHours(1));
        $passportTokenMock->shouldReceive('__get')->with('expires_at')->andReturn(Carbon::now()->addHours(1));

        // Create a real Token instance for PersonalAccessTokenResult
        $realToken = new \Laravel\Passport\Token;
        $realToken->id = 'token-id-123';
        $realToken->expires_at = Carbon::now()->addHours(1);

        $oauthMock = Mockery::mock(\Laravel\Passport\PersonalAccessTokenResult::class)->makePartial();
        $oauthMock->accessToken = 'access-token-123';
        $oauthMock->shouldReceive('getAttribute')->with('accessToken')->andReturn('access-token-123');
        $oauthMock->shouldReceive('__get')->with('accessToken')->andReturn('access-token-123');
        $oauthMock->shouldReceive('__get')->with('token')->andReturn($realToken);
        // Set token property directly using reflection
        $reflection = new \ReflectionClass($oauthMock);
        $tokenProperty = $reflection->getProperty('token');
        $tokenProperty->setAccessible(true);
        $tokenProperty->setValue($oauthMock, $realToken);

        $userMock->shouldReceive('createToken')
            ->with(Mockery::any())
            ->andReturn($oauthMock);

        // Mock OTPContract for middleware (getByToken) and controller (getActive)
        $otpRepositoryMock = Mockery::mock(OTPContract::class);
        $otpRepositoryMock->shouldReceive('getByToken')
            ->with('otp-token-123')
            ->andReturn($otpMock);
        $otpRepositoryMock->shouldReceive('getActive')
            ->with('user-id-123', '123456')
            ->andReturn($otpForController);

        // Mock OTPContract for OTPServices (revoke)
        $otpRepositoryMock->shouldReceive('revoke')
            ->with('user-id-123', '123456')
            ->andReturn(true);

        $this->app->instance(OTPContract::class, $otpRepositoryMock);

        // Mock UserContract for middleware (find) and OTPServices (update)
        $userRepositoryMock = Mockery::mock(UserContract::class);
        $userRepositoryMock->shouldReceive('find')
            ->with('user-id-123')
            ->andReturn($userMock);
        $userRepositoryMock->shouldReceive('update')
            ->with(Mockery::type('array'), 'user-id-123')
            ->andReturn(true);

        $this->app->instance(UserContract::class, $userRepositoryMock);

        // Mock OTPServices
        $otpServicesMock = Mockery::mock(OTPServices::class);
        $otpServicesMock->shouldReceive('revokeAndUpdateEmailVerified')
            ->with('user-id-123', '123456')
            ->andReturn(true);

        $this->app->instance(OTPServices::class, $otpServicesMock);

        // Mock DB::transaction for OTPServices
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        // Mock Passport::refreshToken() for RefreshTokenTrait
        $passportRefreshTokenQuery = Mockery::mock();
        $passportRefreshTokenQuery->shouldReceive('create')
            ->with(Mockery::type('array'))
            ->andReturn((object) ['id' => 'refresh-token-id-123']);

        // Use app instance to mock Passport::refreshToken()
        $this->app->instance('passport.refreshToken', $passportRefreshTokenQuery);

        // Mock Passport::$refreshTokensExpireIn for RefreshTokenTrait
        Passport::$refreshTokensExpireIn = new \DateInterval('P1M');

        // Set user in auth before middleware calls loginUsingId
        // The middleware will call auth()->loginUsingId() which will set the user
        // We need to ensure the user is available when auth()->user() is called
        $this->actingAs($userMock, 'api');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer otp-token-123',
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.verification.otp'), [
            'code' => '123456',
        ]);

        $response->assertStatus(200);
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

    #[Test]
    public function verification_otp_code_fail_because_otp_code_not_registered()
    {
        // Mock User
        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->id = 'user-id-456';
        $userMock->email = 'test.user.otp.false@mailinator.com';
        $userMock->name = 'Test User';

        // Mock OTP for middleware (OTPTrait uses OTP::where()->where()->first())
        $otpMock = Mockery::mock(OTP::class)->makePartial();
        $otpMock->otp_token = 'otp-token-456';
        $otpMock->code = '123456';
        $otpMock->user_id = 'user-id-456';
        $otpMock->is_active = true;
        $otpMock->expired_at = Carbon::now()->addMinutes(15);

        // Mock OTPContract for middleware (getByToken) and controller (getActive)
        $otpRepositoryMock = Mockery::mock(OTPContract::class);
        $otpRepositoryMock->shouldReceive('getByToken')
            ->with('otp-token-456')
            ->andReturn($otpMock);
        $otpRepositoryMock->shouldReceive('getActive')
            ->with('user-id-456', '1234')
            ->andReturn(false);

        $this->app->instance(OTPContract::class, $otpRepositoryMock);

        // Mock UserContract for middleware (find)
        $userRepositoryMock = Mockery::mock(UserContract::class);
        $userRepositoryMock->shouldReceive('find')
            ->with('user-id-456')
            ->andReturn($userMock);

        $this->app->instance(UserContract::class, $userRepositoryMock);

        // Set user in auth before middleware calls setUser
        $this->actingAs($userMock, 'api');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer otp-token-456',
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.verification.otp'), [
            'code' => '1234',
        ]);

        $response->assertStatus(400);
    }

    #[Test]
    public function user_cant_access_verification_otp_without_otp_token()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.verification.otp'), [
            'code' => rand(1000, 9999),
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function user_can_access_resend_email_otp_for_refresh_otp_code()
    {
        // Mock User
        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->id = 'user-id-789';
        $userMock->email = 'test.user.otp.resend.mail@mailinator.com';
        $userMock->name = 'Test User';

        // Mock OTP for middleware (OTPTrait uses OTP::where()->where()->first())
        $otpMock = Mockery::mock(OTP::class)->makePartial();
        $otpMock->otp_token = 'otp-token-789';
        $otpMock->code = '123456';
        $otpMock->user_id = 'user-id-789';
        $otpMock->is_active = true;
        $otpMock->expired_at = Carbon::now()->addMinutes(15);

        // Mock OTP for resend (new OTP)
        $newOtpMock = Mockery::mock(OTP::class)->makePartial();
        $newOtpMock->otp_token = 'new-otp-token-789';
        $newOtpMock->code = '654321';
        $newOtpMock->user_id = 'user-id-789';

        // Mock OTPContract for middleware (getByToken) and controller (generate)
        $otpRepositoryMock = Mockery::mock(OTPContract::class);
        $otpRepositoryMock->shouldReceive('getByToken')
            ->with('otp-token-789')
            ->andReturn($otpMock);
        $otpRepositoryMock->shouldReceive('generate')
            ->with('user-id-789')
            ->andReturn($newOtpMock);

        $this->app->instance(OTPContract::class, $otpRepositoryMock);

        // Mock UserContract for middleware (find)
        $userRepositoryMock = Mockery::mock(UserContract::class);
        $userRepositoryMock->shouldReceive('find')
            ->with('user-id-789')
            ->andReturn($userMock);

        $this->app->instance(UserContract::class, $userRepositoryMock);

        // Set user in auth before middleware calls setUser
        $this->actingAs($userMock, 'api');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer otp-token-789',
            'Accept' => 'application/json',
        ])->postJson(route('api.authentication.verification.resend.email.otp'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'otp_token',
            ],
        ]);
    }
}

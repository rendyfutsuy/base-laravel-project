<?php

namespace Modules\Authentication\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use Laravel\Passport\RefreshTokenRepository;
use Modules\Authentication\Http\Resources\AuthResource;
use Modules\Authentication\Http\Services\AuthenticationService;
use Modules\Authentication\Http\Services\Traits\RefreshTokenTrait;
use Modules\Authentication\Http\Repositories\Contracts\OTPContract;
use Modules\Authentication\Http\Repositories\Contracts\UserContract;
use Modules\Authentication\Http\Resources\AuthenticatedUserResource;

class AuthController extends Controller
{
    use ApiResponseTrait, RefreshTokenTrait;

    private $clientRefreshToken;

    private $userRepository;

    private $otpRepository;

    private $auth;

    public function __construct(
        RefreshTokenRepository $refreshToken,
        UserContract $userRepository,
        OTPContract $otpRepository,
        AuthenticationService $auth
    ) {
        $this->clientRefreshToken = $refreshToken;
        $this->userRepository = $userRepository;
        $this->otpRepository = $otpRepository;
        $this->auth = $auth;
    }

    public function login(AuthLoginRequest $request)
    {
        try {
            $credential = $this->auth->login($request->only(['email', 'password']));
        } catch (\Throwable $th) {
            return $this->resultResponse('failed', $th->getMessage(), 400);
        }

        return new AuthResource($credential);
    }

    public function logout()
    {
        try {
            $this->auth->logout();
        } catch (\Throwable $th) {
            return $this->resultResponse('success', $th->getMessage(), 400);
        }

        return $this->resultResponse('success', 'Email Successfully Logout', 200);
    }

    /**
     * End current authentification session based on current user Bearer token.
     */
    public function revokeToken(): JsonResponse
    {
        $this->revokeTokenByUser(auth()->user());

        return $this->resultResponse('success', 'Successfully Revoked Token', 200);
    }

    /**
     * End all un-revoken authentification session based on current user Bearer token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeAllTokenByUserId(int $userId)
    {
        $user = User::find($userId);

        $this->revokeTokenByUser($user);

        return $this->resultResponse('success', 'Successfully Revoked All Tokens', 200);
    }

    /**
     * create new token for new atutentification session by refreshing current user refresh token on Bearer token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshMyToken(Request $request)
    {
        $this->revokeCurrentRefreshToken($request->bearerToken());

        $oauth = auth()->user()->createToken(config('passport.token'));

        if (! $oauth->accessToken) {
            return $this->resultResponse('failed', 'Failed to Generate Token Credential', 400);
        }

        $authenticatedUserResponse = [
            'token_type' => 'Bearer',
            'expires_in' => Carbon::parse($oauth->token->expires_at)->diffInSeconds(),
            'access_token' => $oauth->accessToken,
            'refresh_token' => $this->refreshToken($oauth->token->id),
            'user' => new AuthenticatedUserResource(auth()->user()),
        ];

        return $this->resultResponse('success', 'Successfully Logged In', 200, $authenticatedUserResponse);
    }
}

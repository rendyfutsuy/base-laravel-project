<?php

namespace Modules\Authentication\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use Modules\Authentication\Http\Resources\AuthResource;
use Modules\Authentication\Http\Services\AuthenticationService;
use Modules\Authentication\Http\Services\Traits\RefreshTokenTrait;
use Modules\Authentication\Http\Repositories\Contracts\OTPContract;
use Modules\Authentication\Http\Repositories\Contracts\UserContract;
use Modules\Authentication\Http\Resources\AuthenticatedUserResource;

class AuthController extends Controller
{
    use ApiResponseTrait, RefreshTokenTrait;

    private $userRepository;

    private $otpRepository;

    private $auth;

    public function __construct(
        UserContract $userRepository,
        OTPContract $otpRepository,
        AuthenticationService $auth
    ) {
        $this->userRepository = $userRepository;
        $this->otpRepository = $otpRepository;
        $this->auth = $auth;
    }

    /**
     * @group Authentication
     *
     * Authenticate user and receive access token.
     *
     * @bodyParam email string required The user's email address. Example: user@example.com
     * @bodyParam password string required The user's password. Example: password123
     *
     * @response 200 {
     *   "id": "user-id-123",
     *   "name": "John Doe",
     *   "email": "user@example.com",
     *   "expires_in": 3600,
     *   "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
     *   "refresh_token": "refresh-token-123"
     * }
     * @response 400 {
     *   "status": "failed",
     *   "message": "Invalid credentials",
     *   "status_code": 400
     * }
     */
    public function login(AuthLoginRequest $request)
    {
        try {
            $credential = $this->auth->login($request->only(['email', 'password']));
        } catch (\Throwable $th) {
            return $this->resultResponse('failed', $th->getMessage(), 400);
        }

        return new AuthResource($credential);
    }

    /**
     * @group Authentication
     *
     * @authenticated
     *
     * Logout the authenticated user.
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "Email Successfully Logout",
     *   "status_code": 200
     * }
     */
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
     * @group Authentication
     *
     * @authenticated
     *
     * Revoke the current access token.
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "Successfully Revoked Token",
     *   "status_code": 200
     * }
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
     * @group Authentication
     *
     * Refresh the access token using refresh token.
     *
     * @header Authorization Bearer {refresh_token}
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "Successfully Logged In",
     *   "status_code": 200,
     *   "data": {
     *     "token_type": "Bearer",
     *     "expires_in": 3600,
     *     "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
     *     "refresh_token": "new-refresh-token-123",
     *     "user": {
     *       "id": "user-id-123",
     *       "name": "John Doe",
     *       "email": "user@example.com",
     *       "roles": []
     *     }
     *   }
     * }
     * @response 400 {
     *   "status": "failed",
     *   "message": "Failed to Generate Token Credential",
     *   "status_code": 400
     * }
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

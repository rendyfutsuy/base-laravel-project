<?php

namespace Modules\Authentication\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Modules\Authentication\Events\SendMailOTP;
use Modules\Authentication\Http\Services\OTPServices;
use Modules\Authentication\Http\Services\Traits\RefreshTokenTrait;
use Modules\Authentication\Http\Repositories\Contracts\OTPContract;
use Modules\Authentication\Http\Repositories\Contracts\UserContract;
use Modules\Authentication\Http\Resources\AuthenticatedUserResource;

class VerificationController extends Controller
{
    use ApiResponseTrait, RefreshTokenTrait;

    private $otpRepository;

    private $userRepository;

    private $otpServices;

    public function __construct(
        OTPContract $otpRepository,
        UserContract $userRepository,
        OTPServices $otpServices
    ) {
        $this->otpRepository = $otpRepository;
        $this->userRepository = $userRepository;
        $this->otpServices = $otpServices;
    }

    /**
     * verify user account, with comparing verification code that user got on email.
     * it will also revoke any verification session made before.
     *
     * @param  \Illuminate\Support\Facades\Request  $resquest
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        $otp = $this->otpRepository->getActive(auth()->user()->id, $request->code);

        if (! $otp) {
            return $this->resultResponse('failed', 'OTP is invalid. Please enter correct OTP', 400);
        }

        $oauth = auth()->user()->createToken(config('passport.token'));

        if (! $oauth->accessToken) {
            return $this->resultResponse('failed', 'Failed to Generate Token Credential', 400);
        }

        $this->otpServices->revokeAndUpdateEmailVerified(auth()->user()->id, $otp->code);

        $authenticatedUserResponse = [
            'token_type' => 'Bearer',
            'expires_in' => Carbon::parse($oauth->token->expires_at)->diffInSeconds(),
            'access_token' => $oauth->accessToken,
            'refresh_token' => $this->refreshToken($oauth->token->id),
            'user' => new AuthenticatedUserResource(auth()->user()),
        ];

        return $this->resultResponse('success', 'Successfully Logged In', 200, $authenticatedUserResponse);
    }

    /**
     * resend verification email to registered email.
     * and renew verification session and code
     *
     * @param  \Illuminate\Support\Facades\Request  $resquest
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendMailOTP(Request $request)
    {
        $otp = $this->otpRepository->generate(auth()->user()->id);
        event('send.mail.otp', new SendMailOTP(auth()->user()->id, $otp->code));

        $token = [
            'otp_token' => $otp->otp_token,
        ];

        return $this->resultResponse('success', 'Successfully Send OTP', 200, $token);
    }
}

<?php

namespace Modules\Authentication\Http\Controllers;

use App\Helpers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Modules\Authentication\Events\SendMailOTP;
use Modules\Authentication\Http\Requests\RegisterRequest;
use Modules\Authentication\Http\Services\Traits\OTPTrait;
use Modules\Authentication\Http\Repositories\Contracts\OTPContract;
use Modules\Authentication\Http\Repositories\Contracts\UserContract;

class RegisterController extends Controller
{
    use ApiResponseTrait, OTPTrait;

    private $userRepository;

    private $otpRepository;

    public function __construct(UserContract $userRepository, OTPContract $otpRepository)
    {
        $this->userRepository = $userRepository;
        $this->otpRepository = $otpRepository;
    }

    /**
     * @group Authentication
     * 
     * Register a new user account. An OTP will be sent to the provided email.
     * 
     * @bodyParam name string required The user's full name. Example: John Doe
     * @bodyParam email string required The user's email address. Must be unique. Example: user@example.com
     * @bodyParam password string required The user's password. Must be at least 8 characters. Example: password123
     * @bodyParam password_confirmation string required The password confirmation. Must match the password. Example: password123
     * 
     * @response 200 {
     *   "status": "success",
     *   "message": "Successfully Send OTP",
     *   "status_code": 200,
     *   "data": {
     *     "otp_token": "otp-token-123"
     *   }
     * }
     * @response 400 {
     *   "errors": [
     *     "The email has already been taken.",
     *     "The password confirmation does not match."
     *   ]
     * }
     */
    public function register(RegisterRequest $request)
    {
        $inputData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ];

        $user = $this->userRepository->store($inputData);

        if (! $user) {
            return $this->resultResponse('failed', 'Failed to Create Account', 500);
        }

        $otp = $this->otpRepository->generate($user->id);
        event('send.mail.otp', new SendMailOTP($user->id, $otp->code));

        $token = [
            'otp_token' => $otp->otp_token,
        ];

        return $this->resultResponse('success', 'Successfully Send OTP', 200, $token);
    }
}

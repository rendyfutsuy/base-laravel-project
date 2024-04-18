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
     * Register new User to Rendy Terumi with unregistered email and password.
     * also send verification by by email. so user can verify their account if the inputted account is real.
     *
     * @param  \Modules\RAMv1\Http\Requests\RegisterValidation\RegisterRequest  $resquest
     * @return \Illuminate\Http\JsonResponse
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

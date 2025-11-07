<?php

namespace Modules\Authentication\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseTrait;
use Modules\Authentication\Http\Services\Traits\OTPTrait;
use Modules\Authentication\Http\Repositories\Contracts\OTPContract;
use Modules\Authentication\Http\Repositories\Contracts\UserContract;

class OTPValidationMiddleware
{
    use ApiResponseTrait, OTPTrait;

    private int $defaultStatus = 401;

    private $otpRepository;

    private $userRepository;

    public function __construct(OTPContract $otpRepository, UserContract $userRepository)
    {
        $this->otpRepository = $otpRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $authorization = $request->bearerToken();

        if (empty($authorization)) {
            return $this->resultResponse('failed', 'Invalid Credentials Token', $this->defaultStatus);
        }

        $otp = $this->getOTPbyToken($request->bearerToken());

        if (! $otp) {
            return $this->resultResponse('failed', 'Failed to retrieve OTP Token', 401);
        }

        $interval = now()->diff($otp->expired_at);

        if ($interval->format('%i') < 0) {
            return $this->resultResponse('failed', 'OTP Token has Expired', 400);
        }

        // Use setUser instead of loginUsingId for TokenGuard compatibility
        $user = $this->userRepository->find($otp->user_id);
        auth()->setUser($user);

        return $next($request);
    }
}

<?php

namespace Modules\Authentication\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseTrait;
use Modules\Authentication\Http\Services\Traits\RefreshTokenTrait;

class RefreshTokenMiddleware
{
    use ApiResponseTrait, RefreshTokenTrait;

    private int $defaultStatus = 401;

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $authorization = $request->bearerToken();

        if (empty($authorization)) {
            return response()->json([
                'message' => 'No Token Given',
            ], $this->defaultStatus);
        }

        $userId = $this->getUserIdByRefreshToken($request->bearerToken());

        if (! $userId) {
            return $this->resultResponse('failed', 'Failed to Generate Token Credential', 400);
        }

        auth()->loginUsingId($userId);

        return $next($request);
    }
}

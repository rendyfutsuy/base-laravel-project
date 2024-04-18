<?php

namespace Modules\Authentication\Http\Services\Traits;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Laravel\Passport\Passport;

trait RefreshTokenTrait
{
    /**
     * refresh token based on refresh token given
     *
     * @param  string  $tokenAccessId
     * @return string
     */
    public function refreshToken($tokenAccessId)
    {
        $tokenExpireIn = Passport::$refreshTokensExpireIn;
        $interval = CarbonInterval::make($tokenExpireIn)
            ->totalSeconds;

        $token = Passport::refreshToken()->create([
            'access_token_id' => $tokenAccessId,
            'id' => bcrypt(Carbon::now()),
            'revoked' => false,
            'expires_at' => $interval,
        ]);

        return $token->id;
    }

    public function getUserIdByRefreshToken($refreshToken)
    {
        $accessToken = Passport::refreshToken()->where('id', $refreshToken)->where('revoked', false)->first();
        if (! $accessToken) {
            return false;
        }

        $user = Passport::token()->find($accessToken->access_token_id);

        return $user->user_id;
    }

    protected function revokeTokenByUser(User $user)
    {
        $tokens = $user->tokens;

        foreach ($tokens as $token) {
            $this->clientRefreshToken->revokeRefreshTokensByAccessTokenId($token->id);
            $token->revoke();
        }
    }

    protected function revokeToken($refreshToken)
    {
        $this->clientRefreshToken->revokeRefreshToken($refreshToken);
    }

    protected function revokeCurrentRefreshToken($refreshToken)
    {
        $refreshToken = Passport::refreshToken()->where('id', $refreshToken)->where('revoked', false)->first();
        if (! $refreshToken) {
            return;
        }

        $refreshToken->revoked = true;
        $refreshToken->save();

        $oauth = Passport::token()->where('id', $refreshToken->access_token_id)->where('revoked', false)->first();
        if (! $oauth) {
            return;
        }

        $oauth->revoked = true;
        $oauth->save();
    }
}

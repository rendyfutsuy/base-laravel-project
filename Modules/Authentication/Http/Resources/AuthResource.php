<?php

namespace Modules\Authentication\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Authentication\Http\Services\Traits\RefreshTokenTrait;

class AuthResource extends JsonResource
{
    use RefreshTokenTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $oauth = $this->createToken(config('passport.token'));

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'expires_in' => Carbon::parse($oauth->token->expires_at)->diffInSeconds(),
            'access_token' => $oauth->accessToken,
            'refresh_token' => $this->refreshToken($oauth->token->id),
        ];
    }
}

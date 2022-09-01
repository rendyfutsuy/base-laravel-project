<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $token = $this->createToken(config('passport.token'))->accessToken;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'token' => $token,
        ];
    }
}

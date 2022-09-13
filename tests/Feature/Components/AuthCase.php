<?php

namespace Tests\Feature\Components;

use App\Models\User;

trait AuthCase
{
    protected function login($email = null)
    {
        $user = User::where('email', $email)->first();
        if (! $user) {
            return [
                'user' => null,
                'token' => null,
            ];
        }

        return [
            'user' => $user,
            'token' => $user->createToken(config('passport.token'))->accessToken,
        ];
    }
}

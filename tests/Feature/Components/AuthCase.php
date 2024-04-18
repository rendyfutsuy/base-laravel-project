<?php

namespace Tests\Feature\Components;

use App\Models\User;

trait AuthCase
{
    public function getAuthenticate(string $username, string $password)
    {
        if (request()->hasSession()) {
            request()->session()->flush();
        }

        $users = User::withoutGlobalScope('company')->get();

        foreach ($users as $user) {
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });
        }

        $auth = User::withoutGlobalScope('company')
            ->where(function ($users) use ($username) {
                $users->where('email', $username)
                    ->orWhere('name', $username);
            })->first();

        return [
            'access_token' => $auth->createToken(config('passport.token'))->accessToken,
        ];
    }

    protected function login($email = null)
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->markTestSkipped('User not found, please check your assigned user data or please try again. if its somehow work, theres something wrong with your auth flow in test case fix it yourself. :)');
            abort(500, 'User not found, please check your assigned user data or please try again. if its somehow work, theres something wrong with your auth flow in test case fix it yourself. :)');

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

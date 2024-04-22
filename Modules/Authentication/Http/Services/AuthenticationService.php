<?php

namespace Modules\Authentication\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Modules\Authentication\Http\Services\Traits\AuthBase;

class AuthenticationService
{
    use AuthBase;

    public function login($params = [])
    {
        $credentials = [
            'email' => $params['email'] ?? null,
            'password' => $params['password'] ?? null,
        ];

        // return false on any failed attempt to login
        if (! Auth::attempt($credentials)) {
            abort(500, 'Email or PIN is Wrong');
        }

        $user = auth()->user();

        // return false if Email not verified
        if (! $this->isEmailVerified($user)) {
            abort(500, 'Your email is not verified. Please check your email and verify your email address.');
        }

        // return false if user is Inactive
        if (! $this->isActivated($user)) {
            abort(500, 'Your account is not activated. Please contact Customer Service for more detail.');
        }

        // if any Additional Validation Failed, return false
        try {
            $this->isAllAdditionalValidationPassed($user);
        } catch (\Throwable $th) {
            abort(500, $th->getMessage());
        }

        // return user data if authentication success
        return $user;
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->tokens()->delete();
        } else {
            abort(500, 'Something went wrong when ending this session..');
        }
    }
}

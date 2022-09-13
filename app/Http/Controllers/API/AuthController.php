<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Services\Traits\ResultResponse;

class AuthController extends Controller
{
    use ResultResponse;

    public function login(AuthLoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (Auth::attempt($credentials)) {
            return new AuthResource(auth()->user());
        }

        return $this->resultResponse('failed', 'Email or PIN is Wrong', 400);
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->tokens()->delete();
        }

        return $this->resultResponse('success', 'Email Successfully Logout', 200);
    }
}

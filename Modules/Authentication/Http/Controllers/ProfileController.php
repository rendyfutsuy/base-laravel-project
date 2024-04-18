<?php

namespace Modules\Authentication\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Authentication\Http\Resources\UserResource;
use Modules\Authentication\Http\Repositories\Contracts\UserContract;

class ProfileController extends Controller
{
    protected $user;

    public function __construct(UserContract $user)
    {
        $this->user = $user;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        return (new UserResource(auth()->user()))->response()->setStatusCode(200);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        DB::transaction(function () use ($request) {
            $this->user->update(['password' => $request->input('password')], auth()->user()->id);
        });

        return (new UserResource(auth()->user()))->response()->setStatusCode(200);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        DB::transaction(function () use ($request) {
            $this->user->update($request->all(), auth()->user()->id);
        });

        return (new UserResource(auth()->user()))->response()->setStatusCode(200);
    }
}

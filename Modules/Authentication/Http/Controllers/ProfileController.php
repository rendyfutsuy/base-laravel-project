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
     * @group Authentication
     * @authenticated
     * 
     * Get authenticated user's profile information.
     * 
     * @response 200 {
     *   "id": "user-id-123",
     *   "name": "John Doe",
     *   "email": "user@example.com",
     *   "roles": []
     * }
     */
    public function profile()
    {
        return (new UserResource(auth()->user()))->response()->setStatusCode(200);
    }

    /**
     * @group Authentication
     * @authenticated
     * 
     * Change authenticated user's password.
     * 
     * @bodyParam password string required The new password. Example: newpassword123
     * 
     * @response 200 {
     *   "id": "user-id-123",
     *   "name": "John Doe",
     *   "email": "user@example.com",
     *   "roles": []
     * }
     */
    public function changePassword(Request $request)
    {
        DB::transaction(function () use ($request) {
            $this->user->update(['password' => $request->input('password')], auth()->user()->id);
        });

        return (new UserResource(auth()->user()))->response()->setStatusCode(200);
    }

    /**
     * @group Authentication
     * @authenticated
     * 
     * Update authenticated user's profile information.
     * 
     * @bodyParam name string The user's name. Example: Jane Doe
     * @bodyParam email string The user's email address. Example: jane@example.com
     * 
     * @response 200 {
     *   "id": "user-id-123",
     *   "name": "Jane Doe",
     *   "email": "jane@example.com",
     *   "roles": []
     * }
     */
    public function update(Request $request)
    {
        DB::transaction(function () use ($request) {
            $this->user->update($request->all(), auth()->user()->id);
        });

        return (new UserResource(auth()->user()))->response()->setStatusCode(200);
    }
}

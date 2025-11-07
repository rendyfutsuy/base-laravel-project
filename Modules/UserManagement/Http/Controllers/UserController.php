<?php

namespace Modules\UserManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Services\Traits\ResultResponse;
use Modules\UserManagement\Events\UserStored;
use Modules\UserManagement\Events\UserUpdated;
use Modules\UserManagement\Events\UserDestroyed;
use Modules\Authentication\Http\Resources\UserResource;
use Modules\Authentication\Http\Resources\UserResourceCollection;
use Modules\Authentication\Http\Repositories\Contracts\UserContract;

class UserController extends Controller
{
    use ResultResponse;

    protected $user;

    public function __construct(UserContract $user)
    {
        $this->user = $user;
    }

    /**
     * @group User Management
     *
     * @authenticated
     *
     * Get paginated list of users.
     *
     * @queryParam page integer Page number. Example: 1
     * @queryParam per_page integer Items per page. Example: 10
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": "user-id-123",
     *       "name": "John Doe",
     *       "email": "user@example.com",
     *       "roles": []
     *     }
     *   ],
     *   "links": {...},
     *   "meta": {...}
     * }
     */
    public function index()
    {
        $users = $this->user->paginated();

        return new UserResourceCollection($users);
    }

    /**
     * @group User Management
     *
     * @authenticated
     *
     * Create a new user.
     *
     * @bodyParam name string required The user's full name. Example: John Doe
     * @bodyParam email string required The user's email address. Example: user@example.com
     * @bodyParam password string required The user's password. Example: password123
     *
     * @response 200 {
     *   "id": "user-id-123",
     *   "name": "John Doe",
     *   "email": "user@example.com",
     *   "roles": []
     * }
     */
    public function store(Request $request)
    {
        $user = DB::transaction(function () use ($request) {
            return $this->user->store($request->all());
        });

        event('user.stored', new UserStored($user));

        return (new UserResource($user))->response()->setStatusCode(200);
    }

    /**
     * @group User Management
     *
     * @authenticated
     *
     * Get user details by ID.
     *
     * @urlParam id string required The user ID. Example: user-id-123
     *
     * @response 200 {
     *   "id": "user-id-123",
     *   "name": "John Doe",
     *   "email": "user@example.com",
     *   "roles": []
     * }
     */
    public function show($id)
    {
        $user = $this->user->find($id);

        return (new UserResource($user))->response()->setStatusCode(200);
    }

    /**
     * @group User Management
     *
     * @authenticated
     *
     * Update user information.
     *
     * @urlParam id string required The user ID. Example: user-id-123
     *
     * @bodyParam name string The user's name. Example: Jane Doe
     * @bodyParam email string The user's email address. Example: jane@example.com
     * @bodyParam password string The user's password. Example: newpassword123
     *
     * @response 200 {
     *   "id": "user-id-123",
     *   "name": "Jane Doe",
     *   "email": "jane@example.com",
     *   "roles": []
     * }
     * @response 400 {
     *   "status": "failed",
     *   "message": "Requested User have wrong Role",
     *   "status_code": 400
     * }
     */
    public function update(Request $request, $id)
    {
        if (! $this->user->validateUserRole($this->user->find($id))) {
            return $this->resultResponse('failed', 'Requested User have wrong Role', 400);
        }

        $userBefore = $this->user->find($id)->toArray() ?? [];

        $user = DB::transaction(function () use ($request, $id) {
            $this->user->update($request->all(), $id);

            return $this->user->find($id);
        });

        $userAfter = $user->toArray() ?? [];

        event('user.updated', new UserUpdated($user, $userBefore, $userAfter));

        return (new UserResource($user))->response()->setStatusCode(200);
    }

    /**
     * @group User Management
     *
     * @authenticated
     *
     * Delete a user.
     *
     * @urlParam id string required The user ID. Example: user-id-123
     *
     * @response 200 {
     *   "message": "delete user@example.com success"
     * }
     */
    public function destroy($id)
    {
        $user = $this->user->find($id);

        DB::transaction(function () use ($id) {
            $this->user->delete($id);
        });

        event('user.deleted', new UserDestroyed($user));

        return response()->json([
            'message' => 'delete '.$user->email.' success',
        ], 200);
    }
}

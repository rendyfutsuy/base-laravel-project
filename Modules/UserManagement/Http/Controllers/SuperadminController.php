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
use Modules\Authentication\Http\Repositories\Contracts\SuperadminContract;

class SuperadminController extends Controller
{
    use ResultResponse;

    protected $admin;

    public function __construct(SuperadminContract $admin)
    {
        $this->admin = $admin;
    }

    /**
     * @group User Management
     * @authenticated
     * 
     * Get paginated list of superadmins.
     * 
     * @queryParam page integer Page number. Example: 1
     * @queryParam per_page integer Items per page. Example: 10
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": "user-id-123",
     *       "name": "Super Admin",
     *       "email": "admin@example.com",
     *       "roles": []
     *     }
     *   ],
     *   "links": {...},
     *   "meta": {...}
     * }
     */
    public function index()
    {
        $admins = $this->admin->paginated();

        return new UserResourceCollection($admins);
    }

    /**
     * @group User Management
     * @authenticated
     * 
     * Create a new superadmin.
     * 
     * @bodyParam name string required The superadmin's full name. Example: Super Admin
     * @bodyParam email string required The superadmin's email address. Example: admin@example.com
     * @bodyParam password string required The superadmin's password. Example: password123
     * 
     * @response 200 {
     *   "id": "user-id-123",
     *   "name": "Super Admin",
     *   "email": "admin@example.com",
     *   "roles": []
     * }
     */
    public function store(Request $request)
    {
        $admin = DB::transaction(function () use ($request) {
            return $this->admin->store($request->all());
        });

        event('user.stored', new UserStored($admin));

        return (new UserResource($admin))->response()->setStatusCode(200);
    }

    /**
     * @group User Management
     * @authenticated
     * 
     * Get superadmin details by ID.
     * 
     * @urlParam id string required The superadmin ID. Example: user-id-123
     * 
     * @response 200 {
     *   "id": "user-id-123",
     *   "name": "Super Admin",
     *   "email": "admin@example.com",
     *   "roles": []
     * }
     */
    public function show($id)
    {
        $admin = $this->admin->find($id);

        return (new UserResource($admin))->response()->setStatusCode(200);
    }

    /**
     * @group User Management
     * @authenticated
     * 
     * Update superadmin information.
     * 
     * @urlParam id string required The superadmin ID. Example: user-id-123
     * @bodyParam name string The superadmin's name. Example: Admin Updated
     * @bodyParam email string The superadmin's email address. Example: admin.updated@example.com
     * @bodyParam password string The superadmin's password. Example: newpassword123
     * 
     * @response 200 {
     *   "id": "user-id-123",
     *   "name": "Admin Updated",
     *   "email": "admin.updated@example.com",
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
        if (! $this->admin->validateUserRole($this->admin->find($id))) {
            return $this->resultResponse('failed', 'Requested User have wrong Role', 400);
        }

        $admin = DB::transaction(function () use ($request, $id) {
            $this->admin->update($request->all(), $id);

            return $this->admin->find($id);
        });

        event('user.updated', new UserUpdated($admin));

        return (new UserResource($admin))->response()->setStatusCode(200);
    }

    /**
     * @group User Management
     * @authenticated
     * 
     * Delete a superadmin.
     * 
     * @urlParam id string required The superadmin ID. Example: user-id-123
     * 
     * @response 200 {
     *   "message": "delete admin@example.com success"
     * }
     */
    public function destroy($id)
    {
        $admin = $this->admin->find($id);

        DB::transaction(function () use ($id) {
            $this->admin->delete($id);
        });

        event('user.deleted', new UserDestroyed($admin));

        return response()->json([
            'message' => 'delete '.$admin->email.' success',
        ], 200);
    }
}

<?php

namespace Modules\Hierarchy\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Hierarchy\Models\Permission;
use Modules\Hierarchy\Events\PermissionSynced;
use Modules\Hierarchy\Events\PermissionsStored;
use Modules\Hierarchy\Http\Requests\PermissionsSynchro;
use Modules\Hierarchy\Http\Resources\PermissionResource;
use Modules\Hierarchy\Http\Requests\PermissionsSynchroUsers;
use Modules\Hierarchy\Http\Resources\PermissionSyncedResource;
use Modules\Hierarchy\Http\Resources\PermissionResourceCollection;
use Modules\Hierarchy\Http\Services\Repositories\Contracts\PermissionContract;

class PermissionController extends Controller
{
    protected $permission;

    public function __construct(PermissionContract $permission)
    {
        $this->permission = $permission;
    }

    /**
     * @group Hierarchy
     *
     * @authenticated
     *
     * Get paginated list of permissions.
     *
     * @queryParam page integer Page number. Example: 1
     * @queryParam per_page integer Items per page. Example: 10
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": "permission-id-123",
     *       "name": "api.user.index",
     *       "guard_name": "api",
     *       "roles": []
     *     }
     *   ],
     *   "links": {...},
     *   "meta": {...}
     * }
     */
    public function index()
    {
        $permissions = $this->permission->paginated();

        return new PermissionResourceCollection($permissions);
    }

    /**
     * @group Hierarchy
     *
     * @authenticated
     *
     * Create a new permission.
     *
     * @bodyParam name string required The permission name. Example: api.example.store
     * @bodyParam roles array Array of role IDs. Example: ["role-id-1", "role-id-2"]
     *
     * @response 200 {
     *   "id": "permission-id-123",
     *   "name": "api.example.store",
     *   "guard_name": "api",
     *   "roles": [
     *     {
     *       "id": "role-id-1",
     *       "name": "ADMIN"
     *     }
     *   ]
     * }
     */
    public function store(Request $request)
    {
        $permission = DB::transaction(function () use ($request) {
            $permission = $this->permission->store($request->all());

            event('permission.stored', new PermissionsStored($permission));

            return $permission;
        });

        return (new PermissionResource($permission))->response()->setStatusCode(200);
    }

    /**
     * @group Hierarchy
     *
     * @authenticated
     *
     * Get permission details by ID.
     *
     * @urlParam id string required The permission ID. Example: permission-id-123
     *
     * @response 200 {
     *   "id": "permission-id-123",
     *   "name": "api.example.store",
     *   "guard_name": "api",
     *   "roles": []
     * }
     */
    public function show($id)
    {
        $permission = $this->permission->find($id);

        return (new PermissionResource($permission))->response()->setStatusCode(200);
    }

    /**
     * @group Hierarchy
     *
     * @authenticated
     *
     * Resync roles to a permission.
     *
     * @urlParam permission string required The permission ID. Example: permission-id-123
     *
     * @bodyParam roles array required Array of role IDs. Example: ["role-id-1", "role-id-2"]
     * @bodyParam roles.* string required Role ID. Example: role-id-1
     *
     * @response 200 {
     *   "id": "permission-id-123",
     *   "name": "api.example.store",
     *   "guard_name": "api",
     *   "roles": [
     *     {
     *       "id": "role-id-1",
     *       "name": "ADMIN"
     *     }
     *   ]
     * }
     */
    public function resync(PermissionsSynchro $request, Permission $permission)
    {
        return DB::transaction(function () use ($permission, $request) {
            $permission = $this->permission->resync($request->roles, $permission);

            event('permission.resynchronized', new PermissionSynced($permission));

            return new PermissionSyncedResource($permission);
        });
    }

    /**
     * @group Hierarchy
     *
     * @authenticated
     *
     * Resync a permission to multiple users.
     *
     * @urlParam permission string required The permission ID. Example: permission-id-123
     *
     * @bodyParam users array required Array of user IDs. Example: ["user-id-1", "user-id-2"]
     * @bodyParam users.* string required User ID. Example: user-id-1
     *
     * @response 200 {
     *   "id": "permission-id-123",
     *   "name": "api.example.store",
     *   "guard_name": "api",
     *   "roles": []
     * }
     */
    public function resyncToUser(PermissionsSynchroUsers $request, Permission $permission)
    {
        return DB::transaction(function () use ($request, $permission) {
            $users = $this->permission->getUsersByIds($request->users);

            foreach ($users as $user) {
                $this->permission->resyncToUser($user, $permission);

                event('permission.synchronized', new PermissionSynced($permission, $user));
            }

            return new PermissionSyncedResource($permission);
        });
    }
}

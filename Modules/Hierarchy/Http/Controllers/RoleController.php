<?php

namespace Modules\Hierarchy\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Hierarchy\Models\Role;
use App\Http\Controllers\Controller;
use Modules\Hierarchy\Events\RolesStored;
use Modules\Hierarchy\Events\RoleSynchronized;
use Modules\Hierarchy\Http\Requests\RoleSynchro;
use Modules\Hierarchy\Http\Resources\RoleResource;
use Modules\Authentication\Http\Resources\UserResource;
use Modules\Hierarchy\Http\Resources\RoleSynchResource;
use Modules\Hierarchy\Http\Resources\RoleResourceCollection;
use Modules\Hierarchy\Http\Services\Repositories\Contracts\RoleContract;

class RoleController extends Controller
{
    protected $role;

    public function __construct(RoleContract $role)
    {
        $this->role = $role;
    }

    /**
     * @group Hierarchy
     *
     * @authenticated
     *
     * Get paginated list of roles.
     *
     * @queryParam page integer Page number. Example: 1
     * @queryParam per_page integer Items per page. Example: 10
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": "role-id-123",
     *       "name": "ADMIN",
     *       "guard_name": "api",
     *       "permissions": []
     *     }
     *   ],
     *   "links": {...},
     *   "meta": {...}
     * }
     */
    public function index()
    {
        $roles = $this->role->paginated();

        return new RoleResourceCollection($roles);
    }

    /**
     * @group Hierarchy
     *
     * @authenticated
     *
     * Create a new role.
     *
     * @bodyParam name string required The role name. Example: ADMIN
     *
     * @response 200 {
     *   "id": "role-id-123",
     *   "name": "ADMIN",
     *   "guard_name": "api",
     *   "permissions": []
     * }
     */
    public function store(Request $request)
    {
        $role = DB::transaction(function () use ($request) {
            $role = $this->role->store($request->all());

            event('role.stored', new RolesStored($role));

            return $role;
        });

        return (new RoleResource($role))->response()->setStatusCode(200);
    }

    /**
     * @group Hierarchy
     *
     * @authenticated
     *
     * Get role details by ID.
     *
     * @urlParam id string required The role ID. Example: role-id-123
     *
     * @response 200 {
     *   "id": "role-id-123",
     *   "name": "ADMIN",
     *   "guard_name": "api",
     *   "permissions": []
     * }
     */
    public function show($id)
    {
        $role = $this->role->find($id);

        return (new RoleResource($role))->response()->setStatusCode(200);
    }

    /**
     * @group Hierarchy
     *
     * @authenticated
     *
     * Sync permissions to a role.
     *
     * @urlParam role string required The role ID. Example: role-id-123
     *
     * @bodyParam permissions array required Array of permission IDs. Example: ["permission-id-1", "permission-id-2"]
     * @bodyParam permissions.* string required Permission ID. Example: permission-id-1
     *
     * @response 200 {
     *   "id": "role-id-123",
     *   "name": "ADMIN",
     *   "guard_name": "api",
     *   "permissions": [
     *     {
     *       "id": "permission-id-1",
     *       "name": "api.user.index"
     *     }
     *   ]
     * }
     */
    public function sync(RoleSynchro $request, Role $role)
    {
        return DB::transaction(function () use ($role, $request) {
            $role = $this->role->sync($request->permissions, $role);

            event('role.resynchronized', new RoleSynchronized($role));

            return new RoleSynchResource($role);
        });
    }

    /**
     * @group Hierarchy
     *
     * @authenticated
     *
     * Resync a user's role.
     *
     * @urlParam user string required The user ID. Example: user-id-123
     * @urlParam role string required The role ID. Example: role-id-123
     *
     * @response 200 {
     *   "id": "user-id-123",
     *   "name": "John Doe",
     *   "email": "user@example.com",
     *   "roles": [
     *     {
     *       "id": "role-id-123",
     *       "name": "ADMIN"
     *     }
     *   ]
     * }
     */
    public function resync(User $user, Role $role)
    {
        return DB::transaction(function () use ($role, $user) {
            $user = $this->role->resync($user, $role);

            event('role.synchronized', new RoleSynchronized($role, $user));

            return new UserResource($user);
        });
    }
}

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = $this->role->paginated();

        return new RoleResourceCollection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = $this->role->find($id);

        return (new RoleResource($role))->response()->setStatusCode(200);
    }

    /**
     * sync multiple permission to role.
     *
     * @return \App\Http\Resources\RoleSynchResource
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
     * sync multiple permission to role.
     *
     * @return \App\Http\Resources\RoleSynchResource
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

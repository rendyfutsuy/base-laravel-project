<?php

namespace App\Http\Controllers\Superadmin;

use App\Models\User;
use App\Events\RolesStored;
use Illuminate\Http\Request;
use App\Events\RoleSynchronized;
use App\Http\Requests\RoleSynchro;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\RoleSynchResource;
use App\Http\Resources\RoleResourceCollection;
use App\Http\Repositories\Contracts\RoleContract;

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
            $role = $this->role->resync($user, $role);

            return new UserResource($role);
        });
    }
}

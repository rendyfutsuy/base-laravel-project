<?php

namespace Modules\Hierarchy\Http\Controllers;

use App\Models\User;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = $this->permission->paginated();

        return new PermissionResourceCollection($permissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = $this->permission->find($id);

        return (new PermissionResource($permission))->response()->setStatusCode(200);
    }

    /**
     * sync multiple permission to role.
     *
     * @return \App\Http\Resources\PermissionSyncedResource
     */
    public function resync(PermissionsSynchro $request, Permission $permission)
    {
        return DB::transaction(function () use ($permission, $request) {
            $permission = $this->permission->resync($request->roles, $permission);

            event('permission.resynchronized', new PermissionSynced($permission));

            return new PermissionSyncedResource($permission);
        });
    }

    public function resyncToUser(PermissionsSynchroUsers $request, Permission $permission)
    {
        return DB::transaction(function () use ($request, $permission) {
            $users = User::whereIn('id', $request->users)->get();

            foreach ($users as $user) {
                $this->permission->resyncToUser($user, $permission);

                event('permission.synchronized', new PermissionSynced($permission, $user));
            }

            return new PermissionSyncedResource($permission);
        });
    }
}

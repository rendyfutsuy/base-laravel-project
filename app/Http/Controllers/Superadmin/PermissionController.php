<?php

namespace App\Http\Controllers\Superadmin;

use App\Events\PermissionsStored;
use Illuminate\Http\Request;
use App\Events\PermissionSynced;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\PermissionsSynchro;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\PermissionSyncedResource;
use App\Http\Resources\PermissionResourceCollection;
use App\Http\Repositories\Contracts\PermissionContract;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $permission = DB::transaction(function () use ($request) { 
            $permission = $this->permission->store($request->all());

            event('permission.stored', new PermissionsStored($permission));
            
            return $permission;
        });

        return (new PermissionResource($permission))->response()->setStatusCode(200);;
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

        return (new PermissionResource($permission))->response()->setStatusCode(200);;
    }
    
    /**
     * sync multiple permission to role.
     *
     * @param  \Spatie\Permission\Models\Permission  $permission
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
}

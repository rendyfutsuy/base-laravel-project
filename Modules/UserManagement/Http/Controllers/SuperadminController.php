<?php

namespace Modules\UserManagement\Http\Controllers;

use Modules\UserManagement\Events\UserStored;
use Modules\UserManagement\Events\UserUpdated;
use Illuminate\Http\Request;
use Modules\UserManagement\Events\UserDestroyed;
use Illuminate\Support\Facades\DB;
use Modules\UserManagement\Http\Services\Traits\ResultResponse;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admins = $this->admin->paginated();

        return new UserResourceCollection($admins);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $admin = $this->admin->find($id);

        return (new UserResource($admin))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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

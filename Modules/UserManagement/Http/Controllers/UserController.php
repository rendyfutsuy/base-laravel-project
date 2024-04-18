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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->user->paginated();

        return new UserResourceCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->user->find($id);

        return (new UserResource($user))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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

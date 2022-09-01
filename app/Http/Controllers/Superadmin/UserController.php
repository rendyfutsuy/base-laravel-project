<?php

namespace App\Http\Controllers\Superadmin;

use App\Models\User;
use App\Events\UserStored;
use App\Events\UserUpdated;
use Illuminate\Http\Request;
use App\Events\UserDestroyed;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Services\Traits\ResultResponse;
use App\Http\Resources\UserResourceCollection;
use App\Http\Repositories\Contracts\UserContract;

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
     * @param  \Illuminate\Http\Request  $request
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(! $this->user->validateUserRole($this->user->find($id))) {
            return $this->resultResponse('failed', 'Requested User have wrong Role', 400);
        }

        $user = DB::transaction(function () use ($request, $id) {
            $this->user->update($request->all(), $id);
            return $this->user->find($id);
        });

        event('user.updated', new UserUpdated($user));

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
            'message' => 'delete '. $user->email .' success'
        ], 200);
    }
}

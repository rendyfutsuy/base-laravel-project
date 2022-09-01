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
use App\Http\Repositories\Contracts\StaffContract;

class StaffController extends Controller
{
    use ResultResponse;
    
    protected $staff;
    
    public function __construct(StaffContract $staff)
    {
        $this->staff = $staff;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staffs = $this->staff->paginated();

        return new UserResourceCollection($staffs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $staff = DB::transaction(function () use ($request) {
            return $this->staff->store($request->all());
        });

        event('user.stored', new UserStored($staff));

        return (new UserResource($staff))->response()->setStatusCode(200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $staff = $this->staff->find($id);

        return (new UserResource($staff))->response()->setStatusCode(200);
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
        if(! $this->staff->validateUserRole($this->staff->find($id))) {
            return $this->resultResponse('failed', 'Requested User have wrong Role', 400);
        }

        $staff = DB::transaction(function () use ($request, $id) {
            $this->staff->update($request->all(), $id);
            return $this->staff->find($id);
        });

        event('user.updated', new UserUpdated($staff));

        return (new UserResource($staff))->response()->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $staff = $this->staff->find($id);
        
        DB::transaction(function () use ($id) {
            $this->staff->delete($id);
        });

        event('user.deleted', new UserDestroyed($staff));

        return response()->json([
            'message' => 'delete '. $staff->email .' success'
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ExampleCreated;
use App\Events\ExampleDeleted;
use App\Events\ExampleUpdated;
use App\Http\Resources\ExampleDetail;
use App\Http\Resources\ExampleCollection;
use App\Http\Repositories\Contracts\ExampleContract;

class RepositoryExampleController extends Controller
{
    /** @var ExampleContract */
    protected $repository;

    public function __construct(ExampleContract $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        $examples = $this->repository->all();

        return new ExampleCollection($examples);
    }

    public function store(Request $request)
    {
        $example = $this->repository->store($request->all());

        event(
            'example.created',
            new ExampleCreated($example)
        );

        return response()->json([
            'message' => 'success',
            'example' => $example,
        ]);
    }

    public function update(Request $request, $id)
    {
        $example = $this->repository->find($id);
        $this->repository->update($request->all(), $id);

        event(
            'example.updated',
            new ExampleUpdated($example)
        );

        return new ExampleDetail($example);
    }

    public function delete($id)
    {
        $example = $this->repository->find($id);
        $this->repository->delete($id);

        event(
            'example.deleted',
            new ExampleDeleted($example)
        );

        return new ExampleDetail($example);
    }
}

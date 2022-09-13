<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ExampleCollection;
use App\Http\Services\Example3rdParty;
use App\Http\Services\Searches\ExampleSearch;

class ExampleController extends Controller
{
    public function index(Request $request)
    {
        $factory = app()->make(ExampleSearch::class);
        $examples = $factory->apply()->paginate(10);

        return new ExampleCollection($examples);
    }

    public function getFromApi()
    {
        $example = new Example3rdParty();
        $response = $example->fetch();

        return response()->json($response['body'], $response['status']);
    }

    public function postToApi(Request $request)
    {
        $example = new Example3rdParty();
        $response = $example->store($request->all());

        return response()->json($response['body'], $response['status']);
    }
}

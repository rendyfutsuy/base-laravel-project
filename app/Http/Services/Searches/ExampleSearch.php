<?php

namespace App\Http\Services\Searches;

use App\Models\User;
use App\Models\TestExample;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Services\Searches\HttpSearch;
use App\Http\Services\Searches\Filters\Sort;
use App\Http\Services\Searches\Filters\Search;
use App\Http\Services\Searches\Filters\Status;

class ExampleSearch extends HttpSearch
{
    protected function filters(): array
    {
        return [
            Search::class,
            Status::class,
            Sort::class,
        ];
    }

    /**
     * @return Builder<User>
     */
    protected function passable()
    {
        return TestExample::query();
    }

    /**
     * @inheritdoc
     */
    protected function thenReturn($example)
    {
        return $example;
    }
}

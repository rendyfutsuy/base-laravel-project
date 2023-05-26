<?php

namespace App\Http\Repositories;

use App\Models\TestExample;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Repositories\Contracts\ExampleContract;

class ExampleRepository extends BaseRepository implements ExampleContract
{
    protected $model;

    public function __construct(TestExample $user)
    {
        $this->model = $user;
    }

    public function getActivated(): Collection
    {
        return $this->model->where('status', TestExample::ACTIVATED)->get();
    }
}

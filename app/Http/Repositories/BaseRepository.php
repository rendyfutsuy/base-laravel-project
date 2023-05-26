<?php

namespace App\Http\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Http\Repositories\Contracts\BaseRepositoryContract;

class BaseRepository implements BaseRepositoryContract
{
    /**
     * @var Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find($id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @return Model
     */
    public function findByCriteria(array $criteria): ?Model
    {
        return $this->model
            ->where($criteria)
            ->first();
    }

    public function getByCriteria(array $criteria): Collection
    {
        return $this->model
            ->where($criteria)
            ->get();
    }

    public function store(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * @return void
     */
    public function update(array $attributes, $id)
    {
        return $this->model
            ->where('id', $id)
            ->update($attributes);
    }

    /**
     * @return void
     */
    public function delete($id)
    {
        return $this->model
            ->where('id', $id)
            ->delete();
    }
}

<?php

namespace App\Http\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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

    public function paginated()
    {
        return $this->model->query()->paginate(request('per_page', 10));
    }

    public function find($id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function findByCriteria(array $criteria): ?Model
    {
        return $this->model
            ->where($criteria)
            ->first();
    }

    public function getByCriteria(array $criteria): Collection
    {
        return $this->model
            ->where(function ($filters) use ($criteria) {
                foreach ($criteria as $key => $value) {
                    $filters->where($key, $value);
                }
            })
            ->get();
    }

    public function store(array $attributes): Model
    {
        return DB::transaction(function () use ($attributes) {
            return $this->model->create($attributes);
        });
    }

    /**
     * @return void
     */
    public function update(array $attributes, $id)
    {
        return DB::transaction(function () use ($attributes, $id) {
            return $this->model
                ->where('id', $id)
                ->update($attributes);
        });
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

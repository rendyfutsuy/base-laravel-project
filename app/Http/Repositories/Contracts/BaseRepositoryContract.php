<?php

namespace App\Http\Repositories\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryContract
{
    public function all(): Collection;

    public function find($id): Model;

    /**
     * @return Model
     */
    public function findByCriteria(array $criteria): ?Model;

    public function getByCriteria(array $criteria): Collection;

    /**
     * @return Model
     */
    public function store(array $attributes): ?Model;

    /**
     * @return void
     */
    public function update(array $attributes, $id);

    /**
     * @return void
     */
    public function delete($id);
}

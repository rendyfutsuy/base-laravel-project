<?php

namespace Modules\Authentication\Http\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

interface SuperadminContract
{
    public function paginated();

    public function validateUserRole(User $user): bool;

    public function find($id): User;

    public function store(array $attributes): Model;

    public function update(array $attributes, $id);

    public function delete($id);
}

<?php

namespace App\Http\Repositories;

use App\Http\Repositories\Contracts\SuperadminContract;

class SuperadminRepository extends UserRepository implements SuperadminContract
{
    protected $role = 'SUPER_ADMIN';
}

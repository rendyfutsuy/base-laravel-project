<?php

namespace App\Http\Repositories;

use App\Http\Repositories\UserRepository;
use App\Http\Repositories\Contracts\StaffContract;

class StaffRepository extends UserRepository implements StaffContract
{
    protected $role = 'STAFF';
}
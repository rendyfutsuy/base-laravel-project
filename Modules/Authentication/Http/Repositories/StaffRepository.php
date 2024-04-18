<?php

namespace Modules\Authentication\Http\Repositories;

use Modules\Authentication\Http\Repositories\Contracts\StaffContract;

class StaffRepository extends UserRepository implements StaffContract
{
    // Have Same function structure with UserRepository
    // the only different is User's Role
    protected $role = 'STAFF';
}

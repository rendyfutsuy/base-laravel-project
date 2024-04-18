<?php

namespace Modules\Authentication\Http\Repositories;

use Modules\Authentication\Http\Repositories\Contracts\SuperadminContract;

class SuperadminRepository extends UserRepository implements SuperadminContract
{
    // Have Same function structure with UserRepository
    // the only different is User's Role
    protected $role = 'SUPER_ADMIN';
}

<?php

namespace Modules\UserManagement\Http\Controllers;

use Modules\Authentication\Http\Repositories\Contracts\StaffContract;

class StaffController extends UserController
{
    // Have Same function structure with UserController
    // the only different is User's Role
    public function __construct(StaffContract $staff)
    {
        $this->user = $staff;
    }
}

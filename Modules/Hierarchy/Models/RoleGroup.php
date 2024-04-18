<?php

namespace Modules\Hierarchy\Models;

use App\Models\BaseModel;

class RoleGroup extends BaseModel
{
    protected $table = 'role_groups';

    protected $fillable = [
        'name',
    ];
}

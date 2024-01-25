<?php

namespace App\Models\Hierarchy;

use App\Models\Traits\UuidModelTrait;
use Spatie\Permission\Models\Role as Base;

class Role extends Base
{
    use UuidModelTrait;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'guard_name',
        'is_default',
    ];

    public function setGuardNameAttribute()
    {
        $this->attributes['guard_name'] = 'api';
    }

    public function revokePermissionTo($permission)
    {
        return parent::revokePermissionTo($permission);
    }
}

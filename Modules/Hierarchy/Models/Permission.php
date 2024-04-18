<?php

namespace Modules\Hierarchy\Models;

use App\Models\Traits\UuidModelTrait;
use Spatie\Permission\Models\Permission as Base;

class Permission extends Base
{
    use UuidModelTrait;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'guard_name',
    ];
}

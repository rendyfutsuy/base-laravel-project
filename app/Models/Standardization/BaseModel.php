<?php

namespace App\Models\Standardization;

use App\Models\Traits\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use SoftDeletes, UuidModelTrait;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;
}

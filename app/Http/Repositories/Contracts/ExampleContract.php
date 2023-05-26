<?php

namespace App\Http\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ExampleContract
{
    public function getActivated(): Collection;
}

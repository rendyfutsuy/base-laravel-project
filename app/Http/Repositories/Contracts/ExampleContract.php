<?php

namespace App\Http\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ExampleContract
{
    /**
     * @return Collection
     */
    public function getActivated(): Collection;
}
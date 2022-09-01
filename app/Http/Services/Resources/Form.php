<?php

namespace App\Http\Services\Resources;

use Illuminate\Support\Arr;

abstract class Form
{
    /** @var array */
    protected $parameters = [];

    /**
     * Get value from parameters using dot notation
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    protected function get($key, $default = null)
    {
        return Arr::get($this->parameters, $key, $default);
    }

    /**
     * check if key exists or not
     *
     * @param  string $key
     */
    protected function hasKey($key): bool
    {
        return array_key_exists($key, $this->parameters);
    }
}


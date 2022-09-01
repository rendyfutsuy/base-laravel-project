<?php

namespace App\Http\Services\Searches\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Services\Searches\Contracts\FilterContract;

class Status implements FilterContract
{
    /** @var string|null */
    protected $status;

    /**
     * @param string|null $status
     * @return void
     */
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function handle(Builder $query, Closure $next)
    {
        if (!$this->keyword()) {
            return $next($query);
        }

        $query->where('status', $this->status);

        return $next($query);
    }

    /**
     * Get status keyword.
     *
     * @return mixed
     */
    protected function keyword()
    {
        if ($this->status) {
            return $this->status;
        }

        $this->status = request('status', null);

        return request('status');
    }
}

<?php

namespace App\Http\Services\Searches\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Services\Searches\Contracts\FilterContract;

class Search implements FilterContract
{
    /** @var string|null */
    protected $search;

    /**
     * @param string|null $search
     * @return void
     */
    public function __construct($search)
    {
        $this->search = $search;
    }

    /**
     * @return mixed
     */
    public function handle(Builder $query, Closure $next)
    {
        if (!$this->keyword()) {
            return $next($query);
        }
        
        $query->where('name', 'LIKE', '%' . $this->search . '%');

        return $next($query);
    }

    /**
     * Get search keyword.
     *
     * @return mixed
     */
    protected function keyword()
    {
        if ($this->search) {
            return $this->search;
        }

        $this->search = request('search', null);

        return request('search');
    }
}

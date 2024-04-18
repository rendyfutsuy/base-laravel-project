<?php

namespace Modules\Notification\Http\Services\Searches\NotificationList;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Services\Searches\Filters\Search as Base;

class NotificationSearch extends Base
{
    /**
     * @return mixed
     */
    public function handle(Builder $query, Closure $next)
    {
        $search = $this->search;

        if (! $this->keyword()) {
            return $next($query);
        }

        $query->where(function ($notifications) use ($search) {
            $notifications->search($search);
        });

        return $next($query);
    }
}

<?php

namespace Modules\Notification\Http\Services\Searches\NotificationList;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Services\Searches\Contracts\FilterContract;

class NotificationIsRead implements FilterContract
{
    /** @var string|null */
    protected $notificationIsRead;

    /**
     * @param  string|null  $notificationIsRead
     * @return void
     */
    public function __construct($notificationIsRead)
    {
        $this->notificationIsRead = $notificationIsRead;
    }

    /**
     * @return mixed
     */
    public function handle(Builder $query, Closure $next)
    {

        if ($this->notificationIsRead() == null) {
            return $next($query);
        }

        $notificationIsRead = $this->notificationIsRead();

        $query->where(function ($notifications) use ($notificationIsRead) {
            $notifications->where('is_read', $notificationIsRead);
        });

        return $next($query);
    }

    /**
     * Get Filtering Conditions.
     *
     * @return mixed
     */
    protected function notificationIsRead()
    {
        if ($this->notificationIsRead) {
            return $this->notificationIsRead;
        }

        $this->notificationIsRead = request('notification_is_read', null);

        return request('notification_is_read');
    }
}

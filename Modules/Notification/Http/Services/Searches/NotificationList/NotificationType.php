<?php

namespace Modules\Notification\Http\Services\Searches\NotificationList;

use App\Http\Services\Searches\Contracts\FilterContract;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class NotificationType implements FilterContract
{
    /** @var string|null */
    protected $notificationType;

    /**
     * @param  string|null  $notificationType
     * @return void
     */
    public function __construct($notificationType)
    {
        $this->notificationType = $notificationType;
    }

    /**
     * @return mixed
     */
    public function handle(Builder $query, Closure $next)
    {

        if (! $this->notificationType()) {
            return $next($query);
        }

        $notificationTypes = $this->notificationType();

        $query->where(function ($notifications) use ($notificationTypes) {
            $notifications->whereIn('type', $notificationTypes);
        });

        return $next($query);
    }

    /**
     * Get Filtering Conditions.
     *
     * @return mixed
     */
    protected function notificationType()
    {
        if ($this->notificationType) {
            return $this->notificationType;
        }

        $this->notificationType = request('notification_types', null);

        return request('notification_types');
    }
}

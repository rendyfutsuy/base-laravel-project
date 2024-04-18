<?php

namespace Modules\Notification\Http\Services\Searches;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Services\Searches\HttpSearch;
use Modules\Notification\Models\Notification;
use Modules\Notification\Http\Services\Searches\NotificationList\NotificationSort;
use Modules\Notification\Http\Services\Searches\NotificationList\NotificationType;
use Modules\Notification\Http\Services\Searches\NotificationList\NotificationIsRead;
use Modules\Notification\Http\Services\Searches\NotificationList\NotificationSearch as Search;

class NotificationSearch extends HttpSearch
{
    protected function filters(): array
    {
        return [
            NotificationType::class,
            NotificationIsRead::class,
            Search::class,
            NotificationSort::class,
        ];
    }

    /**
     * @return Builder<Notification>
     */
    protected function passable()
    {
        return Notification::query()
            ->when(isset($this->assignedUser), function ($notifications) {
                $notifications->where('user_id', $this->assignedUser->id);
            });
    }

    public function assignUser(User $user)
    {
        $this->assignedUser = $user;

        return $this;
    }
}

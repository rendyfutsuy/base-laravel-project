<?php

namespace Modules\Notification\Http\Services\Searches\NotificationList;

use App\Http\Services\Searches\Filters\Sort as Base;
use Modules\Notification\Models\Notification;

class NotificationSort extends Base
{
    /** @var string */
    protected $defaultSortField = 'notifications.created_at';

    /** @var string */
    protected $defaultSortOrder = 'desc';

    /** Editable */
    public function classes(): array
    {
        return [
            new Notification(),
        ];
    }
}

<?php

namespace Modules\Notification\Http\Repositories\Contracts;

use Modules\Notification\Models\Notification;

interface NotificationContract
{
    public function countUnread();

    public function find($id): ?Notification;

    public function updateRead($id): bool;
}

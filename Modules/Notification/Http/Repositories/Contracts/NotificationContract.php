<?php

namespace Modules\Notification\Http\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface NotificationContract
{
    public function countUnread();

    public function find($id): Model;

    public function updateRead($id): bool;
}

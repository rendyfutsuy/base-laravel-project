<?php

namespace Modules\UserManagement\Listeners\UserDeleted;

use Modules\UserManagement\Events\UserDestroyed;

class AddUserDeletedLogToUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserDestroyed $event): void
    {
        //
    }
}
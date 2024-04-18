<?php

namespace Modules\UserManagement\Listeners\UserCreated;

use Modules\UserManagement\Events\UserStored;

class SendEmailUserCreatedNotificationToUser
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
    public function handle(UserStored $event): void
    {
        //
    }
}

<?php

namespace Modules\UserManagement\Listeners\UserUpdated;

use Modules\UserManagement\Events\UserUpdated;

class SendEmailUserUpdatedNotificationToUser
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
    public function handle(UserUpdated $event): void
    {
        //
    }
}

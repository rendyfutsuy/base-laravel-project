<?php

namespace Modules\UserManagement\Listeners\UserUpdated;

use Modules\UserManagement\Events\UserUpdated;
use Modules\Notification\Http\Services\Features\NotificationService;

class AddUserUpdatedLogToUser
{
    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     */
    public function handle(UserUpdated $event): void
    {
        $receiver = $event->user;
        $this->service->userUpdated(
            $event->user,
            $receiver,
            $event->userBefore,
            $event->userAfter
        );
    }
}

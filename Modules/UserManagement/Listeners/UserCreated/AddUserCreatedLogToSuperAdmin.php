<?php

namespace Modules\UserManagement\Listeners\UserCreated;

use Modules\UserManagement\Events\UserStored;
use Modules\Notification\Http\Services\Features\NotificationService;

class AddUserCreatedLogToSuperAdmin
{
    public $service;

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
    public function handle(UserStored $event): void
    {
        $receiver = $event->receiver;
        $this->service->userCreated(
            $event->user,
            $receiver
        );
    }
}

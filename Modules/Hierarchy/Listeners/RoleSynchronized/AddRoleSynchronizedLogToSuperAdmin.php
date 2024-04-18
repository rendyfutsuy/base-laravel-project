<?php

namespace Modules\Hierarchy\Listeners\RoleSynchronized;

use Modules\Hierarchy\Events\RoleSynchronized;
use Modules\Notification\Http\Services\Features\NotificationService;

class AddRoleSynchronizedLogToSuperAdmin
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
    public function handle(RoleSynchronized $event): void
    {
        $receiver = $event->receiver;
        $this->service->roleSyncedToUser(
            $event->role,
            $event->user,
            $receiver
        );
    }
}

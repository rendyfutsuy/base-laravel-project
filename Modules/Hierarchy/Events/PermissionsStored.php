<?php

namespace Modules\Hierarchy\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Hierarchy\Models\Permission;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PermissionsStored
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var Modules\Hierarchy\Models\Permission */
    public $permission;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

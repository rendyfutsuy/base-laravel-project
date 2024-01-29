<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\Hierarchy\Permission;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PermissionSynced
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var App\Models\Hierarchy\Permission */
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

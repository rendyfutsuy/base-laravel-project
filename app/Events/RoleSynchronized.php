<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Hierarchy\Role;

class RoleSynchronized
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var App\Models\Hierarchy\Role */
    public $role;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Role $role)
    {
        // Updated Role updated_at value
        $role->save();
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

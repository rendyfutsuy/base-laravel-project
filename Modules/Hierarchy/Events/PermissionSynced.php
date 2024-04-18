<?php

namespace Modules\Hierarchy\Events;

use App\Models\User;
use Modules\Hierarchy\Models\Permission;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PermissionSynced
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var Modules\Hierarchy\Models\Permission */
    public $permission;

    /** @var App\Models\User */
    public $user;

    /** @var App\Models\User */
    public $receiver;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Permission $permission, ?User $user = null)
    {
        $this->permission = $permission;
        $this->user = $user;
        $this->receiver = User::whereHas('roles', function ($roles) {
            $roles->where('name', 'SUPER_ADMIN');
        })->first();
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

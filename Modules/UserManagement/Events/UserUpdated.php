<?php

namespace Modules\UserManagement\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var \App\Models\User */
    public $user;

    /** @var array */
    public $userBefore;

    /** @var array */
    public $userAfter;

    /** @var \App\Models\User */
    public $receiver;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $userBefore = [], $userAfter = [])
    {
        $this->user = $user;
        $this->userBefore = $userBefore;
        $this->userAfter = $userAfter;
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

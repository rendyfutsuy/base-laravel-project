<?php

namespace Modules\Authentication\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMailOTP
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;

    public $code;

    public $redirect;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userId, $code, $redirect = null)
    {
        $this->userId = $userId;
        $this->code = $code;
        $this->redirect = $redirect;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [];
    }
}

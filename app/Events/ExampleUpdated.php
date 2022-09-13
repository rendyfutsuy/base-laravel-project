<?php

namespace App\Events;

use App\Models\TestExample;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ExampleUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $example;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TestExample $example)
    {
        $this->example = $example;
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

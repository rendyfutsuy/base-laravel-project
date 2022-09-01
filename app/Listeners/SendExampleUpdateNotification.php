<?php

namespace App\Listeners;

use App\Events\ExampleUpdated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\SendExampleChangeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendExampleUpdateNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ExampleUpdated $event)
    {
        Mail::to(config('example.owner_email'))
            ->send(new SendExampleChangeNotification($event->example));
    }
}

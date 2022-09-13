<?php

namespace App\Listeners;

use App\Events\ExampleUpdated;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendExampleChangeNotification;

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

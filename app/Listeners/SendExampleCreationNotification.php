<?php

namespace App\Listeners;

use App\Events\ExampleCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\SendExampleCreationNotification as MailSendExampleCreationNotification;

class SendExampleCreationNotification
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
    public function handle(ExampleCreated $event)
    {
        Mail::to(config('example.owner_email'))
            ->send(new MailSendExampleCreationNotification($event->example));
    }
}

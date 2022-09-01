<?php

namespace App\Listeners;

use App\Events\ExampleDeleted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\SendExampleDeleteNotification as MailSendExampleDeleteNotification;

class SendExampleDeleteNotification
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
    public function handle(ExampleDeleted $event)
    {
        Mail::to(config('example.owner_email'))
            ->send(new MailSendExampleDeleteNotification($event->example));
    }
}

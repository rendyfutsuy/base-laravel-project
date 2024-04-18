<?php

namespace Modules\Authentication\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Modules\Authentication\Mail\MailOTP;

class SendMailOTPNotification
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
    public function handle($event)
    {
        $user = User::find($event->userId);
        $code = $event->code;
        $redirect = $event->redirect;

        if ($user) {
            Mail::to($user->email)
                ->send(new MailOTP($code, $user, $redirect));
        }
    }
}

<?php

namespace Modules\Authentication\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailOTP extends Mailable
{
    use Queueable, SerializesModels;

    protected $code;

    protected $redirect;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code, $user, $redirect = null)
    {
        $this->code = $code;
        $this->user = $user;
        $this->redirect = $redirect;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.email_verification')
            ->subject('Verification Code OTP')
            ->with(
                [
                    'to' => $this->user,
                    'otp' => $this->code,
                ]
            );
    }
}

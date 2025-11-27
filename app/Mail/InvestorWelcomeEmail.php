<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Password;

class InvestorWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $investor;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($investor)
    {
        $this->investor = $investor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $passwordBroker = Password::broker('users_welcome');
        $token = $passwordBroker->createToken($this->investor);

        $resetPasswordUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $this->investor->email,
            ], false));

        return $this->markdown('emails.investors.welcome', ['investor' => $this->investor, 'resetPasswordUrl' => $resetPasswordUrl]);
    }
}

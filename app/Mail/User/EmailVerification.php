<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    private $secret;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $message = (new MailMessage)
            ->line('Thank you for add secondary backup email. To finish, you just need to confirm that we got your email right.')
            ->line('To confirm your email please click this link:')
            ->action('Verify Email Address', route('backend.personal.email.verification', $this->secret))
            ->line('Thank you for using our application!')
            ->render();

        return $this->from(config('mail.from.address'), 'Admin ' . config('app.name'))
            ->subject('Email Verification ' . config('app.name'))
            ->html($message->toHtml())
            ->buildView();
    }
}

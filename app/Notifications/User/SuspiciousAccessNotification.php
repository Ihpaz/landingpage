<?php

namespace App\Notifications\User;

use App\Models\User\Device;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuspiciousAccessNotification extends Notification
{
    use Queueable;

    private $datetime;
    private $ip_address;
    private $email;
    private $platform;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($datetime, $ip_address, $email, $platform)
    {
        $this->datetime = $datetime;
        $this->ip_address = $ip_address;
        $this->email = $email;
        $this->platform = $platform;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Suspicious Access')
            ->line('Someone recently used your password to sign in ' . config('app.name') . ' using ' . $this->email)
            ->line('We prevented to sign-in attempt in case this was a hijacker trying to access your account')
            ->line('Please review the details or the sign-in attempt:')
            ->line('')
            ->line('Datetime: ' . $this->datetime)
            ->line('Platform: ' . $this->platform)
            ->line('IP Address: ' . $this->ip_address)
            ->line('If you do not recognize this sign-in attempt, please change your password or block this sign-in device for next activity!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

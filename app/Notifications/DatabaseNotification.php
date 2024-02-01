<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DatabaseNotification extends Notification
{
    use Queueable;
    private $notification;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $from = $this->notification['from'] ?? 'System';
        $img_thumbnail = Cache::remember('user_img_' . $from, 60 * 60, function () use ($from) {
            $user = User::where('email', $from)->first();
            return $user ? $user->user_thumbnail : asset('img/cms.png');
        });
        
        return [
            'from' => $this->notification['from'] ?? 'System',
            'subject' => $this->notification['subject'] ?? 'Information',
            'message' => $this->notification['message'],
            'img_thumbnail' => $img_thumbnail ?? null,
            'url' => $this->notification['url'] ?? route('backend.notification.index')
        ];
    }
}

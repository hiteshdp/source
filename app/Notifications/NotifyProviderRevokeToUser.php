<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class NotifyProviderRevokeToUser extends Notification
{
    use Queueable;

    public $userName;
    public $content;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($userName,$content)
    {
        $this->userName = $userName;
        $this->content = $content;
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
        ->subject('Access Revoked: Health Reports')
        ->greeting('Dear '.$this->userName.',')
        ->line(new HtmlString(nl2br($this->content['body'])))
        ->salutation("\r\n\r\n Wellkasa Team,  \r\n admin@wellkasa.com");
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

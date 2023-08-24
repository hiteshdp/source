<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class NewUserCreatedByAdminNotification extends Notification
{
    use Queueable;
    private $message;
    private $senderName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message,$senderName)
    {
        $this->message = $message;
        $this->senderName = $senderName;
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
                    ->subject('Wellkasa - New User Created')
                    ->line(new HtmlString(nl2br($this->message['body'])))
                    ->markdown('vendor.notifications.newUserCreatedByAdmin', ['senderName' => $this->senderName ]);
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
            'thanks' => $this->newsletterData['thanks']
        ];
    }
}

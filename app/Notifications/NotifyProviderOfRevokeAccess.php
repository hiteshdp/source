<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class NotifyProviderOfRevokeAccess extends Notification
{
    use Queueable;

    public $userName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($userName)
    {
        $this->userName = $userName;
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
        $content = [
            'body'=> "We hope this email finds you in good health. We are writing to inform you that access to one of your patient's tracker reports has been revoked.

            Thank you for your trust in Wellkasa. We value your partnership in managing your patient's health effectively and providing them with quality care.
            
            If you have any concerns or questions regarding this revocation, please don't hesitate to reach out to us. We are here to assist you and address any queries you may have.
            
            Wishing you good health and well-being."
        ];
        return (new MailMessage)
        ->subject('Patient Revoked Access: Health Reports')
        ->greeting('Dear '.$this->userName.',')
        ->line(new HtmlString(nl2br($content['body'])))
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

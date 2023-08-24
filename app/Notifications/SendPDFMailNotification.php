<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use DB;

class SendPDFMailNotification extends Notification
{
    use Queueable;
    public $senderName;
    public $filename;
    public $fromAddress;
    public $interactionReportFileName;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($senderName,$fromAddress,$filename,$interactionReportFileName)
    {
        $this->senderName = $senderName;
        $this->fromAddress = $fromAddress;
        $this->filename = $filename;
        $this->interactionReportFileName = $interactionReportFileName;
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
                ->subject('Interaction PDF')
                ->line('Thank you for visiting the clinic. We have attached the Wellkasa interaction checker report.')
                ->line('Please review it at your convenience.')
                ->attach($this->filename,[
                    'as' => $this->interactionReportFileName.'.pdf',
                    'mime' => 'text/pdf',
                ])
                ->markdown('vendor.notifications.interactionReportMail', ['senderName' => $this->senderName ]);
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
            'thanks' => $this->userEmailVerifiedData['thanks']
        ];
    }
}

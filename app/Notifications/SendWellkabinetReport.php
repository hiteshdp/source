<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendWellkabinetReport extends Notification
{
    use Queueable;
    public $senderName;
    public $filename;
    public $stReportFileName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($senderName,$filename,$stReportFileName)
    {
        $this->senderName = $senderName;
        $this->filename = $filename;
        $this->stReportFileName = $stReportFileName;
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
                ->subject('Wellkabinet Report')
                ->line($this->senderName.' requested to send this Wellkabinet report to you. Please review the attached report, from Wellkasa, at your convenience.')
                ->attach($this->filename,[
                    'as' => $this->stReportFileName.'.pdf',
                    'mime' => 'text/pdf',
                ])
                ->markdown('vendor.notifications.reportMail');
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

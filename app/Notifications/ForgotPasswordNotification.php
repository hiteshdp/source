<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ForgotPasswordNotification extends Notification
{
    use Queueable;
    public $userName;
    public $password;
    public $senderName;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($userName,$password,$senderName)
    {
        $this->userName = $userName;
        $this->password = $password;
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
                ->subject('Forgot Password')
                ->line("Forgotten your password? Don't worry, Below is your new password")
                ->line(new HtmlString('<b>New Password:</b> '.$this->password))
                ->line("If you have any question or encounter any problem while login, please contact our support team.")
                ->markdown('vendor.notifications.forgotpassword', ['userName' => $this->userName , 'senderName' => $this->senderName ]);
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

<?php
/*
|-----------------------------------------------------------------------------
| LARAVEL APP BASE CLASS, MODEL CLASS & MAIL INTERFACE DEFINE
|-----------------------------------------------------------------------------
*/
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Notification;
use App\Models\User;
use App\Notifications\NewsletterNotification;
use Illuminate\Support\Facades\Mail;
use App\Models\Settings;

class NewsletterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Newletter Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles newsletter subscription from wordpress page ( not in use as of now )
    |
    */

    /**
     * This function complies load view page
     *
     * @return  \Illuminate\Http\Response               Redirect to related response load view
     */
    public function index()
    {
        return view('welcome');
    }
    
    /**
     * This function sends email notification from user input
     * 
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data as email id.
     * @return  \Illuminate\Http\Response               Redirect to related response send notification as json type
    */
    public function sendNotification(Request $request) {

        //Get the email and send notification
        $userEmail = $request->userEmail;
        $adminEmail = Settings::where('name','Email')->get()->pluck('value')->first();
        $userData = [
            'body' => '<b>'.$userEmail.'</b> has been subscribed to Wellkasa newsletter.',
        ];
        $email_Sent = Notification::route('mail' , $adminEmail)->notify(new NewsletterNotification($userData));
        
        //Check email sending status
        if($email_Sent)
        {   
            $isSucess = '1';
            return json_encode($isSucess ,true);
        }
        else
        {
            $isSucess = '0';
            return json_encode($isSucess ,true);
        }
        
    }
}
<?php
namespace App\Helpers;
use App\Models\Logs;
use Auth;
use DB;
use Config;
use Illuminate\Support\Facades\Mail;
use App\Models\UserRole;
use Carbon\Carbon;
use Notification;
use App\Notifications\EmailTypeNotification;
use App\Models\Settings;
use App\Models\NaturalMedicineReference;
use App\Models\ProfileMembers;
use App\Models\User;
use App\Notifications\UserSubscriptionNotification;
use App\Models\MedicineCabinet;
use App\Models\DrugsInteractions;
use App\Notifications\NewUserCreatedByAdminNotification;
use App\Models\Subscriptions;
use App\Models\Product;
use App\Models\ProductTherapy;
use App\Models\UserProductOrder;
use App\Models\MedicineCabinetNotes;
use App\Models\EventSymptoms;
use App\Models\Severity;
use App\Models\Event;
use App\Models\UserSymptoms;
use App\Models\QuizResult;
use App\Models\QuizIntroScreen;
use App\Models\ProviderUser;
use App\Models\ProviderUserCode;
use Illuminate\Support\Facades\Session;
use App\Notifications\SendVerificationCodeForAccess;
use App\Notifications\SendVerificationCodeForRevoke;
use App\Notifications\SendVerificationCodeForRevokeToProvider;

class Helpers
{
    /*
	 * Below method to log API request data
	 */
	public static function apiLogs($requestData,$requestLabel,$method){
	    $fileName = date('d-m-Y');
        $content = $requestLabel.' : '.$method.PHP_EOL.date('d-m-Y H:i:s').PHP_EOL.$requestData;
        $path = public_path() . "/apilogs";
        if (!is_dir($path))
        {
            mkdir($path, 0777);

        }
        $fp = fopen($path . "/".$fileName.".log","a+");
        fwrite($fp,$content. PHP_EOL.PHP_EOL);
        fclose($fp);
	}

    /*
	 * Below method for used to the send email usin smtp 
	 */
	public static function sendEmail($to_email,$email_title,$email_body,$email_subjet,$attachment=""){

		$msg = array();

		// Get smtp confiure value from settings table    
		$email_config = DB::table('settings')->select('value', 'name')->whereIn('name', array('port', 'username', 'password', 'from_email','Email','host'))->get()->toArray();

		// Convert MD array to single array
		$email_config_array = array_column($email_config, 'value', 'name');

		// Setuo config array values
		$config = array(
			//'driver' => 'smtp',
			'driver' => 'sendmail',
			'host' => $email_config_array['host'],
			'port' => $email_config_array['port'],
			'encryption' => 'tls',
			'username' => $email_config_array['username'],
			'from' => array('address' => $email_config_array['Email'], 'name' => $email_config_array['Email']),
			'password' => $email_config_array['password'],
			'sendmail' => '/usr/sbin/sendmail -bs',
			'pretend' => false,
			// 'stream' => [
            //     'ssl' => [
            //         'verify_peer' => false,
            //         'verify_peer_name' => false,
            //         'allow_self_signed' => true,
            //     ],
            // ],
		);


      

		// Set Config value in smtp
		Config::set('mail', $config);

		// Set email title
		$data['title'] = $email_title;

		$input_data = array('to_email' => $to_email, 'email_subjet' => $email_subjet,'email_body'=> $email_body);    


		// // Function for send test mail
		// Mail::raw(['html' => 'tet'], function ($message) use ($input_data) {

		// 	$message->to($input_data['to_email'], 'Test')

		// 		->subject($input_data['email_subjet']);
		// });


		Mail::send([], [], function ($message) use ($input_data,$attachment) {
			$message->to($input_data['to_email'], 'Test')
			->subject($input_data['email_subjet'])
			->setBody($input_data['email_body'], 'text/html'); // for HTML rich messages
            if(!empty($attachment)){
                $message->attach($attachment); 
            }
		});

		// Send for mail send or not
		if (Mail::failures()) {
			// If mail not send than show error 
			$msg['status'] = 0;
			$msg['message'] = trans('messages.mail_sent_fail');
			$http_code = '400';
		} else {
			$msg['status'] = 1;
			$msg['message'] = trans('messages.mail_sent_success');
			$http_code = '200';
		}
		
		// Send email responase
		return $msg;

    }

    /**
     * Below method to store logs in table
    */
    public static function storeLogs($url=Null,$requestData=Null,$responseData=Null,$type=Null,$startCron=Null,$endCron=Null){
        // Delete 15 days ago records
        $currentDate = date('d-m-Y H:i:s');
        $oldRecordDate =  date('Y-m-d',strtotime('-10 days', strtotime($currentDate)));
        Logs::whereDate('created_at', '<=', $oldRecordDate)->delete();
        
        $userId = Null;
        if($type == 0){
            // Get login user id
            if(isset(Auth::user()->id) && !empty(Auth::user()->id)){
                $userId = Auth::user()->id;
            }
            
        }

        $logs = new Logs();
        $logs->userId = $userId;
        $logs->url = $url;
        $logs->requestData = $requestData;
        $logs->responseData = $responseData;
        $logs->type = $type;
        $logs->startCron = $startCron;
        $logs->endCron = $endCron;
        $logs->created_at = $currentDate;
        $logs->save();
    }


    /**
     * Below method to get limited word
    */
    public static function limit_text($text, $limit=2) {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos   = array_keys($words);
            $text  = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }

    public static function clean($string) {
        // $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
     }

    /***
     * Gets previous route name
     */
    public static function getPrevRouteName(){
        $url = url()->previous();
        $route = app('router')->getRoutes($url)->match(app('request')->create($url))->getName();
        return $route; 
    }

    /****
     * Function to add user role id 
     */
    public static function addUserRole($userId,$roleId=2){
        
        $now = Carbon::now();
        $currentTime = $now->toDateTimeString();
        // Get count of current user id with any role id
        $checkRole = UserRole::where('user_id',$userId)->whereNotNull('role_id')->count();
        // If current user does not exist in user_roles table then add user id and its role id = 2
        if($checkRole==0){
            $userRole = new UserRole;
            $userRole->user_id = $userId;
            $userRole->role_id = $roleId;
            $userRole->status = '1';
            $userRole->created_at = $currentTime;
            $userRole->save();
        }else{
            // User already exists with role id then update current data in user_roles table
            UserRole::update([
                'role_id' => $roleId,
                'status' => '1',
                'updated_at' => $currentTime 
            ])->where('user_id',$userId);
        }
        
    }

    /****
     * Function to display error on email domain name of mailinator
     */
    public static function blockMailinatorEmailSignUp($userEmail){
        
        $blockMailDomain = array('mailinator'); 
        $domain_name = explode(".",substr(strrchr($userEmail, "@"), 1));
        $emailName = $domain_name[0];  

        // If current user mail has mailinator domain then display error
        if(in_array($emailName, $blockMailDomain)){
          return '1';
        }else{
          return '0';
        }
        
    }

    /****
     * Function to send mail notification to admin if user has registered with mail id except in the given list
     */
    public static function sendUserEmailSpecificNotification($userEmail){
        
        $adminEmail = Settings::where('name','Email')->get()->pluck('value')->first();

        $acceptedMailNameList = array('gmail', 'outlook', 'hotmail', 'msn'); // these mail ids are accepted
        $domain_name = explode(".",substr(strrchr($userEmail, "@"), 1));
        $emailName = $domain_name[0];  

        // If current user mail id is not from the accepted mail name list, then send mail notification to admin
        if(!in_array($emailName, $acceptedMailNameList)){
            $sendData = [
                'body' => 'A new user has been registered on myWellkasa website with (@'.$emailName.') email.<br><br>
                User Email: <b>'.$userEmail.'</b>' ,
            ];
            Notification::route('mail' , $adminEmail)->notify(new EmailTypeNotification($sendData));
        }
        
    }

    /****
     * Function to get interactions based on drugsIds and natural medicine ids ( therapy Id )
     */
    public static function getInteractions($drugId,$naturalMedicineIds){
        
        $drugApiId = $drugId;
        $therapyApiId = $naturalMedicineIds;


        //API call to get Drugs details Start
        $headers = [
            'Content-Type' => 'application/json',
            'X-API-Key' => env("X_API_Key", "fec509d0791cb9845cbed9ced253485ce09b27948075acb08ab481430ebe2d33"),
        ];
        
        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
            'allow_redirects' => false

        ]);
        $interactionsData = [];
        foreach ($therapyApiId as $therapyApiId){
            
            try{
                
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.therapeuticresearch.com/nm/interactions?drug_ids=".$drugApiId."&monograph_ids=".$therapyApiId,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "x-api-key: fec509d0791cb9845cbed9ced253485ce09b27948075acb08ab481430ebe2d33"
                    ),
                ));
                
                $response = curl_exec($curl);
                $err = curl_error($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                
                curl_close($curl);


                $response_body = json_decode($response, true);
                
                //Check 200 response from API call
                if($httpCode == 200){
                    $drug_interaction_api_responseArr = $response_body;
                    
                    $drugName = DB::table('drugs')->select(DB::raw("CONCAT(name,' - ',CONCAT_WS('',brand_name)) AS name"))
                    ->where('apiDrugId',$drugApiId)->get()->pluck('name')->first();

                    $therapyName = DB::table('therapy')->select("therapy AS name")
                    ->where('apiId',$therapyApiId)->get()->pluck('name')->first();
                        

                    // Get the array from API response for drug interactions
                    foreach ($drug_interaction_api_responseArr as $drug_interaction_api_response ) {
                        
                        $interactionRating = $drug_interaction_api_response['rating-text'] ? $drug_interaction_api_response['rating-text'] : NULL;
                        $severity = $drug_interaction_api_response['severity-text'] ? $drug_interaction_api_response['severity-text'] : NULL;
                        $occurrence = $drug_interaction_api_response['occurrence-text'] ? $drug_interaction_api_response['occurrence-text'] : NULL;
                        $levelOfEvidence = $drug_interaction_api_response['level-of-evidence'] ? $drug_interaction_api_response['level-of-evidence'] : NULL;
                        $description = $drug_interaction_api_response['description'] ? $drug_interaction_api_response['description'] : NULL;
                        $referenceNumbers = $drug_interaction_api_response['reference-numbers'] ? $drug_interaction_api_response['reference-numbers'] : NULL;
                        
                        $data['drugName'] = $drugName;
                        $data['therapy'] = $therapyName;
                        $data['interactionRating'] = $interactionRating;
                        $data['severity'] = $severity;
                        $data['occurrence'] = $occurrence;
                        $data['levelOfEvidence'] = $levelOfEvidence;
                        

                        //Reference Start
                        $therapyReferenceDetails = NaturalMedicineReference::whereIn('referenceId',$referenceNumbers)
                        ->whereNull('deleted_at')->get()->toArray();
                        if(!empty($therapyReferenceDetails)){
                            foreach ($therapyReferenceDetails as $therapyReferenceDetailsKey => $therapyReferenceDetailsValue) {
                            
                                $referenceNumber = $therapyReferenceDetailsValue['referenceId'];
                                $referenceDescription = $therapyReferenceDetailsValue['description'];
        
                                $clickHereToResearch = '';
                                if(!empty($therapyReferenceDetailsValue['medicalPublicationId'])){
                                    $medicalPublicationId = $therapyReferenceDetailsValue['medicalPublicationId'];
                                    $researchApi = 'https://pubmed.ncbi.nlm.nih.gov/'.$medicalPublicationId;
                                    $clickHereToResearch = '<br/><a href='.$researchApi.' target=\'_blank\'>Click to see research</a>';
                                }
                                else
                                {
                                    $clickHereToResearch = '<br/><a href="javascript:void(0)" class="text-secondary">Click to see research</a>';
                                }
                                $referenceDescriptionDataContent = $referenceDescription.' '.$clickHereToResearch;
                                
                                //Replace double quote with single quote as it was creating issue on anchor tag
                                $referenceDescriptionDataContent = str_replace('"', "'", $referenceDescriptionDataContent);
                                $anchorTag = '<a tabindex="0" class="dd" data-placement="top" role="button" data-toggle="popover" data-html="true" data-trigger="focus" data-content="'.$referenceDescriptionDataContent.'" target="_blank" >'.$referenceNumber.'</a>';

                                $description = str_replace("(".$referenceNumber.",","(".$anchorTag.",",$description);

                                $description = str_replace("(".$referenceNumber.")","(".$anchorTag.")",$description);

                                $description = str_replace(",".$referenceNumber.",",",".$anchorTag.",",$description);

                                $description = str_replace(",".$referenceNumber.")",",".$anchorTag.")",$description);
                            }
                        }
                        //Reference End

                        $data['description'] = $description;
                        $interactionsData[] = $data;
                        
                    }
                    
                }
                else{
                    continue;
                }

            } catch (ClientException $e) {
                continue;            
            }
        }
        return $interactionsData;
    
    }

     /****
     * Function to removed Space
     */
    public static function removedSpace($string){
        $string = str_replace(" ","_",$string);
        $string = strtolower($string);
        return $string;
    }

    //Function to generated random 6 digit code
    public static function generateVerifiedCode($length = 6) {
        return substr(str_shuffle(str_repeat($x='0123456789', ceil($length/strlen($x)) )),1,$length);
    }

    /***
     * Update the remaining count from the given profile member count of logged in user
     */
    public static function updateRemainingProfileMembersCount($userId) {
        
        // get total number of member profile added by logged in user and update to its remaining count field in users table
        $getTotalMembersCount = ProfileMembers::where('addedByUserId',$userId)->count();
        $remainingProfileMemberCount = Auth::user()->profileMemberCount - $getTotalMembersCount;
        $remainingProfileMemberCount = $remainingProfileMemberCount < 0 ? Auth::user()->profileMemberCount : $remainingProfileMemberCount; 
        $updateRemainingCount = User::where('id',$userId)->update([
            'remainingProfileMemberCount' => $remainingProfileMemberCount
        ]);

    }

     /****
     * Function to send mail notification to admin if user has create subscriptions
     */
    public static function sendSubscriptionEmailNotification($userDetails,$subscriptionDetails){
        // Send email to user
        $sendData = [
            'body' => 'Thank you for payment, your subscription payment is successful with <span style="color:#35C0ED;">WellKabinet</span>, Below is the payment details.<br><br>
            <b>ID : </b>'.$userDetails->id.'<br>
            <b>Name : </b>'.$userDetails->name.'<br>
            <b>Email : </b>'.$userDetails->email.'<br>
            <b>Stripe ID : </b>'.$subscriptionDetails->stripe_id.'<br>
            <b>Billing Cycle Date : </b>'.$subscriptionDetails->billing_cycle_date.'<br>
            <b>Current Period Start : </b>'.$subscriptionDetails->current_period_start.'<br>
            <b>Current Period End : </b>'.$subscriptionDetails->current_period_end.'<br>
            <b>Customer : </b>'.$subscriptionDetails->customer.'<br>
            <b>Amount : </b>'.$subscriptionDetails->amount.'<br>
            <b>Currency : </b>'.$subscriptionDetails->currency.'<br>
            <b>Interval : </b>'.$subscriptionDetails->interval_val.'<br>
            <b>Stripe Status : </b>'.$subscriptionDetails->stripe_status.'.' 
        ];
        Notification::route('mail' , $userDetails->email)->notify(new UserSubscriptionNotification($sendData));

        // Send email to admin
         $adminSendData = [
            'body' => 'Payment has been received through subscription on <span style="color:#35C0ED;">WellKabinet</span>, Below is the payment details.<br><br>
            <b>ID : </b>'.$userDetails->id.'<br>
            <b>Name : </b>'.$userDetails->name.'<br>
            <b>Email : </b>'.$userDetails->email.'<br>
            <b>Stripe ID : </b>'.$subscriptionDetails->stripe_id.'<br>
            <b>Billing Cycle Date : </b>'.$subscriptionDetails->billing_cycle_date.'<br>
            <b>Current Period Start : </b>'.$subscriptionDetails->current_period_start.'<br>
            <b>Current Period End : </b>'.$subscriptionDetails->current_period_end.'<br>
            <b>Customer : </b>'.$subscriptionDetails->customer.'<br>
            <b>Amount : </b>'.$subscriptionDetails->amount.'<br>
            <b>Currency : </b>'.$subscriptionDetails->currency.'<br>
            <b>Interval : </b>'.$subscriptionDetails->interval_val.'<br>
            <b>Stripe Status : </b>'.$subscriptionDetails->stripe_status.'.' 
        ];

        $adminEmail = Settings::where('name','Email')->get()->pluck('value')->first();
        Notification::route('mail' , $adminEmail)->notify(new UserSubscriptionNotification($adminSendData));
        
    }


    /*
     * Function to send mail notification to admin if user has Cancel subscriptions
    */
    public static function sendCancelSubscriptionEmailNotification($userDetails,$subscriptionDetails){
        // Send email to user
        $sendData = [
            'body' =>'Your <span style="color:#35C0ED;">WellKabinet</span> Subscriptions canceled successfully, Below are subscription details.<br><br>
            <b>ID : </b>'.$userDetails->id.'<br>
            <b>Name : </b>'.$userDetails->name.'<br>
            <b>Email : </b>'.$userDetails->email.'<br>
            <b>Stripe ID : </b>'.$subscriptionDetails->stripe_id.'<br>
            <b>Billing Cycle Date : </b>'.$subscriptionDetails->billing_cycle_date.'<br>
            <b>Current Period Start : </b>'.$subscriptionDetails->current_period_start.'<br>
            <b>Current Period End : </b>'.$subscriptionDetails->current_period_end.'<br>
            <b>Customer : </b>'.$subscriptionDetails->customer.'<br>
            <b>Amount : </b>'.$subscriptionDetails->amount.'<br>
            <b>Currency : </b>'.$subscriptionDetails->currency.'<br>
            <b>Interval : </b>'.$subscriptionDetails->interval_val.'<br>
            <b>Stripe Status : </b>'.$subscriptionDetails->stripe_status.'.' 
        ];

        Notification::route('mail' , $userDetails->email)->notify(new UserSubscriptionNotification($sendData));

        // Send email to admin
         $adminSendData = [
            'body' => $userDetails->name.' has been canceled subscription on <span style="color:#35C0ED;">WellKabinet</span>, Below are subscription details.<br><br>
            <b>ID : </b>'.$userDetails->id.'<br>
            <b>Name : </b>'.$userDetails->name.'<br>
            <b>Email : </b>'.$userDetails->email.'<br>
            <b>Stripe ID : </b>'.$subscriptionDetails->stripe_id.'<br>
            <b>Billing Cycle Date : </b>'.$subscriptionDetails->billing_cycle_date.'<br>
            <b>Current Period Start : </b>'.$subscriptionDetails->current_period_start.'<br>
            <b>Current Period End : </b>'.$subscriptionDetails->current_period_end.'<br>
            <b>Customer : </b>'.$subscriptionDetails->customer.'<br>
            <b>Amount : </b>'.$subscriptionDetails->amount.'<br>
            <b>Currency : </b>'.$subscriptionDetails->currency.'<br>
            <b>Interval : </b>'.$subscriptionDetails->interval_val.'<br>
            <b>Stripe Status : </b>'.$subscriptionDetails->stripe_status.'.' 
        ];

        $adminEmail = Settings::where('name','Email')->get()->pluck('value')->first();
        Notification::route('mail' , $adminEmail)->notify(new UserSubscriptionNotification($adminSendData));
        
    }



     /*
     * Function to send mail notification to admin if plan is expired then auto pay and plan is active autometic
    */
    public static function sendRecurringSubscriptionEmailNotification($userDetails,$subscriptionDetails){
        // Send email to user
        $sendData = [
            'body' => 'Your <span style="color:#35C0ED;">WellKabinet</span> subscription has been renewed with below subscription details.<br><br>
            <b>ID : </b>'.$userDetails->id.'<br>
            <b>Name : </b>'.$userDetails->name.'<br>
            <b>Email : </b>'.$userDetails->email.'<br>
            <b>Stripe ID : </b>'.$subscriptionDetails->stripe_id.'<br>
            <b>Billing Cycle Date : </b>'.$subscriptionDetails->billing_cycle_date.'<br>
            <b>Current Period Start : </b>'.$subscriptionDetails->current_period_start.'<br>
            <b>Current Period End : </b>'.$subscriptionDetails->current_period_end.'<br>
            <b>Customer : </b>'.$subscriptionDetails->customer.'<br>
            <b>Amount : </b>'.$subscriptionDetails->amount.'<br>
            <b>Currency : </b>'.$subscriptionDetails->currency.'<br>
            <b>Interval : </b>'.$subscriptionDetails->interval_val.'<br>
            <b>Stripe Status : </b>'.$subscriptionDetails->stripe_status.'<br>
            <b>Trial Start At : </b>'.$subscriptionDetails->trial_start_at.'<br>
            <b>Trial End At : </b>'.$subscriptionDetails->trial_ends_at.'<br>
            <b>Date : </b>'.$subscriptionDetails->trial_ends_at.'.' 
        ];

        
        Notification::route('mail' , $userDetails->email)->notify(new UserSubscriptionNotification($sendData));

        // Send email to admin
         $adminSendData = [
            'body' => $userDetails->name.' <span style="color:#35C0ED;">WellKabinet</span> is renew, Below are subscription details.<br><br>
            <b>ID : </b>'.$userDetails->id.'<br>
            <b>Name : </b>'.$userDetails->name.'<br>
            <b>Email : </b>'.$userDetails->email.'<br>
            <b>Stripe ID : </b>'.$subscriptionDetails->stripe_id.'<br>
            <b>Billing Cycle Date : </b>'.$subscriptionDetails->billing_cycle_date.'<br>
            <b>Current Period Start : </b>'.$subscriptionDetails->current_period_start.'<br>
            <b>Current Period End : </b>'.$subscriptionDetails->current_period_end.'<br>
            <b>Customer : </b>'.$subscriptionDetails->customer.'<br>
            <b>Amount : </b>'.$subscriptionDetails->amount.'<br>
            <b>Currency : </b>'.$subscriptionDetails->currency.'<br>
            <b>Interval : </b>'.$subscriptionDetails->interval_val.'<br>
            <b>Stripe Status : </b>'.$subscriptionDetails->stripe_status.'<br>
            <b>Trial Start At : </b>'.$subscriptionDetails->trial_start_at.'<br>
            <b>Trial End At : </b>'.$subscriptionDetails->trial_ends_at.'<br>
            <b>Date : </b>'.$subscriptionDetails->trial_ends_at.'.' 
        ];

        $adminEmail = Settings::where('name','Email')->get()->pluck('value')->first();
        Notification::route('mail' , $adminEmail)->notify(new UserSubscriptionNotification($adminSendData));
        
    }

    /****
     * Function to send mail notification to admin if user has been failed payment
    */
    public static function sendSubscriptionFailedEmailNotification($userDetails,$subscriptionDetails){
        // Send email to user
        $sendData = [
            'body' => 'Your <span style="color:#35C0ED;">WellKabinet</span> Recurring canceled due to payment of this billing cycle not received, Below are payment details.<br><br>
            <b>ID : </b>'.$userDetails->id.'<br>
            <b>Name : </b>'.$userDetails->name.'<br>
            <b>Email : </b>'.$userDetails->email.'<br>
            <b>Stripe ID : </b>'.$subscriptionDetails->stripe_id.'<br>
            <b>Billing Cycle Date : </b>'.$subscriptionDetails->billing_cycle_date.'<br>
            <b>Current Period Start : </b>'.$subscriptionDetails->current_period_start.'<br>
            <b>Current Period End : </b>'.$subscriptionDetails->current_period_end.'<br>
            <b>Customer : </b>'.$subscriptionDetails->customer.'<br>
            <b>Amount : </b>'.$subscriptionDetails->amount.'<br>
            <b>Currency : </b>'.$subscriptionDetails->currency.'<br>
            <b>Interval : </b>'.$subscriptionDetails->interval_val.'<br>
            <b>Stripe Status : </b>'.$subscriptionDetails->stripe_status.'.' 
        ];
        Notification::route('mail' , $userDetails->email)->notify(new UserSubscriptionNotification($sendData));

        // Send email to admin
         $adminSendData = [
            'body' => 'A user <span style="color:#35C0ED;">WellKabinet</span> Recurring canceled due to payment of this billing cycle not received, Below are payment details.<br><br>
            <b>ID : </b>'.$userDetails->id.'<br>
            <b>Name : </b>'.$userDetails->name.'<br>
            <b>Email : </b>'.$userDetails->email.'<br>
            <b>Stripe ID : </b>'.$subscriptionDetails->stripe_id.'<br>
            <b>Billing Cycle Date : </b>'.$subscriptionDetails->billing_cycle_date.'<br>
            <b>Current Period Start : </b>'.$subscriptionDetails->current_period_start.'<br>
            <b>Current Period End : </b>'.$subscriptionDetails->current_period_end.'<br>
            <b>Customer : </b>'.$subscriptionDetails->customer.'<br>
            <b>Amount : </b>'.$subscriptionDetails->amount.'<br>
            <b>Currency : </b>'.$subscriptionDetails->currency.'<br>
            <b>Interval : </b>'.$subscriptionDetails->interval_val.'<br>
            <b>Stripe Status : </b>'.$subscriptionDetails->stripe_status.'.' 
        ];

        $adminEmail = Settings::where('name','Email')->get()->pluck('value')->first();
        Notification::route('mail' , $adminEmail)->notify(new UserSubscriptionNotification($adminSendData));
        
    }
    
    /****
     * Function to get Interaction icon for each rx-drugs / natural medicine data listing in medicine cabinet 
    */
    public static function getInteractionIcon($userId,$profileMemberId='',$type,$naturalMedicineId='',$drugId='',$productId=''){

        $interactionsRatingValue = asset('images/').'/'.'gray-info.png';
        $interactionsProductRatingValue = asset('images/').'/'.'gray-info.png';
        $finalInteractionsRatingValue = asset('images/').'/'.'gray-info.png';

        // Get interaction icon for natural medicine data
        if($type == 'naturalMedicine'){
                
            // get interaction icon based on the interactions with other drugs added by the logged in user
            $drugIds = MedicineCabinet::where('medicine_cabinet.userId',$userId)->whereNotNull('drugId');
            
            // Check if profile member id exist and get the data from it, else exclude profile member data check
            if(!empty($profileMemberId)){
                $drugIds = $drugIds->where('profileMemberId',$profileMemberId);
            }else{
                $drugIds = $drugIds->whereNull('profileMemberId');
            }
            $drugIds = $drugIds->pluck('drugId')->toArray();
            
            foreach($drugIds as $drugIdVal){

                $getInteractionsData = DrugsInteractions::where('drugId',$drugIdVal)
                ->join('therapy','therapy.id','=','drugs_interactions.naturalMedicineId')
                ->where('naturalMedicineId',$naturalMedicineId)
                ->whereNull('drugs_interactions.deleted_at')->get()->toArray();
                
                $interactionsRatings = array();

                if(!empty($getInteractionsData)){
                    
                    foreach($getInteractionsData as $getInteractionsDataKey => $getInteractionsDataVal){
                        $getInteractionsDataDescription = json_decode($getInteractionsDataVal['interactionDetails'],true);
                        $temp[] = $getInteractionsDataDescription['rating-label'];
                        $interactionsRatings = $temp;
                    }
                    $interactionsRatings = array_unique($interactionsRatings);
                    
                    // if ratings has only one value then set accordingly
                    if(sizeof($interactionsRatings) == '1'){
                        if(in_array('Major',$interactionsRatings)){
                            $interactionsRatingValue = asset('images/').'/'.'red-info.png';
                        }
                        if(in_array('Moderate',$interactionsRatings)){
                            $interactionsRatingValue = asset('images/').'/'.'orange-info.png';
                        }
                        if(in_array('Minor',$interactionsRatings)){
                            $interactionsRatingValue = asset('images/').'/'.'green-info.png';
                        }
                    }

                    // if ratings has two different values then set according to priority coded as below
                    if(sizeof($interactionsRatings) == '2'){
                        if(in_array("Major",$interactionsRatings) && in_array("Moderate", $interactionsRatings)){
                            $interactionsRatingValue = asset('images/').'/'.'red-info.png';
                        }
                        if(in_array("Major",$interactionsRatings) && in_array("Minor", $interactionsRatings)){
                            $interactionsRatingValue = asset('images/').'/'.'red-info.png';
                        }
                        if(in_array("Moderate",$interactionsRatings) && in_array("Minor", $interactionsRatings)){
                            $interactionsRatingValue = asset('images/').'/'.'orange-info.png';
                        }
                    }

                    // if ratings has 3 different values then set according to priority coded as below
                    if(sizeof($interactionsRatings) == '3'){
                        if(in_array("Major",$interactionsRatings) && in_array("Moderate", $interactionsRatings)  && in_array("Minor", $interactionsRatings)){
                            $interactionsRatingValue = asset('images/').'/'.'red-info.png';
                        }
                    }
                    
                }
                
            }
            return $interactionsRatingValue;


        }

        // Get interaction icon for drugs data
        else if($type == 'rxDrug'){
            
            // get interaction icon based on the interactions with other drugs added by the logged in user
            $naturalMedicineIds = MedicineCabinet::where('medicine_cabinet.userId',$userId)->whereNotNull('naturalMedicineId');
            
            // Check if profile member id exist and get the data from it, else exclude profile member data check
            if(!empty($profileMemberId)){
                $naturalMedicineIds = $naturalMedicineIds->where('profileMemberId',$profileMemberId);
            }else{
                $naturalMedicineIds = $naturalMedicineIds->whereNull('profileMemberId');
            }
            $naturalMedicineIds = $naturalMedicineIds->pluck('naturalMedicineId')->toArray();

            if(!empty($naturalMedicineIds)){

                foreach($naturalMedicineIds as $naturalMedicineIdVal){

                    $getInteractionsData = DrugsInteractions::where('naturalMedicineId',$naturalMedicineIdVal)
                    ->join('therapy','therapy.id','=','drugs_interactions.naturalMedicineId')
                    ->where('drugId',$drugId)
                    ->whereNull('drugs_interactions.deleted_at')->get()->toArray();
                    
                    $interactionsRatings = array();
    
                    if(!empty($getInteractionsData)){
                        foreach($getInteractionsData as $getInteractionsDataKey => $getInteractionsDataVal){
                            $getInteractionsDataDescription = json_decode($getInteractionsDataVal['interactionDetails'],true);
                            $temp[] = $getInteractionsDataDescription['rating-label'];
                            $interactionsRatings = $temp;
                        }
                        $interactionsRatings = array_unique($interactionsRatings);
                        
                        // if ratings has only one value then set accordingly
                        if(sizeof($interactionsRatings) == '1'){
                            if(in_array('Major',$interactionsRatings)){
                                $interactionsRatingValue = asset('images/').'/'.'red-info.png';
                            }
                            if(in_array('Moderate',$interactionsRatings)){
                                $interactionsRatingValue = asset('images/').'/'.'orange-info.png';
                            }
                            if(in_array('Minor',$interactionsRatings)){
                                $interactionsRatingValue = asset('images/').'/'.'green-info.png';
                            }
                        }
    
                        // if ratings has two different values then set according to priority coded as below
                        if(sizeof($interactionsRatings) == '2'){
                            if(in_array("Major",$interactionsRatings) && in_array("Moderate", $interactionsRatings)){
                                $interactionsRatingValue = asset('images/').'/'.'red-info.png';
                            }
                            if(in_array("Major",$interactionsRatings) && in_array("Minor", $interactionsRatings)){
                                $interactionsRatingValue = asset('images/').'/'.'red-info.png';
                            }
                            if(in_array("Moderate",$interactionsRatings) && in_array("Minor", $interactionsRatings)){
                                $interactionsRatingValue = asset('images/').'/'.'orange-info.png';
                            }
                        }
    
                        // if ratings has 3 different values then set according to priority coded as below
                        if(sizeof($interactionsRatings) == '3'){
                            if(in_array("Major",$interactionsRatings) && in_array("Moderate", $interactionsRatings)  && in_array("Minor", $interactionsRatings)){
                                $interactionsRatingValue = asset('images/').'/'.'red-info.png';
                            }
                        }
                        
                    }
                    
                }


                // Check if user has added product to get the interaction data
                $productIds = MedicineCabinet::where('medicine_cabinet.userId',$userId)
                ->whereNotNull('medicine_cabinet.productId')->whereNull('medicine_cabinet.deleted_at');

                $getNaturalMedicineIds = MedicineCabinet::where('medicine_cabinet.userId',$userId)->whereNotNull('medicine_cabinet.productId')->whereNull('medicine_cabinet.deleted_at');

                // Check if profile member id exist and get the data from it, else exclude profile member data check
                if(!empty($profileMemberId)){
                    $productIds = $productIds->where('profileMemberId',$profileMemberId);
                    $getNaturalMedicineIds = $getNaturalMedicineIds->where('medicine_cabinet.profileMemberId',$profileMemberId);
                }else{
                    $productIds = $productIds->whereNull('profileMemberId');
                    $getNaturalMedicineIds = $getNaturalMedicineIds->whereNull('medicine_cabinet.profileMemberId');
                }
                $getProductIds = $productIds->join('product','medicine_cabinet.productId','=','product.id')
                ->pluck('product.productId')->toArray();

                $getNaturalMedicineIds = $getNaturalMedicineIds->join('product','medicine_cabinet.productId','=','product.id')
                ->join('product_therapy','product.productId','=','product_therapy.productId')
                ->whereNull('product_therapy.deleted_at')->whereIn('product_therapy.productId',$getProductIds)->pluck('product_therapy.therapyId')->toArray();
               
                if(!empty($getNaturalMedicineIds)){
                    $interactionsProductRatings = array();
                    foreach($getNaturalMedicineIds as $therapyId){

                        $getInteractionsData = DrugsInteractions::where('naturalMedicineId',$therapyId)
                        ->join('therapy','therapy.id','=','drugs_interactions.naturalMedicineId')
                        ->join('product_therapy','product_therapy.therapyId','=','therapy.id')
                        ->whereIn('product_therapy.productId',$getProductIds) 
                        ->where('drugId',$drugId)
                        ->whereNull('drugs_interactions.deleted_at')->get()->toArray();
                        

        
                        if(!empty($getInteractionsData)){
                            foreach($getInteractionsData as $getInteractionsDataKey => $getInteractionsDataVal){
                                $getInteractionsDataDescription = json_decode($getInteractionsDataVal['interactionDetails'],true);
                                $tempp[] = $getInteractionsDataDescription['rating-label'];
                                $interactionsProductRatings = $tempp;
                            }
                            $interactionsProductRatings = array_unique($interactionsProductRatings);
                            
                            // if ratings has only one value then set accordingly
                            if(sizeof($interactionsProductRatings) == '1'){
                                if(in_array('Major',$interactionsProductRatings)){
                                    $interactionsProductRatingValue = asset('images/').'/'.'red-info.png';
                                }
                                if(in_array('Moderate',$interactionsProductRatings)){
                                    $interactionsProductRatingValue = asset('images/').'/'.'orange-info.png';
                                }
                                if(in_array('Minor',$interactionsProductRatings)){
                                    $interactionsProductRatingValue = asset('images/').'/'.'green-info.png';
                                }
                            }
        
                            // if ratings has two different values then set according to priority coded as below
                            if(sizeof($interactionsProductRatings) == '2'){
                                if(in_array("Major",$interactionsProductRatings) && in_array("Moderate", $interactionsProductRatings)){
                                    $interactionsProductRatingValue = asset('images/').'/'.'red-info.png';
                                }
                                if(in_array("Major",$interactionsProductRatings) && in_array("Minor", $interactionsProductRatings)){
                                    $interactionsProductRatingValue = asset('images/').'/'.'red-info.png';
                                }
                                if(in_array("Moderate",$interactionsProductRatings) && in_array("Minor", $interactionsProductRatings)){
                                    $interactionsProductRatingValue = asset('images/').'/'.'orange-info.png';
                                }
                            }
        
                            // if ratings has 3 different values then set according to priority coded as below
                            if(sizeof($interactionsProductRatings) == '3'){
                                if(in_array("Major",$interactionsProductRatings) && in_array("Moderate", $interactionsProductRatings)  && in_array("Minor", $interactionsProductRatings)){
                                    $interactionsProductRatingValue = asset('images/').'/'.'red-info.png';
                                }
                            }
                            
                        }
                        
                    }

                    // Include interaction of product & rx drug if there
                    if(!empty($interactionsRatingValue) && !empty($interactionsProductRatingValue)){
                        $interactionsRatingValue = explode(",", $interactionsRatingValue);
                        $interactionsProductRatingValue = explode(",", $interactionsProductRatingValue);
                        
                        $interactionsRatingValue = array_merge($interactionsRatingValue,$interactionsProductRatingValue);
                       
                        $major = asset('images/').'/'.'red-info.png';
                        $moderate = asset('images/').'/'.'orange-info.png';
                        $minor = asset('images/').'/'.'green-info.png';
                        $none = asset('images/').'/'.'gray-info.png';
                        
                        // arrange unique values from the interaction ratings
                        $interactionsRatingValue = array_unique($interactionsRatingValue);

                        // delete none interaction if there from merged interaction
                        $interactionsRatingValue = array_diff($interactionsRatingValue,[$none]);

                        // if ratings has only one value then set accordingly, else check with two data
                        if(sizeof($interactionsRatingValue) == '1'){
                            
                            if(in_array($major,$interactionsRatingValue)){
                                $finalInteractionsRatingValue = $major;
                            }
                            if(in_array($moderate,$interactionsRatingValue)){
                                $finalInteractionsRatingValue = $moderate;
                            }
                            if(in_array($minor,$interactionsRatingValue)){
                                $finalInteractionsRatingValue = $minor;
                            }

                        }else{

                            if(in_array($major,$interactionsRatingValue) && in_array($moderate, $interactionsRatingValue)){
                                $finalInteractionsRatingValue = $major;
                            }
                            if(in_array($major,$interactionsRatingValue) && in_array($minor, $interactionsRatingValue)){
                                $finalInteractionsRatingValue = $major;
                            }
                            if(in_array($moderate,$interactionsRatingValue) && in_array($minor, $interactionsRatingValue)){
                                $finalInteractionsRatingValue = $moderate;
                            }
                        }                        

                        $interactionsRatingValue = $finalInteractionsRatingValue;

                    }
                    

                }



            }
            else{

                $productIds = MedicineCabinet::where('medicine_cabinet.userId',$userId)
                ->whereNotNull('medicine_cabinet.productId')->whereNull('medicine_cabinet.deleted_at');

                $naturalMedicineIds = MedicineCabinet::where('medicine_cabinet.userId',$userId)->whereNotNull('medicine_cabinet.productId')->whereNull('medicine_cabinet.deleted_at');

                // Check if profile member id exist and get the data from it, else exclude profile member data check
                if(!empty($profileMemberId)){
                    $productIds = $productIds->where('profileMemberId',$profileMemberId);
                    $naturalMedicineIds = $naturalMedicineIds->where('medicine_cabinet.profileMemberId',$profileMemberId);
                }else{
                    $productIds = $productIds->whereNull('profileMemberId');
                    $naturalMedicineIds = $naturalMedicineIds->whereNull('medicine_cabinet.profileMemberId');
                }
                $getProductIds = $productIds->join('product','medicine_cabinet.productId','=','product.id')
                ->pluck('product.productId')->toArray();

                $naturalMedicineIds = $naturalMedicineIds->join('product','medicine_cabinet.productId','=','product.id')
                ->join('product_therapy','product.productId','=','product_therapy.productId')
                ->whereNull('product_therapy.deleted_at')->whereIn('product_therapy.productId',$getProductIds)->pluck('product_therapy.therapyId')->toArray();

                if(!empty($naturalMedicineIds)){
                    
                    foreach($naturalMedicineIds as $therapyId){

                        $getInteractionsData = DrugsInteractions::where('naturalMedicineId',$therapyId)
                        ->join('therapy','therapy.id','=','drugs_interactions.naturalMedicineId')
                        ->join('product_therapy','product_therapy.therapyId','=','therapy.id')
                        ->whereIn('product_therapy.productId',$getProductIds) 
                        ->where('drugId',$drugId)
                        ->whereNull('drugs_interactions.deleted_at')->get()->toArray();
                        
                        
        
                        if(!empty($getInteractionsData)){
                            $interactionsOnlyProductsRatings = array();
                            foreach($getInteractionsData as $getInteractionsDataKey => $getInteractionsDataVal){
                                $getInteractionsDataDescription = json_decode($getInteractionsDataVal['interactionDetails'],true);
                                $temporaryArr[] = $getInteractionsDataDescription['rating-label'];
                                $interactionsOnlyProductsRatings = $temporaryArr;
                            }
                            $interactionsOnlyProductsRatings = array_unique($interactionsOnlyProductsRatings);
                            
                            // if ratings has only one value then set accordingly
                            if(sizeof($interactionsOnlyProductsRatings) == '1'){
                                if(in_array('Major',$interactionsOnlyProductsRatings)){
                                    $interactionsProductRatingValue = asset('images/').'/'.'red-info.png';
                                }
                                if(in_array('Moderate',$interactionsOnlyProductsRatings)){
                                    $interactionsProductRatingValue = asset('images/').'/'.'orange-info.png';
                                }
                                if(in_array('Minor',$interactionsOnlyProductsRatings)){
                                    $interactionsProductRatingValue = asset('images/').'/'.'green-info.png';
                                }
                            }
        
                            // if ratings has two different values then set according to priority coded as below
                            if(sizeof($interactionsOnlyProductsRatings) == '2'){
                                if(in_array("Major",$interactionsOnlyProductsRatings) && in_array("Moderate", $interactionsOnlyProductsRatings)){
                                    $interactionsProductRatingValue = asset('images/').'/'.'red-info.png';
                                }
                                if(in_array("Major",$interactionsOnlyProductsRatings) && in_array("Minor", $interactionsOnlyProductsRatings)){
                                    $interactionsProductRatingValue = asset('images/').'/'.'red-info.png';
                                }
                                if(in_array("Moderate",$interactionsOnlyProductsRatings) && in_array("Minor", $interactionsOnlyProductsRatings)){
                                    $interactionsProductRatingValue = asset('images/').'/'.'orange-info.png';
                                }
                            }
        
                            // if ratings has 3 different values then set according to priority coded as below
                            if(sizeof($interactionsOnlyProductsRatings) == '3'){
                                if(in_array("Major",$interactionsOnlyProductsRatings) && in_array("Moderate", $interactionsOnlyProductsRatings)  && in_array("Minor", $interactionsOnlyProductsRatings)){
                                    $interactionsProductRatingValue = asset('images/').'/'.'red-info.png';
                                }
                            }
                            
                        }
                        
                    }

                    $interactionsRatingValue = $interactionsProductRatingValue;

                }

            }
            
            return $interactionsRatingValue;

        }

        // Get interaction icon for product data
        else if($type == 'product'){

            // get interaction icon based on the interactions with other natural medicine(therapy) added by the logged in user
            $productIds = MedicineCabinet::where('medicine_cabinet.userId',$userId)->whereNotNull('medicine_cabinet.productId')->whereNull('medicine_cabinet.deleted_at');
            
            $drugIds = MedicineCabinet::where('medicine_cabinet.userId',$userId)->whereNotNull('drugId')->whereNull('deleted_at');

            // Check if profile member id exist and get the data from it, else exclude profile member data check
            if(!empty($profileMemberId)){
                $productIds = $productIds->where('medicine_cabinet.profileMemberId',$profileMemberId);
                $drugIds = $drugIds->where('profileMemberId',$profileMemberId);
            }else{
                $productIds = $productIds->whereNull('medicine_cabinet.profileMemberId');
                $drugIds = $drugIds->whereNull('profileMemberId');
            }
            $therapyIds = $productIds->join('product','medicine_cabinet.productId','=','product.id')
            ->join('product_therapy','product.productId','=','product_therapy.productId')
            ->where('product.productId',$productId)->whereNull('product_therapy.deleted_at')
            ->pluck('therapyId')->toArray();

            $drugIds = $drugIds->pluck('drugId')->toArray();

            if(!empty($drugIds)){

                // drugs array
                foreach($drugIds as $drugId){

                    $getInteractionsData = DrugsInteractions::whereIn('naturalMedicineId',$therapyIds)
                    ->join('therapy','therapy.id','=','drugs_interactions.naturalMedicineId')
                    ->where('drugId',$drugId)
                    ->whereNull('drugs_interactions.deleted_at')->get()->toArray();

                    $interactionsRatings = array();
                    if(!empty($getInteractionsData)){

                        foreach($getInteractionsData as $getInteractionsDataKey => $getInteractionsDataVal){
                            $getInteractionsDataDescription = json_decode($getInteractionsDataVal['interactionDetails'],true);
                            $temp[] = $getInteractionsDataDescription['rating-label'];
                            $interactionsRatings = $temp;
                        }
                        $interactionsRatings = array_unique($interactionsRatings);
                        
                        // if ratings has only one value then set accordingly
                        if(sizeof($interactionsRatings) == '1'){
                            if(in_array('Major',$interactionsRatings)){
                                $interactionsRatingValue = asset('images/').'/'.'red-info.png';
                            }
                            if(in_array('Moderate',$interactionsRatings)){
                                $interactionsRatingValue = asset('images/').'/'.'orange-info.png';
                            }
                            if(in_array('Minor',$interactionsRatings)){
                                $interactionsRatingValue = asset('images/').'/'.'green-info.png';
                            }
                        }

                        // if ratings has two different values then set according to priority coded as below
                        if(sizeof($interactionsRatings) == '2'){
                            if(in_array("Major",$interactionsRatings) && in_array("Moderate", $interactionsRatings)){
                                $interactionsRatingValue = asset('images/').'/'.'red-info.png';
                            }
                            if(in_array("Major",$interactionsRatings) && in_array("Minor", $interactionsRatings)){
                                $interactionsRatingValue = asset('images/').'/'.'red-info.png';
                            }
                            if(in_array("Moderate",$interactionsRatings) && in_array("Minor", $interactionsRatings)){
                                $interactionsRatingValue = asset('images/').'/'.'orange-info.png';
                            }
                        }

                        // if ratings has 3 different values then set according to priority coded as below
                        if(sizeof($interactionsRatings) == '3'){
                            if(in_array("Major",$interactionsRatings) && in_array("Moderate", $interactionsRatings)  && in_array("Minor", $interactionsRatings)){
                                $interactionsRatingValue = asset('images/').'/'.'red-info.png';
                            }
                        }
                    }                                     

                }

            }

            return $interactionsRatingValue;
        }
    }

    //Function to generate random number
    public static function generateRandomNumber($length = 9) {
        return substr(str_shuffle(str_repeat($x='0123456789', ceil($length/strlen($x)) )),1,$length);
    }

    //Function to generate random password ()
    public static function generateRandomPassword($length = 9) {
        $alphanumeric = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
        return substr(str_shuffle(str_repeat($alphanumeric.'@$', ceil($length/strlen($x)) )),1,$length);
    }

    //Function to generated random string password
    public static function generateRandomString($length = 8) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    /****
     * Function to get Interaction label from the interaction image name for the Interaction filters in WellKabinet (Medicine Cabinet) Screen.
    */
    public static function getLabelNameForInteractionFilters($interactionsRatingValue){
        
        $interactionLabel = 'noneInteraction'; // default value if no interaction is found

        // if interaction is found then extract the name of the image and assign the interaction label accordingly
        if(!empty($interactionsRatingValue)){
            switch ($interactionsRatingValue){
                case str_contains($interactionsRatingValue,'red-info'):
                    $interactionLabel = 'majorInteraction';
                    break;
                case str_contains($interactionsRatingValue,'orange-info'):
                    $interactionLabel = 'moderateInteraction';
                    break;
                case str_contains($interactionsRatingValue,'green-info'):
                    $interactionLabel = 'minorInteraction';
                    break;
                case str_contains($interactionsRatingValue,'gray-info'):
                    $interactionLabel = 'noneInteraction';
                    break;      
            }
        }

        return $interactionLabel; 
        
    }

    /****
     * Function to get Interaction label from the interaction image name.
    */
    public static function getPriorityFromInteractionName($interactionsRatingValue){
        
        $interactionPriority = '4'; // default value if no interaction is found

        // if interaction is found then extract the name of the image and assign the interaction label accordingly
        if(!empty($interactionsRatingValue)){
            switch ($interactionsRatingValue){
                case str_contains($interactionsRatingValue,'red-info'):
                    $interactionPriority = '1';
                    break;
                case str_contains($interactionsRatingValue,'orange-info'):
                    $interactionPriority = '2';
                    break;
                case str_contains($interactionsRatingValue,'green-info'):
                    $interactionPriority = '3';
                    break;
                case str_contains($interactionsRatingValue,'gray-info'):
                    $interactionPriority = '4';
                    break;      
            }
        }

        return $interactionPriority; 
        
    }


    public static function getProductsInteractionWithDrug($getProductIds,$drugId,$dataDisplayType,$profileMemberId){

        // Get Logged in user id
        $userId = Auth::user()->id;

        $html = '';
        $htmlNew = '';
        $class = "";
        $finalArray = array();
        $circle_class = "";


        // Get the therapy ids of product_therapy table
        $productIds = MedicineCabinet::where('medicine_cabinet.userId',$userId)->whereNotNull('medicine_cabinet.productId')->whereNull('medicine_cabinet.deleted_at');

        // Check if current medicine cabinet access is of profile member, else exclude profile member id selection
        if(!empty($profileMemberId)){
            $productIds = $productIds->where('medicine_cabinet.profileMemberId',$profileMemberId);
        }
        else{
            $productIds = $productIds->whereNull('medicine_cabinet.profileMemberId');
        }

        $naturalMedicineIds = $productIds->join('product','medicine_cabinet.productId','=','product.id')
        ->join('product_therapy','product.productId','=','product_therapy.productId')
        ->whereNull('product_therapy.deleted_at')->whereIn('product_therapy.productId',$getProductIds)->pluck('product_therapy.therapyId')->toArray();


        if(!empty($naturalMedicineIds)){
            foreach ($naturalMedicineIds as $naturalMedicineIdVal){
    
                $getDrugData = DB::table('drugs')->where('id',$drugId)->get()->first();

                $getInteractionsData = DrugsInteractions::where('drugId',$drugId)
                ->join('therapy','therapy.id','=','drugs_interactions.naturalMedicineId')
                ->join('product_therapy','product_therapy.therapyId','=','therapy.id')
                ->whereIn('product_therapy.productId',$getProductIds)
                ->where('naturalMedicineId',$naturalMedicineIdVal)
                ->whereNull('drugs_interactions.deleted_at')->get()->toArray();

                if(!empty($getInteractionsData)){
                    $getInteractionsData = json_decode(json_encode($getInteractionsData),true);
                    foreach($getInteractionsData as $key => $fdata){
                        $interData = $fdata['interactionDetails'];
                        $interData = json_decode($interData,true);
                        $referenceNumbers = $interData['reference-numbers'];
                        $description = '';
                        
                        if(strtolower($interData['rating-label']) == 'major'){
                            $class = "red-card";
                            $circle_class = asset('images/major.svg');
                            $interactionPriority = '1';
                        }else if(strtolower($interData['rating-label']) == 'moderate'){
                            $class = "orange-card";
                            $circle_class = asset('images/moderate.svg');
                            $interactionPriority = '2';
                        }else if(strtolower($interData['rating-label']) == 'minor'){
                            $class = "green-card";
                            $circle_class = asset('images/minor.svg');
                            $interactionPriority = '3';
                        }else{
                            $class = "gray-card";
                            $circle_class = asset('images/none.svg');
                            $interactionPriority = '4';
                        }
    
                        $description = $fdata['description'];
                        
                        //Reference Start
                        $therapyReferenceDetails = NaturalMedicineReference::whereIn('number',$referenceNumbers)
                        ->whereNull('deleted_at')->get()->toArray();
                        if(!empty($therapyReferenceDetails)){
                            foreach ($therapyReferenceDetails as $therapyReferenceDetailsKey => $therapyReferenceDetailsValue) {
                            
                                $referenceNumber = $therapyReferenceDetailsValue['number'];
                                $referenceDescription = $therapyReferenceDetailsValue['description'];
        
                                $clickHereToResearch = '';
                                if(!empty($therapyReferenceDetailsValue['medicalPublicationId'])){
                                    $medicalPublicationId = $therapyReferenceDetailsValue['medicalPublicationId'];
                                    $researchApi = 'https://pubmed.ncbi.nlm.nih.gov/'.$medicalPublicationId;
                                    $clickHereToResearch = '<br/><a href='.$researchApi.' target=\'_blank\'>Click to see research</a>';
                                }
                                else
                                {
                                    $clickHereToResearch = '<br/><a href="javascript:void(0)" class="text-secondary">Click to see research</a>';
                                }
                                $referenceDescriptionDataContent = $referenceDescription.' '.$clickHereToResearch;
                                
                                //Replace double quote with single quote as it was creating issue on anchor tag
                                $referenceDescriptionDataContent = str_replace('"', "'", $referenceDescriptionDataContent);
                                $anchorTag = '<a tabindex="0" class="dd" data-placement="top" role="button" data-toggle="popover" data-html="true" data-trigger="focus" data-content="'.$referenceDescriptionDataContent.'" target="_blank" >'.$referenceNumber.'</a>';
    
                                $description = str_replace("(".$referenceNumber.",","(".$anchorTag.",",$description);
    
                                $description = str_replace("(".$referenceNumber.")","(".$anchorTag.")",$description);
    
                                $description = str_replace(",".$referenceNumber.",",",".$anchorTag.",",$description);
    
                                $description = str_replace(",".$referenceNumber.")",",".$anchorTag.")",$description);
                            }
                        }
                        //Reference End
    
                        $data['description'] = $description;
                        $interactionsData[] = $data;
                        
                        
                        // Get level of evidence definition - Start
                        $levelOfEvidenceDefinitionVal = '';
                        $levelOfEvidenceDefinition = $interData['level-of-evidence-definition'];
                        if(!empty($levelOfEvidenceDefinition)){
                            switch ($levelOfEvidenceDefinition) {
                                case "High-quality randomized controlled trial (RCT)":
                                    $levelOfEvidenceDefinitionVal = 'a1';
                                    break;
                                case "High-quality meta-analysis (quantitative systematic review)":
                                    $levelOfEvidenceDefinitionVal = 'a2';
                                    break;
                                case "Nonrandomized clinical trial":
                                    $levelOfEvidenceDefinitionVal = 'b1';
                                    break;
                                case "Nonquantitative systematic review":
                                    $levelOfEvidenceDefinitionVal = 'b2';
                                    break;
                                case "Lower quality RCT":
                                    $levelOfEvidenceDefinitionVal = 'b3';
                                    break;
                                case "Clinical cohort study":
                                    $levelOfEvidenceDefinitionVal = 'b4';
                                    break;
                                case "Case-control study":
                                    $levelOfEvidenceDefinitionVal = 'b5';
                                    break;
                                case "Historical control":
                                    $levelOfEvidenceDefinitionVal = 'b6';
                                    break;
                                case "Epidemiologic study":
                                    $levelOfEvidenceDefinitionVal = 'b7';
                                    break;
                                case "Consensus":
                                    $levelOfEvidenceDefinitionVal = 'c1';
                                    break;
                                case "Expert opinion":
                                    $levelOfEvidenceDefinitionVal = 'c2';
                                    break;
                                case "Anecdotal evidence":
                                    $levelOfEvidenceDefinitionVal = 'd1';
                                    break;
                                case "In vitro or animal study":
                                    $levelOfEvidenceDefinitionVal = 'd2';
                                    break;
                                case "Theoretical based on pharmacology":
                                    $levelOfEvidenceDefinitionVal = 'd3';
                                    break;
                            }
                        }
                        // Get level of evidence definition - End
    
    
                        $temp = array();
                        $temp['therapy'] = $fdata['therapy'];
                        $temp['title'] = $interData['title'];
                        $temp['severity'] = $fdata['severity'];
                        $temp['drugName'] = $fdata['drugName']." - ".$getDrugData->brand_name;
                        $temp['drugId'] = $drugId;
                        $temp['naturalMedicineId'] = $naturalMedicineIdVal;
                        $temp['interaction_display_name'] = $fdata['interaction_display_name'];
                        $temp['occurrence'] = $fdata['occurrence'];
                        $temp['levelOfEvidence'] = $fdata['levelOfEvidence'];
                        $temp['levelOfEvidenceDefinition'] = $levelOfEvidenceDefinitionVal;
                        $temp['description'] = $description;
                        $temp['interactionPriority'] = $interactionPriority;
                        $temp['interactionRating'] = strtolower($interData['rating-label']);
                        $temp['interactionIcon'] = $circle_class;
                        $temp['class'] = $class;
                        $temp['interactionRatingText'] = $interData['rating-text'];
                        $temp['isInteractionsFound'] = 1;
                        array_push($finalArray,$temp);
                    }
                }
            }


            //sort list of data of interactions with Rx Drugs in interaction rating priority order - code start 
            usort($finalArray, function($finalArrayOne, $finalArrayTwo) {
                return $finalArrayOne['interactionPriority'] <=> $finalArrayTwo['interactionPriority'];
            });
            //sort list of data of interactions with Rx Drugs in interaction rating priority order - code end

            $lastArray = $finalArray;
            
            // just display with data if '1' and not with the html design, else display with html design
            if($dataDisplayType == '1'){
                return $lastArray;
            }else{

                // Arrange array by the interaction_display_name name
                foreach($finalArray as $entry => $vals)
                {
                    if(isset($vals['interaction_display_name']) || !empty($vals['interaction_display_name'])){
                        $lastArray[$vals['interaction_display_name']][]=$vals;
                        unset($lastArray[$entry]);
                    }
                }
                foreach ($lastArray as $key => $value) {
                    $no = Helpers::generateRandomNumber();
                    $card_color = $value[0]['class'];
                    $html .= "<div class='card ".$card_color."'>";
                        $html .= "<div class='card-head' id='headingInteractions'".$no.">";
                            $html .= "<h2 class='mb-0 collapsed' data-toggle='collapse' data-target='#collapseInteractions".$no."' aria-expanded='false' aria-controls='collapseInteractions".$no."'>";
                                $html .= "<div class='cabinet-acco-img'><img src=".$value[0]['interactionIcon']." alt='major'></div>";
                                $html .= "<div class='int-headeing'>";
                                    
                                    if($value[0]['isInteractionsFound'] == '1'){
                                        $html .= "<div class='cabinet-acco-title'>";
                                            $html .= $value[0]['interaction_display_name']." <br><span style='color: #4f4f4f;'>Interaction Rating:<strong> ".ucfirst($value[0]['interactionRating'])."</strong></span>";
                                        $html .= "</div>";
                                        $html .= "<div class='combination'>".$value[0]['interactionRatingText']."</div>";
                                    }else{
                                        $html .= "<div class='cabinet-acco-title pt-3'>";
                                            $html .= $value[0]['interaction_display_name']." <br><span style='color: #4f4f4f;'>No interactions found</span>";
                                        $html .= "</div>";
                                    }
    
                                $html .= "</div>";
                            $html .= "</h2>";
                        $html .= "</div>";
                        
                        if($value[0]['isInteractionsFound'] == '1'){
    
                            $html .= "<div id='collapseInteractions".$no."' class='collapse' aria-labelledby='headingInteractions".$no."' data-parent='#accordionInteractions'>";
                                $html .= "<div class='card-body'>";
                                    
                                    $interactionsDataArrCount = count($value) - 1; // get the count of interaction data

                                    foreach ($value as $valueKey => $data) {
                                        $html .= "<p>";
                                            $html .= "<span><b>".$data['title']."</b></span></br></br>";
                                            $html .= "<span>Interaction Rating = ".ucfirst($data['interactionRating'])."</span></br></br>";
                                            $html .= "Severity = ".ucfirst($data['severity'])."<br>";
                                            $html .= "Occurance = ".ucfirst($data['occurrence'])."<br>";
                                            $html .= "Level of Evidence = <a href='javascript:void(0);' onClick='showLevelOfEvidencePopUp(".$data['levelOfEvidenceDefinition'].")' title='Click here to see what it means'>".$data['levelOfEvidence']."</a>";
                                        $html .= "</p>";
                                        $html .= $data['description'];
    
                                        // Add new line if there are more than one interaction data
                                        if($interactionsDataArrCount != $valueKey){
                                            $html .= "<br><br><br>";
                                        }
                                    }
    
                                $html .= "</div>";
                            $html .= "</div>";
                        }
    
                    $html .= "</div>";
                }
                return $html;
            }
        }

    }

    /****
     * Function for read CSV file and send response in array
    */
    public static function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }


    /**
     * Function use to add user details
     * */
    public static function createUser(Array $data) 
    {
        $firstName = array_key_exists('name',$data) ? $data['name'] : null;
        $lastName = array_key_exists('last_name',$data) ? $data['last_name'] : null;
        $email = $data['email'] ? $data['email'] : '';
        $gender = array_key_exists('gender',$data) ? $data['gender'] : '';
        $dateOfBirth = array_key_exists('dateOfBirth',$data) ? $data['dateOfBirth'] : '';
        $role_id = $data['accountType'] ? $data['accountType'] : '';
        $subscription_type = array_key_exists('subscriptionType',$data) ? $data['subscriptionType'] : '';
        $age = null;
        
        /***
         * Check if gender and date of birth has values then mark updated complete profile for this user, 
         * else redirect to profile page to complete profile details to access the website 
         *  */ 
        $updatedCompleteProfile = '0';
        if(!empty($gender) && !empty($dateOfBirth)){
            $updatedCompleteProfile = '1';
            $age = Carbon::parse($dateOfBirth)->diff(Carbon::now())->y;
        }

       
        /* If role id is 2 (i.e, Paitent / Caregiver) then add wellkasa plus subscription in user table, else allow basic type  */
        if($role_id == '2'){

            $planType = '1'; // wellkasa basic
            $appendMsg = '';
            // Check if subscription is added, if added then give plus subscription option
            if(!empty($subscription_type)){
                $planType = '2'; // wellkasa plus subscription
                $type = $subscription_type == '1' ? 'monthly' : 'annual';
                $appendMsg = ' and your loved ones. Your '.$type.' subscription is now active';
            }
            $websiteName = 'Wellkabinet'; // for email message
            $bodyEmailTop = "Thank you for choosing WellKabinet – personal digital medicine cabinet for you".$appendMsg;
            $bottmMsg = 'Wishing you good health!';
        }else{
            $appendMsg = '';
            // Check if subscription is added, if added then change message.
            if(!empty($subscription_type)){
                $type = $subscription_type == '1' ? 'monthly' : 'annual';
                $appendMsg = ' Your '.$type.' subscription is now active';
            }
            $planType = '1';  // wellkasa basic
            $websiteName = 'Wellkasa Rx';  // for email message
            $bodyEmailTop = "Thank you for choosing Wellkasa Rx – your Integrative & Functional Medicine digital companion for clinical and training support.".$appendMsg;
            $bottmMsg = 'Regards!';
        }

        /* Create random password containing alphanumeric and characters */
        $password = Helpers::generateRandomPassword();

        // Begin a transaction 
        DB::beginTransaction();

        // User creation
        $user = User::create([
            'name'=>$firstName,
            'last_name'=>$lastName,
            'email'=>$email,
            'password'=>bcrypt($password),
            'gender'=>$gender,
            'ageRange' => $age,
            'paitentAge' => $age,
            'dateOfBirth' =>$dateOfBirth,
            'status'=>'1',
            'email_verified_at' => Carbon::now()
        ]);

        // if user created successfully
        if(!empty($user))
        {
            //Store in user_role table as well
            DB::table('user_roles')->insert(
                ['user_id' => $user->id, 'role_id' => $role_id,'status'=>'1','created_at' => Carbon::now()]
            );

            // Add complete profile check 
            DB::table('users')->where('id',$user->id)->update([
                'planType' => $planType,
                'updatedCompleteProfile' => $updatedCompleteProfile,
                'profileMemberCount' => '5',
                'remainingProfileMemberCount' => '5'
            ]);

            //Check if subscription is added then add subscription based on input for current user in subscriptions table
            if(!empty($subscription_type)){

                $currentDate = date('Y-m-d 00:00', strtotime(Carbon::now()));
                if($subscription_type == '1'){
                    $tillValidDate = date('Y-m-d 23:59:59', strtotime('+1 month -1 day',strtotime($currentDate)));
                }
                if($subscription_type == '12'){
                    $tillValidDate = date('Y-m-d 23:59:59', strtotime('+1 year -1 day',strtotime($currentDate)));
                }
                DB::table('subscriptions')->insert([
                    'user_id' => $user->id, 
                    'name' => 'default',
                    'billing_cycle_date' => $tillValidDate,
                    'current_period_start' => $currentDate,
                    'current_period_end' => $tillValidDate,
                    'stripe_status' => 'active',
                    'stripe_id' => 'sub_1'.Helpers::generateRandomString(24),
                    'created_at' => Carbon::now(),
                ]);
            }
            

            // Send email to user with credentials - Start
            $userName = strtok($email, '@');
            $sendData = [
                'body' => $bodyEmailTop.'<br>
                Here are the details for your account access:

                <b>URL</b>: <a href="'.url('/login').'">'.url('/login').'</a>
                <b>Email</b>: '.$email.'</a>
                <b>Password</b>: '.$password.'</a>
                
                This is a system generated password. We encourage you to change this password from your '.$websiteName.' profile settings after you login.
                
                '.$bottmMsg.'
                
                For any help please reach us at support@wellkasa.com

                <b>Team Wellkasa</b>',
            ];
            // Send mail to new created user
            Notification::route('mail',$email)->notify(new NewUserCreatedByAdminNotification($sendData,$userName));

            // Send mail to admin with user details when new user is created 
            Notification::route('mail','admin@wellkasa.com')->notify(new NewUserCreatedByAdminNotification($sendData,$userName));

            // Send email to user with credentials - End

            // If data is successfully added then commit the changes
            DB::commit();
            return true;

        }else{

           // If some error occurred while save data then rollback the records from database
           DB::rollback();
           return false;
        }

        return true;
           
    }

    /**
     * Function to update user account and subscription
     * */
    public static function updateUserAccountAndSubscription($userId, $roleId = '', $subscription_type = ''){

        $userId = $userId;
        $role_id = $roleId ? $roleId : '';
        $subscriptionType = $subscription_type ? $subscription_type : '';
        $is_error = 0;

        // Update user role if value exist
        if(!empty($role_id)){
            DB::beginTransaction();
            $updateRole = DB::table('user_roles')->where('user_id',$userId)->update([
                'role_id' => $role_id,
                'updated_at' => Carbon::now()
            ]);
            if($updateRole){
                DB::commit();
            }else{
                DB::rollback();
                $is_error++;
            }
        }

        if(!empty($subscriptionType)){ 
            // Check profile member count
            $currentSelectedUser = User::where('id',$userId)->get()->first();
            if($currentSelectedUser->profileMemberCount == 0){
                // Add user subscription to wellkabinet
                User::where('id',$userId)->update([
                    'planType' => '2',
                    'profileMemberCount' => '5',
                    'remainingProfileMemberCount' => '5',
                ]);
            }

            //Add subscription based on selection for current user in subscriptions table
            $currentDate = date('Y-m-d 00:00', strtotime(Carbon::now()));
            // Monthly subscription
            if($subscriptionType == '1'){
                $tillValidDate = date('Y-m-d 23:59:59', strtotime('+1 month -1 day',strtotime($currentDate)));
            }
            // Yearly subscription
            if($subscriptionType == '12'){
                $tillValidDate = date('Y-m-d 23:59:59', strtotime('+1 year -1 day',strtotime($currentDate)));
            }

            // If user has existing subscription then extend the end date, else add new subscription
            $subscription = Subscriptions::where('user_id',$userId)->get()->first();
            if(!empty($subscription)){
                DB::beginTransaction();
                // if subscription already exists, then extend from the last subscription end date by the subscription type
                if($subscriptionType == '1'){
                    $tillValidDate = date('Y-m-d 23:59:59', strtotime('+1 month -1 day',strtotime($subscription->current_period_end)));
                }
                if($subscriptionType == '12'){
                    $tillValidDate = date('Y-m-d 23:59:59', strtotime('+1 year -1 day',strtotime($subscription->current_period_end)));
                }
                $updateSubscription = Subscriptions::where('id',$subscription->id)->update([
                    "billing_cycle_date" => $tillValidDate,
                    "current_period_end" => $tillValidDate
                ]);
                // If subscription updated successfully then show success response
                if($updateSubscription){
                    
                    DB::commit(); // Commit the transaction in table if data is updated successfully

                }else{
                    // If subscription failed to update then show error response
                    
                    DB::rollback(); // rollback the changes while data failed to update in the table
                    $is_error++;
                }

            }else{
                DB::beginTransaction();
                $subscriptionDetails = new Subscriptions;
                $subscriptionDetails->user_id = $userId; 
                $subscriptionDetails->name = 'default';
                $subscriptionDetails->billing_cycle_date = $tillValidDate;
                $subscriptionDetails->current_period_start = $currentDate;
                $subscriptionDetails->current_period_end = $tillValidDate;
                $subscriptionDetails->stripe_status = 'active';
                $subscriptionDetails->stripe_id = 'sub_1'.Helpers::generateRandomString(24);
                $subscriptionDetails->created_at = Carbon::now();
                
                // If subscription saved successfully then show success response
                if($subscriptionDetails->save()){
                    
                    DB::commit(); // Commit the transaction in table if data is saved successfully

                }else{
                    // If subscription failed to save then show error response
                    
                    DB::rollback(); // rollback the changes while data failed to add in the table
                    $is_error++;
                }
            }
        }
        // If no error while updating / saving records then return true, else return false
        if($is_error == 0){
            return true;
        }else{
            return false;
        }
        
    }

    /***
     * Function to add/update subscription from edit user form
     */
    public static function addUpdateUserSubscription($userId, $subscription_type = '',$extend_date = ''){

        $userId = $userId;
        $subscriptionType = $subscription_type;
        $extendDate = $extend_date;

        DB::beginTransaction();

        // Check profile member count
        $currentSelectedUser = User::where('id',$userId)->get()->first();
        if($currentSelectedUser->profileMemberCount == 0){
            // Add user subscription to wellkabinet
            User::where('id',$userId)->update([
                'planType' => '2',
                'profileMemberCount' => '5',
                'remainingProfileMemberCount' => '5',
            ]);
        }

        //Add subscription based on selection for current user in subscriptions table
        $currentDate = date('Y-m-d 00:00', strtotime(Carbon::now()));
        // Monthly subscription
        if($subscriptionType == '1'){
            $tillValidDate = date('Y-m-d 23:59:59', strtotime('+1 month -1 day',strtotime($currentDate)));
        }
        // Yearly subscription
        if($subscriptionType == '12'){
            $tillValidDate = date('Y-m-d 23:59:59', strtotime('+1 year -1 day',strtotime($currentDate)));
        }
        // Custom end date subscription
        if($subscriptionType == '3'){
            $tillValidDate = $extendDate;
        }
        // If user has existing subscription then extend the end date, else add new subscription
        $subscription = Subscriptions::where('user_id',$userId)->get()->first();
        if(!empty($subscription)){
            // if subscription already exists, then extend from the last subscription end date by the subscription type
            if($subscriptionType == '1'){
                $tillValidDate = date('Y-m-d 23:59:59', strtotime('+1 month -1 day',strtotime($subscription->current_period_end)));
            }
            if($subscriptionType == '12'){
                $tillValidDate = date('Y-m-d 23:59:59', strtotime('+1 year -1 day',strtotime($subscription->current_period_end)));
            }
            if($subscriptionType == '3'){
                $tillValidDate = $extendDate;
            }
            $updateSubscription = Subscriptions::where('id',$subscription->id)->update([
                "billing_cycle_date" => $tillValidDate,
                "current_period_end" => $tillValidDate
            ]);
            // If subscription updated successfully then show success response
            if($updateSubscription){
                
                DB::commit(); // Commit the transaction in table if data is updated successfully
                return true;

            }else{
                // If subscription failed to update then show error response
                
                DB::rollback(); // rollback the changes while data failed to update in the table
                return false;
            }

        }else{

            $subscriptionDetails = new Subscriptions;
            $subscriptionDetails->user_id = $userId; 
            $subscriptionDetails->name = 'default';
            $subscriptionDetails->billing_cycle_date = $tillValidDate;
            $subscriptionDetails->current_period_start = $currentDate;
            $subscriptionDetails->current_period_end = $tillValidDate;
            $subscriptionDetails->stripe_status = 'active';
            $subscriptionDetails->stripe_id = 'sub_1'.Helpers::generateRandomString(24);
            $subscriptionDetails->created_at = Carbon::now();
            
            // If subscription saved successfully then show success response
            if($subscriptionDetails->save()){
                
                DB::commit(); // Commit the transaction in table if data is saved successfully
                return true;

            }else{
                // If subscription failed to save then show error response
                
                DB::rollback(); // rollback the changes while data failed to add in the table
                return false;
            }
        }
        
        
    }


    /**
     * Function use to add/update product details
     * */
    public static function createOrUpdateProduct(Array $data, $type) 
    {
        $productIdVal = array_key_exists('productId',$data) ? $data['productId'] : null;
        $productNameVal = array_key_exists('productName',$data) ? $data['productName'] : null;
        $productSizeVal = array_key_exists('productSize',$data) ? $data['productSize'] : null;
        $productDescriptionVal = array_key_exists('productDescription',$data) ? $data['productDescription'] : null;
        $productBrandVal = array_key_exists('productBrand',$data) ? $data['productBrand'] : null;
        $shopifyProductLinkVal = array_key_exists('shopifyProductLink',$data) ? $data['shopifyProductLink'] : null;
        $productImageLinkVal = array_key_exists('productImageLink',$data) ? $data['productImageLink'] : null;
        $productSkuCodeVal = array_key_exists('skuCode',$data) ? $data['skuCode'] : null;
        $backUpProductIdVal = array_key_exists('backUpProductId',$data) ? $data['backUpProductId'] : null;
        $isActiveVal = array_key_exists('isActive',$data) ? $data['isActive'] : null;

        // Begin a transaction 
        DB::beginTransaction();

        if($type == 'create'){
            // Product creation
            $product = Product::create([
                'productId' => $productIdVal,
                'productName'=>$productNameVal,
                'productSize'=>$productSizeVal,
                'productDescription'=>$productDescriptionVal,
                'productBrand'=>$productBrandVal,
                'shopifyProductLink'=>$shopifyProductLinkVal,
                'productImageLink'=>$productImageLinkVal,
                'skuCode'=>$productSkuCodeVal,
                'backUpProductId'=>$backUpProductIdVal,
                'isActive'=>$isActiveVal,
                'created_at' => Carbon::now()
            ]);
        }
       
        if($type == 'update'){
            // Product update
            $product = Product::where('productId',$productIdVal)->update([
                "productName" => $productNameVal,
                "productSize" => $productSizeVal,
                "productDescription" => $productDescriptionVal,
                "productBrand" => $productBrandVal,
                "shopifyProductLink" => $shopifyProductLinkVal,
                "productImageLink" => $productImageLinkVal,
                'skuCode'=>$productSkuCodeVal,
                'backUpProductId'=>$backUpProductIdVal,
                'isActive'=>$isActiveVal,
            ]);
        }

        // if product created successfully
        if(isset($product))
        {
            // If data is successfully added then commit the changes
            DB::commit();
            return true;

        }else{

           // If some error occurred while save data then rollback the records from database
           DB::rollback();
           return false;
        }

        return true;
           
    }

    /**
     * Function use to add/update product therapy details
     * */
    public static function createOrUpdateProductTherapy(Array $data, $type) 
    {
        $productIdVal = array_key_exists('productId',$data) ? $data['productId'] : null;
        $therapyIdVal = array_key_exists('therapyId',$data) ? $data['therapyId'] : null;
        $interactionDisplayNameVal = array_key_exists('interaction_display_name',$data) ? $data['interaction_display_name'] : null;


        // Begin a transaction 
        DB::beginTransaction();

        if($type == 'create'){
            // ProductTherapy creation
            $productTherapy = ProductTherapy::create([
                'productId'=>$productIdVal,
                'therapyId'=>$therapyIdVal,
                'interaction_display_name'=>$interactionDisplayNameVal,
                'created_at' => Carbon::now()
            ]);
        }
       
        if($type == 'update'){
            // ProductTherapy update
            $productTherapy = ProductTherapy::where('productId',$productIdVal)->where('therapyId',$therapyIdVal)
            ->update([
                'interaction_display_name'=>$interactionDisplayNameVal,
            ]);
        }

        // if productTherapy created or updated successfully
        if(isset($productTherapy))
        {
            // If data is successfully added then commit the changes
            DB::commit();
            return true;

        }else{

           // If some error occurred while save data then rollback the records from database
           DB::rollback();
           return false;
        }

        return true;
           
    }


    /**
     * Function use to add/update user product order details
     * */
    public static function createOrUpdateUserProductOrder(Array $data, $type) 
    {
        $userIdVal = array_key_exists('userId',$data) ? $data['userId'] : null;
        $productIdVal = array_key_exists('productId',$data) ? $data['productId'] : null;
        $lastPurchasedVal = array_key_exists('last_purchased',$data) ? date('Y-m-d H:i:s',strtotime($data['last_purchased'])) : null;
        $nextRefillDateVal = array_key_exists('next_refill_date',$data) ? date('Y-m-d H:i:s',strtotime($data['next_refill_date'])) : null;
        $valueInDollarsVal = array_key_exists('value_in_dollars',$data) ? $data['value_in_dollars'] : '0';
        $orderIdVal = array_key_exists('order_id',$data) ? $data['order_id'] : null;
        $quantityVal = array_key_exists('quantity',$data) ? $data['quantity'] : null;

        $isProductAdded = '1'; // Mark as default value

        // Begin a transaction 
        DB::beginTransaction();

        if($type == 'create'){

            // UserProductOrder creation
            $userProductOrder = UserProductOrder::create([
                'userId'=>$userIdVal,
                'productId'=>$productIdVal,
                'last_purchased'=>$lastPurchasedVal,
                'next_refill_date'=>$nextRefillDateVal,
                'value_in_dollars'=>$valueInDollarsVal,
                'order_id'=>$orderIdVal,
                'quantity'=>$quantityVal,
                'created_at' => Carbon::now()
            ]);

            // Add Product to user's medicine cabinet
            $userId = User::where('email', $userIdVal)->pluck('id')->first(); // get user id from email id in users table
            $productIdFromDb = Product::where('productId', $productIdVal)->pluck('id')->first(); // get product id from productId in product table
            if(!empty($userId) && !empty($productIdFromDb)){
                // Check if same product is added for the same user, if not then add else do not add into the medicine cabinet table.
                $checkMedicineData = MedicineCabinet::where('userId',$userId)
                ->where('productId',$productIdFromDb)->get()->toArray();
                if(empty($checkMedicineData)){
                    // Insert data into Medicine cabinet table
                    $userMedicineCabinetData = DB::table('medicine_cabinet')->insert([
                        'userId' => $userId,
                        'productId' => $productIdFromDb,
                        'isTaking' => '1',
                        'created_at' => Carbon::now()
                    ]);
                    if($userMedicineCabinetData){
                        $isProductAdded = '1'; // Mark as data saved successfully
                    }else{
                        $isProductAdded = '0'; // Mark as data failed to save
                    }

                }

            }else{
                // Mark as user id or product data not found to save in table
                $isProductAdded = '0'; 
            }
            
        }
       
        if($type == 'update'){
            // UserProductOrder update
            $userProductOrder = UserProductOrder::where('productId',$productIdVal)->where('userId',$userIdVal)
            ->update([
                'last_purchased'=>$lastPurchasedVal,
                'next_refill_date'=>$nextRefillDateVal,
                'value_in_dollars'=>$valueInDollarsVal,
                'order_id'=>$orderIdVal,
                'quantity'=>$quantityVal,
            ]);

            // Add Product to user's medicine cabinet
            $userId = User::where('email', $userIdVal)->pluck('id')->first(); // get user id from email id in users table
            $productIdFromDb = Product::where('productId', $productIdVal)->pluck('id')->first(); // get product id from productId in product table
            if(!empty($userId) && !empty($productIdFromDb)){
                // Check if same product is added for the same user, if not then add else do not add into the medicine cabinet table.
                $checkMedicineData = MedicineCabinet::where('userId',$userId)
                ->where('productId',$productIdFromDb)->get()->toArray();
                if(empty($checkMedicineData)){
                    // Insert data into Medicine cabinet table
                    $userMedicineCabinetData = DB::table('medicine_cabinet')->insert([
                        'userId' => $userId,
                        'productId' => $productIdFromDb,
                        'isTaking' => '1',
                        'created_at' => Carbon::now()
                    ]);
                    if($userMedicineCabinetData){
                        $isProductAdded = '1'; // Mark as data saved successfully
                    }else{
                        $isProductAdded = '0'; // Mark as data failed to save
                    }
                }else{
                    // Update existing data to taking '1'
                    $updateUserMedicineCabinetData = MedicineCabinet::where('userId',$userId)
                    ->where('productId',$productIdFromDb)->update([
                        'isTaking' => '1'
                    ]);
                }
            }
        }

        // if userProductOrder & product is added or updated successfully
        if(!empty($userProductOrder) && $isProductAdded == '1' || isset($updateUserMedicineCabinetData))
        {
            // If data is successfully added then commit the changes
            DB::commit();
            return true;

        }else{

           // If some error occurred while save data then rollback the records from database
           DB::rollback();
           return false;
        }

        return true;
           
    }

     /**
     * Function use to get the last 5 notes for given medicine data id
     * */
    public static function getMedicineCabinetNotes($id,$check) 
    {
        $medicineCabinetDataNotes = MedicineCabinetNotes::select('medicine_cabinet_notes.medicineCabinetId as id','notes',DB::raw("DATE_FORMAT(medicine_cabinet_notes.created_at,'%d-%b-%Y') AS date"))
        ->orderBy('medicine_cabinet_notes.created_at','DESC');
        if($check == '1'){
            $medicineCabinetDataNotes = $medicineCabinetDataNotes->where('medicineCabinetId',$id)->count();
        }
        if($check == '2'){
            $medicineCabinetDataNotes = $medicineCabinetDataNotes->leftJoin('medicine_cabinet','medicine_cabinet_notes.medicineCabinetId','=','medicine_cabinet.id')
            ->where('medicine_cabinet_notes.medicineCabinetId',$id)->limit(5)->get()->toArray();            
        }
       
        return $medicineCabinetDataNotes;
    }

    /**
     * Function to get the highlight color by the severity and the event dates in array
     * */
    public static function getHighlightColorEventDates($eventDatesData,$profileMemberId=NULL){
        
        // Get the logged in user id
        $userId = Auth::user()->id;

        // Fetch the symptom ids added by the logged in user or its profile member id - Code Start
        $userSymptomsIds = DB::table('user_symptoms')->where('user_symptoms.userId',$userId)
        ->leftJoin('symptom','user_symptoms.symptomId','=','symptom.id');
        // Check if profileMemberId exists, then get the data accordingly or exclude for this same
        if(!empty($profileMemberId)){
            $userSymptomsIds = $userSymptomsIds->where('user_symptoms.profileMemberId',$profileMemberId);
        }else{
            $userSymptomsIds = $userSymptomsIds->whereNull('user_symptoms.profileMemberId');
        }
        $userSymptomsIds = $userSymptomsIds->where('user_symptoms.status','1')->whereNull('symptom.deleted_at')
        ->whereNull('user_symptoms.deleted_at')->pluck('symptomId')->toArray();
        // Fetch the symptom ids added by the logged in user or its profile member id - Code End

        // Store the multiple severities by date 
        $multipleTimeWindowSeverities = [];
        foreach ($eventDatesData as $eDateVal){

            // Fetch all the severity names by the event data and the user added symptoms
            $severityNames = EventSymptoms::select('severity.severityLabel')->where('event_symptoms.eventId', $eDateVal['id'])
            ->leftJoin('severity','event_symptoms.severityId','=','severity.id')
            ->whereIn('event_symptoms.symptomId',$userSymptomsIds)
            ->groupBy('severity.severityLabel')->orderBy('event_symptoms.id','DESC')->get()->pluck('severityLabel')->toArray();

            // Arrange the severity names based the timewindow event for the same date in an array
            $multipleTimeWindowSeverities[$eDateVal['eventDate']][] = $severityNames;
        }

        // Store the event data in array format, by default empty data
        $eventData = [];
        foreach($multipleTimeWindowSeverities as $severityKey => $severityVal){
            
            // Set the multiple time window based severities array in one array
            $severityData = call_user_func_array('array_merge',$severityVal);

            // Set the unique names from the array list for the same day timewindow 
            $severityData = array_unique($severityData);

            // Fetch the severity name from the severity table
            $severityColorCodes = Severity::groupBy('severityLabel')->orderBy('severityPriority')->pluck('severityLabel')->toArray();
            // Store the default color name for the severity lable
            $defaultSeveritiesColorCode = ['grey', 'green', 'yellow', 'pink', 'red'];
            /**
             *  Check if the severity label names and default color names count is same, then combine array by key value structure. 
             * Else use the default color code by severity name from the array
             *  */
            if(count($severityColorCodes) == count($defaultSeveritiesColorCode)){
                $defaultSeveritiesColorCode = array_combine($severityColorCodes,$defaultSeveritiesColorCode);
            }else{
                // Set default array to arrange the severity color name
                $defaultSeveritiesColorCode = ['None'=>'grey', 'Mild'=>'green', 'Moderate'=>'yellow', 'Major'=>'pink', 'Severe'=>'red'];
            }

            // Set default highlight color to none i.e, grey
            $highlightColor = $defaultSeveritiesColorCode['None'];
    
            // Check the severity name and set the color by priority 
            switch ($severityData){
                case in_array('Severe',$severityData):
                    $highlightColor = $defaultSeveritiesColorCode['Severe'];
                    break;
                case in_array('Major',$severityData):
                    $highlightColor = $defaultSeveritiesColorCode['Major'];
                    break;
                case in_array('Moderate',$severityData):
                    $highlightColor = $defaultSeveritiesColorCode['Moderate'];
                    break;
                case in_array('Mild',$severityData):
                    $highlightColor = $defaultSeveritiesColorCode['Mild'];
                    break;
                case in_array('None',$severityData):
                    $highlightColor = $defaultSeveritiesColorCode['None'];
                    break;      
            }
            // add the event date and store in the json format
            $dataArr['eventDate'] = json_encode($severityKey,JSON_PRETTY_PRINT);
            // Store the highlight color for the severity
            $dataArr['highlightColor'] = $highlightColor;
            
            // Store the data in the Array
            $eventData[] = $dataArr;

        }

        // Return the array data 
        return $eventData;
    }

    /**
     * Function to get the highlight color of notes added and the event dates in array
     * */
    public static function getNotesAddedColor($userId,$profileMemberId=NULL){

        // Fetch the notes added by the logged in user or its profile member id - Code Start
        $userNotes = DB::table('event_notes')->where('userId',$userId);
        // Check if profileMemberId exists, then get the data accordingly or exclude for this same
        if(!empty($profileMemberId)){
            $userNotes = $userNotes->where('event_notes.profileMemberId',$profileMemberId);
        }else{
            $userNotes = $userNotes->whereNull('event_notes.profileMemberId');
        }
        $userNotes = $userNotes->groupBy('eventDate')->whereNull('event_notes.deleted_at')->get()->toArray();
        // Fetch the notes added by the logged in user or its profile member id - Code End
        
        // Store the data in the array, as default define as an empty array
        $addedNotesArray = [];
        // Check if the data is not empty from the query array, if not empty then store the data accordingly
        if(!empty($userNotes)){
            foreach ($userNotes as $userNotesKey => $userNotesValue) {
                // Store the event date of the added notes with the date in the json format for the calendar view
                $addedNotesData['eventDate'] =  json_encode(date('m/d/Y',strtotime($userNotesValue->eventDate)),JSON_PRETTY_PRINT);
                // Store the class of the notes added indication with the array
                $addedNotesData['notesColor'] = 'calendar-notes';
                // Store the whole data into the array data
                $addedNotesArray[] = $addedNotesData;
            }
        }
        // Display the added array data if exists
        return $addedNotesArray;
    }

    /**
     * Function to get the data of the highest severity from the given time window data array
     * */
    public static function getMaxSeverity($timeWindowData) {
        // Fetch the first array object element
        $firstArrayObject = $timeWindowData[0]; 
        // Temporary variable to store array object element based on updated date
        $tmpObj;
        foreach($timeWindowData as $timeWindowDataValue) { 
            // Fetch the severity priority of current object element
            $num = $timeWindowDataValue->severityPriority; 

            // Store current timewindowday value (i.e, Morning = 1, Afternoon = 2, Evening = 3, Night = 4)
            $firstSeverityPriority = $timeWindowDataValue->timeWindowId;
            // Store first array object of timewindowday value
            $lastSeverityPriority = $firstArrayObject->timeWindowId;

            // Check if the severity priority is greater then the first object element, then pass the time window data values
            if($num > $firstArrayObject->severityPriority) { 
                $firstArrayObject = $timeWindowDataValue; 
            }
            // Check if the severity priority is same then check the updated date time and pass that array object value to $tmpObj variable
            if($num == $firstArrayObject->severityPriority)
            {
                // if the first time window day is greater then pass that value in the $tmpObj variable else use the current time window day value
                if($lastSeverityPriority>=$firstSeverityPriority)
                {
                    $tmpObj = $firstArrayObject;
                }
                else
                {
                    $tmpObj = $timeWindowDataValue;
                }  
            }
        }

        // If the tmpObj has the value then return the values, else use the current first array object element
        if(!empty($tmpObj))
        {
            $firstArrayObject = $tmpObj;
        }

        return $firstArrayObject;
    }

    /**
     * Function to get the last N days dates in an array response
     * */
    public static function getLastNDays($days, $format,$chartDate){
        // Check if chartDate is not null then get the range as per given by the chartDate value, else use current date value
        if($chartDate != NULL)
        {
            // Store the time value for the given chartDate
            $dateValue = strtotime($chartDate);
            $m = date("m",$dateValue); $de= date("d",$dateValue); $y= date("Y",$dateValue);
        }
        else
        {
            // Use current date value
            $m = date("m"); $de= date("d"); $y= date("Y");
        }
        // Store the dates array
        $dateArray = array();
        for($i=0; $i<=$days-1; $i++){
            $dateArray[] = date($format, mktime(0,0,0,$m,($de-$i),$y)); 
        }
        return array_reverse($dateArray);
    }

    /**
     * Function to get the severity name by the id from the table
     * */
    public static function getSeverityNameById($id){
        $severityName = Severity::where('id',$id)->get()->pluck('severityLabel')->first();
        return $severityName;
    }

    /**
     * Function to get the trend chart data for current logged user or it's profile member
     */
    public static function getTrendChartData($userId,$profileMemberId=NULL,$noOfDays,$chartDate=NULL){

        // Fetch the severity names sorted by priority 
        $severityNames = Severity::groupBy('severityLabel')->orderBy('severityPriority')->pluck('severityLabel')->toArray();

        // Fetch all the symptom names data in array
        $symptomNames = DB::table('symptom')->select('symptom.id','symptom.symptomName')
        ->where('user_symptoms.userId',$userId);
        if(!empty($profileMemberId)){
            $symptomNames = $symptomNames->where('profileMemberId',$profileMemberId);
        }else{
            $symptomNames = $symptomNames->whereNull('profileMemberId');
        }
        $symptomNames = $symptomNames->join('user_symptoms','user_symptoms.symptomId','=','symptom.id')
        ->where('user_symptoms.status','1')->whereNull('symptom.deleted_at')
        ->whereNull('user_symptoms.deleted_at')->get()->toArray();

        // Fetch last n days dates for graph
        if($chartDate != NULL)
        {
            // Get last N days data based on chartDate value passed
            $lastDaysDateArray = Helpers::getLastNDays($noOfDays,'j M Y',$chartDate);
            // Store number of days for previous days
            $daysVal = '-'.$noOfDays.' days';
            // Update the previous date with the new date alongwith the number of days
            $newPreviousDate = date('Y-m-d', strtotime($chartDate.$daysVal));
        }
        else
        {
            // If chartdate value is empty then get the previous 30 days dates 
            $lastDaysDateArray = Helpers::getLastNDays($noOfDays,'j M Y',NULL);
            // Store null value in new previous date 
            $newPreviousDate = NULL;
        }

        // Store the trend chart data in the array, by default its empty
        $trendChartData = [];

        // Get the previous logged event dates of current user
        $eventDates = Event::select(DB::raw('DATE_FORMAT(eventDate, "%m/%d/%Y") AS eventDate'),'id')->where('userId',$userId);
        // Check if profile member id selection is there, if there then display the data accordingly else exclude the selection
        if(!empty($profileMemberId)){
            $eventDates = $eventDates->where('profileMemberId',$profileMemberId);
        }else{
            $eventDates = $eventDates->whereNull('profileMemberId');
        }
        $eventDates = $eventDates->whereNull('deleted_at')->orderBy('event.id','DESC')->get()->toArray();
        
        // Store the last event dates recorded by logged in user
        $lastEventDates = $eventDates;

        // Arrange the event date format of the data added by the user 
        array_walk ( $lastEventDates, function (&$key) { 
            $key["eventDate"] = date('j M Y',strtotime($key["eventDate"])); 
        });
        // Fetch the eventDate values in the array
        $lastEventDates = array_column($lastEventDates, 'eventDate');

        /**
         * Check the array of last event dates added by the user and current week date has any same record, 
         * then arrange the records accordingly for the trend chart. else show empty array
         *  */ 
        if(count(array_intersect( $lastDaysDateArray,$lastEventDates)) != 0){

            foreach ($lastDaysDateArray as $lastDaysDatekey => $lastDaysDateValue) {
                // Check if symptom names exist then display the records accordingly
                if(!empty($symptomNames)){
                    foreach ($symptomNames as $symptomNamesKey => $symptomNamesValue) {
                        // Fetch the severity by symptom name and its date
                        $lastDaysData = Event::select('event.id',DB::raw('DATE_FORMAT(event.eventDate, "%e %b") AS eventDate'),
                        'symptom.symptomName','severity.id as severityId','severity.severityPriority','event_symptoms.*')->where('event.userId',$userId)
                        ->whereDate('eventDate','=',date('Y-m-d',strtotime($lastDaysDateValue)))
                        ->where('event_symptoms.symptomId',$symptomNamesValue->id)
                        ->leftJoin('event_symptoms','event.id','=','event_symptoms.eventId')
                        ->leftJoin('severity','severity.id','=','event_symptoms.severityId')
                        ->leftJoin('symptom','event_symptoms.symptomId','=','symptom.id');
                        // Check if profile member id selection is there, if there then display the data accordingly else exclude the selection
                        if(!empty($profileMemberId)){
                            $lastDaysData = $lastDaysData->where('event.profileMemberId',$profileMemberId);
                        }else{
                            $lastDaysData = $lastDaysData->whereNull('event.profileMemberId');
                        }
                        $lastDaysData = $lastDaysData->whereNull('event.deleted_at')->orderBy('event.id','DESC')->get()->toArray();

                        // Check if the severity data exist for given date and symptom name
                        if(!empty($lastDaysData)){
                            
                            // Sort the given priority of the symptom name by the each time window
                            usort($lastDaysData, function($finalArrayOne, $finalArrayTwo) {
                                return $finalArrayTwo['severityPriority'] <=> $finalArrayOne['severityPriority'];
                            });

                            // Store the severity name data by date and symptom name
                            $data[$lastDaysDateValue][$symptomNamesValue->symptomName] = Helpers::getSeverityNameById($lastDaysData[0]['severityId']);

                        }else{
                            // Store the severity value to none
                            $data[$lastDaysDateValue][$symptomNamesValue->symptomName] = 'None';
                        }
                        // Add the data for the severity data
                        $trendChartData = $data;
                    }
                }
            }
        }

        
        
        // Store the data to return the function values
        return [
            'severityNames' => $severityNames,
            'symptomNames' => $symptomNames,
            'lastDaysDateArray' => $lastDaysDateArray,
            'trendChartData' => $trendChartData,
            'newPreviousDate'=>$newPreviousDate
        ];
    }

    /**
     * Get the severity listing with the color codes
     */
    public static function severityListing(){

        // Fetch the severity name from the severity table
        $severityColorCodes = Severity::whereNull('deleted_at')->groupBy('severityLabel')->orderBy('severityPriority')->pluck('severityLabel')->toArray();
        // Store the default color name for the severity lable
        $defaultSeveritiesColorCode = ['bg-color-one', 'bg-color-tow', 'bg-color-three', 'bg-color-five'];
        /**
         *  Check if the severity label names and default color names count is same, then combine array by key value structure. 
         * Else use the default color code by severity name from the array
         *  */
        if(count($severityColorCodes) == count($defaultSeveritiesColorCode)){
            $defaultSeveritiesColorCode = array_combine($severityColorCodes,$defaultSeveritiesColorCode);
        }else{
            // Set default array to arrange the severity color name
            $defaultSeveritiesColorCode = ['None'=>'bg-color-one', 'Mild'=>'bg-color-tow', 'Moderate'=>'bg-color-three', 'Severe'=>'bg-color-five'];
        }

        return $defaultSeveritiesColorCode;
    }

    /**
     * Redirect to the manage symptom tracker screen 
     * if the symptoms is not added by current logged in user or profile member id
     */
    public static function redirectToManageSymptomListScreen($profileMemberId=NULL){

        // Store the array data values
        $data = array();

        // Get the user id of logged in user
        $userId = Auth::user()->id;
        // Set the default route to manage symptom list without profile member id
        $manageSymptomListRoute = route('manage-symptom-list');
        
        // Check if profile member id is not empty then add the id in the route to redirect
        if(!empty($profileMemberId)){
            $manageSymptomListRoute = route('manage-symptom-list',\Crypt::encrypt($profileMemberId));
        }

        // Get the symptoms list accordingly added by the user or it's profile member id
        $userSymptomsList = UserSymptoms::getSelectedSymptomsId($userId,$profileMemberId);
        // If the symptoms is not added then redirect to manage symptom list screen with a message as below
        if(empty($userSymptomsList)){
            $data['route'] = redirect($manageSymptomListRoute)->with('error','Please add and select atleast one symptom to track symptom tracker');
            $data['isRedirect'] = '1';
        }else{
            $data['route'] = '';
            $data['isRedirect'] = '0'; 
        }

        // Return the values
        return $data;
    }

    /**
     * Display the point value of price at upper side 
     * next to the price value
     */
    public static function priceSuperScript($price){
        $pos = 1;
        $string = $price;
        /**
         * Check if the price value contains "." in the string, then add the point value in superscript tag
         * Else print the price as it is
         *  */ 
        if (preg_match('/\.\b/', $string)) {
            $words = explode(".", $string);
            array_splice( $words, $pos, 0, '<sup>' );
            $new_string = join(" ",$words);
            return $new_string;
        }else{
            return $price;
        }
    }

    /**
     * Display the quiz test data
     */
    public static function getQuizData(){

        // Store default empty message for the test
        $quizScoreMidasTestMsg = "";
        $quizScoreHitSixTestMsg = "";
        $quizScoreMidasTest = "";
        $quizScoreHitTest = "";
        $midasTestQuizName = "";
        $hit6TestQuizName = "";

        // Get the Midas test id from the constants
        $midasTest = Config::get('constants.MidasTestId');
        // Check if the id exists of midas test id
        $midasTestId = QuizIntroScreen::where('id',$midasTest)->select('id','title','condition_name','quiz_name')->get()->first();
        // Store the default url
        $midasTestRoute = 'javascript:void(0);';
        // If id exists then pass that in the route
        if(!empty($midasTestId)){
            $midasTestRoute = route('quiz/intro',['condition_name'=>$midasTestId->condition_name, 'quiz_name'=>$midasTestId->quiz_name]);
            $midasTestQuizName = $midasTestId->title;
        }
        
        // Get the hit-6 test id from the constants
        $Hit6TestConfigId = Config::get('constants.HitSixTestId');
        // Check if the id exists of hit-6 test id
        $Hit6TestId = QuizIntroScreen::where('id',$Hit6TestConfigId)->select('id','title','condition_name','quiz_name')->get()->first();
        // Store the default url
        $hit6TestRoute = 'javascript:void(0);';
        // If id exists then pass that in the route
        if(!empty($midasTestId)){
            $hit6TestRoute = route('quiz/intro',['condition_name'=>$Hit6TestId->condition_name, 'quiz_name'=>$Hit6TestId->quiz_name]);
            $hit6TestQuizName = $Hit6TestId->title;
        }

        // Check if logged in
        if(Auth::check() == '1'){

            // Fetch the user id for the current logged in user
            $userId = Auth::user()->id;

            // Fetch the midas test data
            $quizScoreMidasTestData = QuizResult::select('score','created_at')
            ->where('user_id',$userId)->where('intro_screen_id',$midasTest)->orderBy('id', 'desc')->get()->first();
            if(!empty($quizScoreMidasTestData)){
                $quizScoreMidasTest = "Score ".$quizScoreMidasTestData->score.",";
                $quizScoreMidasTestMsg = date('m/j/y',strtotime($quizScoreMidasTestData->created_at));
            }

            // Fetch the hit 6 test data
            $quizScoreHitSixTestData = QuizResult::select('score','created_at')
            ->where('user_id',$userId)->where('intro_screen_id',$Hit6TestConfigId)->orderBy('id', 'desc')->get()->first();
            if(!empty($quizScoreHitSixTestData)){
                $quizScoreHitTest = "Score ".$quizScoreHitSixTestData->score.",";
                $quizScoreHitSixTestMsg = date('m/j/y',strtotime($quizScoreHitSixTestData->created_at));
            }
        }

        return [
            'midasTestRoute' => $midasTestRoute,
            'quizScoreMidasTestMsg' => $quizScoreMidasTestMsg,
            'hit6TestRoute' => $hit6TestRoute,
            'quizScoreHitSixTestMsg' => $quizScoreHitSixTestMsg,
            'quizScoreMidasTest' => $quizScoreMidasTest,
            'quizScoreHitTest' => $quizScoreHitTest,
            'midasTestQuizName' => $midasTestQuizName,
            'hit6TestQuizName' => $hit6TestQuizName,
        ];


    }


    /**
     * Add a check to display the next quiz button based on the quiz attempted
     */
    public static function hideNextQuizButton(){
        $isMidasTest = 0;
        $isHitSixTest = 0;
        $hideNextQuizButton = false;
        if(Session::has('unique_id_value')){
            // Store the session values in the variables
            $sessionValues = Session::get('unique_id_value');
            // Remove empty values from the session
            $sessionValues = array_filter($sessionValues);
            foreach ($sessionValues as $key => $value) {
                $checkUniqueIdFromTable = QuizResult::where('unique_id', '=', $value)->get()->first();
                if($checkUniqueIdFromTable['intro_screen_id'] == Config::get('constants.MidasTestId')){
                    $isMidasTest++;
                }
                if($checkUniqueIdFromTable['intro_screen_id'] == Config::get('constants.HitSixTestId')){
                    $isHitSixTest++;
                }
            }

            if($isHitSixTest!=0 && $isMidasTest!=0){
                $hideNextQuizButton = true;
            }
        }
        return $hideNextQuizButton;
    }


    
    /**
     * Assign the attempted quiz score for user who logs in / signup from quiz workflow
     */
    public static function storeQuizAttemptedData($userId){
        
        // Fetch the quiz attempted id from the session
        if(Session::has('unique_id_value')){
            // Store the session values in the variables
            $sessionValues = Session::get('unique_id_value');
            // Remove empty values from the session
            $sessionValues = array_filter($sessionValues);
            foreach ($sessionValues as $key => $value) {
                // Check if the unique id exists then update the quiz data by the user id registered
                $checkUniqueIdFromTable = QuizResult::where('unique_id', '=', $value)->count();
                if($checkUniqueIdFromTable!=0){
                    // Update quiz data of the registered user by the unique id check
                    QuizResult::where('unique_id', '=', $value)->update([
                        'user_id' => $userId
                    ]);
                }
            }
            // Destory the attempted quiz data
            Session::forget('unique_id_value');
        }

    }

    /**
     * Get the quiz names as per defined in the table for midas test and hit-6 test
     */
    public static function getMidasAndHitTestName(){
        $midasTestName = QuizIntroScreen::where('id',Config::get('constants.MidasTestId'))->get()->pluck('title')->first();
        $hitTestName = QuizIntroScreen::where('id',Config::get('constants.HitSixTestId'))->get()->pluck('title')->first();
        return [
            'midasTestName' => $midasTestName,
            'hitTestName' => $hitTestName
        ];    
    }

    /**
     * Get the providers list by logged in user
     */
    public static function getAddedProviderList(){
        $userId = Auth::user()->id;
        $providerIds = [];
        $data = ProviderUser::where('userId',$userId)
        ->whereNull('access_revoke_date')->whereNull('deleted_at')->get()->toArray();
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $providerIds[] = $value['providerId'];
            }
        }
        return [
            'data' => $data,
            'providerIds' => $providerIds
        ];    
    }

    /**
     * Send code for provider access/revoke mail notification 
     * based on the provider id, sent to which user type ('user'/'provider') and type (1 = Access code/ 0 = Revoke code) defined
     */
    public static function sendCodeEmailNotification($providerId,$sendTo,$type){

        // Get current logged in user id
        $userId = Auth::user()->id;
        
        // Get the random 6 digit code
        $code = Helpers::generateVerifiedCode();

        // Store the code in the user verification code table
        $userVerificationCode = new ProviderUserCode();
        if(Auth::user()->isProviderUser() == '1'){
            $userVerificationCode->userId = $providerId;
            $userVerificationCode->providerId = $userId;
        }else{
            $userVerificationCode->userId = $userId;
            $userVerificationCode->providerId = $providerId;
        }

        $userVerificationCode->code = $code;
        $userVerificationCode->type = (string)$type;
        $userVerificationCode->created_at = Carbon::now();
        $userVerificationCode->updated_at = null;
        $userVerificationCode->deleted_at = null;
        if($userVerificationCode->save()){

            // Get the username of the current logged in user
            $userName = !empty(Auth::user()->last_name) ? Auth::user()->name.' '.Auth::user()->last_name : '';

            // Check if send mail for access code to user
            if($type == '1'){
                // Set the body data for the email template
                $sendData = [
                    'body' => 'Please use the code below to allow access to your provider.<br>
                    Your code is <b>'.$code.'</b>.' 
                ];
                // Send email
                Notification::route('mail' , Auth::user()->email)->notify(new SendVerificationCodeForAccess($userName,$sendData));
            }

            // Check if send mail for revoke code to user/provider
            if($type == '0'){

                // Check if send mail for access code to user
                if($sendTo == 'user'){
                    // Set the body data for the email template
                    $sendData = [
                        'body' => 'Please use the code below to revoke access for your provider.<br>
                        Your code is <b>'.$code.'</b>.' 
                    ];
                    // Send email
                    Notification::route('mail' , Auth::user()->email)->notify(new SendVerificationCodeForRevoke($userName,$sendData));
                }

                if($sendTo == 'provider'){
                    // Set the body data for the email template
                    $sendData = [
                        'body' => 'Please use the code below to revoke access for patient.<br>
                        Your code is <b>'.$code.'</b>.' 
                    ];
                    // Send email
                    Notification::route('mail' , Auth::user()->email)->notify(new SendVerificationCodeForRevokeToProvider($userName,$sendData));
                }

            }

            return true;
        }else{
            return false;
        }
    }

    /** Check by user id if user type is provider role */
    public static function isProviderUserType($id){
        $data = DB::table('users')
        ->select('user_roles.role_id as roleId')
        ->leftJoin('user_roles','users.id','=','user_roles.user_id')
        ->where('users.id',$id)
        ->whereNull('users.deleted_at');
        $data = $data->get()->first();
        if(isset($data) && $data->roleId == '5'){
            return true;
        }else{
            return false;
        }
    }

    /** Get the provider details by the id */
    public static function providerDetails($id){
        $data = DB::table('users')
        ->select(DB::raw("CONCAT(name,' ',CONCAT_WS('',last_name)) AS name"),'email')
        ->where('users.id',$id)
        ->whereNull('users.deleted_at');
        $data = $data->get()->first();
        if(isset($data)){
            return $data;
        }else{
            return '';
        }
    }

     /** Get the email by user id */
     public static function getDetailsByUserId($id){
        $data = DB::table('users')
        ->select(DB::raw("CONCAT(name,' ',CONCAT_WS('',last_name)) AS name"),'email')
        ->where('users.id',$id)
        ->whereNull('users.deleted_at');
        $data = $data->get()->first();
        if(isset($data)){
            return $data;
        }else{
            return '';
        }
    }

}   

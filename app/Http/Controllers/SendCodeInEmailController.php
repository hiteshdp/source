<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helpers as Helper;
use Carbon\Carbon;
use DB;
use Auth;
use Validator;
use Crypt;
use App\Models\Provider;
use App\Models\ProviderUser;
use App\Models\ProviderUserCode;
use Illuminate\Support\Facades\Session;
use Notification;
use App\Notifications\SendVerificationCodeForAccess;
use App\Notifications\SendVerificationCodeForRevoke;
use App\Notifications\NotifyProviderAccessToUser;
use App\Notifications\NotifyProviderRevokeToUser;
use App\Notifications\NotifyProviderOfGrantedAccess;
use App\Notifications\NotifyProviderOfRevokeAccess;

class SendCodeInEmailController extends Controller
{

    /**
     * Send code in email to user for authorization of provider access
     */
    public function sendCodeForAccess(Request $request,$providerId,$isRemoveAccess=null){

        try {

            // Check if the action is not from resend button, then execute below code
            if(empty(array_key_exists('is_resend',$request->all()))){

                /***
                 * Validate the required field
                 */
                $validator = Validator::make($request->all(), [
                    'i_agree' => 'required|in:on'
                ]);

                //validation failed
                if ($validator->fails()) 
                {
                    return back()->with('error','Please tick your consent to proceed further.');
                }
            }

            // Delete old codes for current user
            ProviderUserCode::where('userId',Auth::user()->id)
            ->where('providerId',Crypt::decrypt($providerId))->delete();

            // Store the code in the provider user code table
            Helper::sendCodeEmailNotification(Crypt::decrypt($providerId),'user','1');

            return redirect(route('verify-code',$providerId))->with('success','Code to allow access sent to your email');

        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }


    /**
     * Send code in email to user for authorization of remove provider access
     */
    public function sendCodeForRevoke(Request $request,$providerId){

        try {

            // Delete old codes for current user
            ProviderUserCode::where('userId',Auth::user()->id)
            ->where('providerId',Crypt::decrypt($providerId))->delete();

            // Store the code in the provider user code table
            Helper::sendCodeEmailNotification(Crypt::decrypt($providerId),'user','0');
            
            $isRemoveAccess = base64_encode('1');
            return redirect(route('verify-code',['providerId'=>$providerId,'isRemoveAccess'=> $isRemoveAccess]))->with('success','Code to revoke access sent to your email');

        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }

    /**
     * Send code in email to provider for authorization of remove user access
     */
    public function sendCodeForRevokeByProvider(Request $request,$providerId){
        try {

            // Delete old codes for current user
            ProviderUserCode::where('providerId',Crypt::decrypt($providerId))
            ->where('userId',Auth::user()->id)->delete();

            // Store the code in the provider user code table
            Helper::sendCodeEmailNotification(Crypt::decrypt($providerId),'provider','0');
            
            $isRemoveAccess = base64_encode('1');
            return redirect(route('verify-code',['providerId'=>$providerId,'isRemoveAccess'=> $isRemoveAccess]))->with('success','Code to revoke access sent to your email');

        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }

    /**
     * Display code verification screen
     */
    public function displayCodeVerifyPage(Request $request, $providerId, $type=NULL){

        try{
            // Check if the provider id exists from the request parameter
            if(!empty($providerId)){
                // decrypt the provider id
                $decryptedProviderId = Crypt::decrypt($providerId);

                // Check provider user type
                $isProviderUser = Auth::user()->isProviderUser();

                // Store the provider user id to encrypt into the route
                $id = Crypt::encrypt($decryptedProviderId);

                // Check if the type is of revoke code email then use route for that same, else use access code route
                if(isset($type) && base64_decode($type) == '1'){
                    if($isProviderUser=='1'){
                        // Store send verification route with provider id encrypted for user revoke code email
                        $sendCodeRoute = route('send-revoke-code-by-provider',$id);
                    }else{
                        // Store send verification route with provider id encrypted
                        $sendCodeRoute = route('send-revoke-verification-code',$id);
                    }
                    
                }else{
                    // Store send verification route with provider id encrypted
                    $sendCodeRoute = route('send-access-verification-code',$id);
                }
                
                // Check if user type is provider then redirect to different code verification route
                if($isProviderUser == '1'){
                    // Store verify input code route with provider id encrypted
                    $verifyInputCodeRoute = route('verify-revoke-code',$id);
                }else{
                    // Store verify input code route with provider id encrypted
                    $verifyInputCodeRoute = route('verify-input-code',$id);
                }
                
                return view('page.shared-provider-access.verify-code',compact('sendCodeRoute','verifyInputCodeRoute'));
                
            }else{
                return redirect()->back()->with('error','Provider data not found.');
            }

        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }


    /**
     * Verify the code received from email
     */
    public function verifyInputCode(Request $request,$providerId){
        try{
            if(!empty($providerId)){

                //validate request parameters
                $validator = Validator::make($request->all(), [
                    'verification_code' => 'required|numeric|digits:6'
                ]);
                //validation failed
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput($request->all());
                }
                else{
                    $userId = \Auth::user()->id;
                    $code = $request->verification_code;
                    $checkData = ProviderUserCode::select('code','providerId','type')
                    ->where('userId',$userId)->orderBy('id','DESC')->get()->first();
                    if(!empty($checkData)){

                        if($checkData->code == $code){

                            // Begin SQL Transaction
                            DB::beginTransaction();

                            if($checkData->type == 1){
                               
                                // Set the access start date from today
                                $access_start_date = Carbon::now();
                                // Set the access end date from today of 1 year
                                $access_end_date = Carbon::now()->addYears(1);
                                
                                // Add the association of provider user details
                                $providerUserData = new ProviderUser();
                                $providerUserData->userId = $userId;
                                $providerUserData->providerId = $checkData->providerId;
                                $providerUserData->access_start_date = $access_start_date;
                                $providerUserData->access_end_date = $access_end_date;
                                $providerUserData->created_at = Carbon::now();
                                if($providerUserData->save()){

                                    // Delete the code once submit successfully
                                    ProviderUserCode::where('userId',$userId)
                                    ->where('providerId',Crypt::decrypt($providerId))->delete();

                                    // Commit transaction
                                    DB::commit();

                                    // Send the mail to notify user of provider access acknowledgement
                                    $this->notifyPatientOfGrantedAccess(Crypt::decrypt($providerId),$access_start_date,$access_end_date);

                                    // Send the mail to provider of the access granted by patient
                                    $this->notifyProviderOfGrantedAccess(Crypt::decrypt($providerId));
                                   
                                    return redirect('shared-providers-list')->with('message','Access successfully added.');

                                }else{
                                    // Rollback the transaction
                                    DB::rollback();
                                    return redirect()->back()->with('error','Something wen\'t wrong. Please try again.');
                                }
                           


                            }else{
                                ProviderUser::where('userId',$userId)->where('providerId',$checkData->providerId)
                                ->update([
                                    'access_revoke_date' => Carbon::now()
                                ]);
                                // Commit transaction
                                DB::commit();

                                // Send the mail to provider of the access revoked by patient
                                $this->notifyProviderOfRevokeAccess($checkData->providerId);

                                return redirect('shared-providers-list')->with('message','Access successfully removed.');
                            }
 
                        }else{
                            return back()->withErrors('Invalid verification code. please try again.')->withInput($request->all()); 
                        }
                    
                    }else{
                        return back()->withErrors('Found no code sent for this email.')->withInput($request->all());
                    }
                }
            }else{
                return redirect()->back()->with('error','Provider data not found.');
            }
        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }


    /**
     * Verify the code received from email
     */
    public function verifyProviderInputCode(Request $request,$providerId){
        try{

            if(!empty($providerId)){

                //validate request parameters
                $validator = Validator::make($request->all(), [
                    'verification_code' => 'required|numeric|digits:6'
                ]);
                //validation failed
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput($request->all());
                }
                else{

                    $providerId = Crypt::decrypt($providerId);

                    $userId = \Auth::user()->id;
                    $code = $request->verification_code;
                    $checkData = ProviderUserCode::select('code','userId','providerId','type')
                    ->where('userId',$providerId)->where('providerId',$userId)
                    ->orderBy('id','DESC')->get()->first();

                    if(!empty($checkData)){

                        if($checkData->code == $code){
                            
                            // Begin SQL Transaction
                            DB::beginTransaction();

                            if($checkData->type == '1'){
                                
                                // Set the access start date from today
                                $access_start_date = Carbon::now();
                                // Set the access end date from today of 1 year
                                $access_end_date = Carbon::now()->addYears(1);

                                // Add the association of provider user details
                                $providerUserData = new ProviderUser();
                                $providerUserData->userId = $userId;
                                $providerUserData->providerId = $providerId;
                                $providerUserData->access_start_date = $access_start_date;
                                $providerUserData->access_end_date = $access_end_date;
                                $providerUserData->created_at = Carbon::now();
                                if($providerUserData->save()){

                                    // Delete the code from provider_user_code table once submit successfully
                                    ProviderUserCode::where('userId',$providerId)
                                    ->where('providerId',$userId)->delete();

                                    // Commit the transaction
                                    DB::commit();
                            
                                    // Send the mail to notify user of provider access acknowledgement
                                    $this->notifyPatientOfGrantedAccess($providerId,$access_start_date,$access_end_date);

                                    // Send the mail to provider of the access granted by patient
                                    $this->notifyProviderOfGrantedAccess($providerId);

                                    return redirect('shared-providers-list')->with('message','Access successfully added.');
                                
                                }else{
                                    // Rollback the transaction
                                    DB::rollback();
                                    return redirect()->back()->with('error','Something wen\'t wrong. Please try again.');
                                }                               

                            }else{

                                // If the recepient is provider then send the acknowledgement to provider user
                                if(Helper::isProviderUserType($checkData->userId) == '1'){
                                
                                    // Get the user details by id
                                    $userDetails = Helper::getDetailsByUserId($providerId);
                                    // Send the mail to provider of the revoke access by patient
                                    $this->notifyProviderOfRevokeAccess($userId,$userDetails->name,$userDetails->email);

                                    // Get the user details by id
                                    $userDetails = Helper::getDetailsByUserId($userId);
                                    // Send the mail to notify user of provider revoke acknowledgement
                                    $this->notifyPatientOfRevokeAccess($providerId,$userDetails->name,$userDetails->email);

                                }else{
                                    
                                    // Get the user details by id
                                    $userDetails = Helper::getDetailsByUserId($checkData->userId);
                                    // Send the mail to notify user of provider revoke acknowledgement
                                    $this->notifyPatientOfRevokeAccess($userId,$userDetails->name,$userDetails->email);
                                }

                               
                                
                                /*** Unlink provider and user from database - start ***/
                                // Update the access revoked date in the provider_user table
                                ProviderUser::where('providerId',$providerId)->where('userId',$userId)
                                ->update([
                                    'access_revoke_date' => Carbon::now()
                                ]);
                                
                                // Update the access revoked date in the provider_user table
                                ProviderUser::where('userId',$providerId)->where('providerId',$userId)
                                ->update([
                                    'access_revoke_date' => Carbon::now()
                                ]);
                                /*** Unlink provider and user from database - end ***/

                                /** Check if user selected is provider role type by user id, 
                                 * then update record accordingly */
                                if(Helper::isProviderUserType($providerId) == '1'){
                                    // Delete the code from provider_user_code table once submit successfully
                                    ProviderUserCode::where('userId',$providerId)
                                    ->where('providerId',$userId)->delete();

                                    // Commit the transaction
                                    DB::commit();
                                   
                                }else{
                                    // Delete the code from provider_user_code table once submit successfully
                                    ProviderUserCode::where('userId',$userId)
                                    ->where('providerId',$providerId)->delete();
                                    
                                    // Commit the transaction
                                    DB::commit();
                                }

                                return redirect('shared-providers-list')->with('message','Access successfully removed.');
                            }    
 
                        }else{
                            return back()->withErrors('Invalid verification code. please try again.')->withInput($request->all()); 
                        }
                    
                    }else{
                        return back()->withErrors('Found no code sent for this email.')->withInput($request->all());
                    }
                }
            }else{
                return redirect()->back()->with('error','Patient data not found.');
            }
        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }

    /**
     * Function to notify user for the provider access acknowledgement
     */
    public function notifyPatientOfGrantedAccess($providerId,$access_start_date,$access_end_date){

        // Get the provider details by the provider id
        $provider = Helper::providerDetails($providerId);
        // Get the current logged in user name
        $userName = Auth::user()->getUserName();

        // Set the body data for the email template
        $sendAccessAcknowledgeData = [
            'body' => "We hope this email finds you in good health. We are writing to inform you that your request to grant access to your tracker reports has been successfully processed. Beginning from ".date('m/d/Y',strtotime($access_start_date))." until ".date('m/d/Y',strtotime($access_end_date)).", Dr. ".$provider->name." now has authorized access to the following information: First and Last Name, Symptom Data Medications, and Notes.
            
            Thank you for your trust in Wellkasa. We value your partnership in managing your health effectively and ensuring that your healthcare provider has access to the necessary information, with your approval, for providing you with quality care.
            
            Your privacy and the security of your health data remain our utmost priority, and we have implemented robust measures to safeguard your personal information throughout the process. If you have any concerns or questions regarding this access grant, please don\'t hesitate to reach out to us. We are here to assist you and address any queries you may have.
            
            Wishing you good health and well-being."
        ];
        Notification::route('mail' , Auth::user()->email)->notify(new NotifyProviderAccessToUser($userName,$sendAccessAcknowledgeData));
    }


    /**
     * Function to notify user for the provider revoke acknowledgement
     */
    public function notifyPatientOfRevokeAccess($providerId,$userName=null,$email=null){

        // Get the provider details by the provider id
        $provider = Helper::providerDetails($providerId);

        // Set the recepient user name
        $recepientName = $userName ? $userName : Auth::user()->getUserName();

        // Set the receiver email id
        $recepientEmail = $email ? $email : Auth::user()->email;
        // Set the body data for the email template
        $sendRevokeAcknowledgeData = [
            'body' => "We hope this email finds you in good health. We are writing to inform you that the access you granted to Dr. ".$provider->name." for your health reports has been revoked, effective immediately.

            Your privacy and the confidentiality of your health data are of paramount importance to us. If you have any concerns or questions regarding this revocation, please don't hesitate to reach out to us. We are here to assist you and address any queries you may have.
            
            Wishing you good health and well-being."
        ];
        Notification::route('mail' , $recepientEmail)->notify(new NotifyProviderRevokeToUser($recepientName,$sendRevokeAcknowledgeData));
    }

    
    /**
     * Function to notify provider for the patient granted access acknowledgement
     */
    public function notifyProviderOfGrantedAccess($providerId){

        // Get the provider details by the provider id
        $provider = Helper::providerDetails($providerId);
        // Send mail to provider
        Notification::route('mail' , $provider->email)->notify(new NotifyProviderOfGrantedAccess($provider->name));
    }

    /**
     * Function to notify provider for the patient revoked access acknowledgement
     */
    public function notifyProviderOfRevokeAccess($providerId,$userName=null,$email=null){

        // Get the provider details by the provider id
        $provider = Helper::providerDetails($providerId);

        // Set the recepient user name
        $recepientName = $userName ? $userName : $provider->name;

        // Set the receiver email id
        $recepientEmail = $email ? $email : $provider->email;
        
        // Send mail to provider
        Notification::route('mail' , $recepientEmail)->notify(new NotifyProviderOfRevokeAccess($recepientName));
    }
}

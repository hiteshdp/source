<?php
/*
|-----------------------------------------------------------------------------
| LARAVEL APP BASE CLASS, MODEL CLASS DEFINE
|-----------------------------------------------------------------------------
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usertherapy;
use App\Models\Therapy;
use App\Models\User;
use App\Models\Master;
use App\Models\State;
use App\Models\City;
use App\Models\ProfileMembers;
use App\Models\Subscriptions;
use DB;
use Crypt;
use Carbon\Carbon;

class MyProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MyProfile Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles Rx & Basic users profile for the application.
    |
    */

    /**
     * This function displays profile of basic user role.
     *
     * @return  \Illuminate\Http\Response               Redirect to related response data & load its repective view page.
    */
    public function index()
    {
        $userId = \Auth::user()->id;
        $user = User::where('id',$userId)->get()->toArray();
        $userDetails = [];
        foreach($user as $value){
            $details['firstName'] = !empty($value['name']) ? $value['name'] : "-";
            $details['lastName'] = !empty($value['last_name']) ? $value['last_name'] : "-";
            $details['age'] = !empty($value['patientAge']) ? $value['patientAge'] : "-";
            $details['email'] = !empty($value['email']) ? $value['email'] : "-";
            $gender = '';
            $genderValue = Master::select('name')->where('id',$value['gender'])->get()->first();
            if(!empty($genderValue)){
                switch ($genderValue['name']){
                    case 'Male' :
                        $gender = 'M'; 
                        break;
                    case 'Female' :
                        $gender = 'F'; 
                        break;
                    case 'Undisclosed' :
                        $gender = 'Undisclosed'; 
                        break;
                    default:
                        $gender = 'N/A';
                        break;
                }
            }
            $details['gender'] = $gender;

            $details['patientAge'] = !empty($value['patientAge']) ? $value['patientAge'] : "";

            $details['subscriptionDetails'] = "";
            if(\Auth::user()->planType == '2'){
                $subscriptionDetails = array();
                $planDetails = Subscriptions::where('user_id',$userId)->orderBy('id','DESC')->get()->first();
                if(!empty($planDetails)){
                    $count = \Auth::user()->profileMemberCount;
                    $addOnProfiles = $count != 0  ? '('.$count.' Add on profiles)' : '';

                    if(date("Y-m-d H:i:s") <= date('Y-m-d H:i:s', strtotime($planDetails->current_period_end))){
                        $planType = 'Wellkabinet';
                        $planDescription = "Paid ".$planDetails->interval_val.' - $'.$planDetails->amount.' '.$addOnProfiles."<br>";
                        $planNextPayment = "Next payment on ".date('d-M-Y', strtotime($planDetails->billing_cycle_date));
                        $planSubDescription = "Subscription charges will be $".$planDetails->amount." / ".$planDetails->interval_val." for subsequent ".$planDetails->interval_val."s";
                    }else{
                        $planType = 'Wellkasa Basic';
                        $planDescription = '<div class="subscription-links"><a href="'.route('select-plan').'" class="blue-color mr-4">Upgrade to Wellkabinet</a></div>';
                        $planNextPayment = '';
                        $planSubDescription = '';
                    }
                    $subscriptionDetails['subscriptionId'] = $planDetails->subscription_id;
                    $subscriptionDetails['planType'] = $planType;
                    $subscriptionDetails['planDescription'] = $planDescription;
                    $subscriptionDetails['planNextPayment'] = $planNextPayment;
                    $subscriptionDetails['planSubDescription'] = $planSubDescription;
                    $details['subscriptionDetails'] = $subscriptionDetails;
                }
            }

            
            $userDetails[] = $details;
        }
        $userDetails = $userDetails[0];

        //Check if logged in user has a subscription which is not expired and then only provide profile member details if available.
        $subscriptionDetails = Subscriptions::where('user_id',$userId)->orderBy('id','DESC')->get()->first();
        if(!empty($subscriptionDetails) && (date("Y-m-d") <= date('Y-m-d', strtotime($subscriptionDetails->current_period_end))) ){
            
            $showSelectPlan = '0'; // do not display select plan if already subscribed
            
            // Get member profile user details if added by logged in user
            $profileMembers = ProfileMembers::select('profile_members.id',DB::raw('CONCAT(first_name," ",last_name) As name'),'master.name As gender','age','profile_picture')
            ->where(['addedByUserId' => $userId])->join('master','profile_members.gender','=','master.id')->whereNull('profile_members.deleted_at')->get()->toArray();

            if(!empty($profileMembers)){
                foreach($profileMembers as $profileMembersKey => $profileMembersData){
                    if(!empty($profileMembersData['gender'])){
                        switch ($profileMembersData['gender']) {
                            case 'Male':
                                $profileMembers[$profileMembersKey]['gender'] = 'M';
                                break;
                            case 'Female':
                                $profileMembers[$profileMembersKey]['gender'] = 'F';
                                break;
                            case 'Undisclosed':
                                $profileMembers[$profileMembersKey]['gender'] = 'Undisclosed';
                                break;
                            default:
                                $profileMembers[$profileMembersKey]['gender'] = 'N/A';
                                break;
                        }
                        $profileMembers[$profileMembersKey]['genderAge'] = $profileMembers[$profileMembersKey]['gender'].", ".$profileMembers[$profileMembersKey]['age'];
                    }else{
                        $profileMembers[$profileMembersKey]['gender'] = '';
                    }

                    if(!empty($profileMembersData['profile_picture'])){
                        $profileMembers[$profileMembersKey]['profile_picture'] = url('uploads/avatar').'/'.$profileMembersData['profile_picture'];
                    }else{
                        $profileMembers[$profileMembersKey]['profile_picture'] = '';
                    }
                }
            }
        }else{
            $profileMembers = ''; //show no profile members details
            $showSelectPlan = '1'; //display select plan when not subscribed
        }
        

        // get number of profile member remaining to be added 
        $availableCount = \Auth::user()->remainingProfileMemberCount;
        
        return view('page.my-profile',compact('userDetails','profileMembers','availableCount','showSelectPlan'));
    }


    /**
     * Function to display cancel subscription page and its content.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelSubscriptionPage($userId){
        
        // Decrypt user id
        $userId = Crypt::decrypt($userId);
        $dt = date('Y-m-d'); 
        try {
            // Get decryptted user id details of its subscription
            $getUserData = DB::table('subscriptions')->where('user_id',$userId)->orderby('id','desc')->whereDate('billing_cycle_date', '>=', $dt)->first();
            if(!empty($getUserData)){
                if($getUserData->stripe_status == 'canceled'){
                    return back()->withErrors('Already subscription is canceled');
                }else{
                    $nextBillingCycleDate = date('d-M-Y', strtotime($getUserData->billing_cycle_date));
                    $subscriptionId = $getUserData->stripe_id;
                    return view('page.cancel-subscription',compact('nextBillingCycleDate','subscriptionId'));
                }
            }else{
                return back()->withErrors('Found no subscription details of you. Please try again later.');
            }

        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }

    }

    
    /**
     * Function to display cancel subscription page and its content.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteAccount(Request $request){
        // get user id
        $userId = $request->get('userId');
        
        try {
            
            DB::beginTransaction();

            $userDetails = User::find($userId);
            if(!empty($userDetails)){
                $userDetails->deleted_at = Carbon::now();
                if($userDetails->save()){
                    DB::commit();
                    return Response()->json(['status'=>'1','message'=>'Your account has been deleted successfully.']);
                    
                }else{
                    DB::rollback();
                    return Response()->json(['status'=>'0','message'=>'Something went wrong while deleting your account. Please try again later.']);

                }
            }else{
                DB::rollback();
                return Response()->json(['status'=>'0','message'=>'Found no records for this user. Please try again later.']);

            }

        } catch (\Exception $e) {
            return Response()->json(['status'=>'0','message'=>$e->getMessage()]);

        }

    }
    

    /**
     * This function complies display profile on base of Rx user role.
     *
     * @return  \Illuminate\Http\Response         Redirect to related response data & load its repective view page.
     */
    public function viewMyProfileRx()
    {
        $userId = \Auth::user()->id;
        $user = User::where('id',$userId)->get()->toArray();
        $userDetails = [];
        foreach($user as $value){
            $details['firstName'] = !empty($value['name']) ? $value['name'] : "-";
            $details['lastName'] = !empty($value['last_name']) ? $value['last_name'] : "-";
            $details['ageRange'] = !empty($value['ageRange']) ? $value['ageRange'] : "-";
            $details['email'] = !empty($value['email']) ? $value['email'] : "-";
            $details['state'] = !empty($value['state']) ? State::select('name')->where('id',$value['state'])->get()->first()->name : "-";
            $details['city'] = !empty($value['city']) ? City::select('name')->where('id',$value['city'])->get()->first()->name : "-";            
            $details['gender'] = !empty($value['gender']) ? Master::select('name')->where('id',$value['gender'])->get()->first()->name : "-";


            $details['practitionerType'] = !empty($value['practitionerType']) ? Master::select('name')->where('id',$value['practitionerType'])->get()->first()->name : "";
            $details['speciality'] = !empty($value['speciality']) ? Master::select('name')->where('id',$value['speciality'])->get()->first()->name : "";
            $details['myAffiliationIs'] = !empty($value['myAffiliationIs']) ? Master::select('name')->where('id',$value['myAffiliationIs'])->get()->first()->name : "";
            $details['integrativeCareExperience'] = !empty($value['integrativeCareExperience']) ? Master::select('name')->where('id',$value['integrativeCareExperience'])->get()->first()->name : "";


            $userDetails[] = $details;
        }
        $userDetails = $userDetails[0];
        return view('page.my-profile-rx',compact('userDetails'));
    }

}

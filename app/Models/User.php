<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\PasswordReset; 
use App\Notifications\VerifyEmail;
use Laravel\Passport\HasApiTokens;
use DB;
use Laravel\Cashier\Billable;
use Crypt;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasApiTokens, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'gender',
        'dateOfBirth',
        'ageRange',
        'avatar', 
        'provider_id', 
        'provider',
        'access_token',
        'email_verified_at',
        'lastLoggedInDate'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guarded = ['*'];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    // disabled email verification send on user registration - 29/12/2021
    // public function sendEmailVerificationNotification()
    // {
    //     $this->notify(new VerifyEmail); // my notification
    // }
    // disabled email verification send on user registration - 29/12/2021
    
    public function verified()
    {
        $this->verified = 1;
        $this->email_token = null;
        $this->save();
    }
    
    public function isUserHealthCareProvider(){
        $userId = \Auth::user()->id;
        $userRole = DB::table('user_roles')->where('user_id', $userId)->get()->pluck('role_id')->first();
        if($userRole == '3'){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Check if current logged in user role is wellkabinet
     */
    public function isWellabinetUser(){
        $userId = \Auth::user()->id;
        $userRole = DB::table('user_roles')->where('user_id', $userId)->get()->pluck('role_id')->first();
        if($userRole == '2'){
            return true;
        }else{
            return false;
        }
    }
    

    /***
     * Check user type migraine user
     */
    public function isUserMigraineUser(){
        $userId = \Auth::user()->id;
        $userRole = DB::table('user_roles')->where('user_id', $userId)->get()->pluck('role_id')->first();
        if($userRole == '4'){
            return true;
        }else{
            return false;
        }
    }

    /***
     * Check user type provider/doctor user
     */
    public function isProviderUser(){
        $userId = \Auth::user()->id;
        $userRole = DB::table('user_roles')->where('user_id', $userId)->get()->pluck('role_id')->first();
        if($userRole == '5'){
            return true;
        }else{
            return false;
        }
    }

     /***
     * Check user type migraine or provider/doctor user
     */
    public function isMigraineOrProviderUser(){
        $userId = \Auth::user()->id;
        $userRole = DB::table('user_roles')->where('user_id', $userId)->get()->pluck('role_id')->first();
        if($userRole == '4' || $userRole == '5'){
            return true;
        }else{
            return false;
        }
    }

    /***
     * get logged in user's added profile members list for change profile menu.
     */
    public function getProfileMembers(){
        $userId = \Auth::user()->id;
        
        $profileMembers = '';
        
        // Check if logged in user is subscribed to wellkasa plus
        if(\Auth::user()->planType == '2'){
            $subscriptionDetails = DB::table('subscriptions')->where('user_id',$userId)->orderBy('id','DESC')->get()->first();
            if(!empty($subscriptionDetails) && (date("Y-m-d") <= date('Y-m-d', strtotime($subscriptionDetails->current_period_end))) ){
    
                $profileMembers = ProfileMembers::select('profile_members.id',
                DB::raw('CONCAT(first_name," ",last_name) As name'),'profile_picture')
                ->where(['addedByUserId' => $userId])
                ->whereNull('profile_members.deleted_at')->get()->toArray();
    
                if(!empty($profileMembers)){
                    foreach($profileMembers as $profileMembersKey => $profileMembersData){
                        if(!empty($profileMembersData['profile_picture'])){
                            $profileMembers[$profileMembersKey]['profile_picture'] = url('uploads/avatar').'/'.$profileMembersData['profile_picture'];
                        }else{
                            $profileMembers[$profileMembersKey]['profile_picture'] = '';
                        }
                    }
                }
            }
        }
        

        return $profileMembers;
    }


    /***
     * get logged in user's added profile members list for change profile menu.
     */
    public function getProfileMembersWithMedicineCabinetData(){
        $userId = \Auth::user()->id;
        
        $profileMembers = '';
        
        // Check if logged in user is subscribed to wellkasa plus
        if(\Auth::user()->planType == '2'){
            $subscriptionDetails = DB::table('subscriptions')->where('user_id',$userId)->orderBy('id','DESC')->get()->first();
            if(!empty($subscriptionDetails) && (date("Y-m-d") <= date('Y-m-d', strtotime($subscriptionDetails->current_period_end))) ){
    
                $profileMembers = ProfileMembers::select('profile_members.id',
                DB::raw('CONCAT(first_name," ",last_name) As name'),'profile_picture')
                ->where(['addedByUserId' => $userId])
                ->whereNull('profile_members.deleted_at')->get()->toArray();
    
                if(!empty($profileMembers)){
                    foreach($profileMembers as $profileMembersKey => $profileMembersData){
                        $profileMembers[$profileMembersKey]['url'] = route('medicine-cabinet',Crypt::encrypt($profileMembersData['id']));
                        $profileMembers[$profileMembersKey]['event_selection_url'] = route('event-selection',Crypt::encrypt($profileMembersData['id']));
                        $profileMembers[$profileMembersKey]['symptom_tracker_url'] = route('symptom-tracker',Crypt::encrypt($profileMembersData['id']));
                        $profileMembers[$profileMembersKey]['manage_symptom_list'] = route('manage-symptom-list',Crypt::encrypt($profileMembersData['id']));

                    }
                }
            }
        }
        

        return $profileMembers;
    }

     /***
     * check if current user's subscription is expired or not
     */
    public function getSubscriptionStatus(){
        $userId = \Auth::user()->id;
        
        $subscriptionDetails = DB::table('subscriptions')->where('user_id',$userId)->orderBy('id','DESC')->get()->first();
        if(!empty($subscriptionDetails) && (date("Y-m-d") <= date('Y-m-d', strtotime($subscriptionDetails->current_period_end))) ){
            if($subscriptionDetails->stripe_status == 'canceled'){
                // Update the plan type if subscription expires
                User::where('id',$userId)->update(['planType'=>'1']);
                return false; // subscription is canceled
            }else{
                return true; // subscription is active
            }
            
        }else{
            // Update the plan type if subscription expires
            User::where('id',$userId)->update(['planType'=>'1']);
            return false; // subscription is expired
        }
    }


    /***
     * check if current user has any past subscriptions for invoice
     */
    public function getInvoiceStatus(){
        $userId = \Auth::user()->id;
        
        $subscriptionDetails = DB::table('subscriptions')->where('user_id',$userId)
        ->orderBy('id','DESC')->get()->first();

        if(!empty($subscriptionDetails) ){
            return true; // had subscription in past            
        }else{
            return false; // no subscriptions
        }
    }

    /***
     * get current user name.
     */
    public function getUserName(){
        // if user has not yet input his/her name then strip name by the email before @ from the string and display it.
        return \Auth::user()->name ? (!empty(\Auth::user()->name) && !empty(\Auth::user()->last_name) ? \Auth::user()->name." ".\Auth::user()->last_name : \Auth::user()->name) : strtok(\Auth::user()->email, '@');
    }

    public static function status($email){
        return User::withTrashed()->where('email',$email)->select('status','deleted_at')->get()->toArray();
    }

    public function verifyUser()
    {
        return $this->hasOne('App\Models\VerifyUser');
    } 
}

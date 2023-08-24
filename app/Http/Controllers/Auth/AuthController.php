<?php
/*
|-----------------------------------------------------------------------------
| LARAVEL APP BASE CLASS, MODEL CLASS, PACKAGES, HELPERS DEFINE
|-----------------------------------------------------------------------------
*/
namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerification;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use App\Models\User as Users;
use App\Models\Master;
use App\Models\State;
use App\Helpers\Helpers as Helper;
use App\Models\UserVerificationCode;
use Notification;
use App\Notifications\UserEmailVerifiedCode;

class AuthController extends Controller {

    /*
    |--------------------------------------------------------------------------
    | Auth Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the authenticate of new users email verification 
    | validation and redirction on base of pass or fail validation.
    |
    */
    protected $error = '';

    public function index()
    {
      return view('auth/passwords/change-password');
    }

    /**
     * Check the code sent from email to verify the user
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyEmailCode(Request $request)
    {
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
            $checkData = UserVerificationCode::select('verification_code')->where('userId',$userId)->orderBy('id','DESC')->get()->first();
            if(!empty($checkData)){
                $checkVerified = Users::where('id',$userId)->whereNull('email_verified_at')->count();
                if($checkVerified == '1'){
                    if($checkData->verification_code == $code){
                        UserVerificationCode::where('userId',$userId)->delete();
                        Users::where('id',$userId)->update([
                            'email_verified_at' => Carbon::now()
                        ]);
                        $checkUserRole3 = \Auth::user()->isUserHealthCareProvider();
                        if($checkUserRole3){
                            return redirect('my-wellkasa-rx')->with('message','Email verified successfully.');
                        }else{
                            return redirect('event-selection')->with('message','Email verified successfully.');
                        }
                    
                    }else{
                        return back()->withErrors('Invalid verification code. please try again.')->withInput($request->all()); 
                    }
                }else{
                    return back()->with('message','Your email is already verified.')->withInput($request->all());    
                }
            }else{
                return back()->withErrors('Found no verification code for this email.')->withInput($request->all());
            }
        }
    }

    /**
     * Resend email of 6 digit verification code to user
     *
     * @return \Illuminate\Http\Response
     */
    public function resendVerifyEmailCode(Request $request)
    {
        $userId = \Auth::user()->id;
        $userDetails = Users::where('id',$userId)->get()->first();
        if(!empty($userDetails)){
            if(empty($userDetails->email_verified_at)){

                $code = Helper::generateVerifiedCode();

                // if verification code does not exist, then insert a new one.
                $checkIfCodeExist = UserVerificationCode::where('userId',$userId)->count();
                if(empty($checkIfCodeExist) || $checkIfCodeExist == 0){
                    $userVerificationCode = new UserVerificationCode();
                    $userVerificationCode->userId = $userId;
                    $userVerificationCode->verification_code = $code;
                    $userVerificationCode->created_at = Carbon::now();
                    $userVerificationCode->updated_at = null;
                    $userVerificationCode->deleted_at = null;
                    $userVerificationCode->save();
                }else{
                    $userVerificationCodeUpdate = UserVerificationCode::where('userId',$userId)->update([
                        "verification_code" => $code,
                        "updated_at" => Carbon::now()
                    ]);
                }
 
                $sendData = [
                    'body' => 'Please verify your email address to get access to <span style="color:#35C0ED;">myWellkasa</span> where you can Find, Build and Track your customized Integrative Care.<br><br>
                    Please find your 6 digit code for email verification below, <br> ( <b>'.$code.'</b> )<br><br>
                    If you did not create an account, no further action is required.' 
                ];
                Notification::route('mail' , $userDetails->email)->notify(new UserEmailVerifiedCode($sendData));

                return back()->with('success', 'Verification code has been sent again. Please check your email.');
            }else{
                return back()->withErrors('Email not sent. Your verification is already completed.');
            }
        }else{
            return back()->withErrors('User not found.');
        }

    }

    /**
     * This function complies change user password.
     * 
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response
     */
    public function changePassword(Request $request)
    {
        
        $request->validate([
          'current_password' => 'required',
          'password' => 'required|string|min:6|confirmed',
          'password_confirmation' => 'required',
        ]);

        $user = \Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect('change-password')->with('error', 'Current password does not match!');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $checkUserRole3 = \Auth::user()->isUserHealthCareProvider();
        if($checkUserRole3){
            $route = 'my-profile-rx';
        }else{
            $route = 'my-profile';
        }
        return redirect($route)->with('success', 'Password successfully changed!');
    }
    
    /**
     * This function complies display a listing of the users.
     *
     * @return  \Illuminate\Http\Response        Redirect to related route path
     */
    public function viewProfile()
    {
        $userId = \Auth::user()->id;
        
        $genderOptions = Master::where('type','2')->get()->toArray();

        $user = Users::select('name as firstName','last_name as lastName','email','patientAge','gender','avatar','iama','journeyAgainst','state','city','dateOfBirth')->where('id',$userId)->get()->toArray();
        $userDetails = [];
        foreach($user as $value){
            $details['firstName'] = $value['firstName'];
            $details['lastName'] = $value['lastName'];
            $details['email'] = !empty($value['email']) ? $value['email'] : "";
            $details['patientAge'] = $value['patientAge'];
            $details['gender'] = !empty($value['gender']) ? $value['gender'] : "";
            $details['avatar'] = !empty($value['avatar']) ? $value['avatar'] : "";
            $details['dateOfBirth'] = !empty($value['dateOfBirth']) ? date("m/d/Y", strtotime($value['dateOfBirth'])) : "";

            $userDetails[] = $details;
        }
        $userDetails = $userDetails[0];
        return view('auth.profile',compact('userDetails','genderOptions'));
    }

    /**
     * Add/Update profile picture of logged in user
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfilePic(Request $request)
    {

        try{

            $validator = Validator::make($request->all(), [
                'avatar' => 'required|image|mimes:jpg,png,jpeg',
            ]);
    
            //validation failed
            if ($validator->fails()) {
                return response()->json(['status' => '0','message' => $validator->getMessageBag()->toArray()['avatar'] ]);

            }else{


                //Check condtion for file is attach or not
                if ($request->hasFile('avatar'))
                {   
                    if ($request->file('avatar')->isValid()) 
                    {

                        $file = $request->file('avatar');
                        $ext = $file->extension();
                        $name = $request->file('avatar')->getClientOriginalName();
                        $name = str_replace(' ', '_', $name);
                        $fileName = time().'_'.$name;
                            
                        if($request->file('avatar')->move(public_path('uploads/avatar/').'/', $fileName))
                        {
                            $userId = \Auth::user()->id;
                            $user = Users::find($userId);

                            // delete old uploaded photo from the public/uploads/avatar folder if exists
                            $fileToDelete = public_path() . '/uploads/avatar/' . $user->avatar;
                            if(File::exists($fileToDelete)){
                                File::delete($fileToDelete);
                            }

                            //save new photo data in table
                            $user->avatar = $fileName;

                            if($user->save()){
                                return response()->json(['status' => '1','message' =>'Profile picture updated successfully.'],200);
                            }else{
                                return response()->json(['status' => '0','message' =>"Something wen't wrong. Please try again later."],500);
                            }
                        }else{
                            return response()->json(['status' => '0','message' =>"Can't upload image. Please try again later."],500);
                        }
                    }else{
                        return response()->json(['status' => '0','message' =>"File format is invalid. Please try again."],400);
                    }
                }else{
                    return response()->json(['status' => '0','message' =>"Something wrong with the file format. Please try again."],400);
                }

                
            }

        }catch(Exception $e){
            return response()->json(['status' => '0','message' => $e->getMessage()],500);
        }
        
    }

    /**
     * This function complies update user profile
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data related user.
     * @return  \Illuminate\Http\Response               Redirect to related response with related message.
     */
    public function updateProfile(Request $request)
    {
        $userId = \Auth::user()->id;

        //validate request parameters
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'gender' => 'required',
            'email' => 'required|unique:users,email,'.$userId.',id|max:255|regex:/(.+)@(.+)\.(.+)/i',
            'dateOfBirth' => 'date|date_format:m/d/Y'
        ]);

        //validation failed
        if ($validator->fails()) 
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
            $user=Users::find($userId);

            $user->name=$request->get('firstName');
            $user->last_name=$request->get('lastName');
            if(!empty($request->get('dateOfBirth'))){
                $user->dateOfBirth=date('Y-m-d', strtotime($request->get('dateOfBirth')));
                $user->patientAge = date_diff(date_create($request->get('dateOfBirth')), date_create('today'))->y;
            }
            $user->gender=$request->get('gender');
            $email = $request->get('email');
            $user->email = $email;

            if($user->save()){
                return redirect('my-profile')->with('message', 'Profile updated successfully');  
            }else{
                return back()->with('error', 'Something went wrong. Please try again later.'); 
            }
                      
        
        }
        return view('auth.profile');
    }



    /**
     * This function complies show listing of the user profile rx. 
     * Here we check type & country; where as type as
     * 1 = Provider, 2 = Gender, 3 = iama, 4 = On a journey against, 
     * 5 = Practioner type, 6 = Speciality, 7 = My affiliation is, 
     * 8 = Integrative care experience
     *
     * @return  \Illuminate\Http\Response               Redirect to related response with related message.
     */
    public function viewProfileRx()
    {
        $userId = \Auth::user()->id;
        
        $iamaOptions = Master::where('type','3')->get()->toArray();
        $onAJourneyOptions = Master::where('type','4')->get()->toArray();
        $genderOptions = Master::where('type','2')->get()->toArray();
        $states = State::where("country_id",'231')->select("name","id")->orderBy('name')->get()->toArray();
        $practitionerTypeOptions = Master::where('type','5')->get()->toArray();
        $specialityOptions = Master::where('type','6')->get()->toArray();
        $myAffiliationIsOptions = Master::where('type','7')->get()->toArray();
        $integrativeCareExperienceOptions = Master::where('type','8')->get()->toArray();
        
        $user = Users::select('name as firstName','last_name as lastName','email','patientAge','gender','avatar','iama','journeyAgainst','state','city','ageRange','practitionerType','speciality','myAffiliationIs','integrativeCareExperience')->where('id',$userId)->get()->toArray();
        $userDetails = [];
        foreach($user as $value){
            $details['firstName'] = $value['firstName'];
            $details['lastName'] = $value['lastName'];
            $details['email'] = !empty($value['email']) ? $value['email'] : "";
            $details['gender'] = !empty($value['gender']) ? $value['gender'] : "";
            $details['avatar'] = !empty($value['avatar']) ? $value['avatar'] : "";
            $details['state'] = !empty($value['state']) ? $value['state'] : "";
            $details['city'] = !empty($value['city']) ? $value['city'] : "";

            $details['ageRange'] = !empty($value['ageRange']) ? $value['ageRange'] : "";
            $details['practitionerType'] = !empty($value['practitionerType']) ?  $value['practitionerType'] : "";
            $details['speciality'] = !empty($value['speciality']) ? $value['speciality'] : "";
            $details['myAffiliationIs'] = !empty($value['myAffiliationIs']) ? $value['myAffiliationIs'] : "";
            $details['integrativeCareExperience'] = !empty($value['integrativeCareExperience']) ? $value['integrativeCareExperience'] : "";

            $userDetails[] = $details;
        }
        $userDetails = $userDetails[0];
        return view('auth.profile-rx',compact('userDetails','iamaOptions','onAJourneyOptions','genderOptions','states','practitionerTypeOptions','specialityOptions','myAffiliationIsOptions','integrativeCareExperienceOptions'));
    }

    /**
     * This function complies insert the pending information for 
     * profile of wellkasa-rx user
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data related user.
     * @return  \Illuminate\Http\Response               Redirect to related response with related message.
     */
    public function updateProfileRx(Request $request)
    {
        //User details form data validation
        $validator = Validator::make($request->all(), [
            'firstName_lastName' => 'required',
            'state' => 'required',
            'gender' => 'required',
            'ageRange' => 'required',
            'practitionerType' => 'required',
            'speciality' => 'required',
            'myAffiliationIs' => 'required',
            'integrativeCareExperience' => 'required',
            'avatar' => 'image|mimes:jpg,png,jpeg'
        ]);

        //validation failed
        if ($validator->fails()) 
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
            $userId = \Auth::user()->id;
            $user=Users::find($userId);

            if(!empty($request->get('firstName_lastName'))){
                $name = $request->get('firstName_lastName');
                if(strpos($name, " ")!='' && strpos($name, " ")!='0'){
                    $firstName = strtok($name, ' ');
                    $lastName = ltrim(substr($name, strpos($name, " ") + 1));

                    $user->name=$firstName;
                    $user->last_name=$lastName;
                }else{
                    $user->name=$name;
                    $user->last_name=null;
                }
            }

            $user->state=$request->get('state');
            $user->city=$request->get('city');
            $user->gender=$request->get('gender');
            $user->ageRange=$request->get('ageRange');
            $user->practitionerType=$request->get('practitionerType');
            $user->speciality=$request->get('speciality');
            $user->myAffiliationIs=$request->get('myAffiliationIs');
            $user->integrativeCareExperience=$request->get('integrativeCareExperience');

            //Check condtion for file is attach or not
            if ($request->hasFile('avatar'))
            {   
                if ($request->file('avatar')->isValid()) 
                {   
                    $file = $request->file('avatar');
                    $ext = $file->extension();
                    $name = $request->file('avatar')->getClientOriginalName();
                    $name = str_replace(' ', '_', $name);
                    $fileName = time().'_'.$name;
                        
                    if($request->file('avatar')->move(public_path('uploads/avatar/').'/', $fileName))
                    {
                        // delete old uploaded photo from the public/uploads/avatar folder if exists
                        $fileToDelete = public_path() . '/uploads/avatar/' . $user->avatar;
                        if(File::exists($fileToDelete)){
                            File::delete($fileToDelete);
                        }

                        $user->avatar = $fileName;
                    }
                }
            }

            $user->save();
            return redirect('my-profile-rx')->with('message', 'Profile updated successfully');
        
        }
        return view('auth.profile-rx');
    }
}

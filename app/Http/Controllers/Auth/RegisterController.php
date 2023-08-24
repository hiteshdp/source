<?php
/*
|-----------------------------------------------------------------------------
| LARAVEL APP BASE CLASS, MODEL CLASS, PACKAGES, HELPERS DEFINE
|-----------------------------------------------------------------------------
*/
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Route;
use App\Helpers\Helpers as Helper;
use Notification;
use App\Notifications\EmailTypeNotification;
use App\Notifications\UserEmailVerifiedCode;
use \Illuminate\Http\Request;
use App\Models\UserVerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Models\QuizResult;
use App\Models\Symptom;
use App\Models\UserSymptoms;
use Config;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/profile';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    /**
     * Redirect register page to login page
     *
     */
    public function showRegistrationForm()
    {
        return redirect('login');
    }
    
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        Validator::extend('no_mailinator_domain', function($attribute, $value, $parameters)
        {
            $blockValue = Helper::blockMailinatorEmailSignUp($value);
            if($blockValue == 1){
                return false;
            }else{
                return true;
            } 
        },'Email domain should not be of "mailinator.com"');

        return Validator::make($data, [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['no_mailinator_domain','required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            // 'password_confirmation' => ['required', 'string', 'min:8','required_with:password|same:password'],
            // 'terms_of_use' => ['required']
        ]);
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = new User;
        $user->name = $data['firstName'] ? $data['firstName'] : null;
        $user->last_name = $data['lastName'] ? $data['lastName'] : null;
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);

        if($user->save()){

            // Fetch the quiz attempted id from the session if exist and store by user id data
            Helper::storeQuizAttemptedData($user->id);

            $roleId = '4';
            if(!empty($data['roleId'])){
                $roleId = $data['roleId'];
            }
            // Adds User role       
            Helper::addUserRole($user->id,$roleId);
            
            $code = Helper::generateVerifiedCode();

            $userVerificationCode = new UserVerificationCode();
            $userVerificationCode->userId = $user->id;
            $userVerificationCode->verification_code = $code;
            $userVerificationCode->created_at = Carbon::now();
            $userVerificationCode->updated_at = null;
            $userVerificationCode->deleted_at = null;
            $userVerificationCode->save();

            $sendData = [
                'body' => 'Please verify your email address to get access to your personalized Migraine Tracker (your Wellness assistant).<br><br>
                Your email verification code is <b>'.$code.'</b> <br><br>
                If you did not create an account, no further action is required.' 
            ];
            Notification::route('mail' , $user->email)->notify(new UserEmailVerifiedCode($data['firstName'],$sendData));

            // Send mail to admin if mail id is of yahoo/aol
            Helper::sendUserEmailSpecificNotification($data['email']);

            /**
             * Symptom Names:
             * Headache, Nausea, Sensitivity, Fatigue, Sleep Difficulty, Brain Fog
             */
            $symptomsArrayIds = [1,2,3,4,5,7];
            // Add migraine headache for current registered user
            $migraine_headace_ids = Symptom::select('id')
            ->whereIn('id',$symptomsArrayIds)->get()->toArray();
            foreach($migraine_headace_ids as $key => $migraine_headace_ids_val){
                $userSymptomData = new UserSymptoms();
                $userSymptomData->userId = $user->id;
                $userSymptomData->symptomId = $migraine_headace_ids_val['id'];
                $userSymptomData->created_at = Carbon::now();
                $userSymptomData->save();
            }
            


            return $user;
        }

    }

    /**
     * Redirect page to sign up screen if quiz attempted.
     *  else redirect to migraine-tracker page 
     */
    public function migraineSignUpPage(Request $request){
        // When clicked on login button then display login form and hide sign up screen
        if($request->has('login') == 1){
            $hideSignUpTab = 1;
            return view('auth.signup_migraine_user',compact('hideSignUpTab'));
        }else{
            return view('auth.signup_migraine_user');
        }
    }
}

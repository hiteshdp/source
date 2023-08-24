<?php
/*
|-----------------------------------------------------------------------------
| LARAVEL APP BASE CLASS, MODEL CLASS, PACKAGES, HELPERS DEFINE
|-----------------------------------------------------------------------------
*/
namespace App\Http\Controllers\Auth;

use App\Models\User;
use Exception;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Helpers\Helpers as Helper;
use \Illuminate\Http\Request;
use Carbon\Carbon;
use Cookie;
use DB;
use Illuminate\Support\Facades\Session;
use App\Models\QuizResult;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = 'my-wellkasa';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    /**
     * Show/Display URL On Base Of Therapy String
     * 
     * @param   \Illuminate\Http\Request    $request   A request object pass through form data.
     * @return  \Illuminate\Http\Response              Redirect to related route path
     */
    public function showLoginForm(Request $request)
    {
        $url = url()->previous(); // Gets previous Url
        $therapy = "/(?<![\w\d])therapy(?![\w\d])/i"; // Check therapy in url
        // Check if url has therapy string in it then add redirect to last therapy page
        if(preg_match($therapy,$url) == 1){ 
            session(['url.intended' => $url]);
        }
        return view('auth.login');
    }

    /**
     * Define Social Media Providers Array
     */
    protected $providers = [
        'github','facebook','google','twitter'
    ];

    /**
     * Show/Display Login Form
     */
    public function show()
    {
        return view('auth.login');
    }

    /**
     * This function complies authenticate user on base of his role assign 
     * & redirect to related page.
     * 
     * @param   \Illuminate\Http\Request    $request   A request object pass through form data.
     * @param   object                      $user      User Object which contains user details.
     * @return  \Illuminate\Http\Response              Redirect to related route path
     */
    protected function authenticated(Request $request, $user)
    {   
        $checkUserRole3 = Auth::user()->isUserMigraineUser();
        if($checkUserRole3){
            // After success login, insert current date time in lastLoggedInDate field
            User::where('id',$user->id)->update(['lastLoggedInDate'=>Carbon::now()]);

            Session::put('isMigraineUser','1');
            return redirect()->intended('event-selection');
        }else{
            Auth::logout();
            return redirect()->route('login')
            ->withErrors(['msg' => 'Invalid email or password']);
        }
    }

    /**
     * This function complies redirect method provided by the Socialite facade 
     * takes care of redirecting the user to the OAuth provider, 
     * while the user method will examine the incoming request and 
     * retrieve the user's information from the provider after 
     * they have approved the authentication request.
     * 
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @param   string                      $driver     Social Media Driver Name.
     * @return  \Illuminate\Http\Response               Redirect to related response
     * 
     */
    public function redirectToProvider(Request $request, $driver)
    {
        if( ! $this->isProviderAllowed($driver) ) {
            return $this->sendFailedResponse("{$driver} is not currently supported");
        }

        try {
            $request->session()->put('userRoleId', $request->roleId);

            return Socialite::driver($driver)->redirect();
        } catch (Exception $e) {
            // You should show something simple fail message
            return $this->sendFailedResponse($e->getMessage());
        }
    }
    
    /**
     * This function complies check social login happen by the user 
     * success or fail and retrieve the user's information from 
     * the provider after they have approved the authentication request.
     * 
     * @param   string                      $driver     Social Media Driver Name.
     * @return  \Illuminate\Http\Response               Redirect to related response
     */
    public function handleProviderCallback( $driver )
    {
        try {
            $user = Socialite::driver($driver)->user();
        } catch (Exception $e) {
            return $this->sendFailedResponse($e->getMessage());
        }

        // check for email in returned user
        return empty( $user->email )
            ? $this->sendFailedResponse("No email id returned from {$driver} provider.")
            : $this->loginOrCreateAccount($user, $driver);
    }

    /**
     * This function complies user is healthcare provider or patient.
     * 
     * @return  \Illuminate\Http\Response               Redirect to related response
     */
    protected function sendSuccessResponse()
    {
        $checkUserRole3 = Auth::user()->isUserHealthCareProvider();
        if($checkUserRole3){
            return redirect()->intended('my-wellkasa-rx');
        }else{
            return redirect()->intended('my-wellkasa');
        }
        
    }

    /**
     * This function complies send fail message response.
     * @param   string      $msg     A message error response content.
     */

    protected function sendFailedResponse($msg = null)
    {
        return redirect()->route('social.login')
            ->withErrors(['msg' => $msg ?: 'Unable to login, try with another provider to login.']);
    }

    /**
     * This function complies that check user already exist or not. If user exist 
     * then update user data in table or not exist then create user from social media
     * detail.
     * 
     * @param   object                      $providerUser   A request object pass social media oAuth details.
     * @param   string                      $driver         Social Media Driver Name.
     * @return  \Illuminate\Http\Response                   Redirect to related response wih success message.
     */
    protected function loginOrCreateAccount($providerUser, $driver)
    {
        // check for already has account
        $user = User::where('email', $providerUser->getEmail())->first();
        
        // if user already found
        if( $user ) {
            // update the avatar and provider that might have changed
            $user->update([
                //'avatar' => $providerUser->avatar,
                'provider' => $driver,
                'provider_id' => $providerUser->id,
                'access_token' => $providerUser->token,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'lastLoggedInDate' => Carbon::now()
            ]);
        } else {
            // create a new user
            $user = new User;
            $user->name = $providerUser->getName();
            $user->email = $providerUser->getEmail();
            $user->provider = $driver;
            $user->provider_id = $providerUser->getId();
            $user->access_token = $providerUser->token;
            $user->password = '';
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->lastLoggedInDate = Carbon::now();
            if($user->save()){
                if($request->session()->has('userRoleId')){
                    $roleId = $request->session()->get('userRoleId');
                    // Adds User role = 2
                    Helper::addUserRole($user->id,$roleId);
                    session()->forget('userRoleId');
                }
                
            }

        }

        // login the user
        Auth::login($user, true);

        return $this->sendSuccessResponse();
    }

    /**
     * This function complies social media driver allow or permissable.
     * 
     * @param   string      $driver         Social Media Driver Name.
     * @return  \Illuminate\Http\Response   Redirect to related response.
     */
    private function isProviderAllowed($driver)
    {
        return in_array($driver, $this->providers) && config()->has("services.{$driver}");
    }
    
    /**
     *  This function complies destroy cookies & session of login user.
     *  @param   \Illuminate\Http\Request    $request   A request object pass through form data.
     *  @return  \Illuminate\Http\Response              Redirect to default route path i.e. home.
     */
    public function logout(Request $request){

        // Default set route to home
        $route = '/';
        // Check if session has value
        if(Session::has('isMigraineUser')){
            // Check if the logged in user was migriane user
            if(Session::get('isMigraineUser') == 1){
                // Add route to migraine tracker to redirect after logout
                $route = route('migrainemight');
            }
        }

        $this->guard()->logout();
        
        // Destroy loggedInUser cookie after logout
        Cookie::queue(Cookie::forget('loggedInUser'));

        // Destory the session for migraine user flow
        Session::forget('isMigraineUser');
        
        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect($route);
    }
}

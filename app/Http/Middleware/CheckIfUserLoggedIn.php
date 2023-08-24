<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cookie;
use App\Helpers\Helpers as Helper;

class CheckIfUserLoggedIn
{
    /**
     * If user is logged in then give menu items based on 
     * user role ( Healthcare Provider / Paitent Caregiver ) 
     * in cookie for wordpress site
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // After successful login, Add user first name in cookie named loggedInUser 
            $username = Auth::user()->name ? Auth::user()->name : strtok(Auth::user()->email, '@');
            $checkUserRole3 = Auth::user()->isUserHealthCareProvider();
            if($checkUserRole3){
               /***  // Code not in use
                $data = [
                    'name'=>$username,
                    "my-wellkasa"=>[
                        "label"=>"My Wellkasa Rx",
                        "url"=>route('my-wellkasa-rx')
                        
                    ],
                    "change-profile"=>[],
                    "my-profile"=>[
                        "label"=>"My Profile",
                        "url"=>route('my-profile-rx')
                    ]
                ]; */
                $isUserHealthCareProviderRole = true;
            }else{
                /*** // Code not in use
                // Get the profile member data and pass it in the cookie if exists.
                $profileMemberData = Auth::user()->getProfileMembers();
                if(!empty($profileMemberData)){
                    foreach ($profileMemberData as $key => $data){
                        $profileMemberData[$key]['label'] = $data['name'];
                        $profileMemberData[$key]['url'] = route('medicine-cabinet',\Crypt::encrypt($data['id']));
                        $profileMemberData[$key]['profilePic'] = $data['profile_picture'] ? $data['profile_picture'] : asset("images/user.svg");
                        $profileMemberData[$key]['defaultPic'] = asset("images/user.svg");
                        unset($profileMemberData[$key]['id']);
                        unset($profileMemberData[$key]['name']);
                        unset($profileMemberData[$key]['profile_picture']);
                    }

                    // Add current user details menu in the change profile menu
                    $currentUserMenuArr = array();
                    $currentUserMenu['label'] = Auth::user()->getUserName();
                    $currentUserMenu['url'] = route('medicine-cabinet');
                    $currentUserMenu['profilePic'] = asset('public/uploads/avatar/'.Auth::user()->avatar) ? asset('public/uploads/avatar/'.Auth::user()->avatar) : asset("images/user.svg");
                    $currentUserMenu['defaultPic'] = asset("images/user.svg");
                    $currentUserMenuArr = $currentUserMenu;
                    array_unshift($profileMemberData,$currentUserMenuArr);

                }else{
                    $profileMemberData = [];
                }

                $data = [
                    'name'=>$username,
                    "my-wellkasa"=> [
                        "label"=>"WellKabinet",
                        "url"=>route('medicine-cabinet')
                    ],
                    "change-profile"=> [
                        "label"=>"Change Profile",
                        "data"=>$profileMemberData
                    ],
                    "my-profile"=>[
                        "label"=>"My Profile",
                        "url"=>route('my-profile')
                    ]
                ]; */
                $isUserHealthCareProviderRole = false;
            }
            // Cookie::queue('loggedInUser', json_encode($data,true),20); // Code not in use
            
            $loginUrl = \Request::path();
            session(['url.intended' => url()->previous()]);
            $previousRoute = session()->get('url.intended');

            if($loginUrl == 'login'){
                
                // if previous url is also login then redirect to user type route, else redirect back
                if(!empty(strpos($previousRoute,"login"))){

                    if($isUserHealthCareProviderRole == true){
                        return redirect()->route('my-profile-rx');
                    }

                    if($isUserHealthCareProviderRole == false){
                        return redirect()->route('my-profile');
                    }
                }
                else{
                    return redirect()->back();
                }
                
            }
            
        }
        return $next($request);
    }
}

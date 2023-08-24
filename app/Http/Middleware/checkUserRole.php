<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use Redirect;
class checkUserRole
{
    /**
     * Check if the logged in user is paitent caregiver user role 
     * then give the access its related pages else show permission error
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $checkUserRole3 = Auth::user()->isUserHealthCareProvider();
        if(!$checkUserRole3){

            $currentroute = \Request::getRequestUri();
            
            if(Auth::user()->updatedCompleteProfile == '0' && $currentroute != '/complete-profile'){
                
                if(isset($request->isPassMiddleware) && $request->isPassMiddleware!='' && $request->isPassMiddleware == '1'){
                    return $next($request);
                }
                
                return Redirect::to('complete-profile')
                ->withSuccess('Please complete the profile to access all features of WellKabinet');
            
            }else{
                return $next($request);
            }

        } 
        return Redirect::back()->withErrors(['msg' => 'You are not allowed to access this page!']);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use DB;
use Auth;
use Redirect;
class checkWellkabinetMigraineProviderUserRole
{
    /**
     * Check if the logged in user is wellkabinet or migraine user role 
     * then give the access its related pages else show permission error
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $checkUserRole2 = Auth::user()->isWellabinetUser(); 
        $checkUserRole4 = Auth::user()->isUserMigraineUser();
        $checkUserRole5 = Auth::user()->isProviderUser();
        if($checkUserRole2 || $checkUserRole4 || $checkUserRole5){
            return $next($request);
        } 
        return Redirect::back()->withErrors(['msg' => 'You are not allowed to access this page!']);
    }
}

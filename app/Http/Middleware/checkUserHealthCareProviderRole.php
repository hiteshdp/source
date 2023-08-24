<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use DB;
use Auth;
use Redirect;
class checkUserHealthCareProviderRole
{
    /**
     * Check if the logged in user is health care provider user role 
     * then give the access its related pages else show permission error
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $checkUserRole3 = Auth::user()->isUserHealthCareProvider();
        if($checkUserRole3){
            return $next($request);
        } 
        return Redirect::back()->withErrors(['msg' => 'You are not allowed to access this page!']);
    }
}

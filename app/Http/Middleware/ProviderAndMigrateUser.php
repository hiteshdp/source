<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use DB;
use Auth;
use Redirect;
class ProviderAndMigrateUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $checkUserRole4 = Auth::user()->isUserMigraineUser();
        $checkUserRole5 = Auth::user()->isProviderUser();
        if($checkUserRole4 || $checkUserRole5){
            return $next($request);
        } 
        return Redirect::back()->withErrors(['msg' => 'You are not allowed to access this page!']);
    }
}

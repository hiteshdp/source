<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use DB;
use Auth;
use Redirect;
class checkUserSubscriptions
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
        $user = Auth::user();
        if($user){
            $dt = date('Y-m-d'); 
            $checkSubscriptions = DB::table('subscriptions')->where('user_id',$user->id)->orderby('id','desc')->whereDate('current_period_end', '>=', $dt)->first();
            if(isset($checkSubscriptions) && !empty($checkSubscriptions)){
                return redirect('my-profile')->withErrors(['msg' => 'You are already subscribed plan']);
            }
        }
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;
use Auth;
use Closure;
use Illuminate\Http\Request;
use App\Models\UserRole;
use App\Models\User;

class VerifyWebsiteUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check()) {
            // Check if user is user role is not webiste user (Healthcare provider or Paitent Caregiver) then revoke login with permission message
            $userId = \Auth::user()['id'];
            $userRoleCheck = User::where('users.id',$userId)->select('user_roles.role_id as role_id')
            ->join('user_roles','users.id','=','user_roles.user_id')
            ->get()->pluck('role_id')->first();
            if(isset($userRoleCheck) && $userRoleCheck == '1'){
                Auth::logout();
                $userRoleMessage = trans('messages.not_user_role');
                return redirect()->route('login')->withErrors([$userRoleMessage]);
            
            }else{
                // //If user is deactivated then execute this code
                if(isset(\Auth::user()['status']) && \Auth::user()['status'] == "0"){
                    Auth::logout();
                    $accountDeleteMessage = trans('messages.contact_admin_deactivated');
                    return redirect()->route('login')->withErrors([$accountDeleteMessage]);
                }
                // If user is deleted then execute this code
                else if(isset(\Auth::user()['deleted_at']) && \Auth::user()['deleted_at']!='' || !empty(\Auth::user()['deleted_at'])){
                    Auth::logout();
                    $inActiveMessage = trans('messages.user_inactive');
                    return redirect()->route('login')->withErrors([$inActiveMessage]);
                
                }else{
                    return $next($request);
                }
            }

        }else{
            return $next($request);
        }        
        
    }
}

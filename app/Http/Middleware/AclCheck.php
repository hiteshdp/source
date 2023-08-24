<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\UserRole;
use App\Models\RolePermission;
use App\Models\Permission;
use App\Models\Module;

class AclCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$module_name)
    {
        $isAllowed = 0;
        $permissions = [];
        

        //Get the loggedin id of the user
        $user = Auth::user()->id;

        //Get the roles of the user who is loggedin
        $roles = UserRole::where('user_id',$user)->where('status',1)->select('role_id')->get()->pluck('role_id')->toArray();
        //Get the module id which is passed from the route
        $module = Module::where('status',1)->where('name',$module_name)->select('id as module_id')->first();
        
        // if user has any role assigned
        if(!empty($roles)){

            $perm = RolePermission::whereIn('role_id',$roles)->select('permission_id')->get()->pluck('permission_id')->toArray();

            // if module name is not empty
            if($module){

                // if request method is POST
                if($request->isMethod('POST')){

                    $isAllowed = Permission::where('status',1)->whereIn('id',$perm)->where('module',$module->module_id)->where('name','can_create')->count();

                }

                // if request method is GET
                if($request->isMethod('GET')){
                    
                    $isAllowed = Permission::where('status',1)->whereIn('id',$perm)->where('module',$module->module_id)->where('name','can_view')->count();

                }

                // if request method is PUT
                if($request->isMethod('PUT')){

                    $isAllowed = Permission::where('status',1)->whereIn('id',$perm)->where('module',$module->module_id)->where('name','can_update')->count();

                }

                // if request method is DELETE
                if($request->isMethod('DELETE')){

                    $isAllowed = Permission::where('status',1)->whereIn('id',$perm)->where('module',$module->module_id)->where('name','can_delete')->count();

                }

            }
            else{
                $isAllowed = 1;
            }

            // if action is allowed
            if($isAllowed > 0){
                return $next($request);
            }
            else{

                // if no roles are assigned to user
                $this->msg['status'] = 0;
                $this->msg['message'] = "Permission denied. You are not allowed to perform this operation";

                return response()->json($this->msg,401);
            }

        }
        else{
            
            // if no roles are assigned to user
            $this->msg['status'] = 0;
            $this->msg['message'] = "Permission denied. You are not allowed to perform this operation";

            return response()->json($this->msg,401);
        }

        return $next($request);
    }
}

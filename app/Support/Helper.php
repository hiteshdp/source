<?php  
use App\Models\Module;
use App\Models\Settings;
use App\Models\UserRole;
use App\Models\RolePermission;
use App\Models\Permission;

if (! function_exists('pr')) {
    /**
     * Format text.
     *
     * @param  string  $text
     * @return string
     */
    function pr($value)
    {
        echo "<pre>";
        	print_r($value);
        echo "</pre>";die;
    }
}

if (! function_exists('is_migraine_quiz')) {
    /**
     * Format text.
     *
     * @param  string  $text
     * @return string
     */
    function is_migraine_quiz()
    {
        $value = false;
        // Check if session exists
        if(Session::has('is_migraine_quiz') == '1'){
            // Check if session value is 1 then return true, else false
            if(Session::get('is_migraine_quiz') == '1'){
                $value = true;
            }
        }
        return $value;
    }
}
	
if(!function_exists('modulePermissions')){
    /**
     * Function
     *
     * @param  string  $text
     * @return string
     */
    function modulePermissions($requrl,$authId)
    {
        //Get the roles of login user
        $roles = UserRole::where('status',1)->where('user_id',$authId)->pluck('role_id')->toArray();

        //Explode request url from admin and get module name like users
        $url = explode('/', $requrl);
        //Get module information  using module name 
        $endUrl = end($url);
        
        
        if($endUrl =='cms')
        {
            if(strpos($endUrl,'cms') != -1)
            {
                $endUrl = 'cms';
            }
        }
       
        $module = Module::where('status',1)->where('name',$endUrl)->first();
        //Get perminssion details using above role id and module id
        $modulePermission = Permission::where('module',$module->id)->whereIn('id',
            RolePermission::whereIn('role_id',$roles)->whereIn('permission_id',
                Permission::where('module',$module->id)->pluck('id')->toArray()
            )->pluck('permission_id')->toArray())->where('status',1)->pluck('name')->toArray();

        //Initialize constant varialbe 
        $moduleFinal = [config('app.CAN_UPDATE')=>false,config('app.CAN_VIEW')=>false,config('app.CAN_CREATE')=>false,config('app.CAN_DELETE')=>false];
        
        //Check each operation and make them true
        foreach ($modulePermission as $mkey => $mvalue) {
            if($mvalue=='can_update')
                $moduleFinal[config('app.CAN_UPDATE')] = true;
            else if($mvalue=='can_view')
                $moduleFinal[config('app.CAN_VIEW')] = true;
            else if($mvalue=='can_create')
                $moduleFinal[config('app.CAN_CREATE')] = true;
            if($mvalue=='can_delete')
                $moduleFinal[config('app.CAN_DELETE')] = true;
        }

        //Generate the sidebar menu of update and delete for admin dynamically
        if(!($moduleFinal[config('app.CAN_UPDATE')]==false && $moduleFinal[config('app.CAN_DELETE')] == false))
        {
            $moduleFinal['sidemenu'][0] = ['txt'=>'Edit','can_access'=>$moduleFinal[config('app.CAN_UPDATE')]];
            $moduleFinal['sidemenu'][1] = ['txt'=>'Delete','can_access'=>$moduleFinal[config('app.CAN_DELETE')]];
        }
        
        return $moduleFinal;
    }
}	

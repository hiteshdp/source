<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Crypt;
use App\Helpers\Helpers as Helper;

class Provider extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'providers';

    /***
     * Get the providers list of approved status
     */
    public static function list(){

        // image path url
        $avatarPath = asset('uploads/avatar/').'/';

        // default image url
        $defaultImage = asset('images/').'/profile-member.jpg';

        /**
         * Get all the data from the providers table with 
         * its related info from users table
         *  */ 
        $data = DB::table('providers')
        ->select('providers.*',
        'users.name as firstName',
        'users.last_name as lastName',
        DB::raw("(CASE 
                    WHEN `users`.`avatar` != '' 
                    THEN CONCAT('".$avatarPath."',`users`.`avatar`) 
                    ELSE '".$defaultImage."' 
                END) AS image"))
        ->leftJoin('users','providers.userId','=','users.id')
        ->where('providers.isProviderApproved','1');
        // Exclude the providers from the list if the logged in user has already added
        $loggedInUserAddedProvider = Helper::getAddedProviderList();
        $data = $data->whereIn('providers.userId',$loggedInUserAddedProvider['providerIds']);
        $data = $data->get()->toArray();

        // If not empty data then pass the url of the provider details page
        if(!empty($data)){
            foreach($data as $key => $value){
                // Provider details route
                $data[$key]->accessProviderDetailsURL = route('get-provider-details',[Crypt::encrypt($value->userId)]);
                // Access details route
                $data[$key]->accessDetailsURL = route('get-providers-access-data',[Crypt::encrypt($value->userId)]);
                // Remove access route
                $data[$key]->removeAccessURL = route('send-revoke-verification-code',[Crypt::encrypt($value->userId)]);
            }
        }
        return $data;
    }

    /***
     * Get the specific data of provider details by id
     */
    public static function fetchById($id){
        // image path url
        $avatarPath = asset('uploads/avatar/').'/';
        // default image url
        $defaultImage = asset('images/').'/profile-member.jpg';

        $data = DB::table('providers')
        ->select('providers.*',
        'users.name as firstName',
        'users.last_name as lastName',
        DB::raw("(CASE 
                    WHEN `users`.`avatar` != '' 
                    THEN CONCAT('".$avatarPath."',`users`.`avatar`) 
                    ELSE '".$defaultImage."' 
                END) AS image"))
        ->leftJoin('users','providers.userId','=','users.id')
        ->where('providers.userId',$id)
        ->where('providers.isProviderApproved','1');
        $data = $data->get()->first();

        if(!empty($data)){
            // Remove access route
            $data->consentUrl = route('provider.consent',Crypt::encrypt($data->userId));
        }

        return $data;
    }

    /***
     * Get the provider access details by id
     */
    public static function fetchAccessDetailsById($id){
        
        // image path url
        $avatarPath = asset('uploads/avatar/').'/';
        // default image url
        $defaultImage = asset('images/').'/profile-member.jpg';
        
        $data = DB::table('providers')
        ->select('providers.*',
        'provider_user.access_start_date as start_date',
        'provider_user.access_end_date as end_date',
        'provider_user.access_notes as access_notes',
        'provider_user.access_meds as access_meds',
        'provider_user.access_symptoms as access_symptoms',
        DB::raw("(CASE 
                    WHEN `users`.`last_name` != '' 
                    THEN CONCAT(`users`.`name`,' ',`users`.`last_name`) 
                    ELSE `users`.`name` 
                END) AS providerName"),
        DB::raw("(CASE 
                    WHEN `users`.`avatar` != ''
                    THEN CONCAT('".$avatarPath."',`users`.`avatar`) 
                    ELSE '".$defaultImage."' 
                END) AS image"))
        ->leftJoin('users','providers.userId','=','users.id')
        ->leftJoin('provider_user','provider_user.providerId','=','users.id')
        ->where('provider_user.providerId',Crypt::decrypt($id))
        ->whereNull('provider_user.deleted_at')
        ->whereNull('provider_user.access_revoke_date')
        ->where('providers.isProviderApproved','1');
        $data = $data->get()->first();

        $dataShared = '';
        if(!empty($data)){
            // Update the date format to month/date/year for start date
            $data->start_date = date('m/d/Y',strtotime($data->start_date));
            // Update the date format to month/date/year for end date
            $data->end_date = date('m/d/Y',strtotime($data->end_date));

            $dataShared .= 'Name, ';
            // If symptoms access given then add that in the data shared value
            if($data->access_symptoms == '1'){
                $dataShared .= 'Symptoms, ';
            }
            // If meds access given then add that in the data shared value
            if($data->access_meds == '1'){
                $dataShared .= 'Meds, ';
            }
            // If notes access given then add that in the data shared value
            if($data->access_notes == '1'){
                $dataShared .= 'Notes';
            }
            // Add all the access given values
            $data->dataShared = $dataShared;
        }
        return $data;
    }

    /***
     * Get the patients list associated by logged in provider user
     */
    public static function getAssociatedPatientsList($id){

        // image path url
        $avatarPath = asset('uploads/avatar/').'/';

        // default image url
        $defaultImage = asset('images/').'/profile-member.jpg';

        /**
         * Get all the data from the provider_user table with 
         * its related info from users table
         *  */ 
        $data = DB::table('provider_user')
        ->select('provider_user.*',
        'users.name as firstName',
        'users.last_name as lastName',
        'user_roles.role_id as roleId',
        DB::raw("(CASE 
                    WHEN `users`.`avatar` != '' 
                    THEN CONCAT('".$avatarPath."',`users`.`avatar`) 
                    ELSE '".$defaultImage."' 
                END) AS image"))
        ->leftJoin('users','provider_user.userId','=','users.id')
        ->leftJoin('user_roles','provider_user.userId','=','user_roles.user_id')
        ->where('provider_user.providerId',$id)
        ->whereNull('provider_user.deleted_at')
        ->whereNull('provider_user.access_revoke_date')
        ->orderBy('provider_user.id','DESC');
        $data = $data->get()->toArray();
        
        // If not empty data then pass the url of the provider details page
        if(!empty($data)){
            foreach($data as $key => $value){
                // Provider details route
                $data[$key]->reportAccessURL = route('get-report-pdf',[Crypt::encrypt($value->userId)]);

                // Remove access route
                $data[$key]->removeAccessURL = route('send-revoke-code-by-provider',[Crypt::encrypt($value->userId)]);
            }
        }
        return $data;
    }

        
}

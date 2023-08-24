<?php
/*
|-----------------------------------------------------------------------------
| LARAVEL APP BASE CLASS, MODEL CLASS, PACKAGES, HELPERS DEFINE
|-----------------------------------------------------------------------------
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usertherapy;
use App\Models\UserTherapyHistory;
use App\Models\Therapy;
use App\Models\Condition;
use App\Models\Master;
use App\Models\User;
use App\Helpers\Helpers as Helper;
use App\Models\TherapyCondition;
use App\Models\Condition as Conditions;
use App\Models\TherapyDetails;
use Carbon\Carbon;
use DB;
use App\Models\MedicineCabinet;
use App\Models\MedicineCabinetConditions;
use App\Models\MedicineCabinetNotes;

class RectifyTherapyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Rectify Therapy Controller
    |--------------------------------------------------------------------------
    | 
    | This controller handles to rectify therapy data. 
    | It gets therapy data & update therapry condition. It also check if therapy
    | id exist in UserTherapy, UserTherapyHistory & Therapy table then delete this record.
    | After that it resetting the record in Therapy model table.
    |
    */

    /**
     * This function complies delete duplicate therapy name details 
     * from therapy table and add relation in therapy condition table
     * 
     * @param   \Illuminate\Http\Request    $request            A request object pass.
     * @return  \Illuminate\Http\Response   Redirect to related response data arrange and load data in a view.
    */
    public function conditionTherapy(Request $request)
    {
        $allTherapy = Therapy::groupBy('therapy')->orderBy('id')->get()->toArray();
        
        foreach ($allTherapy as $key => $value) {
            $therapy = $value['therapy'];
        
            $therapyName = Therapy::where('therapy',$therapy)->orderBy('id')->get()->toArray();
        
            $i = 0;
            foreach ($therapyName as $k => $data) {
                
                if($i == 0){
                    $therapyID = $data['id'];
                }
                
                $therapyCondition = new TherapyCondition;
                $therapyCondition->conditionId = $data['conditionId'];
                $therapyCondition->therapyId = $therapyID;
                $therapyCondition->effectiveness = $data['effectiveness'];
                $therapyCondition->save();
                echo "Inserted ".$data['id']."</br>";

                if($i > 0){
                    echo "Deleted ".$data['id']."</br>";
                
                    UserTherapy::where('therapyID',$data['id'])->update(['deleted_at'=>Carbon::now()]);
                    UserTherapyHistory::where('therapyID',$data['id'])->update(['deleted_at'=>Carbon::now()]);
                    Therapy::where('id',$data['id'])->update(['deleted_at'=>Carbon::now()]);

                }                    
                
                $i++;
            }
            // exit;
        
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Therapy::whereNotNull('deleted_at')->delete();
        UserTherapy::whereNotNull('deleted_at')->delete();
        UserTherapyHistory::whereNotNull('deleted_at')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        echo "<pre>"; print_r("data arranged successfully"); exit;
        return view('checkdata.index',compact($allTherapy));
        
    }

    /**
     * This function complies import therapy data in csv file.
     * 
     * @param   \Illuminate\Http\Request    $request            A request object pass.
    */
    public function importTherapy(Request $request)
    {
        try{
			//Stopped cron job as multiple data were getting updated
			//die;
            //DB::beginTransaction();

            if (($handle = fopen ( public_path () . '\import\samplecsv.csv', 'r' )) !== FALSE) {
                while ( ($data = fgetcsv ( $handle, 1000, ',' )) !== FALSE ) {
                    
                    if($data[0] == "Therapy Type")
                        continue;
                    
                    /*if($data[4] != 1581)
                        continue;*/

                    //echo "<pre>"; print_r($data); die;
					//Get the Therapy to check if it exist or not
                    //$therapy_count = Therapy::where('apiID',$data[4])->count();
                    $therapy = Therapy::where('apiID',$data[4])->first();

                    //Only add if therapy does not exist
                    if($therapy === null){
						
						unset($therapy);
                        //Add new Therapy to table
                        $therapy = new Therapy();
                        $therapy->therapy = $data[1];
                        $therapy->therapyType = $data[0];
                        $therapy->apiID = $data[4];  
                        $therapy->created_at = Carbon::now(); 
                        $therapy->updated_at = Carbon::now();
                        $therapy->imported_at = Carbon::now();
                        $therapy->save(); 
                    }
                    else
                    {
                        
                        if(is_null($therapy->imported_at))
                        {
                            $therapy->therapy = $data[1];
                            $therapy->therapyType = $data[0];
                            $therapy->updated_at = Carbon::now();
                            $therapy->imported_at = Carbon::now();
                            $therapy->save();
                        }
                    }

                    if($data[2] != ''){
                        //Get the Condition to check if it exist or not
                        $condition_detail = Condition::where('conditionName',$data[2])->first();

                        //Only add if therapy does not exist
                        if($condition_detail === null){

                            //Add new Therapy to table
                            $condition_detail = new Condition();
                            $condition_detail->conditionName = $data[2];
                            $condition_detail->created_at = Carbon::now(); 
                            $condition_detail->updated_at = Carbon::now(); 
                            $condition_detail->save(); 
                        }
                    
                        //Check therapy conditoin and insert if not exist
                        $TherapyCondition = TherapyCondition::where('conditionId',$condition_detail->id)->where('therapyId',$therapy->id)->first();
                        
                        if($TherapyCondition === null){
                            //Add new Therapy conditoin to condition to table
                            $TherapyCondition = new TherapyCondition();
                            $TherapyCondition->conditionId = $condition_detail->id;
                            $TherapyCondition->therapyId = $therapy->id;
                            $TherapyCondition->effectiveness = $data[3];
                            $TherapyCondition->created_at = Carbon::now(); 
                            $TherapyCondition->updated_at = Carbon::now(); 
                            $TherapyCondition = $TherapyCondition->save(); 
                        }
                    }
                }
            fclose ( $handle );
            //If all good commit the changes
            //DB::commit();
            echo "Therapy data updated successfully"; die;
            }
        }catch (\Exception $e){
            //DB::rollback();
            echo 'Message: ' .$e->getMessage();
            // Store logs
            $url = env("EFFECTIVE_API_URL", "Therapy Import");
            $requestData = "No request data";
            $responseData= $e->getMessage();
            $startCron = Carbon::now();
            $endCron = Carbon::now();
            $type = 1;
            Helper::storeLogs($url,$requestData,$responseData,$type,$startCron,$endCron);
        }
    }

    /**
     * This function update master therapy condition from TRC data that we have.
     * It also check if there are records having isProcessed 0 then show imported.
     * 
     * @param   \Illuminate\Http\Request    $request            A request object pass.
    */
    public function updateTherapyConditions(Request $request)
    {
        //Get all therapy detail which not processed.
        $allTherapyDetails = TherapyDetails::orderBy('id')->where('isProcessed',0)->limit(30)->get()->toArray();

        $duplicateConditionNames = [];
        $emptyConditionIds = [];
        foreach ($allTherapyDetails as $therapy_key => $therapy_value) {
            
            $therapyId = $therapy_value['therapyId'];
            $therapyTableId = $therapy_value['id'];

            $conditionDetails = json_decode($therapy_value['effectiveDetail'],true);
            
            // If condition details is not empty then insert the details
            if(!empty($conditionDetails)){
                
                $i = 0;

                // Get condition details from effectiveness array
                foreach ($conditionDetails['effectiveness'] as $key => $value) {
                    $i++;
                    $conditionName = $value['condition'];
                    if(!empty($conditionName)){
                        // Changes the effectiveness label 
                        $effectiveness = strtoupper($value['rating-description']);
                        if($effectiveness == 'INSUFFICIENT RELIABLE EVIDENCE TO RATE'){
                            $effectiveness = 'INSUFFICIENT RELIABLE EVIDENCE to RATE';
                        }

                        // Check if condition name exist in the condition table, if not then insert
                        $conditionNameLowerCase = strtolower($conditionName);
                        $conditionTable = Condition::whereRaw('LOWER(conditionName) = ?',$conditionNameLowerCase);
                        $sameConditionNameCount = $conditionTable->count();
                        if($sameConditionNameCount == 0){
                            
                            //Add new condition to table
                            $condition_detail = new Condition();
                            $condition_detail->conditionName = $conditionName;
                            $condition_detail->created_at = Carbon::now(); 
                            $condition_detail->updated_at = Carbon::now(); 
                            $condition_detail->save(); 
        
                            // Check therapy condition and insert if not exist
                            $TherapyCondition = TherapyCondition::where('conditionId',$condition_detail->id)
                                                                ->where('therapyId',$therapyId)->first();
                            if($TherapyCondition === null){
                                //Add new Therapy condition to condition to table
                                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                                $TherapyCondition = new TherapyCondition();
                                $TherapyCondition->conditionId = $condition_detail->id;
                                $TherapyCondition->therapyId = $therapyId;
                                $TherapyCondition->effectiveness = $effectiveness;
                                $TherapyCondition->created_at = Carbon::now(); 
                                $TherapyCondition->updated_at = Carbon::now(); 
                                $TherapyCondition->save(); 
                                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
                            }
                            // Update isProcessed to 1 when therapy condition is imported successfully 
                            TherapyDetails::where('id',$therapyTableId)->update(['isProcessed'=>'1']);
        
                            // echo "<pre>"; print_r($i.") ".$conditionName." imported successfully");
        
                        }else{
        
                            // if same condition then check if current therapyId is associated with conditionId, then insert record 
                            $sameConditionNameId = $conditionTable->select('id')->get()->first()->id;
                            $TherapyCondition = TherapyCondition::where('conditionId',$sameConditionNameId)
                                                                ->where('therapyId',$therapyId)->first();
        
                            //Add new Therapy condition to condition to table 
                            if(empty($TherapyCondition)){
                                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                                $TherapyCondition = new TherapyCondition();
                                $TherapyCondition->conditionId = $sameConditionNameId;
                                $TherapyCondition->therapyId = $therapyId;
                                $TherapyCondition->effectiveness = $effectiveness;
                                $TherapyCondition->created_at = Carbon::now(); 
                                $TherapyCondition->updated_at = Carbon::now(); 
                                $TherapyCondition->save(); 
                                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                                
                                $i++;
                                TherapyDetails::where('id',$therapyTableId)->update(['isProcessed'=>'1']);
                                // echo "<pre>"; print_r($i.") Added Therapy Id : ".$therapyId." with condition ".$conditionName." name successfully");
        
                            }else{
                                // Get the duplicate condition name that is imported
                                $duplicateConditionNames[] = $conditionName;
                                TherapyDetails::where('id',$therapyTableId)->update(['isProcessed'=>'2']);
                            }
                        }
                    }else{

                        // Get the empty condition name ids that are not imported
                        $emptyConditionIds[] = $therapyTableId;
                        TherapyDetails::where('id',$therapyTableId)->update(['isProcessed'=>'3']);

                    }
                  
                }
            }else{
                // Get the empty condition details ids that are not imported
                $emptyConditionIds[] = $therapyTableId;
                TherapyDetails::where('id',$therapyTableId)->update(['isProcessed'=>'3']);
            }
            
            
        }
        // If there are records having isProcessed 0 then show imported
        if(!empty($allTherapyDetails)){
            echo "<pre>"; print_r("<br>");
            echo "<pre>"; print_r($i." imported out of current 50 records");
            
            // $duplicateRecords = 50 - $i;
            // echo "<pre>"; print_r($duplicateRecords." duplicate conditions found out and not imported out of current 50 records");
            
            // echo "<pre>"; print_r($duplicateConditionNames);

            // echo "<pre>"; print_r("Empty Condition names in effectiveDetail field. below are the ids,");
            // echo "<pre>"; print_r(implode(', ',$emptyConditionIds));
            
            $importRemaining = TherapyDetails::orderBy('id')->where('isProcessed',0)->count();
            $allRecords = TherapyDetails::orderBy('id')->count();
            echo "<pre>"; print_r($importRemaining." import remaining out of ".$allRecords." records");
        }else{
            // If there are no records having isProcessed 0 then execute below code
            echo "<pre>"; print_r("All imported successfully, Nothing to import");
        }
        

        exit;
       
        
    }

      /**
     * This function check condition name available or not in tmp_condition.
     * 
     * @param   \Illuminate\Http\Request    $request            A request object pass.
     * @return  \Illuminate\Http\Response   Json object return Condition Name
    */
    public function checkConditions(Request $request)
    {
        
        $allConditions = DB::table('tmp_condition')->orderBy('id')->get()->toArray();
        $allConditions = json_decode(json_encode($allConditions),true);
        $i = 0;
        foreach ($allConditions as $key => $value) {
            $tmpConditions = Conditions::select('id','conditionName')
            ->where('conditionName',"=",$value['therapy'])->get()->first();
            
            if(empty($tmpConditions)){
                $i++;
                echo $value['therapy']."<br/>";
                // DB::table('tmp_condition')->where("id",$value['id'])->update(["status"=>"3"]);
            }else{
                // DB::table('tmp_condition')->where("id",$value['id'])->update(["status"=>"2"]);
            }
            
        }
        exit;
        
        
    }
    
    
     /**
     * This function check condition name available or not in tmp_condition.
     * 
     * @param   \Illuminate\Http\Request    $request            A request object pass.
     * @return  \Illuminate\Http\Response   Json object return Condition Name
    */
    public function migrateWellkasaData(Request $request)
    {
        
        $allTherapy = DB::table('user_therapy')->orderBy('userId')->whereNull('deleted_at')->get()->toArray();
        foreach ($allTherapy as $keyTherapy => $valueTherapy) {
            //Add new medicin cabinet therapy from old data
            $MedicineCabinet_details = new MedicineCabinet();
            $MedicineCabinet_details->naturalMedicineId = $valueTherapy->therapyID;
            $MedicineCabinet_details->userId = $valueTherapy->userId;
            $MedicineCabinet_details->created_at = Carbon::now(); 
            $MedicineCabinet_details->updated_at = Carbon::now(); 
            $MedicineCabinet_details->save(); 
        
            $allTherapyConditions = DB::table('user_therapy_conditions')->where('userId',$valueTherapy->userId)->where('userTherapyId',$valueTherapy->id)->orderBy('userId')->get()->toArray();
        
            //echo "<pre>"; print_r($allTherapyConditions); die;
           
            $i = 0;
            if(!empty($allTherapyConditions)){
                foreach ($allTherapyConditions as $key => $value) {

                    //Add condition based on added medicine cabinate record added
                    $MedicineCabinetConditions = new MedicineCabinetConditions();
                    $MedicineCabinetConditions->medicineCabinetId = $MedicineCabinet_details->id;
                    $MedicineCabinetConditions->conditionId = $value->conditionId;
                    $MedicineCabinetConditions->customConditionName = $value->otherText;
                    $MedicineCabinetConditions->created_at = Carbon::now(); 
                    $MedicineCabinetConditions->updated_at = Carbon::now(); 
                    $MedicineCabinetConditions->save();    
                    
                    // echo "<pre>"; print_r($allTherapyNotes); die;
                    //  echo "<pre>"; print_r($value); die;
                    $i++;
                    
                }
            }
            

            //Get all notes for therapy
            $allTherapyNotes = DB::table('user_therapy_history')->where('userId',$valueTherapy->userId)->where('therapyID',$valueTherapy->therapyID)->orderBy('id','DESC')->get()->toArray();
                    
            //Check if notes available
            if(!empty($allTherapyNotes))
            {
                foreach ($allTherapyNotes as $keyNotes => $valueNotes) {
                    //Add new medicin cabinet notes from old data
                    $MedicineCabinetNotes = new MedicineCabinetNotes();
                    $MedicineCabinetNotes->medicineCabinetId = $MedicineCabinet_details->id;
                    $MedicineCabinetNotes->notes = $valueNotes->note;
                    $MedicineCabinetNotes->created_at = $valueNotes->created_at; 
                    $MedicineCabinetNotes->updated_at = $valueNotes->updated_at; 
                    $MedicineCabinetNotes->save();
                }
            } 
        
        
        }


        


        

        echo $i." rows added<br/>";
        exit;
        
        
    }

    
    /**
     * This function check wellkabinet users and update the full access subscription till June 15th 2022.
     * 
     * @return  \Illuminate\Http\Response   Json object return user data
    */
    public function updateWellkabinetAccess(Request $request)
    {
        $count = 0;
        
        $allWellkabinetUsers = User::join('user_roles','users.id','=','user_roles.user_id')
        ->where('role_id','2')->orderBy('users.id')
        ->get()->toArray();

        $tillValidDate = date('2022-06-15 23:59:59');
        foreach ($allWellkabinetUsers as $allWellkabinetUsersKey => $allWellkabinetUsersValue) {
            $user_id = $allWellkabinetUsersValue['user_id'];

            // Check profile member count
            $currentSelectedUser = User::where('id',$user_id)->get()->first();
            if($currentSelectedUser->profileMemberCount == 0){
                // Add user subscription to wellkabinet
                User::where('id',$user_id)->update([
                    'planType' => '2',
                    'profileMemberCount' => '5',
                    'remainingProfileMemberCount' => '5',
                ]);
            }

            $checkSubscriptionData = DB::table('subscriptions')->where('user_id',$user_id)->get()->first();

            if(empty($checkSubscriptionData)){
                $generateRandomString = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(24/strlen($x)) )),1,24);
                $currentDate = date('Y-m-d 00:00', strtotime(Carbon::now()));
                DB::table('subscriptions')->insert([
                    'user_id' => $user_id, 
                    'name' => 'default',
                    'billing_cycle_date' => $tillValidDate,
                    'current_period_start' => $currentDate,
                    'current_period_end' => $tillValidDate,
                    'stripe_status' => 'active',
                    'stripe_id' => 'sub_1'.$generateRandomString,
                    'created_at' => Carbon::now(),
                ]);
                $count++;
            }
            // else{
            //     DB::table('subscriptions')->where('user_id',$user_id)
            //     ->orderBy('id','desc')
            //     ->take(1)
            //     ->update([
            //         'billing_cycle_date' => $tillValidDate,
            //         'current_period_end' => $tillValidDate,
            //         'updated_at' => Carbon::now(),
            //     ]);
            //     $count++;
            // }
        }

        echo $count. " rows inserted.";
        
    }

    /**
     * This function gets all the user data with subscription in csv file
     * @return  \Illuminate\Http\Response   Json object return user data
    */
    public function getAllUsersDetailsCsv(Request $request)
    {
        $count = 1;
        
        $allUsers = User::select('users.id as userId','users.name as firstName',
        'users.last_name as lastName', 'users.email as email', 'users.created_at as createdAt',
        'users.gender as genderId', 'users.patientAge as patientAge', 'users.ageRange as ageRange',
        'user_roles.role_id as roleId', 'users.lastLoggedInDate as lastLoggedInDate', 'users.email_verified_at as verified',
        'users.status as status')
        ->join('user_roles','users.id','=','user_roles.user_id')
        ->where('role_id','!=','1')->orderBy('users.id')
        ->groupBy('user_roles.user_id')
        ->whereNull('users.deleted_at')
        ->get()->toArray();

        $currentDate = date('Y-m-d H:i:s');
        $getAllUserData = array();
        foreach ($allUsers as $allUsersKey => $allUsersValue) {
            $user_id = $allUsersValue['userId'];
            $role_id = $allUsersValue['roleId'];

            if($role_id=='2'){
                $accountType = 'Wellkabinet';
            }else{
                $accountType = 'Wellkasa Rx';
            }

            $subscriptionValue = 'No';
            $subscriptionStartDate = '-';
            $subscriptionEndDate = '-';
            $checkSubscriptionData = DB::table('subscriptions')->where('user_id',$user_id)
            ->orderBy('id','DESC')->get()->first();
            if(!empty($checkSubscriptionData) && $checkSubscriptionData->current_period_end >= $currentDate){
                $subscriptionValue = 'Yes';
                $subscriptionStartDate = $checkSubscriptionData->current_period_start;
                $subscriptionEndDate = $checkSubscriptionData->current_period_end;

            }
 
            $data['sr_no'] = $count;
            $data['firstName'] = $allUsersValue['firstName'] ? $allUsersValue['firstName'] : '-';
            $data['lastName'] = $allUsersValue['lastName'] ? $allUsersValue['lastName'] : '-';
            $data['email'] = $allUsersValue['email'];
            $data['accountType'] = $accountType;
            $data['subscriptionStatus'] = $subscriptionValue;
            $data['subscriptionStartDate'] = $subscriptionStartDate;
            $data['subscriptionEndDate'] = $subscriptionEndDate;
            $data['lastLoggedInDate'] = !empty($allUsersValue['lastLoggedInDate']) ? $allUsersValue['lastLoggedInDate'] : '-';
            $data['verified'] = !empty($allUsersValue['verified']) ? 'Yes' : 'No';
            $data['status'] = $allUsersValue['status'] == '1' ? 'Active' : 'In-Active';
            
            $getAllUserData[] = $data;
            
            $count++;
        }


        $fileName = 'result.csv';
        $path = public_path().'/import/get_all_user_details';
        if (!is_dir($path))
        {
            if (mkdir($path)) {
                $fp = fopen($path . "/".$fileName,"a");
                fclose($fp);
            }else{
                die('Failed to create folders...');
            }
        }
        if(isset($getAllUserData) && !empty($getAllUserData)){
            if(file_exists(public_path('import/get_all_user_details/'.$fileName))){
                // Unlink old file
                unlink(public_path('import/get_all_user_details/'.$fileName));
            }
            $resultFile = public_path('import/get_all_user_details/'.$fileName);
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename='.$fileName);
            $output = fopen($resultFile, 'a');    
            fputcsv($output, array('Sr. No.', 'First Name', 'Last Name', 'Email','Account Type','Subscription Status','Subscription Start Date', 'Subscription End Date','Last Logged In Date','Verified','Status'));
            foreach ($getAllUserData as $row) {
                $row = (array)$row;
                fputcsv($output, $row);
            }
        }
    }
}
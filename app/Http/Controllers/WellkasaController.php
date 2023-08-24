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
use App\Models\Master;
use App\Models\User;
use App\Models\Condition;
use App\Helpers\Helpers as Helper;
use Carbon\Carbon;
use DB;
use App\Models\UserTherapyConditions;
use Validator;

class WellkasaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Wellkasa Controller
    |--------------------------------------------------------------------------
    |
    | This controller search by ratings desc, ratings asc, therapy asc & therapy desc. 
    | & show the therapy details. Here we add therapy, save therapy, view therapy & update therapy also.
    |
    */

     /**
     * Function to show therapy details on base of search filter such as
     * rating by desc, rating by asc, therapy by asc & therapy by desc
     *
     * @param   String                     $searchBy    Passing request search string of sorting
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response on add thereapy page with respective data
     */
    public function myWellkasa($searchBy = '',Request $request)
    {

        $userId = \Auth::user()->id;
        $my_therapy = Usertherapy::with('therapy')->join('therapy','therapy.id','=','user_therapy.therapyID')
                        // ->join('user_therapy_conditions','user_therapy.userId','=','user_therapy_conditions.userId')
                        // ->groupBy('user_therapy_conditions.userId')
                        ->select("user_therapy.*")->where('user_therapy.userId',$userId)
                        ->whereNull('user_therapy.deleted_at')->whereNull('therapy.deleted_at');
        if($searchBy != ''){
            switch ($searchBy) {
                case "ratingsdesc":
                    $my_therapy->orderBy('user_therapy.ratings','desc');
                    break;
                case "ratingsasc":
                    $my_therapy->orderBy('user_therapy.ratings','asc');
                    break;
                case "therapyasc":
                    $my_therapy->orderBy(Therapy::select('therapy')->whereColumn('user_therapy.therapyID','therapy.id'),'asc');
                    break;
                case "therapydesc":
                    $my_therapy->orderBy(Therapy::select('therapy')->whereColumn('user_therapy.therapyID','therapy.id'),'desc');
                    break;
                default:
                    $my_therapy->orderBy('user_therapy.ratings','desc');
            }
        }
        else
        {
            $my_therapy->orderBy('user_therapy.id','asc');
        }

        $my_therapy = $my_therapy->get();
       
        if(!empty($my_therapy)){
            $therapyDetails = $my_therapy;
            $therapyDetails = $therapyDetails->toArray();
            foreach ($therapyDetails as $key => $value) {
                $userConditions = UserTherapyConditions::leftJoin('conditions','conditions.id','=','user_therapy_conditions.conditionId')
                            ->select('user_therapy_conditions.*','conditions.canonicalName As canonicalName','conditions.conditionName As conditionName','conditions.id As conditionId')
                            ->where('user_therapy_conditions.userTherapyId',$value['id'])
                            ->where('user_therapy_conditions.userId',$userId)
                            ->whereNull('user_therapy_conditions.deleted_at')      
                            ->whereNull('conditions.deleted_at')      
                            ->get()->toArray();      
                // Show condition details if conditions and otherText detail is available
                if(!empty($userConditions) && !empty($value['otherText'])){
                    $therapyDetails[$key]['conditions']['conditionId'] = "";
                    $therapyDetails[$key]['conditions']['conditionName'] = $value['otherText'];
                    array_push($userConditions,$therapyDetails[$key]['conditions']);
                    $therapyDetails[$key]['conditions'] = $userConditions;
                }
                // Show condition details if only conditions detail is available
                elseif(!empty($userConditions) && empty($value['otherText'])){
                    $therapyDetails[$key]['conditions'] = $userConditions;
                
                }
                // Show condition details if only otherText detail is available
                elseif(empty($userConditions) && !empty($value['otherText'])){
                    $otherText = array('conditionId'=>'','conditionName'=>$value['otherText']);
                    $therapyDetails[$key]['conditions'][] = $otherText;
                }
                // Show condition empty details when otherText & conditions detail is not available
                else{
                    $therapyDetails[$key]['conditions'] = [];
                }

                if(!empty($value['updated_at'])){
                    $userTherapyHistory = UserTherapyHistory::select('note','created_at')
                    ->where('userId',$userId)->where('therapyID',$value['therapyID'])
                    ->whereNotNull('note')->whereNull('deleted_at')->orderBy('id','DESC')->limit(1)->get()->toArray();
                    $notesArr = array();
                    // if user therapy history table has notes then execute if condition
                    if(!empty($userTherapyHistory)){
                        foreach ($userTherapyHistory as $userTherapyHistoryKey => $userTherapyHistoryValue) {
                            $updatedAt = date('d M Y H:i' , strtotime($userTherapyHistoryValue['created_at']));
                            $data['date'] = $updatedAt;
                            $data['notes'] = $userTherapyHistoryValue['note'];
                            $notesArr[] = $data;
                        }
                    }else{
                        if(!empty($value['note'])){
                            // user history table has no notes then get data from user therapy table
                            $updatedAt = date('d M Y H:i' , strtotime($value['created_at']));
                            $data['date'] = $updatedAt;
                            $data['notes'] = $value['note'];
                            $notesArr[] = $data;
                        }
                    }
                    $therapyDetails[$key]['note'] = $notesArr;
                }
                
            }
        }
        $my_therapy = $therapyDetails;

        return view('page.my-wellkasa', compact('my_therapy'));
    }

    /**
     * Function to add therapy page on base of existing therapy apiID is
     * same as already stored in user therapy table
     *
     * @param   Integer                    $therapyId   Passing request therapy id
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response on add thereapy page with respective data
     */
    public function addTherapy($therapyId,Request $request)
    {
        // Get Logged in user id
        $userId = \Auth::user()->id;
        if( $userId == 0){
            $providerDetails = Master::select('id','name')->where('type','1')->whereNull('deleted_at')->get()->toArray();
            $therapy = Therapy::select('therapy','apiID')->where('id',$therapyId)->whereNull('deleted_at')->get()->first();
            $therapyName = $therapy->therapy;   
            $therapyCount = Usertherapy::where('userId',$userId)->where('therapyID',$therapyId)->whereNull('deleted_at')->count();
    
            //Check if existing therapy apiID is same as already stored in user therapy table
            $therapyNames = Usertherapy::join('therapy','therapy.id','=','user_therapy.therapyID')
                            ->where('user_therapy.userId',$userId)
                            ->whereNull('therapy.deleted_at')
                            ->whereNull('user_therapy.deleted_at')
                            ->pluck('therapy.apiID')->toArray();
            if(in_array($therapy->apiID,$therapyNames,TRUE)){
                $therapyCount++;
            }
            return view('page.add-therapy',compact('therapyId','providerDetails','therapyName','therapyCount'));
        }else{
            return redirect()->route('my-wellkasa');
        }
        
    }

    /**
     * This function to save therapy in user therapy
     *
     * @param   Integer                    $therapyId   Passing request therapy id
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data
     *                                                  which contains therapy Id, ratings, provider
     *                                                  note.
     * 
     * @return  \Illuminate\Http\Response               Redirect to related response on add thereapy page with respective data
     */
    public function saveTherapy(Request $request)
    {
        // Get Logged in user id
        $userId = \Auth::user()->id;

        // $validatedData = $request->validate([
        //     'ratings' => 'required',
        //     'provider' => 'required',
        //     'note' => 'required',
        //   ]);
        
        //Check if existing therapy apiID & therapy id is same as already stored in user therapy table
        $therapyCount = Usertherapy::where('userId',$userId)->where('therapyID',$request->therapyID)->whereNull('deleted_at')->count();
        $therapy = Therapy::select('apiID')->where('id',$request->therapyID)->get()->first();
        $therapyNames = Usertherapy::join('therapy','therapy.id','=','user_therapy.therapyID')
                        ->where('user_therapy.userId',$userId)->whereNull('user_therapy.deleted_at')
                        ->pluck('therapy.apiID')->toArray();
        if(in_array($therapy->apiID,$therapyNames,TRUE)){
            $therapyCount++;
        }

        // Save current therapy details if not added already
        if($therapyCount == 0){
            $therapy = new Usertherapy;
            $therapy->userId = $userId;
            $therapy->therapyID = $request->therapyID;
            $therapy->ratings = $request->ratings;
            $therapy->provider = $request->provider;
            $therapy->note = $request->note;
            $therapy->updated_at = null;
            $therapy->save();

            // $userTherapyHistory = new UserTherapyHistory;
            // $userTherapyHistory->userId = $userId;
            // $userTherapyHistory->therapyID = $request->therapyID;
            // $userTherapyHistory->ratings = $request->ratings;
            // $userTherapyHistory->provider = $request->provider;
            // $userTherapyHistory->note = $request->note;
            // $userTherapyHistory->updated_at = null;
            // $userTherapyHistory->save();

            return $request->session()->flash('message', 'Therapy data has been inserted successfully.');
            // return redirect('my-wellkasa')->with('message', 'Therapy data has been inserted successfully');
        }else{
            return $request->session()->flash('error', 'Therapy data has already been saved.');
            // return back()->with('error', 'Therapy data has already been saved');
        }
        
    }

    /**
     * This function to view saved therapy in user therapy
     *
     * @param   Integer                    $therapyId   Passing request therapy id
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data
     *                                                  which contains therapy Id, ratings, provider
     *                                                  note.
     * 
     * @return  \Illuminate\Http\Response               Redirect to related response on edit thereapy page with respective data
     */
    public function viewTherapy($therapyID,Request $request)
    {
        // Get Logged in user id
        $userId = \Auth::user()->id;
    
        $userTherapy = Usertherapy::select('id','ratings', 'provider', 'note','therapyID','shareWithOthers','updated_at')->where('id',$therapyID)->where('userId',$userId)->whereNull('deleted_at')->get()->first();
        if(!empty($userTherapy)){
            $therapy = Therapy::select('therapy')->where('id',$userTherapy->therapyID)
                        ->whereNull('deleted_at')->get()->first();
            // If therapy details exist then execute this code
            if(!empty($therapy->therapy)){
                $therapyName = $therapy->therapy;
                $providerDetails = Master::select('id','name')->where('type','1')->whereNull('deleted_at')->get()->toArray();
                $conditions = Condition::select('conditions.id','conditions.conditionName AS name')
                                        // ->join('therapy_condition','conditions.id','=','therapy_condition.conditionId')
                                        // ->where('therapy_condition.therapyId',$userTherapy->therapyID)
                                        ->whereNull('conditions.deleted_at')->orderby('conditions.conditionName')->get()->toArray();
                
                // Get user therapy conditions 
                $userTherapyConditions = UserTherapyConditions::select(DB::raw('group_concat(conditionId) as conditionIds'))->where('userTherapyId',$therapyID)->first();
                $selectConditionArray = array();
                if(isset($userTherapyConditions) && !empty($userTherapyConditions)){
                    $selectConditionArray = explode(",",$userTherapyConditions->conditionIds);
                }
                
                // gets condition Tags
                $userTherapyConditionsTags = UserTherapyConditions::select('id','otherText','conditionId')->where('userTherapyId',$therapyID)->get()->toArray();
                foreach($userTherapyConditionsTags as $utcKey => $utcValue){
                    $userTherapyConditionsTags[$utcKey]['conditionName'] = '';
                    if($utcValue['conditionId']!=0){
                        $conditionDetail = Condition::select('conditionName','canonicalName')
                                            ->where('id',$utcValue['conditionId'])
                                            ->get()->first();
                        $userTherapyConditionsTags[$utcKey]['conditionName'] = $conditionDetail['conditionName'];
                        $userTherapyConditionsTags[$utcKey]['canonicalName'] = $conditionDetail['canonicalName'];
                        array_push($utcValue,$userTherapyConditionsTags[$utcKey]);

                    }
                    array_push($utcValue,$userTherapyConditionsTags[$utcKey]['conditionName']);
                }

                // gets the user therapy history notes and dates for personal journal data
                $userTherapyHistory = UserTherapyHistory::select('note',DB::raw("DATE_FORMAT(created_at,'%d %M %Y %H:%i') AS date"))
                ->where('userId',$userId)->where('therapyID',$userTherapy->therapyID)
                ->whereNotNull('note')->whereNull('deleted_at')->orderBy('id','DESC')->get()->toArray();

                return view('page.edit-therapy',compact('userTherapy','providerDetails','therapyName','conditions','selectConditionArray','userTherapyConditionsTags','userTherapyHistory'));
            
            }else{
                return redirect('my-wellkasa')->with('error', 'Therapy details not found to update data.');
            }
            
        }else{
            return redirect('my-wellkasa')->with('error', 'Id not found to update data.');
        }
        
    }

    /**
     * This function to update therapy in user therapy
     *
     * @param   Integer                    $therapyId   Passing request therapy id
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data
     *                                                  which contains therapy Id, ratings, provider
     *                                                  note.
     * 
     * @return  \Illuminate\Http\Response               Redirect to related response on my wellkasa page with respective data
     */
    public function updateTherapy(Request $request)
    {
        DB::beginTransaction();
        
        // validate note if user has input 
        $validator = Validator::make($request->all(),[
            'note'  => 'max:500',
        ]);

        // check if condition tags does not exist then apply validation
        if($request->checkCondition == 0 ){
            // Checked validation
            $validator = Validator::make($request->all(),[
                'condition'  => 'required',
            ]);
        }else{
            // Condition tags does exist then do not apply validation
            // No Checked validation
            $validator = Validator::make($request->all(),[
                'condition'  => '',
            ]);
            
            // Check if condition count is more than 10 in table
            if(isset($request->condition)){
                $countCondition = UserTherapyConditions::where('userTherapyId',$request->userTherapyId)->get()->count();
                if($countCondition >= 10){
                    DB::rollback();
                    return redirect()->back()->with('error',"Already have 10 conditions, please remove some to add new conditions.")->withInput($request->all());
                    
                }
                // Check if existing selected conditions and on table conditions is more than 10 
                $totalCondition = count($request->condition) + $countCondition;
                if($totalCondition > 10){
                    $canSelect = 10 - $countCondition;
                    DB::rollback();
                    return redirect()->back()->with('error',"You can only select ".$canSelect." more conditions, please remove some to add new conditions.")->withInput($request->all());
                }
            }
        }
        // Check if condition count is more than 10 from user input
        if(isset($request->condition) && count($request->condition) > 10){
            DB::rollback();
            return redirect()->back()->with('error',"Conditions should not be more than 10")->withInput($request->all());
        }

        

        /*** Other text validation hide
         * 
         $validator->sometimes('otherText', 'required', function($input) {
            if(!empty($input->condition)){
                if(in_array('other', $input->condition)){
                    return true;
                }else{
                    return false;
                }
            } 
        }); 
         */
        

        if (!$validator->fails()) {
            try {
                // Get Logged in user id
                $userId = \Auth::user()->id;
            
                $ratings = $request->ratings;
                $provider = $request->provider;
                $note = $request->note;
                $condition = $request->condition;              
                
                $shareWithOthers = '0';
                if(isset($request->shareWithOthers) && !empty($request->shareWithOthers)){
                    if($request->shareWithOthers == 'on'){
                        $shareWithOthers = '1';
                    }
                }
                // If the user therapy details is not deleted then execute this code
                $checkUserTherapy = Usertherapy::where('id',$request->userTherapyId)->whereNull('deleted_at')->get()->toArray();
                if(!empty($checkUserTherapy)){

                    // Updates user therapy details in user_therapy table whenever user updates anything except conditions
                    Usertherapy::where('id',$request->userTherapyId)
                                ->update(['ratings' => $ratings, 'provider' => $provider, 
                                        'note' => !empty($note) ? $note : $request->oldNote, 'shareWithOthers' => $shareWithOthers]);
                                            
                    // if my personal journal has value then insert in user therapy history table 
                    if(!empty($request->note)){

                        // Adds user therapy details in user_therapy_history table whenever user updates anything except conditions
                        $userTherapyHistory = new UserTherapyHistory;
                        $userTherapyHistory->userId = $userId;
                        $userTherapyHistory->therapyID = $request->therapyID;
                        $userTherapyHistory->ratings = $ratings;
                        $userTherapyHistory->note = $note;
                        $userTherapyHistory->provider = $provider;
                        $userTherapyHistory->shareWithOthers = $shareWithOthers;
                        $userTherapyHistory->save();
                    }

                    if(isset($condition) && !empty($condition)){
                        foreach($condition as $con){
                            $conditionId = 0;
                            $otherText = NULL;
                            // If condition has id then assign id to conditionId variable and pass otherText as NULL
                            if(is_numeric($con) == 1){
                                $conditionId = $con;
                            }else{
                                // else condition has string then assign string to otherText variable and pass conditionId as 0
                                $otherText = $con;
                                // Check duplicate records in other text for the same user therapy condition id
                                $checkDuplicateConditionName = UserTherapyConditions::select('otherText')
                                                                ->where('userTherapyId',$request->userTherapyId)
                                                                ->whereNotNull('otherText')->where('otherText',$otherText)
                                                                ->get()->toArray();
                                // If found duplicate record then terminate code with error message showing condition already added
                                if(isset($checkDuplicateConditionName) && !empty($checkDuplicateConditionName)){
                                    DB::rollback();
                                    return redirect()->back()->with('error',"$otherText condition is already added for this therapy. Please add other condition.");
                                }
                            }
                            // Adds condition and otherText value for user therapy along with other details in user_therapy_conditions table
                            $userTherapyConditions = new UserTherapyConditions();    
                            $userTherapyConditions->userId = $userId;
                            $userTherapyConditions->userTherapyId = $request->userTherapyId;
                            $userTherapyConditions->conditionId = $conditionId;
                            $userTherapyConditions->otherText = $otherText;
                            $userTherapyConditions->save(); 
                        }
                    }
                    // Save the therapy details
                    DB::commit();
                    return redirect('my-wellkasa')->with('message',"Therapy data has been updated");
                
                }else{
                    // User therapy details is deleted then execute this code
                    DB::rollback();
                    return redirect()->back()->with('message',"User therapy details not found to update");
                }

            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('message',"Something Wen't wrong");
            }
        }else{
            /* Validation error message */
            $message = $validator->messages()->first();
            return redirect()->back()->with('error',$message);
        }
    }


    /**
     * This function to delete therapy in user therapy
     *
     * @param   Integer                    $therapyId   Passing request therapy id
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data
     *                                                  which contains therapy Id, ratings, provider
     *                                                  note.
     * 
     * @return  \Illuminate\Http\Response               Redirect to related json response
     */
    public function deleteCondition(Request $request)
    {    
        try{            
            $userId = \Auth::user()->id;
            $usertherapyConditionId = $request->usertherapyConditionId;    
            $userTherapyCheck = UserTherapyConditions::where('id',$usertherapyConditionId)->whereNotNull('deleted_at')->count(); 
            if($userTherapyCheck == 0){
                UserTherapyConditions::where('id',$usertherapyConditionId)->delete();
                $request->session()->flash('message', 'Condition deleted successfully.');
                return json_encode(array('status'=>'0'));
            }else{
                $request->session()->flash('error', 'This condition id is already deleted.');
                return json_encode(array('status'=>'1'));
            }
           
        }catch (Exception $e) {
            /* Something went wrong while displaying acl details */
            $error_message = $e->getMessage();
            return json_encode([
                'message'=> $error_message,
                'status' => 1
            ]);
        } 
    }


    /**
     * This function to delete user therapy details by id.
     *
     * @param   Integer                    $therapyId   Passing request therapy id
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data
     *                                                  which contains user therapy Id
     * 
     * @return  \Illuminate\Http\Response               Redirect to related json response
     */
    public function deleteTherapy(Request $request)
    {    
        try{
            
            $now = Carbon::now();
            $currentTime = $now->toDateTimeString();

            // Get Logged in user id
            $userId = \Auth::user()->id;
            $usertherapyId = $request->usertherapyId;    
            $userTherapyCheck = UserTherapy::where('id',$usertherapyId)->whereNotNull('deleted_at')->count(); 
            if($userTherapyCheck == 0){
                $userTherapy = tap(UserTherapy::where('id',$usertherapyId))->update(['deleted_at' => $currentTime])->first();
                if(!empty($userTherapy->therapyID)){
                    $userTherapyHistory = UserTherapyHistory::where('therapyID',$userTherapy->therapyID)->where('userId',$userId)->update(['deleted_at' => $currentTime]);
                    $request->session()->flash('message', 'Therapy details deleted successfully.');
                    return json_encode(array('status'=>'0'));
                }else{
                    $request->session()->flash('error', 'Someting went wrong, please try again.');
                    return json_encode(array('status'=>'1'));
                }
            }else{
                $request->session()->flash('error', 'This therapy details is already deleted.');
                return json_encode(array('status'=>'1'));
            }
            
           
        }catch (Exception $e) {
            /* Something went wrong while displaying acl details */
            $error_message = $e->getMessage();
            return json_encode([
                'message'=> $error_message,
                'status' => 1
            ]);
        } 
    }

    /**
     * Function to import Migraine headache details in csv format
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response as session message.
     */
    public function getConditionsDetailsCSV(){

        // Check if condition id is not there in request URL, then display error message
        if(!isset($_GET['condition_id'])){
            echo 'Condition id not found';
            exit;
        }else{
            // Check if condition id is not numeric in request URL, then display error message
            if(!is_numeric($_GET['condition_id'])){
                echo 'Condition id not found';
                exit;
            }
            $conditionId = $_GET['condition_id'];
        }

        // Get the migraine headache id
        $conditionData = DB::table('conditions')
        ->where('id','=',$conditionId)
        ->select('id','conditionName')->first();
        if(!empty($conditionData)){
            // Get the therapy details and publication id
           $data = DB::table('therapy_condition')
           ->select('conditionId','therapy_condition.therapyId','effectiveness')
           ->where('therapy_condition.conditionId','=',$conditionData->id )
           ->get()->toArray();
           if(!empty($data)){
                // Convert the data std array object to array format
                $data = json_decode(json_encode($data),true);

                // Add the medical publication ids based on the condition name and it's therapy
                foreach ($data as $key => $value) {
                    $medicalPublicationIds = '';
                    $data_reference = DB::table('therapy_reference')
                    ->select(DB::raw("TRIM(medicalPublicationId) AS medicalPublicationId"))
                    ->where('therapy_reference.therapyId','=',$value['therapyId'])
                    ->where('therapy_reference.conditionName','=',$conditionData->conditionName)
                    ->whereNotNull('medicalPublicationId')
                    ->where('medicalPublicationId','!=','')
                    ->pluck('medicalPublicationId')->toArray();
                    if(!empty($data_reference)){
                        $medicalPublicationIds = implode(',',$data_reference);
                    }
                    $data[$key]['medicalPublicationId'] = $medicalPublicationIds;
                }

                //sort list of data of effectiveness name in alphabetical order - code start 
                usort($data, function($finalArrayOne, $finalArrayTwo) {
                    return $finalArrayOne['effectiveness'] <=> $finalArrayTwo['effectiveness'];
                });
                //sort list of data of effectiveness name in alphabetical order - code end


                $df = fopen("php://output", 'w');
                fputcsv($df, array_keys(reset($data)));
                foreach ($data as $row) {
                    fputcsv($df, $row);
                }
                fclose($df);
                $fileName = $conditionData->conditionName.'_'.time().'.csv';
                header('Content-Disposition: attachment; filename="'.$fileName.'";');
            }
        }else{
            echo "Condition not found.";
            exit;
        }
    }

}

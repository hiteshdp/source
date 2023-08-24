<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usertherapy;
use App\Models\UserTherapyHistory;
use App\Models\Therapy;
use App\Models\Master;
use App\Models\User;
use App\Models\Condition;
use App\Models\TherapyCondition;
use App\Models\TherapyDetails;
use App\Models\UserInteractionsReport;
use App\Models\UserInteractions;
use App\Models\TherapyReference;
use App\Helpers\Helpers as Helper;
use Carbon\Carbon;
use DB;
use App\Models\UserTherapyConditions;
use Validator;
use App\Models\UserIntegrativeProtocol;
use App\Models\UserIntegrativeProtocolConditions;

class IntegrativeProtocolController extends Controller
{
     /*
    |--------------------------------------------------------------------------
    | Integrative Protocol Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles integrative protocols for the application. 
    | In this class we have edit integrative protocol on base condition. 
    | Here we get therapy condition and return therapy details in JSON data 
    | in case of success. There is another method where we save user 
    | integrative protocol on request send as therapy ID. 
    | We show here list of integrative protocol data & also update 
    | Integrative Protocol on base of requested condition id & notes.
    |
    */

    /**
     * Function to edit therapy details on base of integrative protocol
     * condition id.
     *
     * @param   Integer                     $integrativeProtocolConditionsId    Passing request on base of integrative protocol condition id
     * @param   \Illuminate\Http\Request    $request                            A request object pass through form data as Therapy Id.
     * @return  \Illuminate\Http\Response               Redirect to related response on edit interactive page with respective data
     */
    public function editIntegrative($integrativeProtocolConditionsId,Request $request)
    {
        
        // Get Logged in user id
        $userId = \Auth::user()->id;
        $userIntegrativeProtocolConditions = UserIntegrativeProtocolConditions::where('id',$integrativeProtocolConditionsId)->first();
        $finalArray = array();
        if(isset($userIntegrativeProtocolConditions) && !empty($userIntegrativeProtocolConditions)){
            $UserIntegrativeProtocol = UserIntegrativeProtocol::select('therapyID')
                                      ->where('id',$userIntegrativeProtocolConditions->userIntProtocolId)
                                      ->first();
            
            $finalArray['id'] = $userIntegrativeProtocolConditions->id; 
            $finalArray['userId'] = $userIntegrativeProtocolConditions->userId;
            $finalArray['userIntProtocolId'] = $userIntegrativeProtocolConditions->userIntProtocolId;  
            $finalArray['conditionId'] = $userIntegrativeProtocolConditions->conditionId;  
            $finalArray['notes'] = $userIntegrativeProtocolConditions->notes;  
            $therapyId = $UserIntegrativeProtocol->therapyID;  


            // Get therapy name
            $therapy = Therapy::select('therapy')->where('id',$UserIntegrativeProtocol->therapyID)->first();
            $finalArray['therapyName'] = $therapy->therapy;  

            // check if already added conditions then avoid those options in select condition dropdown
            $userIntegrativeProtocolConditionsArr = UserIntegrativeProtocolConditions::where('userIntProtocolId',$userIntegrativeProtocolConditions->userIntProtocolId)
            ->where('conditionId','!=',$userIntegrativeProtocolConditions->conditionId)->get()->toArray();
            $conditionsIds = [];
            if(!empty($userIntegrativeProtocolConditionsArr)){
                foreach ($userIntegrativeProtocolConditionsArr as $userIntegrativeProtocolConditionsArrKey => $userIntegrativeProtocolConditionsArrValue) {
                    $conditionsIds[] = $userIntegrativeProtocolConditionsArr[$userIntegrativeProtocolConditionsArrKey]['conditionId'];
                }
            }

             // Get Condition name
            $conditions = Condition::select('conditions.id','conditions.conditionName AS name')
            ->join('therapy_condition','conditions.id','=','therapy_condition.conditionId')
            ->where('therapyId',$UserIntegrativeProtocol->therapyID)
            ->whereNull('therapy_condition.deleted_at');
            if(!empty($conditionsIds)){
                $conditions = $conditions->whereNotIn('conditions.id',$conditionsIds);
            }
            $conditions = $conditions->whereNull('conditions.deleted_at')->orderby('conditions.conditionName')
            ->get()->toArray();

            $finalArray['conditions'] = $conditions;


            //Get therapy condition
            $therapyCondition = TherapyCondition::select('effectiveness')
                                ->where('conditionId',$userIntegrativeProtocolConditions->conditionId)
                                ->where('therapyID',$therapyId)
                                ->first();
            $finalArray['effectiveness'] = $therapyCondition->effectiveness;   
            $finalArray['updateId'] = $integrativeProtocolConditionsId;


            // Get conditions details
            $condition = Condition::select('conditionName')->where('id',$userIntegrativeProtocolConditions->conditionId)->first();
            $conditionNameClean = $condition->conditionName;
            $conditionName = Helper::removedSpace($condition->conditionName);

           
             // Get Therapy Details
             $therapyCanonicalName = Therapy::where('id',$therapyId)->pluck('canonicalName')->first();
             $therapyDetails = TherapyDetails::where('therapyId',$UserIntegrativeProtocol->therapyID)->first();
               
             $therapyDetailsDescription = $therapyDetails->effectiveDetail;
             $effectiveDetail = json_decode($therapyDetailsDescription,true);
             $description = $effectiveDetail['effectiveness'];
             

             
             $detailArray = '';
             $redirectToConditionSection = '';
             $therapyRoute = '';
             foreach($description as $dec){
                 $condition = Helper::removedSpace($dec['condition']);
                
                 $descriptionData = '';
                if($condition == $conditionName){
                    $descriptionData = $dec['description'];

                    //Reference Start
                    $therapyReferenceDetails = TherapyReference::where('therapyId',$therapyId)->whereIn('referenceNumber',$dec['reference-numbers'])->whereNull('deleted_at')->groupBy('referenceNumber')->get()->toArray();
                    if(!empty($therapyReferenceDetails)){

                        foreach ($therapyReferenceDetails as $therapyReferenceDetailsKey => $therapyReferenceDetailsValue) {
                        
                            $referenceNumber = $therapyReferenceDetailsValue['referenceNumber'];
                            $referenceDescription = $therapyReferenceDetailsValue['referenceDescriptione'];
    
                            $clickHereToResearch = '';
                            if(!empty($therapyReferenceDetailsValue['medicalPublicationId'])){
                                $medicalPublicationId = $therapyReferenceDetailsValue['medicalPublicationId'];
                                $researchApi = 'https://pubmed.ncbi.nlm.nih.gov/'.$medicalPublicationId;
                                $clickHereToResearch = '<br/><a href='.$researchApi.' target=\'_blank\'>Click to see research</a>';
                            }
                            else
                            {
                                $clickHereToResearch = '<br/><a href="javascript:void(0)" class="text-secondary">Click to see research</a>';
                            }
                            $referenceDescriptionDataContent = $referenceDescription.' '.$clickHereToResearch;
                            
                            //Replace double quote with single quote as it was creating issue on anchor tag
                            $referenceDescriptionDataContent = str_replace('"', "'", $referenceDescriptionDataContent);
                            $anchorTag = '<a tabindex="0" class="dd" data-placement="top" role="button" data-toggle="popover" data-html="true" data-trigger="focus" data-content="'.$referenceDescriptionDataContent.'" target="_blank" >'.$referenceNumber.'</a>';

                                
                            $descriptionData = str_replace("(".$referenceNumber.",","(".$anchorTag.",",$descriptionData);
                            $descriptionData = str_replace("(".$referenceNumber.")","(".$anchorTag.")",$descriptionData);
                            $descriptionData = str_replace(",".$referenceNumber.",",",".$anchorTag.",",$descriptionData);
                            $descriptionData = str_replace(",".$referenceNumber.")",",".$anchorTag.")",$descriptionData);
                            
                        }
                    }
                    //Reference End


                    // view monograph section
                    $therapyRoute = $therapyCanonicalName ? route('therapy',$therapyCanonicalName) : '';
                    $viewMonographRoute = '<a href="javascript:void(0);" class="dd" id="redirectToConditionSection">View therapy monograph</a>';
                    // $detailArray = $descriptionData.''.$viewMonographRoute;
                    $detailArray = $descriptionData;

                    // redirect to condition section id
                    $replaceConditionsVal = Helper::clean(strip_tags($conditionNameClean));
                    $replaceConditionsVal = str_replace('-','',$replaceConditionsVal);
                    $filteredConditionName = strtolower($replaceConditionsVal);

                    $redirectToConditionSection = $filteredConditionName;
                }
             }
             

            return view('page.edit-integrative',compact('finalArray','therapyId','detailArray','redirectToConditionSection','therapyRoute'));
        }else{
            return redirect('my-wellkasa')->with('error', 'Id not found to update data.');
        }
    }

    /**
     * Function to get therapy details on base of integrative protocol
     * condition id & therapy id.
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data as Therapy Id & Condition Id.
     * @return  \Illuminate\Http\Response               Redirect to related response as therapy details in JSON format.
     */
    public  function getTherapyCondition(Request $request){
        try{ 

            

            $conditionId = $request->conditionId;
            $therapyId = $request->therapyId;
            $therapyCondition = TherapyCondition::where('conditionId',$conditionId)->where('therapyId',$therapyId)->first();
            if(!empty($therapyCondition)){
                $effectiveness = $therapyCondition->effectiveness;
                
                // Check if current user has subscription, then only display the details of the condition
                $subscriptionStatus =  \Auth::user()->getSubscriptionStatus();
                if(empty($subscriptionStatus)){
                    return json_encode([
                        'effectiveness'=>  str_replace(" ","_",$effectiveness),
                        'therapyDetails' => '',
                        'redirectToConditionSection' => '',
                        'therapyRoute' => '',
                    ]); 
                }

                // Get conditions details
                $condition = Condition::select('conditionName')->where('id',$conditionId)->first();
                $conditionNameClean = $condition->conditionName;
                $conditionName = Helper::removedSpace($condition->conditionName);

                // Get Therapy Details
                $therapyCanonicalName = Therapy::where('id',$request->therapyId)->pluck('canonicalName')->first();
                $therapyDetails = TherapyDetails::where('therapyId',$request->therapyId)->first();
                $therapyDetailsDescription = $therapyDetails->effectiveDetail;
                $effectiveDetail = json_decode($therapyDetailsDescription,true);
                $description = $effectiveDetail['effectiveness'];
                $detailArray = '';
                $therapyRoute = route('therapy',$therapyCanonicalName);
                foreach($description as $dec){

                    $condition = Helper::removedSpace($dec['condition']);
                    $descriptionData = '';
                    if($condition == $conditionName){
                        $descriptionData = $dec['description'];
                        
                        //Reference Start
                        $therapyReferenceDetails = TherapyReference::where('therapyId',$therapyId)->whereIn('referenceNumber',$dec['reference-numbers'])->whereNull('deleted_at')->groupBy('referenceNumber')->get()->toArray();
                        if(!empty($therapyReferenceDetails)){

                            foreach ($therapyReferenceDetails as $therapyReferenceDetailsKey => $therapyReferenceDetailsValue) {
                            
                                $referenceNumber = $therapyReferenceDetailsValue['referenceNumber'];
                                $referenceDescription = $therapyReferenceDetailsValue['referenceDescriptione'];
        
                                $clickHereToResearch = '';
                                if(!empty($therapyReferenceDetailsValue['medicalPublicationId'])){
                                    $medicalPublicationId = $therapyReferenceDetailsValue['medicalPublicationId'];
                                    $researchApi = 'https://pubmed.ncbi.nlm.nih.gov/'.$medicalPublicationId;
                                    $clickHereToResearch = '<br/><a href='.$researchApi.' target=\'_blank\'>Click to see research</a>';
                                }
                                else
                                {
                                    $clickHereToResearch = '<br/><a href="javascript:void(0)" class="text-secondary">Click to see research</a>';
                                }
                                $referenceDescriptionDataContent = $referenceDescription.' '.$clickHereToResearch;
                                
                                //Replace double quote with single quote as it was creating issue on anchor tag
                                $referenceDescriptionDataContent = str_replace('"', "'", $referenceDescriptionDataContent);
                                $anchorTag = '<a tabindex="0" class="dd mr-0" data-placement="top" role="button" data-toggle="popover" data-html="true" data-trigger="focus" data-content="'.$referenceDescriptionDataContent.'" target="_blank" >'.$referenceNumber.'</a>';

                                    
                                $descriptionData = str_replace("(".$referenceNumber.",","(".$anchorTag.",",$descriptionData);
                                $descriptionData = str_replace("(".$referenceNumber.")","(".$anchorTag.")",$descriptionData);
                                $descriptionData = str_replace(",".$referenceNumber.",",",".$anchorTag.",",$descriptionData);
                                $descriptionData = str_replace(",".$referenceNumber.")",",".$anchorTag.")",$descriptionData);
                                
                            }
                        }
                        //Reference End

                        // view monograph section
                        $viewMonographRoute = '<a href="javascript:void(0);" class="dd" id="redirectToConditionSection">View therapy monograph</a>';
                        // $detailArray = $descriptionData.''.$viewMonographRoute;
                        $detailArray = $descriptionData;
                    }
                }

                // redirect to condition section id
                $replaceConditionsVal = Helper::clean(strip_tags($conditionNameClean));
                $replaceConditionsVal = str_replace('-','',$replaceConditionsVal);
                $filteredConditionName = strtolower($replaceConditionsVal);
                
                return json_encode([
                    'effectiveness'=>  str_replace(" ","_",$effectiveness),
                    'therapyDetails' => $detailArray,
                    'redirectToConditionSection' => $filteredConditionName,
                    'therapyRoute' => $therapyRoute
                ]);  
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
     * Function to Save User Integrative Protocol by current logged in user.
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data as Therapy Id.
     * @return  \Illuminate\Http\Response               void; only show message success or fail
     */
    public function saveUserIntegrativeProtocol(Request $request)
    {
        //validate request parameters
        $validator = Validator::make($request->all(), [
            'therapyID' => 'required|numeric'
        ]);
        //validation failed
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
        
        // Get Logged in user id
        $userId = \Auth::user()->id;

        //Check if existing therapy apiID & therapy id is same as already stored in user therapy table
        $therapyCount = UserIntegrativeProtocol::where('userId',$userId)->where('therapyID',$request->therapyID)->whereNull('deleted_at')->count();
        $therapy = Therapy::select('apiID')->where('id',$request->therapyID)->get()->first();
        $therapyNames = UserIntegrativeProtocol::join('therapy','therapy.id','=','user_integrative_protocol.therapyID')
                        ->where('user_integrative_protocol.userId',$userId)->whereNull('user_integrative_protocol.deleted_at')
                        ->pluck('therapy.apiID')->toArray();
        if(in_array($therapy->apiID,$therapyNames,TRUE)){
            $therapyCount++;
        }

        // Save current therapy details if not added already
        if($therapyCount == 0){
            $therapy = new UserIntegrativeProtocol;
            $therapy->userId = $userId;
            $therapy->therapyID = $request->therapyID;
            $therapy->updated_at = null;
            $therapy->save();
            if($therapy){
                return $request->session()->flash('message', 'Therapy data has been saved successfully.');
            }else{
                return $request->session()->flash('error', 'Failed to save therapy data');
            }
            
        }else{
            return $request->session()->flash('error', 'Therapy data has already been saved.');
        }
        
    }

    /**
     * Function to show list Integrative on base of current logged in user.
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response on integrative list page.
     */
    public function listIntegrative(Request $request){
        
        $userId = \Auth::user()->id;
        
        $userIntegrativeProtocolData = UserIntegrativeProtocol::select('id','therapyID')
        ->where('userId', $userId)->orderBy('id','DESC')->get()->toArray();

        if(!empty($userIntegrativeProtocolData)){
        
            foreach ($userIntegrativeProtocolData as $userIntegrativeProtocolDataKey => $userIntegrativeProtocolDataValue) {
                
                $userIntegrativeProtocolConditionsData = UserIntegrativeProtocolConditions::select('id')
                ->where('userId',$userId)->where('userIntProtocolId',$userIntegrativeProtocolDataValue['id'])
                ->orderBy('id','DESC')->get()->toArray();
                
                $data['id'] = $userIntegrativeProtocolDataValue['id'];
                
                $userIntegrativeProtocolResult[$userIntegrativeProtocolDataKey] = $data;
            }

        }else{
            $userIntegrativeProtocolResult = [];
        }
        return view('page.integrative-list', compact('userIntegrativeProtocolResult'));
    }

    /**
     * Function to show therapy integrative
     *
     * @param   Integer                     $therapyId  Passing request on base of therapy id
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response therapy interactive page with related data.
     */
    public function TherapyIntegrative($therapyId,Request $request){
       //Get therapy data from therapy id
       $therapy = Therapy::where('canonicalName',$therapyId)->whereNull('deleted_at')->first();

       if(!empty($therapy)){
           try{
               $therapyId = $therapy->id;
               $therapy_detail = TherapyDetails::where('therapyId',$therapyId)->whereNull('deleted_at')->get()->toArray();
               // Check if therapy data exist in therapy_details table
               if(!empty($therapy_detail)){
                   $effectiveDetail = json_decode($therapy_detail[0]['effectiveDetail'],true);
                   
                   $therapyDates = $therapy_detail[0];
                   $therapyReviewedAt = !empty($therapyDates['therapyReviewedAt']) ? date("m/d/Y", strtotime($therapyDates['therapyReviewedAt'])) : "N/A";
                   $therapyUpdatedAt = !empty($therapyDates['therapyUpdatedAt']) ? date("m/d/Y", strtotime($therapyDates['therapyUpdatedAt'])) : "N/A";
                   $therapy_detail = json_decode($therapy_detail[0]['therapyDetail'], true);
                   
                   if(\Auth::check()){
                       $userId = \Auth::user()->id;
                       if(!empty($userId)){
                           $therapyCount = UserIntegrativeProtocol::where('userId',$userId)->where('therapyID',$therapyId)->whereNull('deleted_at')->count();
                           
                           //Check if existing therapy apiID is same as already stored in user therapy table
                           $therapyApiIds = UserIntegrativeProtocol::join('therapy','therapy.id','=','user_integrative_protocol.therapyID')->whereNull('user_integrative_protocol.deleted_at')->where('user_integrative_protocol.userId',$userId)->pluck('therapy.apiID')->toArray();
                           if(in_array($therapy->apiID,$therapyApiIds,TRUE)){
                               $therapyCount++;
                           }
                       }else{
                           $therapyCount = 0;
                       }    
                   }else{
                       $therapyCount = 0;
                   }
                   

                   //Reference Start
                   $therapyReferenceDetails = TherapyReference::where('therapyId',$therapyId)->whereNull('deleted_at')->groupBy('referenceNumber')->get()->toArray();
                   if(!empty($therapyReferenceDetails)){
                       foreach ($therapyReferenceDetails as $therapyReferenceDetailsKey => $therapyReferenceDetailsValue) {
                       
                           $referenceNumber = $therapyReferenceDetailsValue['referenceNumber'];
                           $referenceDescription = $therapyReferenceDetailsValue['referenceDescriptione'];
    
                           $clickHereToResearch = '';
                           if(!empty($therapyReferenceDetailsValue['medicalPublicationId'])){
                               $medicalPublicationId = $therapyReferenceDetailsValue['medicalPublicationId'];
                               $researchApi = 'https://pubmed.ncbi.nlm.nih.gov/'.$medicalPublicationId;
                               $clickHereToResearch = '<br/><a href='.$researchApi.' target=\'_blank\'>Click to see research</a>';
                           }
                           else
                           {
                               $clickHereToResearch = '<br/><a href="javascript:void(0)" class="text-secondary">Click to see research</a>';
                           }
                           $referenceDescriptionDataContent = $referenceDescription.' '.$clickHereToResearch;
                           
                           //Replace double quote with single quote as it was creating issue on anchor tag
                           $referenceDescriptionDataContent = str_replace('"', "'", $referenceDescriptionDataContent);
                           $anchorTag = '<a tabindex="0" class="dd" data-placement="top" role="button" data-toggle="popover" data-html="true" data-trigger="focus" data-content="'.$referenceDescriptionDataContent.'" target="_blank" >'.$referenceNumber.'</a>';

                           foreach($effectiveDetail['effectiveness'] as $eff_k=>$eff_v)
                           {
                               $effectiveDetail['effectiveness'][$eff_k]['description'] = str_replace("(".$referenceNumber.",","(".$anchorTag.",",$eff_v['description']);
                           }
                           foreach($effectiveDetail['effectiveness'] as $eff_k=>$eff_v)
                           {
                               $effectiveDetail['effectiveness'][$eff_k]['description'] = str_replace("(".$referenceNumber.")","(".$anchorTag.")",$eff_v['description']);
                           }
                           foreach($effectiveDetail['effectiveness'] as $eff_k=>$eff_v)
                           {
                               $effectiveDetail['effectiveness'][$eff_k]['description'] = str_replace(",".$referenceNumber.",",",".$anchorTag.",",$eff_v['description']);
                           }
                           foreach($effectiveDetail['effectiveness'] as $eff_k=>$eff_v)
                           {
                               $effectiveDetail['effectiveness'][$eff_k]['description'] = str_replace(",".$referenceNumber.")",",".$anchorTag.")",$eff_v['description']);
                           }
                       }
                   }
                   //Reference End

                   // Stores Effective Array data
                   $therapyFinalArr = array();
                   // Stores conditions options data
                   $therapyConditionsFinalArray = array();

                   // Checks if effectiveness key exists then store the data
                   if(!empty($effectiveDetail['effectiveness']) && !empty($effectiveDetail['effectiveness'][0]['condition'])){
                       
                       $effectiveDetailArr = $effectiveDetail['effectiveness'];
                       $sessionConditionDetails = array();
                       foreach ($effectiveDetailArr as $effectiveDetailArrKey => $effectiveDetailArrVal) {
                           // Stores condition id & name
                           $temp = array();
                           
                           // Removed all special character from condition's name
                           $replaceConditionsVal = Helper::clean(strip_tags($effectiveDetailArrVal['condition']));
                           $replaceConditionsVal = str_replace('-','',$replaceConditionsVal);
                           $temp['conditionsId'] = strtolower($replaceConditionsVal);
                           $temp['conditionsText'] = strip_tags($effectiveDetailArrVal['condition']);

                           // Sets session based condition details in dropdown
                           $sessionCondition = $request->session()->get('conditionValue');
                           if(!empty($sessionCondition)){
                               $sessionConditionName = preg_replace('/[^A-Za-z0-9\-]/', ' ', $sessionCondition['conditionName']); //Replaces special characters with space from session condition name
                               $conditionNameFromArray = preg_replace('/[^A-Za-z0-9\-]/', ' ',$effectiveDetailArrVal['condition']); //Replaces special characters with space from array condition name
                               // Add the conditions related to the word if it is exact or has space at first or after or before or after & before the word in session conditions dropdown
                               $pattern = "/(?<![\w\d])".$sessionConditionName."(?![\w\d])/i";
                               if(preg_match($pattern,$conditionNameFromArray) == 1){
                                   $sessionConditionValues['conditionsId'] = strtolower($temp['conditionsId']);
                                   $sessionConditionValues['conditionsText'] = $temp['conditionsText'];
                                   $sessionConditionDetails[] = $sessionConditionValues;
                               }
                           }
       

                           //Assigns id name in condition text
                           $id = 'id='.$temp['conditionsId'];
                           
                           // Push array details of condition id & name
                           array_push($therapyConditionsFinalArray,$temp);
                           $description = $effectiveDetailArrVal['description'];
                           
                           // Replace extra newline in description with one new line
                           $description = str_replace('\r\n \r\n','\r\n',json_encode($description));
                           $description = json_decode($description);
                           $description = str_replace(array('\r\n \r\n','\r\n\r\n'),'\r\n',json_encode($description));
                           $description = json_decode($description);

                           switch ($effectiveDetailArrVal['rating-description']) {
                               case "Effective":
                                   $effective[] = "<b $id>".$effectiveDetailArrVal['condition']."</b>. ".nl2br($description);
                                   break;
                               case "Ineffective":
                                   $ineffective[] = "<b $id>".$effectiveDetailArrVal['condition']."</b>. ".nl2br($description);
                                   break;
                               case "Likely Effective":
                                   $likelyEffective[] ="<b $id>".$effectiveDetailArrVal['condition']."</b>. ".nl2br($description);    
                                   break;
                               case "Likely Ineffective":
                                   $likelyIneffective[] ="<b $id>".$effectiveDetailArrVal['condition']."</b>. ".nl2br($description);  
                                   break;
                               case "Possibly Ineffective":
                                   $possiblyIneffective[] ="<b $id>".$effectiveDetailArrVal['condition']."</b>. ".nl2br($description);
                                   break;
                               case "Possibly Effective";  
                                   $possiblyEffective[] ="<b $id>".$effectiveDetailArrVal['condition']."</b>. ".nl2br($description);
                                   break;
                               case "Insufficient Reliable Evidence To Rate":
                                   $insufficient[] ="<b $id>".$effectiveDetailArrVal['condition']."</b>. ".nl2br($description);  
                                   break;        
                           }
                           $data['Effective']['data'] = !empty($effective) ? $effective : '';
                           $data['Effective']['color'] = 'green-new';    

                           $data['Likely effective']['data'] = !empty($likelyEffective) ? $likelyEffective : '';
                           $data['Likely effective']['color'] = 'light-green-new';
                           
                           $data['Possibly effective']['data'] = !empty($possiblyEffective) ? $possiblyEffective : '';
                           $data['Possibly effective']['color'] = 'light-orange-new';    

                           $data['Possibly ineffective']['data'] = !empty($possiblyIneffective) ? $possiblyIneffective : '';
                           $data['Possibly ineffective']['color'] = 'light-pink-new';    

                           $data['Likely ineffective']['data'] = !empty($likelyIneffective) ? $likelyIneffective : '';
                           $data['Likely ineffective']['color'] = 'light-red-new';  

                           $data['Ineffective']['data'] = !empty($ineffective) ? $ineffective : '';
                           $data['Ineffective']['color'] = 'red-new';
                                                               
                           $data['Inconclusive evidence']['data'] = !empty($insufficient) ? $insufficient : '';
                           $data['Inconclusive evidence']['color'] = 'gray-new';  
               
                           $therapyFinalArr = $data;
                       }
                       // Sorts options data alphabetical order
                       sort($therapyConditionsFinalArray);
                       
                       // remove session value after appending the related values for condition names
                       session()->forget('conditionValue');

                   }

                   // Checks if session based details are empty then send empty records
                   if(empty($sessionConditionDetails)){
                       $sessionConditionDetails = '';
                   }
                   // Stores Interactions Array data
                   $therapyInteractiveArr = array();
                   $therapyInteractiveArr['Are there any interactions']['Are there any interactions with medications?']['data'] = $therapy_detail['drug-interactions'];
                   $therapyInteractiveArr['Are there any interactions']['Are there any interactions with Herbs and Supplements?']['data'] = $therapy_detail['herb-interactions'];
                   $therapyInteractiveArr['Are there any interactions']['Are there interactions with Foods?']['data'] = $therapy_detail['food-interactions'];
                   
                   return view('page.therapy-integrative', compact('therapy_detail','therapyId','therapyCount','therapyReviewedAt','therapyUpdatedAt','therapyFinalArr','therapyInteractiveArr','therapyConditionsFinalArray','sessionConditionDetails'));     
               
               }else{
                   return redirect()->back()->with('error',"Therapy Id not found");
               }
              
           
           }catch (\Exception $e){
               return redirect()->back()->with('message',"Something went wrong");
           }
           
       }else{
           return redirect()->back()->with('error',"Therapy Id not found");
       }
    }

    /**
     * This function complies to update Integrative.
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data as  Id.
     * @return  \Illuminate\Http\Response               Redirect to related response to my wellkasa rx page with session message.
     */
    public function updateIntegrative(Request $request){
        try{
            // Check if data is updated, then redirect to my-wellkasa-rx screen with success message else show error message
            if(UserIntegrativeProtocolConditions::where('id',$request->updateId)
            ->update([
                'conditionId'=>$request->condition, 
                'notes'=>$request->note,
                'created_at'=>date('Y-m-d h:i:s'),
                'updated_at'=>date('Y-m-d h:i:s')
            ])){
                return redirect()->route('my-wellkasa-rx')->with('message',"Conditions updated successfully");     
            }else{
                return redirect()->route('my-wellkasa-rx')->with('message',"Something went wrong");     
            }  

        }catch (\Exception $e){
            return redirect()->route('my-wellkasa-rx')->with('message',"Something went wrong");
        }
    }  
    
    /**
     * This function complies add integrative protocol condition on base of
     * user protocol id.
     *
     * @param   Integer                     $userIntProtocolId  A request data as User Protocol Id.
     * @param   \Illuminate\Http\Request    $request            A request object pass through form data.
     * @return  \Illuminate\Http\Response                       Redirect to related response page add interactive condition.
     */
    public function addIntegrativeProtocolCondition($userIntProtocolId,Request $request){
        // Get Logged in user id
        $userId = \Auth::user()->id;

        // Get therapy interative protocol condition details
        $userIntegrativeProtocol = UserIntegrativeProtocol::where('id',$userIntProtocolId)->first();

        // Get therapy details
        $therapyDetails = Therapy::where('id',$userIntegrativeProtocol->therapyID)->first();                       
        $therapyId = $userIntegrativeProtocol->therapyID;
        $therapyName = $therapyDetails->therapy;

        // check if already added conditions then avoid those options in select condition dropdown
        $userIntegrativeProtocolConditions = UserIntegrativeProtocolConditions::where('userIntProtocolId',$userIntProtocolId)->get()->toArray();
        $conditionsIds = [];
        if(!empty($userIntegrativeProtocolConditions)){
            foreach ($userIntegrativeProtocolConditions as $userIntegrativeProtocolConditionsKey => $userIntegrativeProtocolConditionsValue) {
                $conditionsIds[] = $userIntegrativeProtocolConditions[$userIntegrativeProtocolConditionsKey]['conditionId'];
            }
        }

        // Get Condition name
        $conditions = Condition::select('conditions.id','conditions.conditionName AS name')
        ->join('therapy_condition','conditions.id','=','therapy_condition.conditionId')
        ->where('therapyId',$userIntegrativeProtocol->therapyID)->whereNull('therapy_condition.deleted_at');
        if(!empty($conditionsIds)){
            $conditions = $conditions->whereNotIn('conditions.id',$conditionsIds);
        }
        $conditions = $conditions->whereNull('conditions.deleted_at')->orderby('conditions.conditionName')
        ->get()->toArray();

        return view('page.add-integrative-condition',compact('conditions','therapyId','therapyName','userIntProtocolId'));
    }

    /**
     * Function to store Integrative on base of current logged in user.
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data as User Protocol Id, condition & note.
     * @return  \Illuminate\Http\Response               Redirect to related response my wellkasa rx page with session message.
     */
    public function storeIntegrativeProtocol(Request $request){
        // Get Logged in user id
        $userId = \Auth::user()->id;
        try{
            if(UserIntegrativeProtocolConditions::insert([
                'userId'=>$userId, 
                'userIntProtocolId'=>$request->userIntProtocolId,
                'conditionId'=>$request->condition,
                'notes'=>$request->note,
                'created_at'=>date('Y-m-d h:i:s'),
                'updated_at'=>date('Y-m-d h:i:s')
            ])){
                // if click save And Exit
                if($request->submit == 'saveAndExit'){
                    return redirect()->route('my-wellkasa-rx')->with('message',"Conditions added successfully");
                }else{
                    return redirect()->route('add-integrative-protocol-condition',$request->userIntProtocolId)->with('message',"Conditions added successfully");
                }
            }else{
                return redirect()->route('my-wellkasa-rx')->with('message',"Something went wrong");     
            }  
        }catch (\Exception $e){
            return redirect()->route('my-wellkasa-rx')->with('message',"Something went wrong");
        }
    }

    /**
     * Function to delete Integrative condition on based of requested user integrative protocol id.
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data as user integrative protocol id.
     * @return  \Illuminate\Http\Response               Redirect to related response as session message.
     */
    public function deleteIntegrativeCondition(Request $request){
        try{
            // Delete the data of given id
            $deleteData = DB::table('user_integrative_protocol_conditions')->where('id',$request->userIntegrativeId)->delete();
            // Check if data is deleted then show success message else show error message
            if($deleteData){
                $request->session()->flash('message', 'Conditions deleted successfully.');
                return json_encode(array('status'=>'0'));
            }else{
                $request->session()->flash('error', 'Something went wrong.');
                return json_encode(array('status'=>'1'));
            }
        }catch (\Exception $e){
            return redirect()->route('my-wellkasa-rx')->with('message',"Something went wrong");
        }

    }


    /**
     * Function to delete Integrative therapy on base of requested user integrative protocol id.
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data as user integrative protocol id.
     * @return  \Illuminate\Http\Response               Redirect to related response as session message.
     */
    public function deleteIntegrativeTherapy(Request $request){
        try{

            // Get the therapy name from the request.
            $therapyName = Therapy::where('id',$request->therapyId)->pluck('therapy')->first(); 

            // Check if the integrative therapy is already deleted
            $checkData = DB::table('user_integrative_protocol')->where('id',$request->userIntegrativeId)
            ->whereNull('deleted_at')->get()->first();
            if(empty($checkData)){
                $request->session()->flash('error', $therapyName.' integrative therapy is already deleted.');
                return json_encode(array('status'=>'1'));
            }
            
            // Delete the conditions added to current therapy details
            $deleteDataConditions = DB::table('user_integrative_protocol_conditions')
            ->where('userIntProtocolId',$request->userIntegrativeId)->delete();

            // Delete current therapy details
            $deleteIntegrativeTherapy = DB::table('user_integrative_protocol')
            ->where('id',$request->userIntegrativeId)->delete();
            if($deleteIntegrativeTherapy){
                $request->session()->flash('message', $therapyName.' integrative therapy deleted successfully.');
                return json_encode(array('status'=>'0'));
            }else{
                $request->session()->flash('error', 'Something went wrong.');
                return json_encode(array('status'=>'1'));
            }
        }catch (\Exception $e){
            return redirect()->route('my-wellkasa-rx')->with('message',"Something went wrong");
        }

    }

}

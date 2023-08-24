<?php
/*
|-----------------------------------------------------------------------------
| LARAVEL APP BASE CLASS, MODEL CLASS, PACKAGES, HELPERS DEFINE
|-----------------------------------------------------------------------------
*/
namespace App\Http\Controllers;

use App\Models\Drugs;
use App\Models\UserInteractionsReport;
use App\Models\UserInteractions;
use App\Models\UserIntegrativeProtocol;
use App\Models\UserIntegrativeProtocolConditions;
use Illuminate\Http\Request;
use App\Models\Condition;
use App\Models\TherapyCondition;
use App\Models\TherapyDetails;
use App\Helpers\Helpers as Helper;
use App\Models\TherapyReference;
use App\Models\NaturalMedicineReference;
use App\Models\Therapy;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use Cookie;
use Session;
use Auth;
use Validator;
use Crypt;
use PDF;
use Storage;
use Notification;
use App\Notifications\SendPDFMailNotification;

class PDFgenerationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PDF Generation Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles / create pdf view on base of request user 
    | interaction id. There are generated pdf for integrative after 
    | selecting any condition and on base of user interaction report details.
    |
    */
    
    
    /**
     *  This function complies  interaction checker report pdf view &
     *  show all drugs name and natural medicine name added from autocomplete search.
     *
     * @return \Illuminate\Http\Response
     */
    public function pdfview(Request $request)
    {   
        //Decrypt request user interaction id
        $userInteractionId = Crypt::decrypt($request->userInteractionId);

        // Get User Interactions Report details
        $userInteractionsReport = UserInteractionsReport::where('id',$userInteractionId)->first();
        
        // Checked User Interactions Report details exist or not
        if(!empty($userInteractionsReport)){ 
            $finalArray = array();
            $finalArray['drugs'] = array();
            $finalArray['naturalMedicine'] = array();
            $finalArray['therapyName'] = array();
            $finalArray['drugName'] = array();
            $finalArray['reportName'] = $userInteractionsReport->reportName;
            $finalArray['createdBy'] = Auth::user()->name ? (!empty(Auth::user()->name) && !empty(Auth::user()->last_name) ? Auth::user()->name." ".Auth::user()->last_name : Auth::user()->name) : strtok(Auth::user()->email, '@');
            $finalArray['createdOn'] = date('d M Y, h:i A', strtotime(Carbon::now()));
            // Get user Interactions data
            $userInteractions = UserInteractions::where('userInteractionReportId',$userInteractionId)->get()->toArray();
            
            $drugsArray = array();
            $therapyName = '';
            $drugName = '';
            foreach($userInteractions as $interaction){
                $temp = array();

                $drugId = $interaction['drugId'];
                $naturalMedicineId = $interaction['naturalMedicineId'];

                // Get drugs details   
                if(!empty($drugId)){ 
                    $drugIdArray = json_decode($drugId);
                    $drugs = Drugs::whereIn('id',$drugIdArray)->get()->toArray();
                    foreach($drugs as $drug){
                        $temp1 = array();
                        $temp1['id'] = $drug['id'];
                        $temp1['apiDrugId'] = $drug['apiDrugId'];
                        $temp1['name'] = $drug['name'];
                        $temp1['brand_name'] = $drug['brand_name'];
                        $temp1['classification'] = $drug['classification'];
                        $temp1['nutrient_depletions'] = $drug['nutrient_depletions'];
                        $temp1['drugDetail'] = $drug['drugDetail'];
                        $drugName = $drugName.$drug['name'].',';
                        array_push($finalArray['drugs'],$temp1);
                    }
                }
                
                // Get therapy details
                if(!empty($naturalMedicineId)){
                    $naturalMedicineIdArray = json_decode($naturalMedicineId);
                    $naturalMedicine = Therapy::whereIn('id',$naturalMedicineIdArray)->get()->toArray();
                    foreach($naturalMedicine as $medicine){
                        $tempNatural = array();
                        $tempNatural['id'] = $medicine['id'];
                        $tempNatural['therapy'] = $medicine['therapy'];
                        $tempNatural['canonicalName'] = $medicine['canonicalName'];
                        $tempNatural['therapyType'] = $medicine['therapyType'];
                        $tempNatural['apiID'] = $medicine['apiID'];
                        $tempNatural['isProcessed'] = $medicine['isProcessed'];
                        $tempNatural['isReferenceProcessed'] = $medicine['isReferenceProcessed'];
                        $therapyName = $therapyName.$medicine['therapy'].',';
                        array_push($finalArray['naturalMedicine'],$tempNatural);
                    }
                }
            }

            /**
             * To check if Therapy or Drug name is not empty trim & string replace of commas and create final array.
             */
            $finalArray['therapyName'] = !empty($therapyName) ? rtrim(str_replace(',',', ',$therapyName), ', ') : 'No Natural therapies selected for interactions';
            $finalArray['drugName'] = !empty($drugName) ? rtrim(str_replace(',',', ',$drugName), ', ') : 'No Drugs data selected for interactions';
            

            
            $userInteractionsDrugIds = UserInteractions::where('userInteractionReportId',$userInteractionId)->get()->pluck('drugId')->first();
            $userInteractionsNaturalMedicineIds = UserInteractions::where('userInteractionReportId',$userInteractionId)->get()->pluck('naturalMedicineId')->first();
            $getNewDrugsDataId = $userInteractionsDrugIds ? json_decode($userInteractionsDrugIds, true) : '';
            $getNewNaturalMedicinesDataId = $userInteractionsNaturalMedicineIds ? json_decode($userInteractionsNaturalMedicineIds, true) : '';
            $html = array();

            /**
             * If New Drug record & New Natural data found the create interaction checker html view
             * and load this html in pdf view.
             */
            if(!empty($getNewDrugsDataId) && !empty($getNewNaturalMedicinesDataId)){
                $html = '';
                $htmlNew = '';
                $circle_img = '';
                $finalArr = array();
                $circle_class = "gray-dot";
                foreach($getNewDrugsDataId as $drugIdVal){

                    foreach($getNewNaturalMedicinesDataId as $getNewNaturalMedicinesDataIdVal){
                        
                        $getDrugData = DB::table('drugs')->where('id',$drugIdVal)->get()->first();

                        $getInteractionsData = DB::table('drugs_interactions')
                        ->where('drugId',$drugIdVal)
                        ->join('therapy','therapy.id','=','drugs_interactions.naturalMedicineId')
                        ->where('naturalMedicineId',$getNewNaturalMedicinesDataIdVal)
                        ->whereNull('drugs_interactions.deleted_at')->get()->toArray();
                        /**
                         * This condition check interaction details data available.
                         */
                        if(!empty($getInteractionsData)){
                            $getInteractionsData = json_decode(json_encode($getInteractionsData),true);
                            foreach($getInteractionsData as $key => $fdata){
                                
                                $interData = $fdata['interactionDetails'];
                                $interData = json_decode($interData,true);

                                if(strtolower($interData['rating-label']) == 'major'){
                                    $class = "class='text-danger'";
                                    $circle_class = "red-dot";
                                    $circle_img = asset('images/red.svg');
                                }else if(strtolower($interData['rating-label']) == 'moderate'){
                                    $class = "class='text-danger'";
                                    $circle_class = "yellow-dot";
                                    $circle_img = asset('images/yellow.svg');
                                }else if(strtolower($interData['rating-label']) == 'minor'){
                                    $circle_class = "green-dot";
                                    $circle_img = asset('images/green.svg');
                                }else{
                                    $class = "";
                                    $circle_class = "gray-dot";
                                    $circle_img = asset('images/gray.svg');
                                }
        
                                $description = $fdata['description'];
                                
                                
                                $no = $this->generateRandomNumber();
        
                                $temp = array();
                                $temp['therapy'] = $fdata['therapy'];
                                $temp['no'] = $no;
                                $temp['drugName'] = $fdata['drugName']." - ".$getDrugData->brand_name;
                                $temp['severity'] = $fdata['severity'];
                                $temp['interactionRating'] = ucfirst($interData['rating-label']).". ".$fdata['interactionRating'];
                                $temp['circle_class'] = $circle_class;
                                $temp['circle_color'] = $circle_img;
                                $temp['therapyType'] = $fdata['therapyType'];
                                $temp['isInteractionsFound'] = 1;
                                array_push($finalArr,$temp);
                            }
                        }else{
                        /**
                        * This condition check interaction details data not exist.
                        */
                            $no = $this->generateRandomNumber();

                            $getDrugData = DB::table('drugs')->where('id',$drugIdVal)->get()->first();
                            $getTherapyData = DB::table('therapy')->select('therapy','therapyType')
                            ->where('id',$getNewNaturalMedicinesDataIdVal)->get()->toArray();
                            foreach($getTherapyData as $getTherapyDataVal){
                                $temp = array();
                                $temp['therapy'] = $getTherapyDataVal->therapy;
                                $temp['no'] = $no;
                                $temp['drugName'] = $getDrugData->name." - ".$getDrugData->brand_name;
                                $temp['severity'] = '';
                                $temp['interactionRating'] = 'No Interactions found';
                                $temp['circle_class'] = $circle_class;
                                $temp['circle_color'] = asset('images/gray.svg');
                                $temp['therapyType'] = $getTherapyDataVal->therapyType;
                                $temp['isInteractionsFound'] = 0;
                                array_push($finalArr,$temp);
                            }
                        }
                            
                    }
                }

                //sort drugs name alphabetical order - code start 
                usort($finalArr, function($finalArrayOne, $finalArrayTwo) {
                    return $finalArrayOne['drugName'] <=> $finalArrayTwo['drugName'];
                });
                //sort drugs name alphabetical order - code end


                // Arrange array
                foreach($finalArr as $entry => $vals)
                {
                    $lastArray[$vals['therapy']][$vals['drugName']][]= $vals;
                }

                
                asort($lastArray); // Sort therapy name in ascending order
                
                // Get array keys
                $arrayKeys = array_keys($lastArray);
                // Fetch last array key
                $lastArrayKey = array_pop($arrayKeys);

                foreach($lastArray as $key => $val){
                    $className = 'natural-medicine';
                    $imageSrc = asset('images/pill1.svg');
                    // Checked class name
                    foreach($val as $classData){
                        foreach($classData as $classVal){
                            if($classVal['therapyType'] == "Food, Herbs & Supplements"){
                                $className = 'natural-medicine';
                                $imageSrc = asset('images/pill1.svg');
                            } else if($classVal['therapyType'] == "Health & Wellness"){
                                $className = 'yoga';
                                $imageSrc = asset('images/yoga.svg');
                            }
                        }
                    }

                    $htmlNew .= "<table align='top' cellpadding='0' cellspacing='0' style='padding: 0 20px;' width='100%'>";
                        $htmlNew .= "<tr>";
                            $htmlNew .= "<td style='padding-bottom:10px; padding-top:20px; font-weight: bold; color: #44546A; font-size: 16px;'>";
                                $htmlNew .= "<img style='vertical-align: text-top; padding-right:5px;' src='".$imageSrc."' alt='Wellkasa' title='pill1'>".$key;
                            $htmlNew .= "</td>";
                        $htmlNew .= "</tr>";
                        foreach($val as $k => $headerData){
                                                       
                            // Get circle class as per client
                            $circle_class_array = array();
                            foreach($headerData as $hkey=> $hval){
                                array_push($circle_class_array,$hval['circle_class']);
                            }
                            $circle_class_array = array_unique($circle_class_array);
    
                            $circle_class = 'red-dot';
                            $circle_array_count = sizeof($circle_class_array);
                            if($circle_array_count == 1){
                                $circle_class = $circle_class_array[0];
                            }
                            
                            if($circle_array_count == 2){
                                if (in_array("red-dot", $circle_class_array) && in_array("yellow-dot", $circle_class_array)){  // if red, yellow -> then red
                                    $circle_class = 'red-dot';
                                }
    
                                if (in_array("red-dot", $circle_class_array) && in_array("green-dot", $circle_class_array)){  // if red, green -> then show red
                                    $circle_class = 'red-dot';
                                }
    
                                if (in_array("yellow-dot", $circle_class_array) && in_array("green-dot", $circle_class_array)){  // if yellow, green -> then show yellow
                                    $circle_class = 'yellow-dot';
                                }
                            }
                            
                            $no = $this->generateRandomNumber();


                            $htmlNew .= "<tr>";
                                $htmlNew .= "<td style='background: #FAFAFA; padding: 12px; border-radius: 4px;  border-bottom: 10px solid #fff;'>";
                                    $htmlNew .= "<table align='top' cellpadding='0' cellspacing='0' width='100%'>";
                                        $htmlNew .= "<tr>";

                                        foreach($headerData as $headerDataKey => $v){

                                            if($headerDataKey == '0'){

                                                $img = '';
                                                if($circle_class=='yellow-dot'){
                                                    $img = asset('images/yellow.svg');
                                                }else if($circle_class=='green-dot'){
                                                    $img = asset('images/green.svg');
                                                }else if($circle_class=='red-dot'){
                                                    $img = asset('images/red.svg');
                                                }

                                                if($v['isInteractionsFound'] == 0){
                                                    $htmlNew .=  "<td width='20'>";
                                                        $htmlNew .=  "<img src='".asset('images/gray.svg')."' alt='grey' title='grey'>";
                                                    $htmlNew .=  "</td>";                                   
                                                }else{
                                                    $htmlNew .=  "<td width='20'>";
                                                        $htmlNew .=  "<img src='".$img."' alt='grey' title='grey'>";
                                                    $htmlNew .=  "</td>"; 
                                                }

                                                $htmlNew .=  "<td style='color: #44546A; font-size: 12px; padding-right: 10px;'>";
                                                    $htmlNew .= $v['drugName'];
                                                $htmlNew .= "</td>";
                                                $htmlNew .= "<td  style='color: #555555; text-align: right; font-size: 12px;'>";
                                                    $htmlNew .= $v['interactionRating'];
                                                $htmlNew .= "</td>";

                                            }else{
                                                break;
                                            }
                                        }

                                        $htmlNew .= "</tr>";        
                                    $htmlNew .= "</table>";
                                $htmlNew .= "</td>";
                            $htmlNew .= "</tr>";

                              
                        }

                        // add page break after each natural medicine's drug list ends - code start
                        if($key != $lastArrayKey){
                            $htmlNew .= "<div class='page-break'></div>";
                        }
                        // add page break after each natural medicine's drug list ends - code end
                   
                    $htmlNew .= "</table>";
                }
                $finalArray['interactions'] = $htmlNew;                  
            }


            

            view()->share('finalArray',$finalArray);            
            $pdf = PDF::loadView('page.reports.interaction-report.index');
            return $pdf->download(str_replace(" ","_",$userInteractionsReport->reportName).'.pdf');
        }
       
    }

    //Function to generated random number
    public function generateRandomNumber($length = 3) {
        return substr(str_shuffle(str_repeat($x='0123456789', ceil($length/strlen($x)) )),1,$length);
    }

    /**
     * This function generate integrative protocol report pdf view.
     * @param   \Illuminate\Http\Request    $request    A request json object pass through ajax call.
     * @return  \Illuminate\Http\Response               Redirect to related response generate pdf & download it.
    */
    public function pdfIntegrativeView(Request $request)
    {   
        if(empty(json_decode($request->responseData))){
        
            $request->session()->flash('error', 'Please select any conditions to create report. Please try again.');
            return json_encode([
                "status" => "0",
                "message" => "Please select any conditions to create report. Please try again."
            ],400);
        
        }else{

            $finalArray = array();

            $allResponseData = json_decode($request->responseData,true);
            // Get the selected 
            foreach($allResponseData as $allResponseDataKey => $allResponseDataVal){
                $userSelectedConditions = UserIntegrativeProtocol::where('user_integrative_protocol.id',$allResponseDataVal['selectedUserIpID'])
                ->join('user_integrative_protocol_conditions','user_integrative_protocol_conditions.userIntProtocolId','=','user_integrative_protocol.id')
                ->where('user_integrative_protocol_conditions.id',$allResponseDataVal['selectedUserIpConditionID'])
                ->get()->first();
                if(!empty($userSelectedConditions)){
                    $selectedTherapyName = Therapy::where('id',$userSelectedConditions['therapyID'])->first();

                    $imageSrc = asset('images/pill1.svg');
                    if($selectedTherapyName->therapyType == "Food, Herbs & Supplements"){
                        $imageSrc = asset('images/pill1.svg');
                    } else if($selectedTherapyName->therapyType == "Health & Wellness"){
                        $imageSrc = asset('images/yoga.svg');
                    }

                    // Get condition details of the existing therapy data code start
                    $finalArrayData = array();
                    $conditionId = $userSelectedConditions['conditionId'];
                    $therapyId = $userSelectedConditions['therapyID'];
                    $therapyCondition = TherapyCondition::where('conditionId',$conditionId)->where('therapyId',$therapyId)->first();
                    if(!empty($therapyCondition)){
                        $effectiveness = $therapyCondition->effectiveness;
                        
                        // Get conditions details
                        $condition = Condition::select('conditionName')->where('id',$conditionId)->first();
                        $conditionNameClean = $condition->conditionName;
                        $conditionName = Helper::removedSpace($condition->conditionName);

                        // Get Therapy Details
                        $therapyCanonicalName = Therapy::where('id',$userSelectedConditions['therapyID'])->pluck('canonicalName')->first();
                        $therapyDetails = TherapyDetails::where('therapyId',$userSelectedConditions['therapyID'])->first();
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
                
                                        $researchApi = '#';
                                        if(!empty($therapyReferenceDetailsValue['medicalPublicationId'])){
                                            $medicalPublicationId = $therapyReferenceDetailsValue['medicalPublicationId'];
                                            $researchApi = 'https://pubmed.ncbi.nlm.nih.gov/'.$medicalPublicationId;
                                            $anchorTag = '<a style="color:#35C0ED; text-decoration: none;" class="" href="'.$researchApi.'">'.$referenceNumber.'</a>';
                                        }else{
                                            $anchorTag = '<a style="" class="" >'.$referenceNumber.'</a>';
                                        }

                                            
                                        $descriptionData = str_replace("(".$referenceNumber.",","(".$anchorTag.",",$descriptionData);
                                        $descriptionData = str_replace("(".$referenceNumber.")","(".$anchorTag.")",$descriptionData);
                                        $descriptionData = str_replace(",".$referenceNumber.",",",".$anchorTag.",",$descriptionData);
                                        $descriptionData = str_replace(",".$referenceNumber.")",",".$anchorTag.")",$descriptionData);
                                        
                                    }
                                }
                                //Reference End

                                // view monograph section
                                $viewMonographRoute = '<a href="javascript:void(0);" class="dd" id="redirectToConditionSection">View therapy monograph</a>';
                                $detailArray = $descriptionData;
                            }
                        }

                        // redirect to condition section id
                        $replaceConditionsVal = Helper::clean(strip_tags($conditionNameClean));
                        $replaceConditionsVal = str_replace('-','',$replaceConditionsVal);
                        $filteredConditionName = strtolower($replaceConditionsVal);
                        
                        // display description of the condition
                        $finalDataVal = $detailArray;


                        
                        $finalArrayData['therapyRoute'] = $therapyRoute;
                        $finalArrayData['therapyCanonicalName'] = $therapyCanonicalName;
                        $finalArrayData['therapyIcon'] = $imageSrc;
                        $finalArrayData['conditionName'] = $conditionNameClean;
                        $finalArrayData['conditionNameDetails'] = $finalDataVal;
                        $finalArray['therapyData'][$selectedTherapyName->therapy][] = $finalArrayData;
                    }
                    // Get condition details of the existing therapy data code end
                }
            }

            if((array_key_exists("therapyData",$finalArray)) && ($finalArray['therapyData'] !='')){
                $finalArray['createdBy'] = Auth::user()->name ? (!empty(Auth::user()->name) && !empty(Auth::user()->last_name) ? Auth::user()->name." ".Auth::user()->last_name : Auth::user()->name) : strtok(Auth::user()->email, '@');
                $finalArray['createdOn'] = date('d M Y, h:i A',strtotime(Carbon::now()));
               
                view()->share('finalArray',$finalArray);
               
                $pdf = PDF::loadView('page.reports.integrative-report.index');
    
                $path = public_path('pdf/');
                $fileName =  'IntegrativeProtocol_'.time().'.'. 'pdf' ;
                $pdf->save($path . '/' . $fileName);
        
                $pdf = public_path('pdf/'.$fileName);
                return response()->download($pdf);
            }else{
                $request->session()->flash('error','Something went wrong. Please try again.');
                return json_encode([
                    'status' => 0
                ]);
            }
            
        }
       
    }

    /**
     * This function join integrative pdf view.
     * 
     * @param   \Illuminate\Http\Request    $request    A request json object pass through ajax call.
     * @return  \Illuminate\Http\Response               Redirect to related response generate pdf & download it.
    */
    public function pdfJoinIntegrativeView(Request $request)
    {   
        $userInteractionId = Crypt::decrypt($request->userInteractionId);

        // Get User Interactions Report details
        $userInteractionsReport = UserInteractionsReport::where('id',$userInteractionId)->first();
        
        if(!empty($userInteractionsReport)){ // Checked User Interactions Report details exist or not
            $finalArray = array();
            $finalArray['drugs'] = array();
            $finalArray['naturalMedicine'] = array();
            $finalArray['therapyName'] = array();
            $finalArray['drugName'] = array();
            $finalArray['reportName'] = $userInteractionsReport->reportName;
            // Get user Interactions data
            $userInteractions = UserInteractions::where('userInteractionReportId',$userInteractionId)->get()->toArray();

            $drugsArray = array();
            $therapyName = '';
            $drugName = '';
            foreach($userInteractions as $interaction){
                $temp = array();

                $drugId = $interaction['drugId'];
                $naturalMedicineId = $interaction['naturalMedicineId'];

                // Get drugs details    
                $drugIdArray = json_decode($drugId);
                $drugs = Drugs::whereIn('id',$drugIdArray)->get()->toArray();
                foreach($drugs as $drug){
                    $temp1 = array();
                    $temp1['id'] = $drug['id'];
                    $temp1['apiDrugId'] = $drug['apiDrugId'];
                    $temp1['name'] = $drug['name'];
                    $temp1['brand_name'] = $drug['brand_name'];
                    $temp1['classification'] = $drug['classification'];
                    $temp1['nutrient_depletions'] = $drug['nutrient_depletions'];
                    $temp1['drugDetail'] = $drug['drugDetail'];
                    $drugName = $drugName.$drug['name'].',';
                    array_push($finalArray['drugs'],$temp1);
                }
                
                // Get therapy details
                $naturalMedicineIdArray = json_decode($naturalMedicineId);
                $naturalMedicine = Therapy::whereIn('id',$naturalMedicineIdArray)->get()->toArray();
                foreach($naturalMedicine as $medicine){
                    $tempNatural = array();
                    $tempNatural['id'] = $medicine['id'];
                    $tempNatural['therapy'] = $medicine['therapy'];
                    $tempNatural['canonicalName'] = $medicine['canonicalName'];
                    $tempNatural['therapyType'] = $medicine['therapyType'];
                    $tempNatural['apiID'] = $medicine['apiID'];
                    $tempNatural['isProcessed'] = $medicine['isProcessed'];
                    $tempNatural['isReferenceProcessed'] = $medicine['isReferenceProcessed'];
                    $therapyName = $therapyName.$medicine['therapy'].',';
                   array_push($finalArray['naturalMedicine'],$tempNatural);
                }
            }
            $finalArray['therapyName'] = rtrim(str_replace(',',', ',$therapyName), ', ');
            $finalArray['drugName'] = rtrim(str_replace(',',', ',$drugName), ', ');
            view()->share('finalArray',$finalArray);
            $pdf = PDF::loadView('page.join-integrative-pdf-generation');
            return $pdf->download(str_replace(" ","_",$userInteractionsReport->reportName).'.pdf');
        }
       
    }

     /**
     * This function complies save pdf for interaction checker page.
     *
     * @param   integer                     $id                 A request interaction id pass for user wise interaction report
     * @param   integer                     $isSendinEmail      A flag which by default it is null & when it pass 1 then it will send pdf in the email.
     * @param   \Illuminate\Http\Request    $request            A request object pass which contains to email & from email.
     *
     * @return  \Illuminate\Http\Response   Redirect to related response generate pdf & send mail as attachment. It also can download.
    */
    public function pdfSaveView($id,$isSendinEmail=null,Request $request){
        $userInteractionId = $id;
        $userId = \Auth::user()->id;
        $userInteractionsReportData = UserInteractionsReport::select('id','reportName')
                                    ->where('userId',$userId)->where('id',$userInteractionId)->whereNull('deleted_at')->get()->toArray();
                                    $lastArray = array();
                                    $finalDurgMedicinesData = array();
                                    $severityCheckboxArray = array();
                                    $major = 'red-uncheck.svg';
                                    $minor= 'green-uncheck.svg';
                                    $moderate= 'yellow-uncheck.svg';
                                    $severityCheckboxArray['major'] = $major;
                                    $severityCheckboxArray['minor'] = $minor;
                                    $severityCheckboxArray['moderate'] = $moderate;
        if(!empty($userInteractionsReportData)){

            // get the interaction details stored by current logged in user

            $userInteractionsReportData = $userInteractionsReportData[0];
            
            $reportName = $userInteractionsReportData['reportName'];
            $userInteractionsData = UserInteractions::select('drugId','naturalMedicineId')
            ->where('userInteractionReportId',$userInteractionsReportData['id'])->whereNull('deleted_at')->first();

            $getNewDrugsDataId = json_decode($userInteractionsData['drugId'],true);
            $getNewNaturalMedicinesDataId = json_decode($userInteractionsData['naturalMedicineId'],true);
              
            
                            
            
            
            $finalDurgMedicinesData = array();
            if(isset($getNewDrugsDataId) && !empty($getNewDrugsDataId)){
                // Drug section start
                $drugsDataArr = Drugs::select("id","name","brand_name")
                                ->whereIn("id",$getNewDrugsDataId)
                                ->whereNull('deleted_at')
                                ->get()->toArray();  

                foreach($drugsDataArr as $drugVal){
                    $tempDrugArray = array();
                    $tempDrugArray['id'] = $drugVal['id'];
                    $tempDrugArray['name'] = $drugVal['name'].' - '.$drugVal['brand_name'];
                    $tempDrugArray['image'] = 'drug.svg';
                    array_push($finalDurgMedicinesData,$tempDrugArray);
                }                
            }
            
            if(isset($getNewNaturalMedicinesDataId) && !empty($getNewNaturalMedicinesDataId)){
                $naturalMedicinesData = Therapy::select("id","therapy as name","therapyType")
                                    ->whereIn("id",$getNewNaturalMedicinesDataId)
                                    ->whereNull('deleted_at')
                                    ->get()->toArray();  
                    
                foreach($naturalMedicinesData as $medicinesVal){
                    $imageName = 'pill.svg';
                    if($medicinesVal['therapyType'] == "Food, Herbs & Supplements"){
                        $imageName = 'pill.svg';
                    } else if($medicinesVal['therapyType'] == "Health & Wellness"){
                        $imageName = 'yoga.svg';
                    }

                    $tempMedicinesArray = array();
                    $tempMedicinesArray['id'] = $medicinesVal['id'];
                    $tempMedicinesArray['name'] = $medicinesVal['name'];
                    $tempMedicinesArray['image'] = $imageName;
                    array_push($finalDurgMedicinesData,$tempMedicinesArray);
                }

            }
            
            $severityCheckboxArray = array();
            $major = 'red-uncheck.svg';
            $minor= 'green-uncheck.svg';
            $moderate= 'yellow-uncheck.svg';
            if(!empty($getNewDrugsDataId) && !empty($getNewNaturalMedicinesDataId)){
                $class = "";
                $finalArray = array();
                $circle_class = "gray-dot";
                foreach($getNewDrugsDataId as $drugId){

                    foreach($getNewNaturalMedicinesDataId as $getNewNaturalMedicinesDataIdVal){
                        
                        $getDrugData = DB::table('drugs')->where('id',$drugId)->get()->first();
    
                        $getInteractionsData = DB::table('drugs_interactions')
                        ->where('drugId',$drugId)
                        ->join('therapy','therapy.id','=','drugs_interactions.naturalMedicineId')
                        ->where('naturalMedicineId',$getNewNaturalMedicinesDataIdVal)
                        ->whereNull('drugs_interactions.deleted_at')->get()->toArray();
                        if(!empty($getInteractionsData)){
                            $getInteractionsData = json_decode(json_encode($getInteractionsData),true);
                            foreach($getInteractionsData as $key => $fdata){
                                $interData = $fdata['interactionDetails'];
                                $interData = json_decode($interData,true);
                                $referenceNumbers = $interData['reference-numbers'];
                                $description = '';
                                if(strtolower($interData['rating-label']) == 'major'){
                                    $class = "class='text-danger'";
                                    $circle_class = "red-dot.png";
                                }else if(strtolower($interData['rating-label']) == 'moderate'){
                                    $class = "class='text-danger'";
                                    $circle_class = "yellow-dot.png";
                                }else if(strtolower($interData['rating-label']) == 'minor'){
                                    $circle_class = "green-dot.png";
                                }else{
                                    $class = "";
                                    $circle_class = "gray-dot.png";
                                }
                                
                                $description = $fdata['description'];
                                

         
                                $data['description'] = $description;
                                $interactionsData[] = $data;
                                
                                $no = $this->generateRandomNumber();
                                
                                if(strtolower($interData['rating-label']) == 'major'){
                                    $major = 'red-check.svg';
                                }    
                                
                                if(strtolower($interData['rating-label']) == 'minor'){
                                    $minor = 'green-check.svg';
                                } 


                                if(strtolower($interData['rating-label']) == 'moderate'){
                                    $moderate = 'yellow-check.svg';
                                }

                                $temp = array();
                                $temp['therapy'] = $fdata['therapy'];
                                $temp['severity'] = $fdata['severity'];
                                $temp['no'] = $no;
                                $temp['drugName'] = $fdata['drugName']." - ".$getDrugData->brand_name;
                                $temp['title'] = $interData['title'];
                                $temp['occurrence'] = $fdata['occurrence'];
                                $temp['levelOfEvidence'] = $fdata['levelOfEvidence'];
                                $temp['description'] = $description;
                                $temp['interactionRating'] = $interData['rating-label'];
                                $temp['circle_class'] = $circle_class;
                                $temp['class'] = $class;
                                $temp['therapyType'] = $fdata['therapyType'];
                                $temp['isInteractionsFound'] = 1;
                                array_push($finalArray,$temp);
                            }
                        }else{
                            // when no interaction details are found then execute this code
                            $no = $this->generateRandomNumber();
    
                            $getDrugData = DB::table('drugs')->where('id',$drugId)->get()->first();
                            $getTherapyData = DB::table('therapy')->select('therapy','therapyType')
                            ->where('id',$getNewNaturalMedicinesDataIdVal)->get()->toArray();
                            foreach($getTherapyData as $getTherapyDataVal){
                                $temp = array();
                                $temp['therapy'] = $getTherapyDataVal->therapy;
                                $temp['severity'] = '';
                                $temp['no'] = $no;
                                $temp['drugName'] = $getDrugData->name." - ".$getDrugData->brand_name;
                                $temp['title'] =  '';
                                $temp['occurrence'] = '';
                                $temp['levelOfEvidence'] = '';
                                $temp['interactionRating'] = '';
                                $temp['circle_class'] = "gray-dot.png";
                                $temp['class'] = '';
                                $temp['therapyType'] = $getTherapyDataVal->therapyType;
                                $temp['description'] = '';
                                $temp['isInteractionsFound'] = 0;
                                array_push($finalArray,$temp);
                            }
                        }       
                    }
                }

                $severityCheckboxArray['major'] = $major;
                $severityCheckboxArray['minor'] = $minor;
                $severityCheckboxArray['moderate'] = $moderate;
                
                //sort drugs name alphabetical order - code start 
                usort($finalArray, function($finalArrayOne, $finalArrayTwo) {
                    return $finalArrayOne['drugName'] <=> $finalArrayTwo['drugName'];
                });
                //sort drugs name alphabetical order - code end


                // Arrange array
                foreach($finalArray as $entry => $vals)
                {
                    $lastArray[$vals['therapy']][$vals['drugName']][]=$vals;
                }

                asort($lastArray); // Sort therapy name in ascending order
            }
            
        }


        view()->share(compact('lastArray','finalDurgMedicinesData','severityCheckboxArray'));
        $pdf = PDF::loadView('page.save-pdf');
        if($isSendinEmail == 1){
            $loggedInUserName = Auth::user()->getUserName();
            $interactionReportFileName = $userInteractionsReportData['reportName'] ? $userInteractionsReportData['reportName'] : 'Interaction Checker Report';
            $toMail = $request->toMail;
            $fromMail = $request->fromMail;

            $fileName = "InteractionCheckerReport_".time()."."."pdf";
            file_put_contents(public_path() . '/pdf/'.$fileName,$pdf->output());

            $pdfFileUrl = url('/pdf').'/'.$fileName;
           

           $sent = Notification::route('mail' , $toMail)->notify(new SendPDFMailNotification($loggedInUserName,$fromMail,$pdfFileUrl,$interactionReportFileName));
            if(empty($sent)){
                if(file_exists(public_path() . '/pdf/'.$fileName)){
                    unlink(public_path() . '/pdf/'.$fileName);
                }
                
                return redirect()->back()->with('message','Email sent successfully.');
            }else{
                if(file_exists(public_path() . '/pdf/'.$fileName)){
                    unlink(public_path() . '/pdf/'.$fileName);
                }

                return redirect()->back()->with('message','Something went wrong, Please try again.');
            }
            
        
        }else{
            $interactionReportFileName = $userInteractionsReportData['reportName'] ? $userInteractionsReportData['reportName'] : 'Interaction Checker PDF';
            return $pdf->download($interactionReportFileName.'.pdf');
        }
        
    }
}

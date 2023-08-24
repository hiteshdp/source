<?php
/*
|-----------------------------------------------------------------------------
| LARAVEL APP BASE CLASS, MODEL CLASS, PACKAGES, HELPERS DEFINE
|-----------------------------------------------------------------------------
*/
namespace App\Http\Controllers;

use App\Models\Therapy;
use App\Models\Usertherapy;
use App\Models\TherapyCondition;
use App\Models\Condition;
use App\Models\ConditionsMaster;
use App\Models\ConditionsRelation;
use Illuminate\Http\Request;
use App\Models\TherapyDetails;
use App\Helpers\Helpers as Helper;
use App\Models\TherapyReference;
use Carbon\Carbon;
use DB;
use App\Models\UserIntegrativeProtocol;
use App\Models\UserIntegrativeProtocolConditions;
use App\Models\SeoMetaTags;
use App\Models\MedicineCabinet;
use App\Models\ProductRecommendations;

class TherapyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Therapy Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles therapy for the application and
    | redirecting them to your home screen. It contains method 
    | which get the list of therapy on base of request pass. It also generate
    | get the list of effectiveness on base of request condition. 
    |
    */

    /**
     * This function complies load view of therapy page.
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function index()
    {
        return view('page.therapy');
    }

    /**
     * This function complies load view of therapy search page.
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function therapySideEffect()
    {
        return view('page.find-therapies');
    }

    /**
     * This function complies load view of dashboard page.
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function dashboard()
    {
        return view('page.dashboard');
    }

    /**
     * This function complies get the list of therapy on base of request pass in autocomplete search.
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through searh form data.
     * @return  \Illuminate\Http\Response               Redirect to related response as json data
     */
    public function autocompleteTherapy(Request $request)
    {
        //Get the list of therapy on base of search string
        $therapy = Therapy::select("id","therapy","canonicalName")
                ->where("therapy","LIKE","%{$request->input('query')}%")
                ->groupBy('therapy')
                ->whereNull('deleted_at')
                ->get();
        
        $data = array();    
        $i = 0;            
        foreach ($therapy as $key => $thpy)
        {
            $data[$i]['Id'] = $thpy->id."-therapy";
            $data[$i]['Name'] = $thpy->therapy;
            $data[$i]['canonicalName'] = $thpy->canonicalName;
            $i++;
        }

        //Get the conditoin along with Therapies and add into the array
        $condition = Condition::select("id","conditionName","canonicalName")
                ->where("conditionName","LIKE","%{$request->input('query')}%")
                ->where('displayInSearch','1')
                ->groupBy('conditionName')
                ->whereNull('deleted_at')
                ->get();
        
        foreach ($condition as $key => $cond)
        {
            $data[$i]['Id'] = $cond->id."-condition";
            $data[$i]['Name'] = $cond->conditionName;
            $data[$i]['canonicalName'] = $cond->canonicalName;
            $i++;
        }

        return response()->json($data);
    }

    /**
     * This function complies get the list of condition on base of request pass
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response as json data
     */
    public function autocompleteSideEffect(Request $request)
    {

        $condition = Condition::select("id","conditionName")
                ->where("conditionName","LIKE","%{$request->input('query')}%")
                ->groupBy('conditionName')
                ->whereNull('deleted_at')
                ->get();
        
        $data = array();    
        $i = 0;            
        foreach ($condition as $key => $cond)
        {
            $data[$i]['Id'] = $cond->id;
            $data[$i]['Name'] = $cond->conditionName;
            $i++;
        }
        return response()->json($data);
    }

    /**
     * This function complies get the list of effective on base of request pass
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response on view page data
     */
    public function sideEffects()
    {
        $condition = [];
        $conditionsMaster = ConditionsMaster::whereNull('deleted_at')->get()->toArray();
        foreach ($conditionsMaster as $conditionsMasterKey => $conditionsMasterValue) {
            $data['titleName'] = $conditionsMasterValue['name'];
            $conditionRelation = ConditionsRelation::join('conditions','conditions_relation.conditionId','=','conditions.id')
                                ->select('conditions.id','conditions.conditionName')
                                ->where('conditions_relation.conditionMasterId',$conditionsMasterValue['id'])
                                ->whereNull('conditions_relation.deleted_at')
                                ->whereNull('conditions.deleted_at')
                                ->orderBy('conditions.conditionName')
                                ->get()->toArray();
            $data['conditions'] = $conditionRelation;
            
            $condition[] = $data;
        }
        return view('page.side-effects-list', compact('condition'));
    }

    /**
     * This function gets the list of effectiveness on base of request condition id
     *
     * @param   \Illuminate\Http\Request    $request        A request object pass through form data.
     * @param   integer                     $conditionId    A request condition id passing.
     * @return  \Illuminate\Http\Response                   Redirect to related response on view page data
     */
    public function conditionDetails($conditionId, Request $request)
    {

        //Get condition name for selected therapy
        $conditionData = Condition::where('canonicalName',$conditionId)->whereNull('deleted_at');
        $CheckConditionData = $conditionData->get()->toArray(); 
        $condition = array();
        if(!empty($CheckConditionData)){
           
            if(!empty($conditionData->pluck('conditionName'))){ 
                $condition['conditionName'] = $conditionData->pluck('conditionName')->first();
                $condition['ids'] = $conditionData->pluck('id')->toArray();
                $condition['conditionNames'] = $conditionData->pluck('conditionName')->toArray();
            }

        }
        
        $finalArray = array(); // Stores all therapy details
        $effectiveness_color = array(); // Stores the color for each effectiveness
        $sort_order = array(); // Sorts the custom order of array
        
        // for seo dynamic meta tags 
        $metaTitle = '';
        $metaKeywords = '';
        $metaNewsKeywords = '';
        $metaDescription = '';
        $metaOgTitle = '';
        $metaOgDescription = '';

        // Returns the condition details if condition id exist
        if(!empty($condition)){
            
            // get the seo meta tags for the current condition canonical name
            $seoMetaTagsData = SeoMetaTags::where('canonical_condition_name',$conditionId)->whereNull('deleted_at')->first();
            if(!empty($seoMetaTagsData)){
                $metaTitle = $seoMetaTagsData['title'] ? $seoMetaTagsData['title'] : '';
                $metaKeywords = $seoMetaTagsData['meta_keywords'] ? $seoMetaTagsData['meta_keywords'] : '';
                $metaNewsKeywords = $seoMetaTagsData['meta_news_keywords'] ? $seoMetaTagsData['meta_news_keywords'] : '';
                $metaDescription = $seoMetaTagsData['meta_description'] ? $seoMetaTagsData['meta_description'] : '';
                $metaOgTitle = $seoMetaTagsData['og_title'] ? $seoMetaTagsData['og_title'] : '';
                $metaOgDescription = $seoMetaTagsData['og_description'] ? $seoMetaTagsData['og_description'] : '';
            }

            $request->session()->put('conditionValue',['conditionId' => $condition['conditionNames'], 'conditionName' => $condition['conditionName'], 'conditionNames' => $condition['conditionNames']]);

            //Get all therapy list for selected condtion
            $therapy = Therapy::select("therapy_condition.*","therapy.canonicalName as canonicalName","therapy.therapy as therapy","therapy_condition.therapyId as id")
                        ->join("therapy_condition","therapy_condition.therapyId","=","therapy.id")
                        ->whereIn('therapy_condition.conditionId',$condition['ids'])
                        ->whereNull('therapy_condition.deleted_at')
                        ->whereNull('therapy.deleted_at')
                        ->groupBy("therapy.id")
                        ->get();

            $therapy = $therapy->toArray(); 
            //Set the required data structure and also set up the color for the same
            foreach($therapy as $t_key => $t_val)
            {
                switch ($t_val['effectiveness']) {
                    case "EFFECTIVE":
                        $t_val['effectiveness'] = str_replace("EFFECTIVE", "EFFECTIVE", $t_val['effectiveness']);
                        break;
                    case "INEFFECTIVE":
                        $t_val['effectiveness'] = str_replace("INEFFECTIVE", "INEFFECTIVE", $t_val['effectiveness']);
                        break;
                    case "LIKELY EFFECTIVE":
                        $t_val['effectiveness'] = str_replace("LIKELY EFFECTIVE", "LIKELY EFFECTIVE", $t_val['effectiveness']);
                        break;
                    case "LIKELY INEFFECTIVE":
                        $t_val['effectiveness'] = str_replace("LIKELY INEFFECTIVE", "LIKELY INEFFECTIVE", $t_val['effectiveness']);
                        break;
                    case "POSSIBLY INEFFECTIVE":
                        $t_val['effectiveness'] = str_replace("POSSIBLY INEFFECTIVE", "POSSIBLY INEFFECTIVE", $t_val['effectiveness']);
                        break;
                    case "POSSIBLY EFFECTIVE":
                        $t_val['effectiveness'] = str_replace("POSSIBLY EFFECTIVE", "POSSIBLY EFFECTIVE", $t_val['effectiveness']);
                        break;
                    case "INSUFFICIENT RELIABLE EVIDENCE to RATE":
                        $t_val['effectiveness'] = str_replace("INSUFFICIENT RELIABLE EVIDENCE to RATE", "INCONCLUSIVE EVIDENCE", $t_val['effectiveness']);
                        break;
                }
                
                $finalArray[$t_val['effectiveness']][] = $t_val;
                switch ($t_val['effectiveness']) {
                    case "EFFECTIVE":
                        $effectiveness_color[$t_val['effectiveness']] = "green-circle green-new";
                        $sort_order[$t_val['effectiveness']] = 1;
                        break;
                    case "INEFFECTIVE":
                        $effectiveness_color[$t_val['effectiveness']] = "green-circle red-new";
                        $sort_order[$t_val['effectiveness']] = 6;
                        break;
                    case "LIKELY EFFECTIVE":
                        $effectiveness_color[$t_val['effectiveness']] = "green-circle light-green-new";
                        $sort_order[$t_val['effectiveness']] = 2;
                        break;
                    case "LIKELY INEFFECTIVE":
                        $effectiveness_color[$t_val['effectiveness']] = "green-circle light-red-new";
                        $sort_order[$t_val['effectiveness']] = 5;
                        break;
                    case "POSSIBLY INEFFECTIVE":
                        $effectiveness_color[$t_val['effectiveness']] = "green-circle light-pink-new";
                        $sort_order[$t_val['effectiveness']] = 4;
                        break;
                    case "POSSIBLY EFFECTIVE":
                        $effectiveness_color[$t_val['effectiveness']] = "green-circle light-orange-new";
                        $sort_order[$t_val['effectiveness']] = 3;
                        break;
                    case "INCONCLUSIVE EVIDENCE":
                        $effectiveness_color[$t_val['effectiveness']] = "green-circle gray-new";
                        $sort_order[$t_val['effectiveness']] = 7;
                        break;
                    default:
                        $effectiveness_color[$t_val['effectiveness']] = "green-circle";
                }
            }
            array_multisort($sort_order,$finalArray);   

        }else{
            return redirect()->back()->with('error',"Condition Id not found");
        }
        return view('page.condition', compact('finalArray','effectiveness_color','condition','metaTitle','metaKeywords','metaNewsKeywords','metaDescription','metaOgTitle','metaOgDescription'));
    }

    /**
     * This function gets the list of therapy on base of request pass
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response on view page data
     */
    public function therapyList(Request $request)
    {

        if(!empty($request->letter)){
            if(strtoupper($request->letter) == 'ALL'){
                $therapys = Therapy::all()->sortBy('therapy')->unique('therapy')->whereNull('deleted_at');
                foreach ($therapys as $key => $value){
                    echo "<li>";
                        echo "<a  href=".route('therapy', $value->id).">".$value->therapy."</a>";
                    echo "</li>";
                }
            }
            else{
                $therapys = Therapy::where("therapy","LIKE","{$request->letter}%")->groupBy('therapy')->whereNull('deleted_at')->get();
                foreach ($therapys as $key => $value){
                    echo "<li>";
                        echo "<a  href=".route('therapy', $value->id).">".$value->therapy."</a>";
                    echo "</li>";
                }
            }

        }else{
            $therapy = Therapy::all()->sortBy('therapy')->unique('therapy')->whereNull('deleted_at');
            return view('page.therapy-list', compact('therapy'));
        }
    }

    /**
     * This function gets the effective list of the requested therapy id
     *
     * @param   Integer                     $therapyId  Passing request thereapy id for get therapy effective list
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response on view thereapy detail page with respective data
     */
    public function therapyDetails($therapyId, Request $request)
    {
        //Get therapy data from therapy id
        $therapy = Therapy::where('canonicalName',$therapyId)->whereNull('deleted_at')->first();

        if(!empty($therapy)){
            try{

                $metaTitle = '';
                $metaKeywords = '';
                $metaNewsKeywords = '';
                $metaDescription = '';
                $metaOgTitle = '';
                $metaOgDescription = '';

                // get the seo meta tags for the current therapy data
                $seoMetaTagsData = SeoMetaTags::where('canonical_therapy_name',$therapyId)->whereNull('deleted_at')->first();
                if(!empty($seoMetaTagsData)){
                    $metaTitle = $seoMetaTagsData['title'] ? $seoMetaTagsData['title'] : '';
                    $metaKeywords = $seoMetaTagsData['meta_keywords'] ? $seoMetaTagsData['meta_keywords'] : '';
                    $metaNewsKeywords = $seoMetaTagsData['meta_news_keywords'] ? $seoMetaTagsData['meta_news_keywords'] : '';
                    $metaDescription = $seoMetaTagsData['meta_description'] ? $seoMetaTagsData['meta_description'] : '';
                    $metaOgTitle = $seoMetaTagsData['og_title'] ? $seoMetaTagsData['og_title'] : '';
                    $metaOgDescription = $seoMetaTagsData['og_description'] ? $seoMetaTagsData['og_description'] : '';
                }
                
                $saveRoute = '';
                $redirectRoute = '';
                $therapyId = $therapy->id;
                $therapy_detail = TherapyDetails::where('therapyId',$therapyId)->whereNull('deleted_at')->get()->toArray();
                $therapyName = $therapy->therapy;
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
                            
                            // Check if user role is health care provider then check if the therapy is already saved or not
                            if(\Auth::user()->isUserHealthCareProvider()){
                                $therapyCount = UserIntegrativeProtocol::where('userId',$userId)->where('therapyID',$therapyId)->whereNull('deleted_at')->count();
                           
                                //Check if existing therapy apiID is same as already stored in user therapy table
                                $therapyApiIds = UserIntegrativeProtocol::join('therapy','therapy.id','=','user_integrative_protocol.therapyID')->whereNull('user_integrative_protocol.deleted_at')->where('user_integrative_protocol.userId',$userId)->pluck('therapy.apiID')->toArray();
                                if(in_array($therapy->apiID,$therapyApiIds,TRUE)){
                                    $therapyCount++;
                                }
                                $saveRoute = route('save-user-integrative-protocol');
                                $redirectRoute = route('my-wellkasa-rx');
                            }else{
                                /*** // Old code to check if therapy added in my wellkasa
                                    $therapyCount = Usertherapy::where('userId',$userId)->where('therapyID',$therapyId)->whereNull('deleted_at')->count();
                                    
                                    //Check if existing therapy apiID is same as already stored in user therapy table
                                    $therapyApiIds = Usertherapy::join('therapy','therapy.id','=','user_therapy.therapyID')->whereNull('user_therapy.deleted_at')->where('user_therapy.userId',$userId)->pluck('therapy.apiID')->toArray();
                                    if(in_array($therapy->apiID,$therapyApiIds,TRUE)){
                                        $therapyCount++;
                                    }
                                    $saveRoute = route('store-therapy');
                                    $redirectRoute = route('my-wellkasa');
                                    
                                * */


                                // if user type is wellkasa basic / plus user then check the therapy details added for medicine cabinet 
                                $therapyCount = MedicineCabinet::where('userId',$userId)->where('naturalMedicineId',$therapyId)->whereNull('deleted_at')->count();

                                $saveRoute = route('save-medicine-cabinet');
                                $redirectRoute = route('medicine-cabinet');
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
                                /**
                                 * Check from the session array of condition names and match with the effective detail's condition name
                                 * If matches then display the related name in the condition dropdown
                                 *  */
                                foreach ($sessionCondition['conditionNames'] as $conditionNamesFromSessionArr){
                                    if($conditionNamesFromSessionArr == $effectiveDetailArrVal['condition']){
                                        $sessionConditionName = preg_replace('/[^A-Za-z0-9\-]/', ' ', $conditionNamesFromSessionArr); //Replaces special characters with space from session condition name
                                        $conditionNameFromArray = preg_replace('/[^A-Za-z0-9\-]/', ' ',$effectiveDetailArrVal['condition']); //Replaces special characters with space from array condition name
                                        // Add the conditions related to the word if it is exact or has space at first or after or before or after & before the word in session conditions dropdown
                                        $pattern = "/(?<![\w\d])".$sessionConditionName."(?![\w\d])/i";
                                        if(preg_match($pattern,$conditionNameFromArray) == 1){
                                            $sessionConditionValues['conditionsId'] = strtolower($temp['conditionsId']);
                                            $sessionConditionValues['conditionsText'] = $temp['conditionsText'];
                                            $sessionConditionDetails[] = $sessionConditionValues;
                                        }
                                        break;
                                    }
                                }
                            }
        

                            //Assigns id name in condition text
                            $id = 'id='.$temp['conditionsId'];
                            
                            $conditionNameFromArr = $effectiveDetailArrVal['condition'];
                            
                            $conditionRoute = "javascript:void(0)";
                            $title = "Not found efficacy chart for this condition";
                            $style = '';
                            $getCanonicalName = DB::table('conditions')->select('canonicalName','conditionName')
                            ->where('conditionName',$conditionNameFromArr)->get()->first();
                            if(!empty($getCanonicalName->canonicalName)){
                               $conditionRoute = route('condition',$getCanonicalName->canonicalName);
                               $title = "Click here to view ".$getCanonicalName->conditionName." efficacy chart";
                               $style = "style='text-decoration: underline;'";
                            }
                            
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
                                    $effective[] = "<b $id> <a href=".$conditionRoute." ".$style." title='".$title."'>".$effectiveDetailArrVal['condition']."</a></b>.&nbsp ".nl2br($description);
                                    break;
                                case "Ineffective":
                                    $ineffective[] = "<b $id> <a href=".$conditionRoute." ".$style." title='".$title."'>".$effectiveDetailArrVal['condition']."</a></b>.&nbsp ".nl2br($description);
                                    break;
                                case "Likely Effective":
                                    $likelyEffective[] ="<b $id> <a href=".$conditionRoute." ".$style." title='".$title."'>".$effectiveDetailArrVal['condition']."</a></b>.&nbsp ".nl2br($description);    
                                    break;
                                case "Likely Ineffective":
                                    $likelyIneffective[] ="<b $id> <a href=".$conditionRoute." ".$style." title='".$title."'>".$effectiveDetailArrVal['condition']."</a></b>.&nbsp ".nl2br($description);  
                                    break;
                                case "Possibly Ineffective":
                                    $possiblyIneffective[] ="<b $id> <a href=".$conditionRoute." ".$style." title='".$title."'>".$effectiveDetailArrVal['condition']."</a></b>.&nbsp ".nl2br($description);
                                    break;
                                case "Possibly Effective";  
                                    $possiblyEffective[] ="<b $id> <a href=".$conditionRoute." ".$style." title='".$title."'>".$effectiveDetailArrVal['condition']."</a></b>.&nbsp ".nl2br($description);
                                    break;
                                case "Insufficient Reliable Evidence To Rate":
                                    $insufficient[] ="<b $id> <a href=".$conditionRoute." ".$style." title='".$title."'>".$effectiveDetailArrVal['condition']."</a></b>.&nbsp ".nl2br($description);  
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

                    // Store Subscription Status
                    $subscriptionStatus = '1';
                    $subscriptionRenewLink = 'javascript:void(0)';
                    if(!empty(\Auth::user())){
                        $subscriptionStatus =  \Auth::user()->getSubscriptionStatus();
                        // Get the wellkasa rx subscription link for wellkasa rx user logged in
                        if( \Auth::user()->isUserHealthCareProvider() == '1'){
                            $subscriptionRenewLink = 'https://wellkasa.com/products/wellkasa-rx';
                        }else{
                            // Get the wellkabinet subscription link for wellkabinet user logged in
                            $subscriptionRenewLink = 'https://wellkasa.com/products/wellkabinet';
                        }
                    }

                    // Get the product recommendations data by the therapy id
                    $productRecommendationsData = ProductRecommendations::getRecommendedProductData($therapyId);

                    return view('page.therapy-details', compact('therapy_detail','therapyName','therapyId','therapyCount','therapyReviewedAt','therapyUpdatedAt','therapyFinalArr','therapyInteractiveArr','therapyConditionsFinalArray','sessionConditionDetails','saveRoute','redirectRoute','metaTitle','metaKeywords','metaNewsKeywords','metaDescription','metaOgTitle','metaOgDescription','subscriptionStatus','subscriptionRenewLink','productRecommendationsData'));     
                
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
     * This function gets the list of condition therapy effectiveness in table format.
     *
     * @return  \Illuminate\Http\Response               Redirect to related response on view page data
     */
    public function displayConditionTherapy()
    {
        //Get all therapy list for linked condtions
        $therapy = Therapy::select("therapy_condition.effectiveness AS effectiveness","therapy.therapy AS therapyName","conditions.conditionName AS conditionName")
        ->leftJoin("therapy_condition","therapy_condition.therapyId","=","therapy.id")
        ->leftJoin("conditions","therapy_condition.conditionId","=","conditions.id")
        ->whereNull('therapy_condition.deleted_at')
        ->whereNull('therapy.deleted_at')
        ->orderBy('therapy.therapy',"ASC")
        ->get()->toArray();

        return view('page.get-conditions-therapy', compact('therapy'));
    }

    /**
     * This function complies update therapy type
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as json data
     */
    public function updateTherapyType()
    {
        //Get all therapy list where therapyType is empty
        $therapy = Therapy::where('therapyType','=','')->whereNull('deleted_at')->limit(50)->get()->toArray();
        foreach($therapy as $therapyKey => $therapyValue){

            if(!empty($therapyValue['apiID'])){
                
                $query = '"id":'.$therapyValue['apiID'];
                $therapyDetail = TherapyDetails::whereRaw("`effectiveDetail` LIKE '%{$query}%'")
                ->whereNull('deleted_at')
                ->get()->toArray()[0];

                $therapyDetailDecoded = json_decode($therapyDetail['effectiveDetail'],true);
                if(isset($therapyDetailDecoded['type']) && $therapyDetailDecoded['type']!=''){
                    $therapyType = $therapyDetailDecoded['type'];
                    Therapy::where('apiID',$therapyValue['apiID'])
                    ->update(['therapyType'=>$therapyType]);                    
                }else{
                    Therapy::where('apiID',$therapyValue['apiID'])
                    ->update(['therapyType'=>'no_type']);
                }   
                
            }   
        }       

    }
}
    
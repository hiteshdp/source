<?php

namespace App\Http\Controllers;

use App\Models\Drugs;
use App\Models\DrugsInteractions;
use App\Models\UserInteractionsReport;
use App\Models\UserInteractions;
use App\Models\Therapy;
use App\Models\Usertherapy;
use Illuminate\Http\Request;
use App\Models\TherapyDetails;
use App\Helpers\Helpers as Helper;
use App\Models\TherapyReference;
use App\Models\NaturalMedicineReference;
use App\Models\UserIntegrativeProtocol;
use App\Models\UserIntegrativeProtocolConditions;
use App\Models\Condition;
use App\Models\TherapyCondition;
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

class InteractionCheckerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Interaction Checker Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles Interaction checker to show drugs, natural medicine,
    | for the application. The main thing is that it display interactions between one or more 
    | drugs and natural medicine.
    |
    */

    /**
     * Function to show all drugs name and natural medicine name in autocomplete search.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDrugsNaturalMedicineList(Request $request){
        $data = array();    
        $i = 0;
        //Get the Drugs along with Therapies and add into the array
        $condition = Drugs::select("id","name","brand_name")
                ->where(DB::raw('CONCAT(name," - ",brand_name)'),"LIKE","%{$request->input('query')}%");
        if(isset($request->drugsDataIds) && !empty($request->drugsDataIds)){
            $condition = $condition->whereNotIn('id',$request->drugsDataIds);
        }  
        // $condition = $condition->groupBy('name')          
        $condition = $condition->whereNull('deleted_at')->get();

        foreach ($condition as $key => $cond)
        {
            $data[$i]['Id'] = $cond->id."-drugs";
            $data[$i]['Name'] = $cond->name." - ".$cond->brand_name;
            $data[$i]['Class'] = 'drugs';
            $i++;
        }

        $therapy = Therapy::select("id","therapy","canonicalName","therapyType")
        ->where("therapy","LIKE","%{$request->input('query')}%");
        if(isset($request->naturalMedicinesDataIds) && !empty($request->naturalMedicinesDataIds)){
            $therapy = $therapy->whereNotIn('id',$request->naturalMedicinesDataIds);
        } 
        // $therapy = $therapy->groupBy('therapy')
        $therapy = $therapy->whereNull('deleted_at')
        ->get();

        foreach ($therapy as $key => $thpy)
        {
            $data[$i]['Id'] = $thpy->id."-naturalMedicine";
            $data[$i]['Name'] = $thpy->therapy;
            $className = 'natural-medicine';
            if(!empty($thpy->therapy)){
                if($thpy->therapyType == "Food, Herbs & Supplements"){
                    $className = 'natural-medicine';
                } else if($thpy->therapyType == "Health & Wellness"){
                    $className = 'yoga';
                }
            }
            $data[$i]['Class'] = $className;
            $i++;
        }


        return response()->json($data);
        
    
    }

    /**
     * Function to show interactions between one or more drugs and natural medicine.
     *
     * @return \Illuminate\Http\Response
     */
    public function showInteractions(Request $request){
 
        $drugsData = [];
        $naturalMedicinesData = [];
        $selectedData = [];
        $data = [];
        
        if($request->drugsId){
            
            $drugsDataArr = Drugs::select("id","name","brand_name")
            ->where("id",$request->drugsId)
            ->whereNull('deleted_at')
            ->first();
          
            
            if(isset($drugsDataArr) && !empty($drugsDataArr)){
                $html = '';
                $html .= '<span class="btn btn-outline-dark btn-sm mb-2 conditionTags mr-2" id="drug-id-'.$drugsDataArr->id.'">';
                $html .= '<a class="drugs" title="'.$drugsDataArr->name.' - '.$drugsDataArr->brand_name.'" href="javascript:void(0);">'.$drugsDataArr->name.' - '.$drugsDataArr->brand_name.'</a>';
                $html .= '<a onclick="deleteDrugs(this);" data-drug="drug-id-'.$drugsDataArr->id.'" title="Click here to delete drug name '.$drugsDataArr->name.' - '.$drugsDataArr->brand_name.'." class="close-btn" aria-label="Close">';
                $html .= '<span aria-hidden="true">×</span>';
                $html .= '</a> </span>';
                $data['section'] = $html;
                $data['drugsDataId'] = $drugsDataArr->id;
                $selectedData = $data;
            }
        }
        if($request->medicineId){
            $naturalMedicinesData = Therapy::select("id","therapy as name","therapyType")
                    ->where("id",$request->medicineId)
                    ->whereNull('deleted_at')
                    ->first();
            
            if(isset($naturalMedicinesData) && !empty($naturalMedicinesData)){
                $className = 'natural-medicine';
                if($naturalMedicinesData->therapyType == "Food, Herbs & Supplements"){
                    $className = 'natural-medicine';
                } else if($naturalMedicinesData->therapyType == "Health & Wellness"){
                    $className = 'yoga';
                }

                $html = '';
                $html .= '<span class="btn btn-outline-dark btn-sm mb-2 conditionTags mr-2" id="nm-id-'.$naturalMedicinesData->id.'">';
                $html .= '<a class="'.$className.'" title="'.$naturalMedicinesData->name.'" href="javascript:void(0);">'.$naturalMedicinesData->name.'</a>';
                $html .= '<a onclick="deleteNaturalMedicine(this);" data-nm="nm-id-'.$naturalMedicinesData->id.'" title="Click here to delete natural medicine '.$naturalMedicinesData->name.'." class="close-btn" aria-label="Close">';
                $html .= '<span aria-hidden="true">×</span>';
                $html .= '</a> </span>';

                $data['section'] = $html;
                $data['naturalMedicinesDataId'] = $naturalMedicinesData->id;
                $selectedData = $data;
            }   
        }
        
        return response()->json($selectedData);
    }

    /**
     * Function to import Drugs Data From TRC API
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response as session message.
     */
    public function importDrugsData(Request $request){
        
        //API call to get Drugs details Start
        $headers = [
            'Content-Type' => 'application/json',
            'X-API-Key' => env("X_API_Key", "fec509d0791cb9845cbed9ced253485ce09b27948075acb08ab481430ebe2d33"),
        ];
        
        $client = new \GuzzleHttp\Client([
            'headers' => $headers
        ]);

        // API call to get 3 pages of response for drug details from TRC API
        $response1 = $client->request('GET', "https://api.therapeuticresearch.com/nm/drugs?page=1&limit=2098");
        $response2 = $client->request('GET', "https://api.therapeuticresearch.com/nm/drugs?page=2&limit=2098");
        $response3 = $client->request('GET', "https://api.therapeuticresearch.com/nm/drugs?page=3&limit=2095");

        // Check 200 response for all APIs
        if($response1->getStatusCode() == 200 && $response2->getStatusCode() == 200 && $response3->getStatusCode() == 200){
            
            // Store array response from the API call
            $drug_detail_api_response1 = json_decode($response1->getBody(), true);
            $drug_detail_api_response2 = json_decode($response2->getBody(), true);
            $drug_detail_api_response3 = json_decode($response3->getBody(), true);

            // Merge 3 API calls response in one array
            $drug_detail_api = array_merge($drug_detail_api_response1, $drug_detail_api_response2, $drug_detail_api_response3);

            if(!empty($drug_detail_api)){
                
                $drug_detail_array = $drug_detail_api;
                foreach($drug_detail_array as $drug_detail_array_key => $drug_detail_array_value){
                    
                    // Check if drug name is not empty, then insert else do not insert drug details
                    if(!empty($drug_detail_array_value['name'])){
                        
                        $searchForComma = ',';
                        $searchForSemiColon = ';';
                        $drugNameArr = $drug_detail_array_value['name'];

                        // Check if drug names are more than one in key name then convert drug names into array and store data into drugs table
                        if( strpos($drugNameArr, $searchForComma) !== false || strpos($drugNameArr, $searchForSemiColon) !== false ) {
                           
                            if(strpos($drugNameArr, $searchForSemiColon) !== false){
                                $drugNameArr = explode(";",$drugNameArr);    
                            }else{
                                $drugNameArr = explode(",",$drugNameArr);
                            }
                            

                            foreach($drugNameArr as $drugName){
                                // Check if drug id from current API already exists in database, if exists then update else insert new record
                                $checkDrugExists = Drugs::where('apiDrugId',$drug_detail_array_value['id'])->where('name',$drugName)->count();
                                if($checkDrugExists == '0'){
                                    $drugs = new Drugs();
                                    $drugs->apiDrugId = $drug_detail_array_value['id'] ? $drug_detail_array_value['id'] : NULL;
                                    $drugs->name = $drugName;
                                    $drugs->brand_name = $drug_detail_array_value['brand-name'] ? $drug_detail_array_value['brand-name'] : NULL;
                                    $drugs->classification = $drug_detail_array_value['classification'] ? $drug_detail_array_value['classification'] : NULL;
                                    $drugs->nutrient_depletions = $drug_detail_array_value['nutrient-depletions'] ? json_encode($drug_detail_array_value['nutrient-depletions'],true) : NULL;
                                    $drugs->drugDetail = json_encode($drug_detail_array_value,true);
                                    $drugs->created_at = Carbon::now();
                                    $drugs->save();
                                }else{
                                    // Update Existing Drug details 
                                    Drugs::where('apiDrugId',$drug_detail_array_value['id'])
                                    ->where('name',$drugName)
                                    ->update([
                                        'name'=>$drugName ? $drugName : NULL, 
                                        'brand_name'=>$drug_detail_array_value['brand-name'] ? $drug_detail_array_value['brand-name'] : NULL,
                                        'classification'=>$drug_detail_array_value['classification'] ? $drug_detail_array_value['classification'] : NULL,
                                        'nutrient_depletions'=> $drug_detail_array_value['nutrient-depletions'] ? json_encode($drug_detail_array_value['nutrient-depletions'],true) : NULL,
                                        'drugDetail'=>$drug_detail_array_value ? json_encode($drug_detail_array_value,true): NULL
                                    ]);
                                }
                            }

                        }else{
                            // Check if drug id from current API already exists in database, if exists then update else insert new record
                            $checkDrugExists = Drugs::where('apiDrugId',$drug_detail_array_value['id'])->count();
                            if($checkDrugExists == '0'){
                                $drugs = new Drugs();
                                $drugs->apiDrugId = $drug_detail_array_value['id'] ? $drug_detail_array_value['id'] : NULL;
                                $drugs->name = $drug_detail_array_value['name'] ? addslashes($drug_detail_array_value['name']) : NULL;
                                $drugs->brand_name = $drug_detail_array_value['brand-name'] ? $drug_detail_array_value['brand-name'] : NULL;
                                $drugs->classification = $drug_detail_array_value['classification'] ? $drug_detail_array_value['classification'] : NULL;
                                $drugs->nutrient_depletions = $drug_detail_array_value['nutrient-depletions'] ? json_encode($drug_detail_array_value['nutrient-depletions'],true) : NULL;
                                $drugs->drugDetail = json_encode($drug_detail_array_value,true);
                                $drugs->created_at = Carbon::now();
                                $drugs->save();    
                            }else{
                                // Update Existing Drug details 
                                Drugs::where('apiDrugId',$drug_detail_array_value['id'])
                                ->update([
                                    'name'=>$drug_detail_array_value['name'] ? $drug_detail_array_value['name'] : NULL, 
                                    'brand_name'=>$drug_detail_array_value['brand-name'] ? $drug_detail_array_value['brand-name'] : NULL,
                                    'classification'=>$drug_detail_array_value['classification'] ? $drug_detail_array_value['classification'] : NULL,
                                    'nutrient_depletions'=>$drug_detail_array_value['nutrient-depletions'] ? json_encode($drug_detail_array_value['nutrient-depletions'],true) : NULL,
                                    'drugDetail'=>$drug_detail_array_value ? json_encode($drug_detail_array_value,true) : NULL
                                ]);

                            }
                        }


                    }else{
                        continue;
                    }
                }
                
                
            }
        }
    }

    /**
     * Function to import Drugs Interaction Data From TRC API
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response as session message.
     */

    public function importDrugsInteractionsData(Request $request){

        //API call to get Drugs details Start
        $headers = [
            'Content-Type' => 'application/json',
            'X-API-Key' => env("X_API_Key", "fec509d0791cb9845cbed9ced253485ce09b27948075acb08ab481430ebe2d33"),
        ];
        
        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
            'allow_redirects' => false

        ]);

        $drugsDetails = Drugs::where('isProcessed','0')->whereNull('deleted_at')->orderBy('id')->get()->toArray();

        // Get each drugs details
        foreach ($drugsDetails as $drugsDetailsKey => $drugsDetailsValue) {
            $drugId = $drugsDetailsValue['id'];
            $drugApiId = $drugsDetailsValue['apiDrugId'];
            $drugName = $drugsDetailsValue['name'];

          
            try{
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.therapeuticresearch.com/nm/interactions?drug_ids=".$drugApiId,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "postman-token: 71eb62f8-43a3-7be6-1039-36e49fcd4aa2",
                "x-api-key: fec509d0791cb9845cbed9ced253485ce09b27948075acb08ab481430ebe2d33"
                ),
                ));
                
                $response = curl_exec($curl);
                $err = curl_error($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                
                curl_close($curl);

                $response_body = json_decode($response, true);
                //Check 200 response from API call
                if($httpCode == '200'){
                    $drug_interaction_api_responseArr = $response_body;
                    
                    // check if data exists from the api response
                    if(!empty($drug_interaction_api_responseArr)){

                        // Get the array from API response for drug interactions
                        foreach ($drug_interaction_api_responseArr as $drug_interaction_api_response ) {
                            
                            // check if monograph id exists, if exist then using it get therapy id from our database
                            if(!empty($drug_interaction_api_response['monograph-id'])){
                                
                                $apiID = $drug_interaction_api_response['monograph-id'];

                                // check if therapy exists with given apiID, if exists then add data
                                $therapyDetails = DB::table('therapy')->where('apiID',$apiID)->whereNull('deleted_at')->get()->first();
                                if(!empty($therapyDetails)){
                                    
                                    $therapyId = $therapyDetails->id;
                                    $therapyApiId = $therapyDetails->apiID;

                                    $interactionRating = $drug_interaction_api_response['rating-text'] ? $drug_interaction_api_response['rating-text'] : NULL;
                                    $severity = $drug_interaction_api_response['severity-text'] ? $drug_interaction_api_response['severity-text'] : NULL;
                                    $occurrence = $drug_interaction_api_response['occurrence-text'] ? $drug_interaction_api_response['occurrence-text'] : NULL;
                                    $levelOfEvidence = $drug_interaction_api_response['level-of-evidence'] ? $drug_interaction_api_response['level-of-evidence'] : NULL;
                                    $description = $drug_interaction_api_response['description'] ? $drug_interaction_api_response['description'] : NULL;
        
                                    // Insert the drug interactions data if its not added, else update existing data
                                    $drugInteractionsTableCheck = DrugsInteractions::where('drugId',$drugId)
                                    ->where('interactId',$drug_interaction_api_response['interact-id'])->count();
                                    if($drugInteractionsTableCheck=='0'){
                                        $drugInteractions = new DrugsInteractions();
                                        $drugInteractions->drugId = $drugId;
                                        $drugInteractions->naturalMedicineId = $therapyId;
                                        $drugInteractions->drugApiId = $drugApiId;
                                        $drugInteractions->naturalMedicineApiId = $therapyApiId;
                                        $drugInteractions->drugName = $drugName;
                                        $drugInteractions->interactionRating = $interactionRating;
                                        $drugInteractions->severity = $severity;
                                        $drugInteractions->occurrence = $occurrence;
                                        $drugInteractions->levelOfEvidence = $levelOfEvidence;
                                        $drugInteractions->description = $description;
                                        $drugInteractions->interactId = $drug_interaction_api_response['interact-id'];
                                        $drugInteractions->interactionDetails = json_encode($drug_interaction_api_response,true);
                                        $drugInteractions->created_at = Carbon::now();
                                        $drugInteractions->save();
        
                                    }else{
                                        DrugsInteractions::where('drugId',$drugId)
                                        ->where('naturalMedicineId',$therapyId)
                                        ->update([
                                            'drugName' => $drugName,
                                            'interactionRating' => $interactionRating,
                                            'severity' => $severity,
                                            'occurrence' => $occurrence,
                                            'levelOfEvidence' => $levelOfEvidence,
                                            'description' => $description,
                                            'interactionDetails' => json_encode($drug_interaction_api_response,true),
                                        ]);
                                        Drugs::where('id',$drugsDetailsValue['id'])
                                        ->update(['isProcessed' => '1']);
                                    }   


                                }else{
                                    continue;
                                }
                                
                            }else{
                                continue;
                            }
                            
                        }

                    }else{
                        // interactions data not found for current durg
                        Drugs::where('id',$drugsDetailsValue['id'])
                        ->update(['isProcessed' => '2']);
                        continue;    
                    }
                    
                }
                else{
                    // interactions data not found for current durg
                    Drugs::where('id',$drugsDetailsValue['id'])
                    ->update(['isProcessed' => '2']);
                    continue;
                }

            } catch (ClientException $e) {
                // Some error while fetching interactions data for current durg
                Drugs::where('id',$drugsDetailsValue['id'])
                ->update(['isProcessed' => '2']);
                continue;                
            }

            // After successfully fetching interactions data update isProcessed to 1 in drugs table
            Drugs::where('id',$drugsDetailsValue['id'])
            ->update(['isProcessed' => '1']);
        }

    }

    /**
     * This function complies to get all condition url list
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response as session message.
     */
    public function getAllConditionsUrls(){
        $conditions = DB::table('get_conditions_url')->select('canonicalName')->whereNull('deleted_at')->get()->toArray();

        $conditions = json_decode(json_encode($conditions),true);
        foreach ($conditions as $key => $value) {
        $XML = '
        <url>
        <loc>https://wellkasa.app/condition/'.$value['canonicalName'].'</loc>
        <lastmod>2021-11-12T09:04:15+00:00</lastmod>
        <priority>1.00</priority>
        </url>';

        $XML = str_replace('&', '&amp;', $XML);
        $XML = str_replace('<', '&lt;', $XML);
        // echo '<pre>' . $XML . '</pre>';

        }

        $therapies = DB::table('get_therapies_url')->select('canonicalName')->whereNull('deleted_at')->get()->toArray();

        $therapies = json_decode(json_encode($therapies),true);
        foreach ($therapies as $key => $value) {
            $XML = '
            <url>
            <loc>https://wellkasa.app/therapy/'.$value['canonicalName'].'</loc>
            <lastmod>2021-11-12T09:04:15+00:00</lastmod>
            <priority>1.00</priority>
            </url>';
            
            $XML = str_replace('&', '&amp;', $XML);
            $XML = str_replace('<', '&lt;', $XML);
            // echo '<pre>' . $XML . '</pre>';
        }
    


    }

    /**
     * This function to Get Interaction Data between natural medicine and durgs
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response as session message.
     */
    public function getInteractions(Request $request){
        $getNewDrugsDataId = $request->getNewDrugsDataId;
        $getNewNaturalMedicinesDataId = $request->getNewNaturalMedicinesDataId;
        $drugsDataId = $request->drugsDataId;
        $html = array();
        if(!empty($getNewDrugsDataId) && !empty($getNewNaturalMedicinesDataId)){
            $html = '';
            $htmlNew = '';
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
                                $circle_class = "red-dot";
                            }else if(strtolower($interData['rating-label']) == 'moderate'){
                                $class = "class='text-danger'";
                                $circle_class = "yellow-dot";
                            }else if(strtolower($interData['rating-label']) == 'minor'){
                                $circle_class = "green-dot";
                            }else{
                                $class = "";
                                $circle_class = "gray-dot";
                            }
    
                            $description = $fdata['description'];
                            
                            //Reference Start
                            $therapyReferenceDetails = NaturalMedicineReference::whereIn('number',$referenceNumbers)
                            ->whereNull('deleted_at')->get()->toArray();
                            if(!empty($therapyReferenceDetails)){
                                foreach ($therapyReferenceDetails as $therapyReferenceDetailsKey => $therapyReferenceDetailsValue) {
                                
                                    $referenceNumber = $therapyReferenceDetailsValue['number'];
                                    $referenceDescription = $therapyReferenceDetailsValue['description'];
            
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
    
                                    $description = str_replace("(".$referenceNumber.",","(".$anchorTag.",",$description);
    
                                    $description = str_replace("(".$referenceNumber.")","(".$anchorTag.")",$description);
    
                                    $description = str_replace(",".$referenceNumber.",",",".$anchorTag.",",$description);
    
                                    $description = str_replace(",".$referenceNumber.")",",".$anchorTag.")",$description);
                                }
                            }
                            //Reference End
     
                            $data['description'] = $description;
                            $interactionsData[] = $data;
                            
                            
                            // Get level of evidence definition - Start
                            $levelOfEvidenceDefinitionVal = '';
                            $levelOfEvidenceDefinition = $interData['level-of-evidence-definition'];
                            if(!empty($levelOfEvidenceDefinition)){
                                switch ($levelOfEvidenceDefinition) {
                                    case "High-quality randomized controlled trial (RCT)":
                                        $levelOfEvidenceDefinitionVal = 'a1';
                                        break;
                                    case "High-quality meta-analysis (quantitative systematic review)":
                                        $levelOfEvidenceDefinitionVal = 'a2';
                                        break;
                                    case "Nonrandomized clinical trial":
                                        $levelOfEvidenceDefinitionVal = 'b1';
                                        break;
                                    case "Nonquantitative systematic review":
                                        $levelOfEvidenceDefinitionVal = 'b2';
                                        break;
                                    case "Lower quality RCT":
                                        $levelOfEvidenceDefinitionVal = 'b3';
                                        break;
                                    case "Clinical cohort study":
                                        $levelOfEvidenceDefinitionVal = 'b4';
                                        break;
                                    case "Case-control study":
                                        $levelOfEvidenceDefinitionVal = 'b5';
                                        break;
                                    case "Historical control":
                                        $levelOfEvidenceDefinitionVal = 'b6';
                                        break;
                                    case "Epidemiologic study":
                                        $levelOfEvidenceDefinitionVal = 'b7';
                                        break;
                                    case "Consensus":
                                        $levelOfEvidenceDefinitionVal = 'c1';
                                        break;
                                    case "Expert opinion":
                                        $levelOfEvidenceDefinitionVal = 'c2';
                                        break;
                                    case "Anecdotal evidence":
                                        $levelOfEvidenceDefinitionVal = 'd1';
                                        break;
                                    case "In vitro or animal study":
                                        $levelOfEvidenceDefinitionVal = 'd2';
                                        break;
                                    case "Theoretical based on pharmacology":
                                        $levelOfEvidenceDefinitionVal = 'd3';
                                        break;
                                }
                            }
                            // Get level of evidence definition - End


                            $no = $this->generateRandomNumber();
                            $temp = array();
                            $temp['therapy'] = $fdata['therapy'];
                            $temp['title'] = $interData['title'];
                            $temp['severity'] = $fdata['severity'];
                            $temp['no'] = $no + 2;
                            $temp['drugName'] = $fdata['drugName']." - ".$getDrugData->brand_name;
                            $temp['drugId'] = $drugId;
                            $temp['getNewNaturalMedicinesDataIdVal'] = $getNewNaturalMedicinesDataIdVal;
                            $temp['occurrence'] = $fdata['occurrence'];
                            $temp['levelOfEvidence'] = $fdata['levelOfEvidence'];
                            $temp['levelOfEvidenceDefinition'] = $levelOfEvidenceDefinitionVal;
                            $temp['description'] = $description;
                            // $temp['interactionRating'] = $fdata['interactionRating'];
                            $temp['interactionRating'] = strtolower($interData['rating-label']);
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
                            $temp['title'] = '';
                            $temp['severity'] = '';
                            $temp['no'] = $no;
                            $temp['drugName'] = $getDrugData->name." - ".$getDrugData->brand_name;
                            $temp['drugId'] = $drugId;
                            $temp['getNewNaturalMedicinesDataIdVal'] = $getNewNaturalMedicinesDataIdVal;
                            $temp['occurrence'] = '';
                            $temp['levelOfEvidence'] = '';
                            $temp['levelOfEvidenceDefinition'] = '';
                            $temp['interactionRating'] = '';
                            $temp['circle_class'] = "gray-dot";
                            $temp['class'] = '';
                            $temp['therapyType'] = $getTherapyDataVal->therapyType;
                            $temp['description'] = '';
                            $temp['isInteractionsFound'] = 0;
                            array_push($finalArray,$temp);
                        }
                    }       
                }
            }
            
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

            foreach($lastArray as $key => $val){
                $className = 'natural-medicine';
                // Checked class name
                foreach($val as $classData){
                    foreach($classData as $classVal){
                        if($classVal['therapyType'] == "Food, Herbs & Supplements"){
                            $className = 'natural-medicine';
                        } else if($classVal['therapyType'] == "Health & Wellness"){
                            $className = 'yoga';
                        }
                    }
                }
                $htmlNew .= "<div>";
                    $htmlNew .= "<a class='".$className."'></a>";
                    $htmlNew .= "<span class='pillname mt-3'>".$key."</span></br>";
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
                        $htmlNew .= "<div class='accordion md-accordion' id='accordionEx' role='tablist' aria-multiselectable='true'>";
                        
                            // check if no interaction then add no-interaction class name else empty - code start
                            $classIsNoInteraction = '';
                            $serverityVal = '';
                            $serverityDataVal = '';  
                            $severityValue = array();
                            foreach($headerData as $v){
                                if($v['isInteractionsFound'] == 0){
                                    $classIsNoInteraction = "no-interaction";
                                }
                                if($v['isInteractionsFound'] == 1){
                                
                                array_push($severityValue,$v['interactionRating']);
                                
                                }
                            }
                            // check if no interaction then add no-interaction class name else empty - code end


                            // check interaction rating  - code start
                            $severityValue = array_unique($severityValue);

                            $severityValueCount = sizeof($severityValue);
                            $serverityVal = "severity-high";
                            $serverityDataVal = "data-severity='high'";

                            if($severityValueCount == 1){
                                if(in_array("major",$severityValue)){
                                    $serverityVal = "severity-high";
                                    $serverityDataVal = "data-severity='high'";
                                }
                                if(in_array("moderate", $severityValue)){
                                    $serverityVal = "severity-moderate";
                                    $serverityDataVal = "data-severity='moderate'";
                                }
                                if(in_array("minor",$severityValue)){
                                    $serverityVal = "severity-mild";
                                    $serverityDataVal = "data-severity='mild'";
                                }
                            }

                            if($severityValueCount == 2){
                                if(in_array("major",$severityValue)  && in_array("moderate", $severityValue)){
                                    $serverityVal = "severity-high";
                                    $serverityDataVal = "data-severity='high'";
                                }
                                if(in_array("major",$severityValue)  && in_array("minor", $severityValue)){
                                    $serverityVal = "severity-high";
                                    $serverityDataVal = "data-severity='high'";
                                }
        
                                if(in_array("moderate", $severityValue) && in_array("minor",$severityValue)){
                                    $serverityVal = "severity-moderate";
                                    $serverityDataVal = "data-severity='moderate'";
                                }
                            }

                            if($severityValueCount == 0){
                                $serverityVal = "";
                                $serverityDataVal = "";
                            }
                            // check interaction rating - code start
                            

                            $htmlNew .= "<div class='card mb-2 ".$serverityVal."' ".$serverityDataVal.">";
                                $htmlNew .= "<div class='card-header ".$classIsNoInteraction."'  role='tab' id='headingTwo'>";
                                    $htmlNew .= "<a class='collapsed' data-toggle='collapse' data-parent='#accordionEx' href='#collapseTwo".$no."' aria-expanded='false' aria-controls='collapseTwo".$no."'>";
                                        $htmlNew .= "<h3 class='acc-title mb-0 ".$circle_class."'>";
                                            $htmlNew .= $k; 
                                            // Checked interactions found or not
                                            foreach($headerData as $v){
                                                if($v['isInteractionsFound'] == 0){
                                                    $htmlNew .= "<span>No interactions found</span>";
                                                }
                                            }
                                            
                                        $htmlNew .= "</h3>";
                                    $htmlNew .= "</a>";
                                $htmlNew .= "</div>";
                                foreach($headerData as $v){
                                    if($v['isInteractionsFound'] == 1){
                                        $htmlNew .= "<div class='accordion md-accordion' id='accordionEx' role='tablist' aria-multiselectable='true'>";   
                                            $htmlNew .= "<div id='collapseTwo".$no."' class='collapse' role='tabpanel' aria-labelledby='headingTwo".$no."' data-parent='#accordionEx'>";
                                                $htmlNew .= "<div class='card-body'>";
                                                    $htmlNew .= "<div>";
                                                        $htmlNew .= "<span><b>".$v['title']."</b></span></span></br></br>";
                                                        $htmlNew .= "<span> Interaction Rating = <span>".ucfirst($v['interactionRating'])."</span></span></br></br>";
                                                        $htmlNew .= "<span> Severity = ".$v['severity']."</span></br>";
                                                        $htmlNew .= "<span> Occurrence = ".$v['occurrence']."</span></br>";
                                                        $htmlNew .= "<span> Level of Evidence =  <a href='javascript:void(0);' onClick='showLevelOfEvidencePopUp(".$v['levelOfEvidenceDefinition'].")' title='Click here to see what it means'>".$v['levelOfEvidence']."</a></span></br></br>";
                                                        $htmlNew .= "<span>".$v['description']."</span></br>";
                                                    $htmlNew .= "</div>";
                                                $htmlNew .= "</div>";
                                            $htmlNew .= "</div>";
                                        $htmlNew .= "</div>";    
                                    }
                                }
                            $htmlNew .= "</div>";
                        
                        $htmlNew .= "</div>";
                    }
                $htmlNew .= "</div>";
                
            }

            
            
            return response()->json($htmlNew);
        }else{
            return response()->json($html);
        }
    }  

    /**
     * This function complies save interaction PDF
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to download pdf file.
     */
    public function saveInteractionPdf(Request $request)
    {   
        $pdf = PDF::loadView('page.interaction-checker');
        return $pdf->download('newsave.pdf');
    }


    //Function to generated random number
    public function generateRandomNumber($length = 3) {
        return substr(str_shuffle(str_repeat($x='0123456789', ceil($length/strlen($x)) )),1,$length);
    }

    /**
     * Function to save interactions between drugs and natural medicine added by current logged in user.
     *
     * @return \Illuminate\Http\Response
     */
    public function saveInteractions(Request $request){

        //validate request parameters
        $validator = Validator::make($request->all(),[
            'drugIds' => 'required_without:naturalMedicineIds',
            'naturalMedicineIds' => 'required_without:drugIds',
            'reportName'  => 'required',
        ]);
        // if data request validated successfully
        if (!$validator->fails()){
            try{
                
                $userId = \Auth::user()->id;
                $reportName = $request->reportName;

                // Save user interaction report data
                $userInteractionsReportData = new UserInteractionsReport();
                $userInteractionsReportData->userId = $userId;
                $userInteractionsReportData->reportName = $reportName;
                $userInteractionsReportData->created_at = Carbon::now();
                $userInteractionsReportData->updated_at = null;

                if($userInteractionsReportData->save()){

                    $userInteractionReportId = $userInteractionsReportData->id;
                    $drugIds = null;
                    if(!empty($request->drugIds)){
                        $drugIds = json_encode($request->drugIds,true);
                    }
                    
                    $naturalMedicineIds = null;
                    if(!empty($request->naturalMedicineIds)){
                        $naturalMedicineIds = json_encode($request->naturalMedicineIds,true);
                    }

                    // Save user interaction data
                    $userInteractionsData = new UserInteractions();
                    $userInteractionsData->userInteractionReportId = $userInteractionReportId;
                    $userInteractionsData->drugId = $drugIds;
                    $userInteractionsData->naturalMedicineId = $naturalMedicineIds;
                    $userInteractionsData->created_at = Carbon::now();
                    $userInteractionsData->updated_at = null;
                    $userInteractionsData->save();
                    $request->session()->flash('message', 'Interaction report saved successfully');

                    return json_encode([
                        "status" => "1",
                        "message" => "Interaction report saved successfully"
                    ],201);

                }else{
                    $request->session()->flash('error', 'Failed to save interaction report');
                    return json_encode([
                        "status" => "0",
                        "message" => "Failed to save interaction report"
                    ],400);
                }
        
            }catch(Exception $e){
                $request->session()->flash('error', $e->getMessage());
                return json_encode([
                    "status" => "0",
                    "message" => $e->getMessage()
                ],500);
            }
        }else{
            /* Validation error message */
            $message = $validator->messages()->first();
            $request->session()->flash('error', $message);
            return json_encode([
                "status" => "0",
                "message" => $message
            ],400);
        }
        
    }

    /**
     * Function to import Natural Medicine Reference
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response as session message.
     */
    public function importNaturalMedicineReference(Request $request){
        
        //API call to get Drugs details Start
        $headers = [
            'Content-Type' => 'application/json',
            'X-API-Key' => env("X_API_Key", "fec509d0791cb9845cbed9ced253485ce09b27948075acb08ab481430ebe2d33"),
        ];
        
        $client = new \GuzzleHttp\Client([
            'headers' => $headers
        ]);

        // API call to get 3 pages of response for drug details from TRC API
        $response = $client->request('GET', "https://api.therapeuticresearch.com/nm/references?page=1&limit=5000000");

        // Check 200 response for all APIs
        if($response->getStatusCode() == 200){
            $drug_detail_api_response = json_decode($response->getBody(), true);
            if(!empty($drug_detail_api_response)){
                
                $drug_detail_array = $drug_detail_api_response;
                foreach($drug_detail_array as $drug_detail_array_key => $drug_detail_array_value){
                    $data = [];
                    $data['referenceId'] = $drug_detail_array_value['id'];
                    $data['description'] = $drug_detail_array_value['description'];
                    $data['medicalPublicationId'] = $drug_detail_array_value['medical-publication-id'] !== '' || $drug_detail_array_value['medical-publication-id'] !== null || !empty($drug_detail_array_value['medical-publication-id']) ? $drug_detail_array_value['medical-publication-id'] : null;
                    $data['referenceApiResponse'] = json_encode($drug_detail_array_value,true);
                    $data['created_at'] = Carbon::now();
                    DB::table('natural_medicine_reference')->insert($data);
                }
                
                
            }
        }
    
    }

    /**
     * Function to show interactions & integrative protocol data added by 
     * current logged in user in my wellkasa rx page.
     *
     * @return \Illuminate\Http\Response
     */
    public function listInteractions(Request $request){

        $userId = \Auth::user()->id;
        $userInteractionsReportData = UserInteractionsReport::select('id','reportName')
        ->where('userId',$userId)->orderBy('id','DESC')->get()->toArray();

        if(!empty($userInteractionsReportData)){
            foreach ($userInteractionsReportData as $userInteractionsReportDataKey => $userInteractionsReportDataValue) {

                $data['id'] = $userInteractionsReportDataValue['id'];
                $data['reportName'] = $userInteractionsReportDataValue['reportName'];
                $userInteractionData = UserInteractions::select('drugId','naturalMedicineId')
                ->where('userInteractionReportId',$userInteractionsReportDataValue['id'])
                ->get()->first();
                
                if(!empty($userInteractionData)){
                    $data['drugs'] = [];
                    if(!empty($userInteractionData->drugId)){
                        $data['drugs'] = DB::table('drugs')->select(DB::raw("CONCAT(name,' - ',brand_name) AS name"))
                        ->whereIn('id',json_decode($userInteractionData->drugId))->get()->toArray();
                    }
                    $data['naturalMedicines'] = [];
                    if(!empty($userInteractionData->naturalMedicineId)){
                        $data['naturalMedicines'] = DB::table('therapy')->select('therapy AS name')
                        ->whereIn('id',json_decode($userInteractionData->naturalMedicineId))->get()->toArray();
                    }
                }else{
                    $data['drugs'] = [];
                    $data['naturalMedicines'] = [];
                }
                
                $userInteractionsReportData[$userInteractionsReportDataKey] = $data;
            }

        }else{
            $userInteractionsReportData = [];
        }

        // Get Integrative Protocols list
        $integrativeProtocols = UserIntegrativeProtocol::where('userId',$userId)->get()->toArray();
        $integrativeProtocolsData = [];
        $therapyConditionFinalArray = array();
        if(isset($integrativeProtocols) && !empty($integrativeProtocols)){
            foreach($integrativeProtocols as $integrativeKey => $integrativeVal){
                $temp = array();
                $temp['id'] = $integrativeVal['id'];
                $temp['userId'] = $integrativeVal['userId'];
                $temp['therapyID'] = $integrativeVal['therapyID'];
                $therapy = Therapy::select("id","therapy","therapyType")->where('id',$integrativeVal['therapyID'])->get()->first();
                $className = asset('images/pill1.svg');
                if(!empty($therapy)){
                    $temp['therapyName'] =$therapy->therapy;

                    if($therapy->therapyType == "Food, Herbs & Supplements"){
                        $className = asset('images/pill1.svg');
                    } else if($therapy->therapyType == "Health & Wellness"){
                        $className = asset('images/yoga.svg');
                    }
                    $temp['imageName'] = $className;
                }
                else{
                    $temp['therapyName'] ="";
                    $temp['imageName'] = $className;
                }

                // Get conditions details
                $userIntegrativeProtocolConditions = UserIntegrativeProtocolConditions::select('user_integrative_protocol_conditions.*','conditions.conditionName')
                                                    ->join("conditions","user_integrative_protocol_conditions.conditionId","=","conditions.id")
                                                    ->where('userIntProtocolId',$integrativeVal['id'])->get()->toArray();
                
                $therapyConditionArray = array();                                    
                foreach($userIntegrativeProtocolConditions as $userIntegrativeKey => $userIntegrativeVal){
                    $conditionId = $userIntegrativeVal['conditionId'];
                    $therapy_condition = TherapyCondition::select('effectiveness')->where('conditionId',$conditionId)->where('therapyId',$integrativeVal['therapyID'])->first();
                    $userIntegrativeProtocolConditions[$userIntegrativeKey]['effectiveness'] = $therapy_condition->effectiveness;
                    $userIntegrativeProtocolConditions[$userIntegrativeKey]['conditionId'] = $conditionId;
                    


                    // Get therapy conditions details
                    $therapyConditions = Condition::select('conditions.id','conditions.conditionName AS name')
                                        ->join('therapy_condition','conditions.id','=','therapy_condition.conditionId')
                                        ->where('therapyId',$integrativeVal['therapyID'])
                                        ->where('conditions.id',$conditionId)
                                        ->whereNull('conditions.deleted_at')->orderby('conditions.conditionName')
                                        ->get()->toArray();
                     
                    $therapyWiseConditionArray = array();                      
                    foreach($therapyConditions as $conditions){
                        $temp2 = array();
                        $temp2['id'] = $conditions['id'];
                        $temp2['name'] = $conditions['name'];
                        $temp2['therapyID'] = $integrativeVal['therapyID'];
                        array_push($therapyWiseConditionArray,$temp2);

                    }                  
                    array_push($therapyConditionArray,$therapyWiseConditionArray);
                    
                }

                // Display alphabetical order of condition names under each therapy - code start
                if(!empty($userIntegrativeProtocolConditions)){

                    usort($userIntegrativeProtocolConditions, function($userIntegrativeProtocolConditionsNameOne, $userIntegrativeProtocolConditionsNameTwo) {
                        return $userIntegrativeProtocolConditionsNameOne['conditionName'] <=> $userIntegrativeProtocolConditionsNameTwo['conditionName'];
                    });
                }
                // Display alphabetical order of condition names under each therapy - code end

                $temp['userIntegrativeProtocolConditions'] = $userIntegrativeProtocolConditions;
                $temp['userIntegrativeProtocolConditionsCount'] = count($userIntegrativeProtocolConditions);                              
                $temp['therapyConditions'] = $therapyConditionArray;
               
                array_push($integrativeProtocolsData,$temp);    
            }
            

            foreach($integrativeProtocolsData as $therapyKey => $therapyVal){
                foreach($therapyVal['therapyConditions'] as $therapyConditionVal){
                    foreach($therapyConditionVal as $conditionKey => $conditionVal){
                        $tempTherapyArray = array();
                        $tempTherapyArray['id'] = $conditionVal['id'];
                        $tempTherapyArray['name'] = $conditionVal['name'];
                        $tempTherapyArray['therapyID'] = $conditionVal['therapyID'];
                        array_push($therapyConditionFinalArray,$tempTherapyArray);
                    }
                   
                }
            }

            // Display alphabetical order of therapy names - code start 
            if(!empty($integrativeProtocolsData)){
                
                usort($integrativeProtocolsData, function($a, $b) {
                    return $a['therapyName'] <=> $b['therapyName'];
                });
            }
            // Display alphabetical order of therapy names - code end 

            // Display unique records for filter condition dropdown - code start
            if(!empty($therapyConditionFinalArray)){
                
                usort($therapyConditionFinalArray, function($a, $b) {
                    return $a['name'] <=> $b['name'];
                });

                $therapyConditionFinalArray = array_values(array_column($therapyConditionFinalArray, null, 'name'));
            }
            // Display unique records for filter condition dropdown - code end

        }
        return view('page.my-wellkasa-rx', compact('userInteractionsReportData','integrativeProtocolsData','therapyConditionFinalArray'));
    }


    /**
     * Function to show edit interactions page with saved 
     * drugs & natural medicine data added by current logged in user.
     *
     * @return \Illuminate\Http\Response
     */
    public function editInteractionPage(Request $request){
        try {
        
            $drugsData = [];
            $naturalMedicinesData = [];
            $selectedData = [];
            $data = [];

            $userInteractionId = Crypt::decrypt($request->userInteractionId);
            $userId = \Auth::user()->id;
            $userInteractionsReportData = UserInteractionsReport::select('id','reportName')
            ->where('userId',$userId)->where('id',$userInteractionId)->whereNull('deleted_at')->get()->toArray();
            if(!empty($userInteractionsReportData)){
                
                // get the interaction details stored by current logged in user

                $userInteractionsReportData = $userInteractionsReportData[0];
                
                $reportName = $userInteractionsReportData['reportName'];
                $userInteractionsData = UserInteractions::select('drugId','naturalMedicineId')
                ->where('userInteractionReportId',$userInteractionsReportData['id'])->whereNull('deleted_at')->first();

                $userStoredDrugIds = json_decode($userInteractionsData['drugId'],true);
                $userStoredNaturalMedicineIds = json_decode($userInteractionsData['naturalMedicineId'],true);

                return view('page.edit-interaction',compact('userInteractionId','reportName','userStoredDrugIds','userStoredNaturalMedicineIds'));

            }else{
                // return to previous page if user stored interaction details is not found
                return redirect('my-wellkasa-rx')->with('error','Interaction id not found. Please try again.');
            }
        }catch (Exception $e) {
            /* Something went wrong */
            $error_message = $e->getMessage();
            return back()->with('error',$error_message);
        }
        
        return view('page.edit-interaction');
    }

    /**
     * Function to delete interactions added by current logged in user.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteInteraction(Request $request){
        try{            
            $userId = \Auth::user()->id;
            $userInteractionId = $request->userInteractionId;    
            
            DB::beginTransaction();

            $userTherapyCheck = UserInteractionsReport::where('id',$userInteractionId)->whereNotNull('deleted_at')->count(); 
            
            if($userTherapyCheck == 0){
               
                UserInteractionsReport::where('id',$userInteractionId)->delete();
                UserInteractions::where('userInteractionReportId',$userInteractionId)->delete();

                DB::commit();
                $request->session()->flash('message', 'Interaction deleted successfully.');
                return json_encode(array('status'=>'0'));
            }else{
                DB::rollback();
                $request->session()->flash('error', 'This interaction id is already deleted. Please refresh this page.');
                return json_encode(array('status'=>'1'));
            }
           
        }catch (Exception $e) {
            /* Something went wrong */
            $error_message = $e->getMessage();
            return json_encode([
                'message'=> $error_message,
                'status' => 1
            ]);
        } 
    }

     /**
     * Function to update interactions added by current logged in user.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateInteraction(Request $request){

        //validate request parameters
        $validator = Validator::make($request->all(),[
            'userInteractionReportId' => 'required',
            'drugIds' => 'required_without:naturalMedicineIds',
            'naturalMedicineIds' => 'required_without:drugIds',
            'reportName'  => 'required',
        ]);
        // if data request validated successfully
        if (!$validator->fails()){
            try{
                
                DB::beginTransaction();

                $userId = \Auth::user()->id;
                $reportName = $request->reportName;
                $userInteractionReportId = $request->userInteractionReportId;

                // Update user interaction report data
                $userInteractionsReportData = UserInteractionsReport::where('id',$userInteractionReportId)
                ->where('userId',$userId);
                $userInteractionsReportData = $userInteractionsReportData->update([
                    "reportName" => $reportName,
                    "updated_at" => Carbon::now()
                ]);

                if($userInteractionsReportData){

                    $drugIds = null;
                    if(!empty($request->drugIds)){
                        $drugIds = json_encode($request->drugIds,true);
                    }
                    $naturalMedicineIds = null;
                    if(!empty($request->naturalMedicineIds)){
                        $naturalMedicineIds = json_encode($request->naturalMedicineIds,true);
                    }

                    // Update user interaction data
                    $userInteractionsData = UserInteractions::where('userInteractionReportId',$userInteractionReportId);
                    $userInteractionsData = $userInteractionsData->update([
                        "drugId" => $drugIds,
                        "naturalMedicineId" => $naturalMedicineIds,
                        "updated_at" => Carbon::now()
                    ]);
                    if($userInteractionsData){
                        
                        DB::commit();
                        
                        $request->session()->flash('message', 'Interaction report updated successfully');
                        return json_encode([
                            "status" => "1",
                            "message" => "Interaction report updated successfully"
                        ],200);
                    }else{
                       
                        DB::rollback();

                        $request->session()->flash('error', 'Failed to update interaction details. Please try again.');
                        return json_encode([
                            "status" => "0",
                            "message" => "Failed to update interaction details. Please try again."
                        ],400);
                    }

                }else{

                    DB::rollback();
                    
                    $request->session()->flash('error', 'Failed to update interaction report. Please try again.');
                    return json_encode([
                        "status" => "0",
                        "message" => "Failed to update interaction report. Please try again."
                    ],400);
                }
        
            }catch(Exception $e){
                $request->session()->flash('error', $e->getMessage());
                return json_encode([
                    "status" => "0",
                    "message" => $e->getMessage()
                ],500);
            }
        }else{
            /* Validation error message */
            $message = $validator->messages()->first();
            $request->session()->flash('error', $message);
            return json_encode([
                "status" => "0",
                "message" => $message
            ],400);
        }
        
    }
}
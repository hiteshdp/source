<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Drugs;
use App\Models\DrugsInteractions;

use App\Models\Therapy;
use App\Models\Usertherapy;
use Illuminate\Http\Request;
use App\Models\TherapyDetails;
use App\Helpers\Helpers as Helper;
use App\Models\TherapyReference;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use Cookie;
use Session;

class importDrugsInteractionData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:drugsinteractionsdata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //API call to get Drugs details Start
        $headers = [
            'Content-Type' => 'application/json',
            'X-API-Key' => env("X_API_Key", "fec509d0791cb9845cbed9ced253485ce09b27948075acb08ab481430ebe2d33"),
        ];
        
        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
            'allow_redirects' => false

        ]);

        $drugsDetails = Drugs::where('isProcessed','0')->whereNull('deleted_at')->limit('2000')->orderBy('id')->get()->toArray();

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
                                    // $drugInteractionsTableCheck = DrugsInteractions::where('drugId',$drugId)
                                    // ->where('interactId',$drug_interaction_api_response['interact-id'])->count();
                                    // if($drugInteractionsTableCheck=='0'){
                                    //     $drugInteractions = new DrugsInteractions();
                                    //     $drugInteractions->drugId = $drugId;
                                    //     $drugInteractions->naturalMedicineId = $therapyId;
                                    //     $drugInteractions->drugApiId = $drugApiId;
                                    //     $drugInteractions->naturalMedicineApiId = $therapyApiId;
                                    //     $drugInteractions->drugName = $drugName;
                                    //     $drugInteractions->interactionRating = $interactionRating;
                                    //     $drugInteractions->severity = $severity;
                                    //     $drugInteractions->occurrence = $occurrence;
                                    //     $drugInteractions->levelOfEvidence = $levelOfEvidence;
                                    //     $drugInteractions->description = $description;
                                    //     $drugInteractions->interactId = $drug_interaction_api_response['interact-id'];
                                    //     $drugInteractions->interactionDetails = json_encode($drug_interaction_api_response,true);
                                    //     $drugInteractions->created_at = Carbon::now();
                                    //     $drugInteractions->save();
        
                                    // }else{
                                    //     DrugsInteractions::where('drugId',$drugId)
                                    //     ->where('naturalMedicineId',$therapyId)
                                    //     ->update([
                                    //         'drugName' => $drugName,
                                    //         'interactionRating' => $interactionRating,
                                    //         'severity' => $severity,
                                    //         'occurrence' => $occurrence,
                                    //         'levelOfEvidence' => $levelOfEvidence,
                                    //         'description' => $description,
                                    //         'interactionDetails' => json_encode($drug_interaction_api_response,true),
                                    //     ]);
                                    //     Drugs::where('id',$drugsDetailsValue['id'])
                                    //     ->update(['isProcessed' => '1']);
                                    // }   


                                }else{
                                    continue;
                                }
                                
                            }else{
                                continue;
                            }
                            
                        }

                    }else{
                        // interactions data not found for current durg
                        // Drugs::where('id',$drugsDetailsValue['id'])
                        // ->update(['isProcessed' => '2']);
                        continue;    
                    }
                    
                }
                else{
                    // interactions data not found for current durg
                    // Drugs::where('id',$drugsDetailsValue['id'])
                    // ->update(['isProcessed' => '2']);
                    continue;
                }

            } catch (ClientException $e) {
                // Some error while fetching interactions data for current durg
                // Drugs::where('id',$drugsDetailsValue['id'])
                // ->update(['isProcessed' => '2']);
                continue;                
            }

            // After successfully fetching interactions data update isProcessed to 1 in drugs table
            // Drugs::where('id',$drugsDetailsValue['id'])
            // ->update(['isProcessed' => '1']);
        }

    }
}

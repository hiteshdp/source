<?php

namespace App\Console\Commands;

use App\Models\Therapy;
use App\Models\TherapyDetails;
use App\Helpers\Helpers as Helper;
use Illuminate\Console\Command;
use App\Models\TherapyReference;
use Carbon\Carbon;
use DB;

class TherapyCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'therapy:reference';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update/Add therapy reference details from TRC API';

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
        try{
            
            $startCron = Carbon::now();
            $type=1;

            // Cron started 
            Helper::storeLogs('php artisan therapy:reference', 'Therapy Reference Cron started', '', $type, $startCron, ''); 

            // Get current date
            $currentDate = date('Y-m-d');

            // Get the current day of the month
            $currentDay = date('d');

            // Check if it's the first day of the month (day = 01)
            if ($currentDay === '01') {
                // The first day of the month. Query execute;
                // Reset isReferenceProcessed 0 if not getting in database than reset all data with 0
                $updateIsProcessed = Therapy::where('id', '>', '0')->update(['isReferenceProcessed' => 0]);

                if (!empty($updateIsProcessed)) {
                    // Update therapy isReferenceProcessed to 0 when processedAt value if greater than current date
                    Helper::storeLogs('php artisan therapy:reference', $currentDate, 'Updated therapy reference isReferenceProcessed to 0', $type, Carbon::now(), '');
                }
            }
            
            
            // Get therapy name
            $getTherapy = Therapy::select('therapy','id','apiID')->orderBy('id', 'ASC')->limit(1)->where('isReferenceProcessed',0)->get()->toArray();
           
            //Check if API response there
            foreach ($getTherapy as $therapy) {

				//In any case, if API call is success then update the therapy status on single call
                Therapy::where('id', $therapy['id'])->update(['isReferenceProcessed' => 1, 'referenceProcessedAt' => $currentDate]);
				
                $headers2 = [
                    'Content-Type' => 'application/json',
                    'X-API-Key' => env("X_API_Key", "fec509d0791cb9845cbed9ced253485ce09b27948075acb08ab481430ebe2d33"),
                ];
                
                
                $client2 = new \GuzzleHttp\Client([
                    'headers' => $headers2
                ]);
        
                
                $response2 = $client2->request('GET', env("EFFECTIVE_API_URL", "https://api.therapeuticresearch.com/nm/monographs/").$therapy['apiID']);
                
                // Log for effectiveness api called by apiID for the therapy
                Helper::storeLogs('php artisan therapy:reference','https://api.therapeuticresearch.com/nm/monographs/'.$therapy['apiID'],'API called for effectiveness details',$type,Carbon::now(),Carbon::now()); 

                //Check if API response there
                if($response2->getStatusCode() == 200){
                    $effective_detail = json_decode($response2->getBody(), true);

                    foreach($effective_detail['effectiveness'] as $eff_key=>$eff_val)
                    {

                        //Check if Therapy ID and condition is processed or not
                        $checkExRefCount = therapyReference::where('conditionName',$eff_val['condition'])->where('therapyId',$therapy['id'])->whereIn('referenceNumber',$eff_val['reference-numbers'])->count();
                        
                        if($checkExRefCount == count(array_unique($eff_val['reference-numbers']))){

                            // Log for reference numbers
                            Helper::storeLogs('php artisan therapy:reference',"apiID ".$therapy['apiID'].", Skip if all reference numbers already\n exist in therapy reference table",'All exist, so skip',$type,$startCron,Carbon::now()); 
                            continue;
                        }


                        //Get the reference numbers and convert it to comma separated values
                        if(!empty($eff_val['reference-numbers']))
                        {
                            $reference_numbers = implode(",", $eff_val['reference-numbers']);
                        }
                        else
                        {
                            // Log for reference numbers not found
                            Helper::storeLogs('php artisan therapy:reference','Fetch effectiveness details for therapy id :'.$therapy['id'],'Reference number not found',$type,$startCron,Carbon::now()); 

                            //If no reference number then continue on next
                            $reference_numbers = '';
                            continue;
                        }

                        //API call to get the reference details based on API reference number
                        $headers3 = [
                            'Content-Type' => 'application/json',
                            'X-API-Key' => env("X_API_Key", "fec509d0791cb9845cbed9ced253485ce09b27948075acb08ab481430ebe2d33"),
                        ];
                        
                        $client3 = new \GuzzleHttp\Client([
                            'headers' => $headers3
                        ]);
                        
                        $response3 = $client3->request('GET', env("REFERENCE_API_URL", "https://api.therapeuticresearch.com/nm/references?numbers=").$reference_numbers);

                        // Log for reference numbers from the therapy apiID
                        Helper::storeLogs('php artisan therapy:reference','https://api.therapeuticresearch.com/nm/references?numbers='.$reference_numbers,'API called for reference numbers',$type,Carbon::now(),Carbon::now()); 

                        //Check if API response there
                        if($response3->getStatusCode() == 200){
                            $reference_detail = json_decode($response3->getBody(), true);
                            
                            // Log for reference numbers response received
                            Helper::storeLogs('php artisan therapy:reference','https://api.therapeuticresearch.com/nm/references?numbers='.$reference_numbers,'Got success response',$type,Carbon::now(),Carbon::now()); 
                        
                            foreach($reference_detail as $ref_key=>$ref_val)
                            {
                                // Checked details available or not if avaiable than update data other wise insert data
                                $checkRefCount = therapyReference::where('referenceId',$ref_val['id'])->where('therapyId',$therapy['id'])->where('conditionName',$eff_val['condition'])->count();
                                if($checkRefCount > 0){
                                    //Update data in table
                                    $saveData = therapyReference::where('referenceId',$ref_val['id'])
                                                        ->update(['conditionName' => $eff_val['condition'], 'referenceDescriptione' => $ref_val['description'], 'medicalPublicationId' => $ref_val['medical-publication-id'], 
                                                        'referenceApiResponse' => json_encode($ref_val),
                                                        'updated_at' => Carbon::now()]);
                                    $message = 'Updated details of Reference Id: '.$ref_val['id'];
                                }
                                else
                                {
                                    //Insert data in tables 
                                    $therapyReference = new therapyReference();
                                    $therapyReference->therapyId = $therapy['id'];
                                    $therapyReference->conditionName = $eff_val['condition'];
                                    $therapyReference->referenceId = $ref_val['id'];  
                                    $therapyReference->referenceNumber = $ref_val['number'];  
                                    $therapyReference->referenceDescriptione = $ref_val['description'];  
                                    $therapyReference->medicalPublicationId = $ref_val['medical-publication-id']; 
                                    $therapyReference->referenceApiResponse = json_encode($ref_val); 
                                    $therapyReference->created_at = Carbon::now(); 
                                    $therapyReference->updated_at = Carbon::now(); 
                                    $saveData = $therapyReference->save();   

                                    $message = 'Inserted details of Reference Id: '.$ref_val['id'];
                                }

                                if ($saveData) {
                                    Therapy::where('id', $therapy['id'])->update(['isReferenceProcessed' => 1,'referenceProcessedAt' => $currentDate]);

                                    // Log for reference numbers details updated/inserted into the therapy reference table
                                    Helper::storeLogs('php artisan therapy:reference', 'DB operation', $message, $type, Carbon::now(), Carbon::now()); 
                                }
                            } 
                        } else {
                            // Store logs
                            $url = env("REFERENCE_API_URL", "https://api.therapeuticresearch.com/nm/references?numbers=").$reference_numbers;
                            $requestData = $reference_numbers;
                            $responseData= "Something went wrong with Reference API call";
                            $endCron = Carbon::now();
                            Helper::storeLogs($url, $requestData, $responseData, $type, $startCron, $endCron);
                        }    
                    }

                } else {
                    // Store logs
                    $url = env("EFFECTIVE_API_URL", "https://api.therapeuticresearch.com/nm/monographs/").$therapy['apiID'];
                    $requestData = $therapy['apiID'];
                    $responseData= "Error in API call";
                    $endCron = Carbon::now();
                    Helper::storeLogs($url, $requestData, $responseData, $type, $startCron, $endCron);
                }
            }  
            
            // Cron ended 
            Helper::storeLogs('php artisan therapy:reference', 'Therapy Reference Cron ended', '', $type, $startCron, Carbon::now()); 

        }catch (\Exception $e){
            echo 'Message: ' .$e->getMessage();
            // Store logs
            $url = env("EFFECTIVE_API_URL", "https://api.therapeuticresearch.com/nm/monographs/");
            $requestData = "No request data";
            $responseData= $e->getMessage();
            $startCron = Carbon::now();
            $endCron = Carbon::now();
            $type = 1;
            Helper::storeLogs($url, $requestData, $responseData, $type, $startCron, $endCron);
        }
    }
}

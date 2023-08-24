<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Therapy;
use App\Models\TherapyDetails;
use Carbon\Carbon;
use App\Helpers\Helpers as Helper;

class getTherapyDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'therapy:details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get therapy details and update in database table';

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

        $startCron = Carbon::now();
        $type=1;
        
        // Cron started 
        Helper::storeLogs('php artisan therapy:details', 'Therapy Details Cron started', '', $type, $startCron, '');

        // Get current date
        $currentDate = date('Y-m-d');

        // Get the current day of the month
        $currentDay = date('d');

        // Check if it's the first day of the month (day = 01)
        if ($currentDay === '01') {
            // The first day of the month. Query execute;
            $updateIsProcessed = Therapy::where('id', '>', '0')->update(['isProcessed' => '0']);

            if (!empty($updateIsProcessed)) {
                // Update therapy isProcessed to 0 when processedAt value if greater than current date
                Helper::storeLogs('php artisan therapy:details', $currentDate, 'Updated therapy isProcessed to 0', $type, Carbon::now(), '');
            }
        }
       
        // Get therapy name
        $getTherapy = Therapy::select('therapy', 'id', 'apiID')->where('isProcessed', 0)->orderBy('id', 'ASC')->limit(5)->get()->toArray();
       
        if (isset($getTherapy) && !empty($getTherapy)) {
            
            // Cron started for therapy found 
            Helper::storeLogs('php artisan therapy:details', 'Fetch therapy details', 'Got therapy details from therapy table', $type, Carbon::now(), '');

            foreach ($getTherapy as $therapy) {
                if (!empty($therapy)) {
                    try{

                        if ($therapy['apiID']=='0') {

                            Therapy::where('id', $therapy['id'])->update(['isProcessed' => 1, 'processedAt' => $currentDate]);

                            // CRON log API called
                            Helper::storeLogs('php artisan therapy:details', 'https://api.therapeuticresearch.com/nm/patient-handouts/'.$therapy['apiID'], 'apiID is 0 for therapy id '.$therapy['id'], $type, Carbon::now(), Carbon::now());

                            continue;
                        }

                        //API call to get general Monograph details Start
                        $headers = [
                            'Content-Type' => 'application/json',
                            'X-API-Key' => env("X_API_Key", "fec509d0791cb9845cbed9ced253485ce09b27948075acb08ab481430ebe2d33"),
                        ];
                        
                        $client = new \GuzzleHttp\Client(['headers' => $headers]);

                        $response = $client->request('GET', env("API_URL", "https://api.therapeuticresearch.com/nm/patient-handouts/").$therapy['apiID']);
                        //API call to get general Monograph details End

                        // CRON log therapy monograph API called
                        Helper::storeLogs('php artisan therapy:details', 'https://api.therapeuticresearch.com/nm/patient-handouts/'.$therapy['apiID'], 'Monograph details API called', $type, Carbon::now(), '');

                        $therapy_detail = array();
                        
                        try{
                            //API call to get Effective Monograph details Start
                            $headers2 = [
                                'Content-Type' => 'application/json',
                                'X-API-Key' => env("X_API_Key", "fec509d0791cb9845cbed9ced253485ce09b27948075acb08ab481430ebe2d33"),
                            ];
                            
                            
                            $client2 = new \GuzzleHttp\Client(['headers' => $headers2]);
                    
                            $response2 = $client2->request('GET', env("EFFECTIVE_API_URL", "https://api.therapeuticresearch.com/nm/monographs/").$therapy['apiID']);
                            //API call to get Effective Monograph details End

                            // CRON log therapy effective monograph API called
                            Helper::storeLogs('php artisan therapy:details', 'https://api.therapeuticresearch.com/nm/monographs/'.$therapy['apiID'], 'Effective Monograph API called', $type, Carbon::now(), '');

                            //Check if General Monograph API response there
                            if ($response->getStatusCode() == 200) {
                                //Check if API response there
                                if ($response2->getStatusCode() == 200) {
                                
                                    $therapy_detail = json_decode($response->getBody(), true);
                                    $effective_detail = json_decode($response2->getBody(), true);
                                    
                                    // CRON log API store therapy monograph json records
                                    Helper::storeLogs('php artisan therapy:details', 'https://api.therapeuticresearch.com/nm/patient-handouts/'.$therapy['apiID'], "General Monograph details records found", $type, Carbon::now(), '');


                                    // CRON log API store therapy effective monograph json records
                                    Helper::storeLogs('php artisan therapy:details', 'https://api.therapeuticresearch.com/nm/monographs/'.$therapy['apiID'], "Effective Monograph details records found", $type, Carbon::now(), '');

                                    if (!empty($therapy_detail)) {
                                    
                                        // Update therapy id
                                        Therapy::where('id', $therapy['id'])->update(['updated_at' => Carbon::now()]);

                                        // CRON log API update the therapy table of updated_at value for given therapy id
                                        Helper::storeLogs('php artisan therapy:details', 'Therapy Id '.$therapy['id'], "Updated updated_at field value", $type, Carbon::now(), '');
                                    

                                        $message = '';
                                        
                                        // Checked details available or not if avaiable than update data other wise insert data
                                        $checkDetailsCount = TherapyDetails::where('therapyId', $therapy['id'])->count();
                                    
                                        if ($checkDetailsCount > 0) {
                                            $saveData = TherapyDetails::where('therapyId', $therapy['id'])
                                                    ->update(['therapyDetail' => json_encode($therapy_detail), 'effectiveDetail' => json_encode($effective_detail), 'therapyReviewedAt' => date('Y-m-d H:i:s', strtotime($therapy_detail['reviewed-at'])), 'therapyUpdatedAt' => date('Y-m-d H:i:s', strtotime($therapy_detail['updated-at'])), 'updated_at' => Carbon::now()]);

                                            $message = 'Data Updated in therapy details table';
                                        } else {
                                        
                                            // Save data in tables    
                                            $therapyDetails = new TherapyDetails();
                                            $therapyDetails->therapyId = $therapy['id'];
                                            $therapyDetails->therapyDetail = json_encode($therapy_detail);
                                            $therapyDetails->effectiveDetail = json_encode($effective_detail);  
                                            $therapyDetails->therapyReviewedAt = (!empty($therapy_detail['reviewed-at']) && $therapy_detail['reviewed-at'] != null || $therapy_detail['reviewed-at'] != 'null')?date('Y-m-d H:i:s', strtotime($therapy_detail['reviewed-at'])):null;
                                            $therapyDetails->therapyUpdatedAt = (!empty($therapy_detail['updated-at']) && $therapy_detail['updated-at'] != null || $therapy_detail['updated-at'] != 'null')?date('Y-m-d H:i:s', strtotime($therapy_detail['updated-at'])):null; 
                                            $therapyDetails->created_at = Carbon::now(); 
                                            $therapyDetails->updated_at = Carbon::now(); 
                                            $saveData = $therapyDetails->save();

                                            $message = 'Data Inserted in therapy details table';
                                        }

                                        if ($saveData) {
                                            $updateTherapyDetails = ['isProcessed' => 1,'processedAt' => $currentDate];
                                            Therapy::where('id', $therapy['id'])->update($updateTherapyDetails);

                                            // CRON log API to get save/update given therapy id in therapy details table
                                            Helper::storeLogs('php artisan therapy:details', 'Therapy Id '.$therapy['id'], $message."\n".json_encode($updateTherapyDetails, true), $type, Carbon::now(), Carbon::now());
                                        }

                                    } else {
                                        // Store logs
                                        $url = env("API_URL", "https://api.therapeuticresearch.com/nm/patient-handouts/").$therapy['apiID'];
                                        $requestData = 'Therapy apiID: ' .$therapy['apiID'];
                                        $responseData=json_encode($response);
                                        $endCron = Carbon::now();
                                        Helper::storeLogs($url, $requestData, $responseData, $type, $startCron, $endCron);   
                                    }
                                } else {
                                    // Store logs
                                    $url = env("EFFECTIVE_API_URL", "https://api.therapeuticresearch.com/nm/monographs/").$therapy['apiID'];
                                    $requestData = 'Therapy apiID: '.$therapy['apiID'];
                                    $responseData=$response2;
                                    $endCron = Carbon::now();
                                    Helper::storeLogs($url, $requestData, $responseData, $type, $startCron, $endCron);   
                                }  
                            } else {
                                // Store logs
                                $url = env("API_URL", "https://api.therapeuticresearch.com/nm/patient-handouts/").$therapy['apiID'];
                                $requestData = 'Therapy apiID: '.$therapy['apiID'];
                                $responseData=$response;
                                $endCron = Carbon::now();
                                Helper::storeLogs($url, $requestData, $responseData, $type, $startCron, $endCron);
                            }     
                            
                        }catch (\Exception $e){
                            $url = 'php artisan therapy:details';
                            $requestData = 'Therapy apiID: '.$therapy['apiID'];
                            $responseData=$e;
                            $endCron = Carbon::now();
                            Helper::storeLogs($url, $requestData, $responseData, $type, $startCron, $endCron);

                            // Mark it as processed to process other data
							$updateTherapyDetails = ['isProcessed' => 2,'processedAt' => $currentDate];
                            Therapy::where('id', $therapy['id'])->update($updateTherapyDetails);
                        }        
                    }catch (\Exception $e){
                        $url = 'php artisan therapy:details';
                        $requestData = 'Therapy apiID: '.$therapy['apiID'];
                        $responseData=$e;
                        $endCron = Carbon::now();
                        Helper::storeLogs($url, $requestData, $responseData, $type, $startCron, $endCron);   
                        
                        // Mark it as processed to process other data
						$updateTherapyDetails = ['isProcessed' => 2,'processedAt' => $currentDate];
                        Therapy::where('id', $therapy['id'])->update($updateTherapyDetails);

                    }
                }
            }
           
            
        } else {
            $url = 'php artisan therapy:details';
            $requestData = '';
            $responseData='Therapy Not found';
            $endCron = Carbon::now();
            Helper::storeLogs($url, $requestData, $responseData, $type, $startCron, $endCron);   
        }

        // Cron ended 
        Helper::storeLogs('php artisan therapy:details', 'Therapy Details Cron ended', '', $type, $startCron, Carbon::now());  
        
    }
}

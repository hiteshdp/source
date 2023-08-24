<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Drugs;
use Carbon\Carbon;
class importDrugsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:drugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports drugs data from TRC API into drugs table';

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
}

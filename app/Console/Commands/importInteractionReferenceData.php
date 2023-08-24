<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Psr7;
use App\Models\NaturalMedicineReference;
use GuzzleHttp\Exception\ClientException;

class importInteractionReferenceData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:interactionReferenceData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inserts/Updates reference number of interaction details along with its content in natural_medicine_reference table';

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
        //API call to get reference data from TRC Start
        $headers = [
            'Content-Type' => 'application/json',
            'X-API-Key' => env("X_API_Key", "fec509d0791cb9845cbed9ced253485ce09b27948075acb08ab481430ebe2d33"),
        ];
        
        $client = new \GuzzleHttp\Client([
            'headers' => $headers
        ]);

        // API call to get of response for reference details from TRC API
        $response = $client->request('GET', "https://api.therapeuticresearch.com/nm/references?page=1&limit=5000000");

        // Check 200 response
        if($response->getStatusCode() == 200){
            $reference_detail_api_response = json_decode($response->getBody(), true);
            if(!empty($reference_detail_api_response)){
                
                $reference_detail_array = $reference_detail_api_response;
                foreach($reference_detail_array as $reference_detail_array_key => $reference_detail_array_value){
                    
                    // Check if reference id is not added in DB then add, else update the existing data
                    $referenceTable = NaturalMedicineReference::updateOrCreate([
                        'referenceId' => $reference_detail_array_value['id']
                    ],[
                        'number' => $reference_detail_array_value['number'],
                        'description' => $reference_detail_array_value['description'],
                        'medicalPublicationId' => $reference_detail_array_value['medical-publication-id'] !== '' || $reference_detail_array_value['medical-publication-id'] !== null || !empty($reference_detail_array_value['medical-publication-id']) ? $reference_detail_array_value['medical-publication-id'] : null,
                        'referenceApiResponse' => json_encode($reference_detail_array_value,true),
                        'updated_at' => Carbon::now(),
                    ]);
                   
                }                
                
            }
        }
    }
}

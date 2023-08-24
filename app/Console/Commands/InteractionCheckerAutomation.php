<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\Helpers as Helper;
use App\Models\DrugsInteractions;
use DB;

class InteractionCheckerAutomation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interactionChecker:automation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comamand to run the interaction checker cronjob';

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
     * Execute the console command - php artisan interactionChecker:automation
     * Automation test function to verify/check interaction data from csv file to database data and generate report with pass/fail status with fail reason if any records get fail with any reason from different scenarios.
     * @return int
    */
    public function handle()
    {
        // Get file from public folder
        $file = public_path('import/interactions.csv');

        // Call helper function for read csv file
        $customerArr = Helper::csvToArray($file);

        $data = array();
        for ($i = 0; $i < count($customerArr); $i ++)
        {

            // Get single column value from CSV file and store in variables
            $therapy = trim($customerArr[$i]['monograph-name']);
            $naturalMedicineApiId = $customerArr[$i]['monograph-id'];
            $drugName = trim($customerArr[$i]['drug-name']);
            $brandName = trim($customerArr[$i]['brand-name']);
            $drugApiId = $customerArr[$i]['drug-id'];
            $ratingLabel = $customerArr[$i]['rating-label'];
            $count = $customerArr[$i]['count'];
            

            // Checked and compare data
            $checkData = DrugsInteractions::select('drugs_interactions.interactionDetails','drugs_interactions.drugApiId','drugs.brand_name','drugs.name as drugs_name','drugs_interactions.naturalMedicineApiId','therapy.therapy')
                        ->join('therapy','drugs_interactions.naturalMedicineId','=','therapy.id')
                        ->join('drugs','drugs_interactions.drugId','=','drugs.id')
                        ->where('drugs_interactions.drugApiId',$drugApiId)
                        ->where(DB::raw('TRIM(lower(drugs.brand_name))'), strtolower($brandName))
                        ->where(DB::raw('TRIM(lower(drugs.name))'), strtolower($drugName))
                        ->where('drugs_interactions.naturalMedicineApiId',$naturalMedicineApiId)
                        ->where(DB::raw('TRIM(lower(therapy.therapy))'), strtolower($therapy))
                        ->whereNull('drugs_interactions.deleted_at')->get()->toArray();
            // Variable declaration with value. If data is not found then store result with status             
            $result = 'Fail';
            $failedReason = '';
            // Checked data found of not
            if(isset($checkData) && !empty($checkData)){
                // If data found that means data is match so set pass in result
                $result = 'Pass';

                foreach($checkData as $chk){
                    // Checked interactionDetails found or not
                    if(isset($chk['interactionDetails']) && !empty($chk['interactionDetails'])){
                        // Json Decoded 
                        $interactionDetails = json_decode($chk['interactionDetails']);
                        // Convert object to array
                        $interactionDetails =(array)$interactionDetails;
                        // Compare rating-label  
                        if(trim(strtolower($interactionDetails['rating-label'])) == strtolower($ratingLabel)){
                            $result = 'Pass';
                            // reset reason flag
                            $failedReason = '';
                            break;
                        }else{
                            $result = 'Fail';
                            $failedReason = 'Rating-label not found';
                        }
                    }else{
                        // If rating-label is column blank then checked directory with column 
                        if($checkData->interactionDetails == $ratingLabel){
                            $result = 'Pass';
                            // reset reason flag
                            $failedReason = '';
                            break;
                        }else{
                            $result = 'Fail';
                            $failedReason = 'Rating-label not found';
                        }
                    }

                }

                $ratingLabelCount = 0;
                // Logic for check rating label count
                foreach($checkData as $countCheck){
                    if(isset($countCheck['interactionDetails']) && !empty($countCheck['interactionDetails'])){
                        // Json Decoded 
                        $interactionDetailsCount = json_decode($countCheck['interactionDetails']);
                        // Convert object to array
                        $interactionDetailsCount =(array)$interactionDetailsCount;
                        // Compare rating-label  
                        if(trim(strtolower($interactionDetailsCount['rating-label'])) == strtolower($ratingLabel)){
                            $ratingLabelCount++;
                        }
                    }
                }
                
               // Compare count 
                if($ratingLabelCount == $count){
                    // Reset flag
                    $ratingLabelCount = 0;
                    $result = 'Pass';
                }else{
                    $result = 'Fail';
                    $failedReason = 'Rating-label count does not matched';
                }
            }else{
                // Checked failed reason
                $checkData = DrugsInteractions::select('drugs_interactions.naturalMedicineId','drugs_interactions.drugId')
                            ->where('drugs_interactions.drugApiId',$drugApiId)
                            ->where('drugs_interactions.naturalMedicineApiId',$naturalMedicineApiId)
                            ->first();
                if(isset($checkData) && !empty($checkData)){
                    // Checked therapy
                    $checkTherapy = DB::table('therapy')
                                    ->where('id',$checkData->naturalMedicineId)
                                    ->where(DB::raw('TRIM(lower(therapy.therapy))'),strtolower($therapy))
                                    ->count();
                    if($checkTherapy == 0){     
                        $failedReason = 'Therapy not found';
                    }

                    // Checked brand name and drug name failed reason
                    $checkDrug = DB::table('drugs')->select('name','brand_name')->where('id',$checkData->drugId)->first();
                    if(isset($checkDrug) && !empty($checkDrug)){
                        if(trim($checkDrug->name) != $drugName){
                            $failedReason = 'Drugs name not found';
                        }

                        if(trim($checkDrug->brand_name) != $brandName){
                            $failedReason = 'Brand name not found';
                        }
                    }else{
                        $failedReason = 'Drugs name or brand name not found';
                    }

                }else{
                    $failedReason = 'Monograph id or drug id not found';
                }
            } 

            $tempData['therapy'] = $therapy;
            $tempData['naturalMedicineApiId'] = $naturalMedicineApiId;
            $tempData['drugName'] = $drugName;
            $tempData['brandName'] = $brandName;
            $tempData['drugApiId'] = $drugApiId;
            $tempData['ratingLabel'] = $ratingLabel;
            $tempData['count'] = $count;
            $tempData['result'] = $result;
            $tempData['failedReason'] = $failedReason;
            
            // DB::table('tmp_interaction_checker_automation')->insert($insertData);
            array_push($data,$tempData);
        }

        if(isset($data) && !empty($data)){
            if(file_exists(public_path('import/interaction_checker_output/result.csv'))){
                // Unlink old file
                unlink(public_path('import/interaction_checker_output/result.csv'));
            }
            $resultFile = public_path('import/interaction_checker_output/result.csv');
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=result.csv');
            $output = fopen($resultFile, 'a');    
            fputcsv($output, array('monograph-name', 'monograph-id', 'drug-name', 'brand-name','drug-id','rating-label','count','Result','Failed-reason'));
            foreach ($data as $row) {
                $row = (array)$row;
                fputcsv($output, $row);
            }
        }
        exit;
    }
}

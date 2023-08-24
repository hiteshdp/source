<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Models\Usertherapy;
use App\Models\UserTherapyHistory;
use App\Models\Therapy;
use App\Models\Condition;
use App\Models\Master;
use App\Models\User;
use App\Helpers\Helpers as Helper;
use App\Models\TherapyCondition;
use App\Models\TherapyDetails;
use Carbon\Carbon;
use DB;

class TherapyConditionImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'therapy:condition-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import condition from the therapy records via TRC API';

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
        $cronStart = Carbon::now();

        // Cron started 
        Helper::storeLogs('php artisan therapy:condition-import', 'Therapy Condition Cron started', '', 1, Carbon::now(), '');

        // Get the current day of the month
        $currentDay = date('d');

        // Check if it's the first day of the month (day = 01)
        if ($currentDay === '01') {
            // The first day of the month. Query execute;
            // Reset isProcessed 0 if not getting in database than reset all data with 0
            $updateIsProcessed = TherapyDetails::where('id', '>', '0')->update(['isProcessed' => '0']);

            if (!empty($updateIsProcessed)) {
                // Update therapy isProcessed to 0 when processedAt value if greater than current date
                Helper::storeLogs('php artisan therapy:condition-import', $cronStart, 'Updated therapy isProcessed to 0', $type, Carbon::now(), '');
            }
        }

        
        $allTherapyDetails = TherapyDetails::orderBy('id', 'ASC')->where('isProcessed', 0)->limit(5)->get()->toArray();

        $duplicateConditionNames = [];
        $emptyConditionIds = [];
        foreach ($allTherapyDetails as $therapy_key => $therapy_value) {
            
            $therapyId = $therapy_value['therapyId'];
            $therapyTableId = $therapy_value['id'];

            $conditionDetails = json_decode($therapy_value['effectiveDetail'], true);
            
            // Log for condition details found
            Helper::storeLogs('php artisan therapy:condition-import', 'Check for therapy '.$therapyId.' effective details', '', 1, Carbon::now(), ''); 


            // If condition details is not empty then insert the details
            if (!empty($conditionDetails)) {
                
                // Log for therapy effective details found
                Helper::storeLogs('php artisan therapy:condition-import', 'Check for therapy '.$therapyId.' effective details', 'Effective details exist', 1, Carbon::now(), ''); 

                $i = 0;

                // Get condition details from effectiveness array
                foreach ($conditionDetails['effectiveness'] as $key => $value) {
                    $i++;
                    $conditionName = $value['condition'];
                    if (!empty($conditionName)) {
                        // Changes the effectiveness label 
                        $effectiveness = strtoupper($value['rating-description']);
                        if ($effectiveness == 'INSUFFICIENT RELIABLE EVIDENCE TO RATE') {
                            $effectiveness = 'INSUFFICIENT RELIABLE EVIDENCE to RATE';
                        }

                        // Check if condition name exist in the condition table, if not then insert
                        $conditionNameLowerCase = strtolower($conditionName);
                        $conditionTable = Condition::whereRaw('LOWER(conditionName) = ?', $conditionNameLowerCase);
                        $sameConditionNameCount = $conditionTable->count();
                        if ($sameConditionNameCount == 0) {
                            // Add canonical name by replacing space to dash
                            $canonicalName = preg_replace('/\s+/', '-', $conditionNameLowerCase);

                            //Add new condition to table
                            $condition_detail = new Condition();
                            $condition_detail->conditionName = $conditionName;
                            $condition_detail->canonicalName = $canonicalName;
                            $condition_detail->created_at = Carbon::now(); 
                            $condition_detail->updated_at = Carbon::now(); 
                            $condition_detail->save(); 

                            if (!empty($condition_detail->id)) {
                                // Log for condition name  found
                                Helper::storeLogs('php artisan therapy:condition-import', 'Inserted New condition :'.$conditionName, 'Success', 1, Carbon::now(), ''); 
                            }
        
                            // Check therapy condition and insert if not exist
                            $TherapyCondition = TherapyCondition::where('conditionId', $condition_detail->id)
                                                                ->where('therapyId', $therapyId)->first();
                            if ($TherapyCondition === null) {
                                //Add new Therapy condition to condition to table
                                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                                $TherapyCondition = new TherapyCondition();
                                $TherapyCondition->conditionId = $condition_detail->id;
                                $TherapyCondition->therapyId = $therapyId;
                                $TherapyCondition->effectiveness = $effectiveness;
                                $TherapyCondition->created_at = Carbon::now(); 
                                $TherapyCondition->updated_at = Carbon::now(); 
                                $TherapyCondition->save(); 
                                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                                // Log for condition and therapy mapping
                                Helper::storeLogs('php artisan therapy:condition-import', 'Inserted condition :'.$conditionName.', and therapy (therapyId '.$therapyId.') condition mapping', 'Success', 1, Carbon::now(), '');         
                            }
                            // Update isProcessed to 1 when therapy condition is imported successfully 
                            TherapyDetails::where('id', $therapyTableId)->update(['isProcessed'=>'1']);

                            // Log for update isProcessed to 1 
                            Helper::storeLogs('php artisan therapy:condition-import', 'TherapyDetails ID : '.$therapyTableId.' of column isProcessed update to 1', 'Success', 1, Carbon::now(), ''); 
        
                        } else {
        
                            // if same condition then check if current therapyId is associated with conditionId, then insert record 
                            $sameConditionNameId = $conditionTable->select('id')->get()->first()->id;
                            $TherapyCondition = TherapyCondition::where('conditionId',$sameConditionNameId)
                                                                ->where('therapyId',$therapyId)->first();
        
                            //Add new Therapy condition to condition to table 
                            if(empty($TherapyCondition)){
                                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                                $TherapyCondition = new TherapyCondition();
                                $TherapyCondition->conditionId = $sameConditionNameId;
                                $TherapyCondition->therapyId = $therapyId;
                                $TherapyCondition->effectiveness = $effectiveness;
                                $TherapyCondition->created_at = Carbon::now(); 
                                $TherapyCondition->updated_at = Carbon::now(); 
                                $TherapyCondition->save(); 
                                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                                
                                $i++;
                                TherapyDetails::where('id',$therapyTableId)->update(['isProcessed'=>'1']);
        
                                // Log for condition name not found
                                Helper::storeLogs('php artisan therapy:condition-import','Inserted therapy condition (therapy '.$therapyId.' & condition '.$sameConditionNameId.') mapping','Success',1,Carbon::now(),''); 

                            }else{
                                // Get the duplicate condition name that is imported
                                $duplicateConditionNames[] = $conditionName;
                                TherapyDetails::where('id',$therapyTableId)->update(['isProcessed'=>'2']);

                                // Log for condition name duplicate found and not inserted
                                Helper::storeLogs('php artisan therapy:condition-import','Not inserted therapy condition (therapy '.$therapyId.' & condition '.$sameConditionNameId.') mapping', 'Duplicate found', 1, Carbon::now(), ''); 
                            }
                        }
                    }else{

                        // Get the empty condition name ids that are not imported
                        $emptyConditionIds[] = $therapyTableId;
                        TherapyDetails::where('id', $therapyTableId)->update(['isProcessed'=>'3']);


                        // Log for condition name not found
                        Helper::storeLogs('php artisan therapy:condition-import', 'Checked for condition name', 'TherapyDetails ID: '.$therapyTableId.' it is empty', 1, Carbon::now(), ''); 
                    }
                  
                }
            } else {
                // Get the empty condition details ids that are not imported
                $emptyConditionIds[] = $therapyTableId;
                TherapyDetails::where('id', $therapyTableId)->update(['isProcessed'=>'3']);

                // Log for condition name not found
                Helper::storeLogs('php artisan therapy:condition-import', 'TherapyDetails ID: '.$therapyTableId.' Checked for condition name', 'it is empty', 1, Carbon::now(), ''); 
            }
            
            
        }
        // If there are records having isProcessed 0 then show imported
        if (!empty($allTherapyDetails)) {

            // Log for number of conditions imported
            Helper::storeLogs('php artisan therapy:condition-import', 'Imported Conditions', $i." imported", 1, Carbon::now(), ''); 

            $importRemaining = TherapyDetails::orderBy('id')->where('isProcessed', 0)->count();
            $allRecords = TherapyDetails::orderBy('id')->count();

            // Log for number of conditions imported
            Helper::storeLogs('php artisan therapy:condition-import', $importRemaining." import remaining out of ".$allRecords." records", "success", 1, date('Y-m-d H:i:s'), ''); 

        } else {
            // Log for there are no records having isProcessed 0
            Helper::storeLogs('php artisan therapy:condition-import', 'All imported successfully, Nothing to import', "", 1, Carbon::now(), ''); 
        }

        // Cron started 
        Helper::storeLogs('php artisan therapy:condition-import', 'Therapy Condition Cron ended', '', 1, $cronStart, Carbon::now()); 
        

        exit;
       
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Helpers\Helpers as Helper;
use DB;

class UpdateMajorToModerateSeverity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:severity';

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
        // Store default success value to 0
        $updateSuccess = 0;
        // Get all the major severity ids
        $allMajorSeverityIds = DB::table('severity')->where('severityLabel','Major')->get()->pluck('id')->toArray(); 
        // Fetch all the event data tracked of major severity ids
        $allEvents = DB::table('event_symptoms')->whereIn('severityId',$allMajorSeverityIds)->get()->toArray(); 
        // Check if data exist for all major severity tracked
        if(!empty($allEvents)){
            foreach($allEvents as $value){
                // Get Moderate Severity id for the selected symptom
                $getModerateSeverityId = DB::table('severity')->where('symptomId',$value->symptomId)
                ->where('severityLabel','Moderate')->get()->pluck('id')->first();
    
                // Begin a transaction
                DB::beginTransaction();
                
                // Update the event tracked of major severity to moderate
                $updateSeverity = DB::table('event_symptoms')->where('id',$value->id)
                ->update([
                    'severityId' => $getModerateSeverityId
                ]);
                
                // Commit the transaction
                DB::commit();
                
                // Increment the value based on successfull update records
                $updateSuccess++;
            }

            echo "Updated ".$updateSuccess. "\n";

        }else{
            echo "No Major Severity Data Recorded\n";
        }

       
       
        
    }
}

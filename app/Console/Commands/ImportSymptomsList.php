<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\Helpers as Helper;
use Carbon\Carbon;
use DB;
use App\Models\Symptom;
use App\Models\Severity;
use App\Models\UserSymptoms;
use App\Models\ConditionSymptom;

class ImportSymptomsList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:symptoms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will import the symptoms list from the csv file into the symptom table with dependent records';

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
        // Get file from public folder
        $file = public_path('import/symptomslist.csv');

        // Call helper function for read csv file
        $symptomArr = Helper::csvToArray($file);

        // Store the count of error data while saving symptom name, default value is 0
        $errorData = 0;
        // Store the count of duplicate symptom name from the CSV file before saving the symptom name, default value is 0
        $duplicateData = 0;
        // Store the count of empty symptom name from the CSV file before saving symptom name, default value is 0
        $emptyNameData = 0;
        // Store the count of successfully added symptom name from the CSV file after saving symptom name, default value is 0
        $successData = 0;

        for ($i = 0; $i < count($symptomArr); $i ++)
        {

            $symptomName = ltrim(rtrim($symptomArr[$i]['symptomName']));
            $symptomSubText = $symptomArr[$i]['symptomSubText'] ? $symptomArr[$i]['symptomSubText'] : null;
            $symptomIcon = $symptomArr[$i]['symptomIcon'] ? $symptomArr[$i]['symptomIcon'] : null;

            $defaultIcon = 'default_icon.png';

            // Check if the symptom name is empty then skip the current data from the array and process further data
            if(empty($symptomName)){
                // Increment the value to get the count of empty symptom name data
                $emptyNameData++;
                // Continue to next data from the array and skip current data from the loop
                continue;
            }

            // Check if already existing symptom, then skip to insert the data
            $checkExistSymptomName = Symptom::where('symptomName',$symptomName)->count();
            if($checkExistSymptomName != 0){
                // Increment the value to get the count of duplicate symptom name data
                $duplicateData++;
                // Continue to next data from the array and skip current data from the loop
                continue;
            }

            // Begin the SQL Query Transaction
            DB::beginTransaction();
            
            // Insert into symptom table
            $symptomData = new Symptom();
            $symptomData->symptomName = $symptomName;
            $symptomData->symptomIcon = $symptomIcon ? $symptomIcon : $defaultIcon;
            $symptomData->symptomSubText = $symptomSubText;
            $symptomData->created_at = Carbon::now();
            if($symptomData->save()){
                
                // Fetch the symptom id after saving the data
                $symptomId = $symptomData->id;

                // Save condition symptom data
                $conditionSymptom = new ConditionSymptom();
                $conditionSymptom->conditionId = '304';
                $conditionSymptom->symptomId = $symptomId;
                $conditionSymptom->created_at = Carbon::now();
                $conditionSymptom->save();

                $severityData = [
                    '0' => [
                        'severityPriority' => '1',
                        'severityLabel' => 'None',
                        'severityColor' => 'bg-color-one',
                    ],
                    '1' => [
                        'severityPriority' => '2',
                        'severityLabel' => 'Mild',
                        'severityColor' => 'bg-color-tow',
                    ],
                    '2' => [
                        'severityPriority' => '3',
                        'severityLabel' => 'Moderate',
                        'severityColor' => 'bg-color-three',
                    ],
                    '3' => [
                        'severityPriority' => '4',
                        'severityLabel' => 'Major',
                        'severityColor' => 'bg-color-four',
                    ],
                    '4' => [
                        'severityPriority' => '5',
                        'severityLabel' => 'Severe',
                        'severityColor' => 'bg-color-five',
                    ],
                
                ];
                foreach ($severityData as $key => $value) {
                    // Save severity data
                    $severityData = new Severity();
                    $severityData->severityPriority = $value['severityPriority'];
                    $severityData->severityLabel = $value['severityLabel'];
                    $severityData->symptomId = $symptomId;
                    $severityData->severityColor = $value['severityColor'];
                    $severityData->created_at = Carbon::now();
                    $severityData->save();
                }

                // Commit the changes
                DB::commit(); 
                // Increment the value to get the count of successfully symptom name data added
                $successData++;
            }else{
                // Rollback the current SQL query and revert transaction
                DB::rollback(); 
                // Increment the value to get the count of failed to save symptom name data
                $errorData++;
            }
        }

        // Print the response of the each count
        echo "Success Data: [".$successData."]\n Error Data: [".$errorData."]\n Empty Data: [".$emptyNameData."]\n Duplicate Data: [".$duplicateData."] \n";
    }
}

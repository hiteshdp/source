<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Helpers\Helpers as Helper;
use voku\helper\HtmlDomParser;

class TherapyDataTest extends TestCase
{
    /**
     *  A function to read the Therapy data from CSV, get the URLs from canonical name of therapy and fetch the page detils, compare the title and conditon data of CSV file with Page details and generate report with pass/fail status in CSV files
     *
     * @return void
     */
    public function testTherapy()
    {
        // Get file from public folder
        $file = public_path('import/therapyData.csv');

        // Call helper function for read csv file
        $customerArr = Helper::csvToArray($file);

        $data = array();
        $isPass = true;
        for ($i = 0; $i < count($customerArr); $i ++)
        {
            $status = '';
            $failedReason = '';

            // Get single column value from CSV file and store in variables
            $therapy = trim($customerArr[$i]['canonical-url']);
            $title = $customerArr[$i]['title'];
            $conditions = trim($customerArr[$i]['conditions']);

            // Initialize an URL to the variable
            $url = "https://dev.wellkasa.app/therapy/".$therapy;
            
            // Use get_headers() function
            $headers = @get_headers($url);
            
            // Use condition to check the existence of URL
            if($headers && strpos( $headers[0], '200')) {
                $status = 'Pass';
            }else {
                $status = 'Fail';
                $failedReason = "URL Doesn't Exist";
                $isPass = false;
            }
            
            if($status == 'Pass'){
                $html = HtmlDomParser::file_get_html($url);
                if($html!='') {
                    
                    //--------------------------------- Title Comparison Start --------------------------
                    
                        // Get therapy name
                        $therapyNameContext = $html->getElementById('therapyNameContext');
                        $therapyName = $therapyNameContext->textContent;
                        // Convert lower case string 
                        $therapyName = trim(strtolower($therapyName));
                        // Removed all space
                        $therapyName = str_replace(" ","",$therapyName);


                        // Removed space and convert from title which is comming from CSV
                        $checkTitle = trim(strtolower($title));
                        $checkTitle = str_replace(" ","",$checkTitle);


                        if($therapyName == $checkTitle){
                            $status = 'Pass';
                        }else{
                            $status = 'Fail';
                            $failedReason = 'Title Does Not Matched';
                            $isPass = false;
                            
                        }

                    //--------------------------------- Title Comparison End --------------------------

                        

                    //--------------------------------- Condition Comparison Start --------------------- 
                    // If title is failed then no need to check conditions
                        if($status == 'Pass'){ 
                            // Get drodpown value
                            $element =  $html->find('#selectConditionDropDown',0);     
                            $conditionArray = array();
                            foreach($element as $ekey => $elemen) {  
                                if(!empty($elemen->plaintext)){
                                    // Convert lower case string 
                                    $plaintext = trim(strtolower($elemen->plaintext));

                                    // Removed all space
                                    $plaintext = str_replace(" ","",$plaintext);
                                    
                                    // Move element in array
                                    array_push($conditionArray,$plaintext);
                                }        
                            } 

                            // Convert string to array
                            $checkConditions = explode(',', $conditions);

                            // Checked value
                            foreach($checkConditions as $con){
                                // Convert lower case string
                                $conCheck = trim(strtolower($con));

                                // Removed all space
                                $conCheck = str_replace(" ","",$conCheck);
                                
                                // Checked condition avaiable or not in that therapy
                                if (in_array($conCheck, $conditionArray))
                                {
                                    $status = 'Pass';
                                }
                                else
                                {
                                    $status = 'Fail';
                                    $failedReason = $con.' Does Not Matched';
                                    $isPass = false;
                                    break;
                                }
                            }

                        }
                    //--------------------------------- Condition Comparison End ---------------------

                }else{
                    $status = 'Fail';
                    $failedReason = 'Therapy Does Not Found';
                }
            }
            //--------------------------------- Push Result In Array Start ---------------------    
             $tempData['canonical-url'] = $therapy;
             $tempData['title'] = $title;
             $tempData['conditions'] = $conditions;
             $tempData['status'] = $status;
             $tempData['reason'] = $failedReason;
             array_push($data,$tempData);
            //--------------------------------- Push Result In Array End ---------------------
        }

        $fileName = time().'-result.csv';
        if(isset($data) && !empty($data)){
            if(file_exists(public_path('import/therapy_checker_output/'.$fileName))){
                // Unlink old file
                unlink(public_path('import/therapy_checker_output/'.$fileName));
            }
            $resultFile = public_path('import/therapy_checker_output/'.$fileName);
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename='.$fileName);
            $output = fopen($resultFile, 'a');    
            fputcsv($output, array('canonical-url', 'title', 'conditions', 'Status','Reason'));
            foreach ($data as $row) {
                $row = (array)$row;
                fputcsv($output, $row);
            }
        }

        if($isPass){
            $this->assertTrue(true);
        }else{
            $this->assertFalse(true);
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\Helpers as Helper;
use Carbon\Carbon;
use DB;

class importProductRecommendationData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:productsrecommendtions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports product recommendations data from the CSV file into the products_recommendations table';

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
        $file = public_path('import/products_recommendation.csv');

        // Check if file exists then execute below code, else show error message
        if(file_exists($file) == '1'){

            // Call helper function for read csv file
            $productsRecommArr = Helper::csvToArray($file);

            // If file data does exist then execute below code else display error message
            if(!empty($productsRecommArr)){

                // Store the count of error data while saving data, default value is 0
                $errorData = 0;
                // Store the count of duplicate data from the CSV file before saving the data into the table, default value is 0
                $duplicateUpdatedData = 0;
                // Store the count of empty product related data from the CSV file before saving data into the table, default value is 0
                $emptyData = 0;
                // Store the count of successfully added data from the CSV file after saving data, default value is 0
                $successData = 0;

                $therapyIds = array();
                $productIds = array();
                
                for ($i = 0; $i < count($productsRecommArr); $i ++)
                {
                    // Get all the product related data
                    $data['product_id'] = ltrim(rtrim($productsRecommArr[$i]['product_id']));
                    $data['therapy_id'] = ltrim(rtrim($productsRecommArr[$i]['therapy_id']));
                    $data['product_ratings'] = ltrim(rtrim($productsRecommArr[$i]['product_ratings']));
                    $data['product_review_count'] = ltrim(rtrim($productsRecommArr[$i]['product_review_count']));
                    $data['product_price'] = ltrim(rtrim($productsRecommArr[$i]['product_price']));
                    $data['product_url'] = ltrim(rtrim($productsRecommArr[$i]['product_url']));
                    $data['affiliate'] = ltrim(rtrim($productsRecommArr[$i]['affiliate']));
                    $data['rank_order'] = ltrim(rtrim($productsRecommArr[$i]['rank_order']));

                    // Get the count of the data which has values
                    $filteredCount = count(array_filter($data));
                    // Get the count of all the data including empty values
                    $recordsCount = count($data);

                    // If any values are empty from the array then skip current record
                    if($filteredCount != $recordsCount){
                        // error for empty record
                        $emptyData++;
                        continue;
                    }else{

                        // Check the product id exist 
                        $productCheck = DB::table('product')->where('productId',$data['product_id'])->get()->first();
                        // If the product data does not exist then exit code from here with error count
                        if(empty($productCheck)){
                            $productIds[] = $data['product_id'];
                            // error for empty record
                            $errorData++;
                            continue;
                        }

                        // Check the therapy id exist 
                        $therapyCheck = DB::table('therapy')->where('id',$data['therapy_id'])->get()->first();
                        // If the therapy data does not exist then exit code from here with error count
                        if(empty($therapyCheck)){
                            $therapyIds[] = $data['therapy_id'];
                            // error for empty record
                            $errorData++;
                            continue;
                        }

                        // Begin the SQL Query Transaction
                        DB::beginTransaction();
                        
                        // Set current updated date
                        $data['updated_at'] = Carbon::now();

                        // Check existing data with same product_id and therapy_id value
                        $checkExistingData = DB::table('products_recommendations')->where('product_id',$data['product_id'])
                        ->where('therapy_id',$data['therapy_id'])->get()->first();

                        // If the value exist then update current data, else insert data
                        if(!empty($checkExistingData)){

                            $updateProductRecomData = DB::table('products_recommendations')->where('product_id',$data['product_id'])
                            ->where('therapy_id',$data['therapy_id'])->update($data);

                            if($updateProductRecomData){
                                // Commit the changes
                                DB::commit(); 
                                // Increment the value to get the count of successfully data added
                                $duplicateUpdatedData++;
                            }else{
                                // Rollback the current SQL query and revert transaction
                                DB::rollback(); 
                                // Increment the value to get the count of failed to save data
                                $errorData++;
                            }

                        }else{

                            // Set current created date
                            $data['created_at'] = Carbon::now();

                            // Insert into products_recommendations table
                            $productsRecomData = DB::table('products_recommendations')->insert($data);

                            if($productsRecomData){
                                // Commit the changes
                                DB::commit(); 
                                // Increment the value to get the count of successfully data added
                                $successData++;
                            }else{
                                // Rollback the current SQL query and revert transaction
                                DB::rollback(); 
                                // Increment the value to get the count of failed to save data
                                $errorData++;
                            }
                        }

                        
                    }            
                }

                // Print the response of the each count
                echo "Inserted Data: [".$successData."]\n Error Data: [".$errorData."]\n Empty Data: [".$emptyData."]\n Updated Data: [".$duplicateUpdatedData."] \n \n";
                
                // Display the product ids if not empty from array which are not in products table
                if(!empty($productIds)){
                    echo "Below ".count($productIds)." product Ids not found in product table \n";
                    print_r($productIds);
                    echo " \n";    
                }

                // Display the therapy ids if not empty from array which are not in therapy table
                if(!empty($therapyIds)){
                    echo "Below ".count($therapyIds)." therapy Ids not found in therapy table \n";
                    print_r($therapyIds);
                    echo " \n";    
                }              

            }else{
                echo "No data found. File is empty \n";
            }

        }else{
            echo $file." file not found \n";
        }

        
    }
}

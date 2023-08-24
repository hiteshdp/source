<?php

namespace App\Http\Controllers;

use App\Models\Drugs;
use App\Models\DrugsInteractions;
use App\Models\UserInteractionsReport;
use App\Models\UserInteractions;
use App\Models\Therapy;
use App\Models\Usertherapy;
use Illuminate\Http\Request;
use App\Models\TherapyDetails;
use App\Helpers\Helpers as Helper;
use App\Models\TherapyReference;
use App\Models\NaturalMedicineReference;
use App\Models\UserIntegrativeProtocol;
use App\Models\UserIntegrativeProtocolConditions;
use App\Models\Condition;
use App\Models\TherapyCondition;
use App\Models\MedicineCabinet;
use App\Models\MedicineCabinetConditions;
use App\Models\MedicineCabinetNotes;
use App\Models\Master;
use App\Models\ProfileMembers;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use Cookie;
use Session;
use Auth;
use Validator;
use Crypt;
use App\Models\Product;
use App\Models\ProductTherapy;
use App\Models\UserProductOrder;
use Notification;
use App\Notifications\SendSupplementsSuggestionMailNotification;

class MedicineCabinetController extends Controller
{

    /*** 
     * List all Rx drugs and Natural Medicines added by logged in user 
     * or get the profile member id exists then get its medicine data
    */
    public function listMedicineCabinet($profileMemberId=null){

        // Get Logged in user id
        $userId = \Auth::user()->id;

        // Get user email id
        $userEmailId = \Auth::user()->email;

        // get user name
        $userName = Auth::user()->getUserName();

        // Check if profile member id exists then decrypt profile member id and get the profile member user name
        if(!empty($profileMemberId)){
            $profileMemberId = Crypt::decrypt($profileMemberId);
            $userName = ProfileMembers::where('addedByUserId',$userId)->where('id',$profileMemberId)
            ->select(DB::raw('CONCAT(first_name," ",last_name) As name'))->pluck('name')->first();
            // if the profile member id is not of added by current logged in user then display error message
            if(empty($userName)){
                return redirect()->route('medicine-cabinet')->with('error','Profile Member Not Found.');
            }
        }
        $allConditions = array();
        $medicineCabinetNaturalMedicineDataArr = array();
        $medicineCabinetRxDrugsDataArr = array();
        $medicineCabinetProductsDataArr = array();
        $medicineCabinetData = array();
        $medicineCabinetDataExist = MedicineCabinet::where('medicine_cabinet.userId',$userId);
        if(!empty($profileMemberId)){
            $medicineCabinetDataExist = $medicineCabinetDataExist->where('profileMemberId',$profileMemberId);
        }else{
            $medicineCabinetDataExist = $medicineCabinetDataExist->whereNull('profileMemberId');
        }
        $medicineCabinetDataExist = $medicineCabinetDataExist->whereNull('medicine_cabinet.deleted_at')->get()->toArray();
        if(!empty($medicineCabinetDataExist)){

            // Fetch Natural Medicines data
            $medicineCabinetNaturalMedicineData = MedicineCabinet::where('medicine_cabinet.userId',$userId);
            
            // Check if profile member id exist and get the data from it, else exclude profile member data check
            if(!empty($profileMemberId)){
                $medicineCabinetNaturalMedicineData = $medicineCabinetNaturalMedicineData->where('profileMemberId',$profileMemberId);
            }else{
                $medicineCabinetNaturalMedicineData = $medicineCabinetNaturalMedicineData->whereNull('profileMemberId');
            }

            $medicineCabinetNaturalMedicineData = $medicineCabinetNaturalMedicineData->select('medicine_cabinet.id as medicineCabinetId',
            'medicine_cabinet.naturalMedicineId','therapy.therapy as name',
            'therapy.canonicalName as canonicalName',
            'medicine_cabinet.isTaking',
            DB::raw('CONCAT(medicine_cabinet.dosage," ",master.name) As dosage'),
            'medicine_cabinet.created_at')
            ->whereNotNull('naturalMedicineId')
            ->join('therapy','medicine_cabinet.naturalMedicineId','=','therapy.id')
            ->leftJoin('master','medicine_cabinet.dosageType','=','master.id')
            ->orderBy('therapy.therapy')
            ->whereNull('medicine_cabinet.deleted_at')
            ->get()->toArray();
            if(!empty($medicineCabinetNaturalMedicineData)){
                foreach($medicineCabinetNaturalMedicineData as $medicineCabinetNaturalMedicineDataKey => $medicineCabinetNaturalMedicineDataVal){
                    $medicineCabinetNaturalMedicineData[$medicineCabinetNaturalMedicineDataKey]['imageName'] = asset('images/').'/'.'beta-carotene.svg';
                    
                    // Get the interaction icon based on the interactions of current natural medicine with existing drugs data by current logged in user
                    $interactionsRatingValue = Helper::getInteractionIcon($userId,$profileMemberId,'naturalMedicine',$medicineCabinetNaturalMedicineDataVal['naturalMedicineId'],'','');
                    $medicineCabinetNaturalMedicineData[$medicineCabinetNaturalMedicineDataKey]['interactionIcon'] = $interactionsRatingValue;

                    // Get the interactions label (i.e, Major, Moderate, Minor, None) to display the data according to its filter in WellKabinet (medicine cabinet) screen
                    $medicineCabinetNaturalMedicineData[$medicineCabinetNaturalMedicineDataKey]['interactionLabel'] = Helper::getLabelNameForInteractionFilters($interactionsRatingValue);
                   
                    // Define type
                    $medicineCabinetNaturalMedicineData[$medicineCabinetNaturalMedicineDataKey]['type'] = 'naturalMedicine';

                    // Get the frequency value for the dosage details
                    $naturalMedicinefrequencyValue = MedicineCabinet::where('medicine_cabinet.id',$medicineCabinetNaturalMedicineDataVal['medicineCabinetId'])
                    ->select('master.name as frequency')
                    ->leftJoin('master','medicine_cabinet.frequency','=','master.id')
                    ->pluck('frequency')->first();
                    if(!empty($naturalMedicinefrequencyValue)){
                        $medicineCabinetNaturalMedicineDosageVal = $medicineCabinetNaturalMedicineDataVal['dosage'];
                        $medicineCabinetNaturalMedicineData[$medicineCabinetNaturalMedicineDataKey]['dosage'] = $medicineCabinetNaturalMedicineDosageVal ? $medicineCabinetNaturalMedicineDosageVal.",<br>".$naturalMedicinefrequencyValue : $medicineCabinetNaturalMedicineDosageVal;
                    }
                }
                $medicineCabinetNaturalMedicineDataArr = $medicineCabinetNaturalMedicineData;

                //sort list of data of natural medicine name in alphabetical order - code start 
                usort($medicineCabinetNaturalMedicineDataArr, function($finalArrayOne, $finalArrayTwo) {
                    return $finalArrayOne['name'] <=> $finalArrayTwo['name'];
                });
                //sort list of data of natural medicine name in alphabetical order - code end

            }


            // Fetch Rx Drugs data
            $medicineCabinetRxDrugsData = MedicineCabinet::where('medicine_cabinet.userId',$userId);
            
            // Check if profile member id exist and get the data from it, else exclude profile member data check
            if(!empty($profileMemberId)){
                $medicineCabinetRxDrugsData = $medicineCabinetRxDrugsData->where('profileMemberId',$profileMemberId);
            }else{
                $medicineCabinetRxDrugsData = $medicineCabinetRxDrugsData->whereNull('profileMemberId');
            }

            $medicineCabinetRxDrugsData = $medicineCabinetRxDrugsData->select('medicine_cabinet.id as medicineCabinetId',
            'medicine_cabinet.drugId',DB::raw('CONCAT(drugs.name," - ",drugs.brand_name) AS name'),
            DB::raw('CONCAT(medicine_cabinet.dosage," ",master.name) As dosage'),
            'medicine_cabinet.isTaking','medicine_cabinet.created_at')
            ->whereNotNull('drugId')
            ->join('drugs','medicine_cabinet.drugId','=','drugs.id')
            ->leftJoin('master','medicine_cabinet.dosageType','=','master.id')
            ->orderBy('drugs.name')
            ->whereNull('medicine_cabinet.deleted_at')
            ->get()->toArray();

            if(!empty($medicineCabinetRxDrugsData)){
                foreach($medicineCabinetRxDrugsData as $medicineCabinetRxDrugsDataKey => $medicineCabinetRxDrugsDataVal){
                    $medicineCabinetRxDrugsData[$medicineCabinetRxDrugsDataKey]['imageName'] = asset('images').'/'.'rx-drug.svg';

                    // Get the interaction icon based on the interactions of current drug data with existing natural medicine data by current logged in user
                    $interactionsRatingValue = Helper::getInteractionIcon($userId,$profileMemberId,'rxDrug','',$medicineCabinetRxDrugsDataVal['drugId'],'');
                    $medicineCabinetRxDrugsData[$medicineCabinetRxDrugsDataKey]['interactionIcon'] = $interactionsRatingValue;
                    
                    // Get the interactions label (i.e, Major, Moderate, Minor, None) to display the data according to its filter in WellKabinet (medicine cabinet) screen
                    $medicineCabinetRxDrugsData[$medicineCabinetRxDrugsDataKey]['interactionLabel'] = Helper::getLabelNameForInteractionFilters($interactionsRatingValue);

                    // Define type
                    $medicineCabinetRxDrugsData[$medicineCabinetRxDrugsDataKey]['type'] = 'rxDrugs';

                    // Get the frequency value for the dosage details
                    $rxfrequencyValue = MedicineCabinet::where('medicine_cabinet.id',$medicineCabinetRxDrugsDataVal['medicineCabinetId'])
                    ->select('master.name as frequency')
                    ->leftJoin('master','medicine_cabinet.frequency','=','master.id')
                    ->pluck('frequency')->first();
                    if(!empty($rxfrequencyValue)){
                        $medicineCabinetRxDrugsDosageVal = $medicineCabinetRxDrugsDataVal['dosage'];
                        $medicineCabinetRxDrugsData[$medicineCabinetRxDrugsDataKey]['dosage'] = $medicineCabinetRxDrugsDosageVal ? $medicineCabinetRxDrugsDataVal['dosage'].",<br>".$rxfrequencyValue : $medicineCabinetRxDrugsDosageVal;
                    }
                    
                }
                $medicineCabinetRxDrugsDataArr = $medicineCabinetRxDrugsData;

                //sort list of data of Rx drugs name in alphabetical order - code start 
                usort($medicineCabinetRxDrugsDataArr, function($finalArrayOne, $finalArrayTwo) {
                    return trim($finalArrayOne['name']) <=> trim($finalArrayTwo['name']);
                });
                //sort list of data of Rx drugs name in alphabetical order - code end
            }

            // Fetch Products Data
            $medicineCabinetProductsData = MedicineCabinet::where('medicine_cabinet.userId',$userId);
            
            // Check if profile member id exist and get the data from it, else exclude profile member data check
            if(!empty($profileMemberId)){
                $medicineCabinetProductsData = $medicineCabinetProductsData->where('profileMemberId',$profileMemberId);
            }else{
                $medicineCabinetProductsData = $medicineCabinetProductsData->whereNull('profileMemberId');
            }

            $medicineCabinetProductsData = $medicineCabinetProductsData->select('medicine_cabinet.id as medicineCabinetId',
            'product.productId as productId',DB::raw('CONCAT(product.productName," by ",product.productBrand," (",product.productSize,")") AS name'),
            'product.productImageLink AS imageName', 'product.shopifyProductLink AS imageRedirectLink',
            'product.isActive AS isActive','product.backUpProductId AS backUpProductId',
            DB::raw('CONCAT(medicine_cabinet.dosage," ",master.name) As dosage'),
            'medicine_cabinet.isTaking','medicine_cabinet.created_at')
            ->whereNotNull('medicine_cabinet.productId')
            ->join('product','medicine_cabinet.productId','=','product.id')
            ->leftJoin('master','medicine_cabinet.dosageType','=','master.id')
            ->orderBy('product.productName')
            ->whereNull('medicine_cabinet.deleted_at')
            ->get()->toArray();

            if(!empty($medicineCabinetProductsData)){
                foreach($medicineCabinetProductsData as $medicineCabinetProductsDataKey => $medicineCabinetProductsDataVal){

                    // Get the interaction icon based on the interactions of current product data with existing natural medicine data by current logged in user
                    $productInteractionsRatingValue = Helper::getInteractionIcon($userId,$profileMemberId,'product','','',$medicineCabinetProductsDataVal['productId']);
                    $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['interactionIcon'] =  $productInteractionsRatingValue;
                    
                    // Get the interactions label (i.e, Major, Moderate, Minor, None) to display the data according to its filter in WellKabinet (medicine cabinet) screen
                    $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['interactionLabel'] = Helper::getLabelNameForInteractionFilters($productInteractionsRatingValue);
                    
                    // Define type
                    $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['type'] = 'product';


                    // Add product image link for redirection
                    $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['imageRedirectLink'] =  $medicineCabinetProductsDataVal['imageRedirectLink'];

                    // Get the frequency value for the dosage details
                    $productfrequencyValue = MedicineCabinet::where('medicine_cabinet.id',$medicineCabinetProductsDataVal['medicineCabinetId'])
                    ->select('master.name as frequency')
                    ->leftJoin('master','medicine_cabinet.frequency','=','master.id')
                    ->pluck('frequency')->first();
                    if(!empty($productfrequencyValue)){
                        $medicineCabinetProductsDosageVal = $medicineCabinetProductsDataVal['dosage'];
                        $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['dosage'] = $medicineCabinetProductsDosageVal ? $medicineCabinetProductsDataVal['dosage'].",<br>".$productfrequencyValue : $medicineCabinetProductsDosageVal;
                    }
                    
                    // Get the product therapy data and back up product data if any and add the description  - code start
                    $productDescription = "";
                    $productDescriptionIngredients = '';
                    $productTherapyData = ProductTherapy::select('therapy.therapy As therapyName','product_therapy.interaction_display_name As OtherTherapyName',
                    'therapy.canonicalName As therapyUrl')->leftJoin('therapy','product_therapy.therapyId','=','therapy.id')
                    ->where('product_therapy.productId',$medicineCabinetProductsDataVal['productId'])
                    ->whereNull('product_therapy.deleted_at')
                    ->get()->toArray();
                    if(!empty($productTherapyData)){
                        $productTherapyDataCount = count($productTherapyData);
                        // Display only if 1 ingredient for product 
                        if($productTherapyDataCount == '1'){
                            $ingredientsTxt = 'ingredient';
                            $productDescriptionIngData = $productTherapyData[0]['therapyUrl'] ? 'is <a href="'.route('therapy',$productTherapyData[0]['therapyUrl']).'" target="_blank">'.$productTherapyData[0]['therapyName'].'</a>.' : $productTherapyData[0]['OtherTherapyName'];
                        }else{
                            // Display if only 2 ingredients for product
                            if($productTherapyDataCount == '2'){

                                $firstProductIngredientName = $productTherapyData[0]['therapyUrl'] ? '<a href="'.route('therapy',$productTherapyData[0]['therapyUrl']).'" target="_blank">'.$productTherapyData[0]['therapyName'].'</a>' : $productTherapyData[0]['OtherTherapyName'];

                                $secondProductIngredientName = $productTherapyData[1]['therapyUrl'] ? '<a href="'.route('therapy',$productTherapyData[1]['therapyUrl']).'" target="_blank">'.$productTherapyData[1]['therapyName'].'</a>' : $productTherapyData[1]['OtherTherapyName'];

                                $productDescriptionIngData = 'are '.$firstProductIngredientName.' and  '.$secondProductIngredientName.'</a>.';
                            }
                            else{
                                $ingredientsArray = array();
                                // Display more ingredients for product
                                foreach($productTherapyData as $productTherapyDataKey => $productTherapyDataVal){
                                    $data = $productTherapyDataVal['therapyUrl'] ? '<a href="'.route('therapy',$productTherapyDataVal['therapyUrl']).'" target="_blank">'.$productTherapyDataVal['therapyName'].'</a>' : $productTherapyDataVal['OtherTherapyName'];
                                    $ingredientsArray[] = $data;
                                }
                                $ingredients = implode(', ',$ingredientsArray); // convert ingredients array data to string with comma separated
                                $ingredients = preg_replace('/,[^,]*$/', ' and '.end($ingredientsArray).'.', $ingredients); // replace last comma with 'and' & append last ingredient data to it

                                // Add 'are' sting in the first with the ingredients data 
                                $productDescriptionIngData = 'are '.$ingredients;
                            }
                            $ingredientsTxt = 'ingredients';
                        }

                        $productDescription .= "The primary ".$ingredientsTxt." in ".$medicineCabinetProductsDataVal['name'].' ';

                        $productDescriptionIngredients = $productDescriptionIngData;

                        
                    }

                    // Check if isActive field is set to false for this product, then append text message for discontinued product
                    if($medicineCabinetProductsDataVal['isActive'] == 'false'){
                        // Remove product image link for redirection
                        $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['imageRedirectLink'] = '';

                        // Check if backUpProductId has value, if it's there then append below message along with it.
                        $discontinuedMsg = ' This product is not currently available.';
                        if(!empty($medicineCabinetProductsDataVal['backUpProductId'])){
                            $backUpProductData = Product::select(DB::raw('CONCAT(product.productName," by ",product.productBrand," (",product.productSize,")") AS name'),'shopifyProductLink')
                            ->where('productId',$medicineCabinetProductsDataVal['backUpProductId'])->get()->first();
                            if(!empty($backUpProductData)){
                                $discontinuedMsg .= ' We recommend <a href="'.$backUpProductData->shopifyProductLink.'" target="_blank">'.$backUpProductData->name.'</a> as an alternate.';
                            }
                        }
                        $productDescriptionIngredients .= $discontinuedMsg;
                    }

                    $productDescription =  $productDescription.$productDescriptionIngredients;

                    $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['productDescription'] = $productDescription;
                    // Get the product therapy data and back up product data if any and add the description  - code end


                    // Get the last purchased & next refill date from user product order table
                    $lastPurchasedDate = '-';
                    $nextRefillDate = '-';
                    $productDatesArray = [];
                    $productDates = UserProductOrder::select('user_product_order.last_purchased As lastPurchasedDate',
                    'user_product_order.next_refill_date As nextRefillDate')
                    ->join('product','user_product_order.productId','=','product.productId')
                    ->join('users','user_product_order.userId','=','users.email')
                    ->where('user_product_order.productId',$medicineCabinetProductsDataVal['productId'])
                    ->where('user_product_order.userId',$userEmailId)
                    ->whereNull('user_product_order.deleted_at')
                    ->orderBy('user_product_order.id','DESC')
                    ->get()->first();
                    if(!empty($productDates)){
                        $productDatesArray['lastPurchasedDate'] = date('m/d/y',strtotime($productDates->lastPurchasedDate));
                        $productDatesArray['nextRefillDate'] = date('m/d/y',strtotime($productDates->nextRefillDate));
                    }
                    $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['productDates'] = $productDatesArray;
                }
                $medicineCabinetProductsDataArr = $medicineCabinetProductsData;

                //sort list of data of Products name in alphabetical order - code start 
                usort($medicineCabinetProductsDataArr, function($finalArrayOne, $finalArrayTwo) {
                    return trim($finalArrayOne['name']) <=> trim($finalArrayTwo['name']);
                });
                //sort list of data of Products name in alphabetical order - code end

            }
            $medicineCabinetData = array_merge($medicineCabinetNaturalMedicineDataArr,$medicineCabinetRxDrugsDataArr);
            $medicineCabinetData = array_merge($medicineCabinetProductsDataArr, $medicineCabinetData);

            // check if condition exists then add the names in the array - code start
            foreach ($medicineCabinetData as $medicineCabinetDataKey => $medicineCabinetDataValue){

                $addedConditionsData = MedicineCabinetConditions::where('medicineCabinetId',$medicineCabinetDataValue['medicineCabinetId'])->select('id','conditionId','customConditionName')->get()->toArray();                

                if(!empty($addedConditionsData)){
                    $conditions = array();
                    foreach ($addedConditionsData as $addedConditionsDataKey => $addedConditionsDataValue){
                        if($addedConditionsDataValue['conditionId'] != '0'){
                            $conditionsArrData = Condition::select('id','conditionName As name','canonicalName')
                            ->where('id',$addedConditionsDataValue['conditionId'])
                            ->get()->first();
                            $conditionsArr['id'] = $conditionsArrData['id'];
                            $conditionsArr['name'] = $conditionsArrData['name'];
                            $conditionsArr['canonicalName'] = $conditionsArrData['canonicalName'];
                            $conditions[] = $conditionsArr;
                        }
                        else{
                            $dataArr['id'] = $addedConditionsDataValue['id'];
                            $dataArr['name'] = $addedConditionsDataValue['customConditionName'];
                            $dataArr['canonicalName'] = '';
                            
                            $conditions[] = $dataArr;
                        }
                        $medicineCabinetData[$medicineCabinetDataKey]['conditionIds'] = $conditions;
                    }
                    
                    // merge all the conditions in one condition array for the filter condition section
                    $allConditions = array_merge($allConditions,$conditions);
                }
            }
            // check if condition exists then add the names in the array - code end

            // Check if conditions data exist then remove duplicate names unique and set it in alphabetical order
            if(!empty($allConditions)){
                //Get the unique conditions list from the array
                $allConditions = array_map("unserialize", array_unique(array_map("serialize", $allConditions)));
                
                // sort all the conditions name alphabetically
                usort($allConditions, function($finalArrayOne, $finalArrayTwo) {
                    return $finalArrayOne['name'] <=> $finalArrayTwo['name'];
                });
            }
        }

        // Show profile member names with route
        $userProfileMembersData = [];
        if(Auth::user()->getSubscriptionStatus()){
            $userProfileMembersData = Auth::user()->getProfileMembersWithMedicineCabinetData();
            if(!empty($userProfileMembersData)){
                $primaryUser[0] = array('id'=>Auth::user()->id,'name'=>Auth::user()->name." ".Auth::user()->last_name,'url'=>route('medicine-cabinet'));
                $userProfileMembersData = array_merge($primaryUser,$userProfileMembersData);
            }
        }

        // Get frequency values from master table
        $frequency = Master::select('id','name')->where('type','9')->get()->toArray();

        // Get dosage type values from master table
        $dosageType = Master::select('id','name')->where('type','10')->get()->toArray();
        

        // return all data in medicine cabinet page
        return view('page.medicine-cabinet',compact('userName','userProfileMembersData','medicineCabinetData','profileMemberId','allConditions','frequency','dosageType'));
    }

    /*** 
     * List all Rx drugs on search in dropdown
    */
    public function listDrugsData(Request $request){

        // Get Logged in user id
        $userId = Auth::user()->id;

        $data = [];
        if($request->input('query')){
            $search = $request->input('query');
            // get drugs data with its id and name
            $data = Drugs::select("id",DB::raw('CONCAT(name," - ",brand_name) AS name'))
            ->where(DB::raw('CONCAT(name," - ",brand_name)'),"LIKE","%$search%");

            // exclude therapy names which is already added in medicine cabinet by logged in user / its profile member
            $rxDrugsIds = MedicineCabinet::where('userId',$userId)
            ->whereNull('deleted_at')->whereNotNull('drugId');

            // exclude drugs added by current profile member of logged in user if exists.
            if(!empty($request->profileMemberId)){
                $rxDrugsIds = $rxDrugsIds->where('profileMemberId',$request->profileMemberId)
                ->whereNotNull('profileMemberId')->pluck('drugId')->toArray();
            }else{
                // exclude drugs added by logged in user if exists.
                $rxDrugsIds = $rxDrugsIds->pluck('drugId')->toArray();
            }

            // Check if ids exist to exclude rx drugs data
            if(!empty($rxDrugsIds)){
                $data = $data->whereNotIn('id',$rxDrugsIds);
            }

            $data = $data->orderBy('name')->whereNotNull('brand_name')->whereNull('deleted_at')
            ->limit(20)
            ->get();
        }
        return response()->json($data);

    }

    /*** 
     * List all natural medicine on search in dropdown
    */
    public function listNaturalMedicineData(Request $request){

        // Get Logged in user id
        $userId = Auth::user()->id;

        $data = [];

        if($request->input('query')){
            $search = $request->input('query');
            
            // get natural medicine data with its id and name
            $data =  Therapy::select('id','therapy As name')
            ->where('therapy',"LIKE","%$search%");
            
            // exclude therapy names which is already added in medicine cabinet by logged in user / its profile member
            $naturalMedicineIds = MedicineCabinet::where('userId',$userId)
            ->whereNull('deleted_at')->whereNotNull('naturalMedicineId');
            
            // exclude therapies added by current profile member of logged in user if exists.
            if(!empty($request->profileMemberId)){
                $naturalMedicineIds = $naturalMedicineIds->where('profileMemberId',$request->profileMemberId)->whereNotNull('profileMemberId')->pluck('naturalMedicineId')->toArray();
            }else{
                // exclude therapies added by current logged in user
                $naturalMedicineIds = $naturalMedicineIds->whereNull('profileMemberId')->pluck('naturalMedicineId')->toArray();
            }

            // Check if ids exist to exclude natural medicine data
            if(!empty($naturalMedicineIds)){
                $data = $data->whereNotIn('id',$naturalMedicineIds);
            }

            $data = $data->orderBy('therapy')->whereNull('deleted_at')
            ->get();
            

        }
        return response()->json($data);

    }


    /*** 
     * List all autocomplete search of product, rx drugs & natural medicine data
    */
    public function autocompleteWellkabinet(Request $request){

        // Get Logged in user id
        $userId = Auth::user()->id;

        //Get the list of product on base of search string
        $product = Product::select("id",DB::raw('CONCAT(product.productName," by ",product.productBrand," (",product.productSize,")") AS productName'))
        ->where(DB::raw('CONCAT(product.productName," by ",product.productBrand," (",product.productSize,")")'),"LIKE","%{$request->input('query')}%");

        // exclude product names which is already added in medicine cabinet by logged in user / its profile member
        $productIds = MedicineCabinet::where('userId',$userId)
        ->whereNull('deleted_at')->whereNotNull('productId');
        
        // exclude therapies added by current profile member of logged in user if exists.
        if(!empty($request->profileMemberId)){
            $productIds = $productIds->where('profileMemberId',$request->profileMemberId)->whereNotNull('profileMemberId')->pluck('productId')->toArray();
        }else{
            // exclude therapies added by current logged in user
            $productIds = $productIds->whereNull('profileMemberId')->pluck('productId')->toArray();
        }

        // Check if ids exist to exclude natural medicine data
        if(!empty($productIds)){
            $product = $product->whereNotIn('id',$productIds);
        }

        $product = $product->orderBy('productName', 'ASC')->whereNull('deleted_at')->get();
        
        $data = array();    
        $i = 0;            
        foreach ($product as $key => $prd)
        {
            $data[$i]['id'] = $prd->id."-product";
            $data[$i]['name'] = $prd->productName;
            $i++;
        }

        //Get the list of therapy on base of search string
        $therapy = Therapy::select("id","therapy","canonicalName")
        ->where("therapy","LIKE","%{$request->input('query')}%");

        // exclude therapy names which is already added in medicine cabinet by logged in user / its profile member
        $naturalMedicineIds = MedicineCabinet::where('userId',$userId)
        ->whereNull('deleted_at')->whereNotNull('naturalMedicineId');
        
        // exclude therapies added by current profile member of logged in user if exists.
        if(!empty($request->profileMemberId)){
            $naturalMedicineIds = $naturalMedicineIds->where('profileMemberId',$request->profileMemberId)->whereNotNull('profileMemberId')->pluck('naturalMedicineId')->toArray();
        }else{
            // exclude therapies added by current logged in user
            $naturalMedicineIds = $naturalMedicineIds->whereNull('profileMemberId')->pluck('naturalMedicineId')->toArray();
        }

        // Check if ids exist to exclude natural medicine data
        if(!empty($naturalMedicineIds)){
            $therapy = $therapy->whereNotIn('id',$naturalMedicineIds);
        }

        $therapy = $therapy->orderBy('therapy')->whereNull('deleted_at')->get();
        
        foreach ($therapy as $key => $thpy)
        {
            $data[$i]['id'] = $thpy->id."-therapy";
            $data[$i]['name'] = $thpy->therapy;
            $i++;
        }


        //Get the list of drugs on base of search string
        $drugs = Drugs::select("id",DB::raw('CONCAT(name," - ",brand_name) AS name'))
        ->where(DB::raw('CONCAT(name," - ",brand_name)'),"LIKE","%{$request->input('query')}%");

        // exclude therapy names which is already added in medicine cabinet by logged in user / its profile member
        $rxDrugsIds = MedicineCabinet::where('userId',$userId)
        ->whereNull('deleted_at')->whereNotNull('drugId');

        // exclude drugs added by current profile member of logged in user if exists.
        if(!empty($request->profileMemberId)){
            $rxDrugsIds = $rxDrugsIds->where('profileMemberId',$request->profileMemberId)
            ->whereNotNull('profileMemberId')->pluck('drugId')->toArray();
        }else{
            // exclude drugs added by logged in user if exists.
            $rxDrugsIds = $rxDrugsIds->whereNull('profileMemberId')->pluck('drugId')->toArray();
        }

        // Check if ids exist to exclude rx drugs data
        if(!empty($rxDrugsIds)){
            $drugs = $drugs->whereNotIn('id',$rxDrugsIds);
        }

        $drugs = $drugs->orderBy('name')->whereNotNull('brand_name')->whereNull('deleted_at')
        ->limit(20)
        ->get();

        foreach ($drugs as $key => $drug)
        {
            $data[$i]['id'] = $drug->id."-rxdrug";
            $data[$i]['name'] = $drug->name;
            $i++;
        }

        return response()->json($data);

    }

    
    /*** 
     * List all conditions data on search in dropdown
    */
    public function listConditionsData(Request $request){

        $data = [];

        if($request->input('query')){
            $search = $request->input('query');
            // get condition data with its id and name
            $data =  Condition::select('id','conditionName As name')
            ->where('conditionName',"LIKE","%$search%")
            ->where('displayInSearch','1')
            ->orderBy('conditionName')->whereNull('deleted_at')
            ->get();
        }
        return response()->json($data);

    }

    /*** 
     * Save Natural medicine / Rx Drug data and its condition in medicine cabinet table
    */
    public function saveMedicineCabinet(Request $request){
        // Get Logged in user id
        $userId = \Auth::user()->id;

        // Store the frequency value from the input request exist
        $frequency = $request->frequency ? $request->frequency : null;
        // Store the dosage value from the input request exist
        $dosage = $request->dosage ? $request->dosage : null;
        // Store the dosageType value from the input request exist
        $dosageType = $request->dosageType ? $request->dosageType : null;  

        $profileMemberId = $request->profileMemberId ? $request->profileMemberId : null;
        $checkMedicineTab = $request->isMedicineTab;
        $naturalMedicineId = $request->naturalMedicineId;
        if(!empty($request->therapyID)){
            $naturalMedicineId = $request->therapyID;
        }
        $drugId = $request->drugId;
        $productId = $request->productId;
        $conditionId = $request->conditionId!='' ? $request->conditionId : null;

        // Add data for natural medicine in medicine cabinet - code start
        if(!empty($checkMedicineTab) && $checkMedicineTab == '1'){

            // Check if selected natural medicine exist, if not then show error message
            $naturalMedicineName = Therapy::select('therapy')->where('id',$naturalMedicineId)
            ->whereNull('deleted_at')->get()->first();
            if(empty($naturalMedicineName)){
                return $request->session()->flash('error', 'Selected natural medicine does not exist. Please try again.');
            }

            // Begin a transaction
            DB::beginTransaction();

            //Check if existing Natural Medicine Id is same as already stored in medicine cabinet table
            $naturalMedicineCount = MedicineCabinet::where('userId',$userId)->where('naturalMedicineId',$naturalMedicineId);
            if(!empty($profileMemberId)){
                $naturalMedicineCount = $naturalMedicineCount->where('profileMemberId',$profileMemberId);
            }else{
                $naturalMedicineCount = $naturalMedicineCount->whereNull('profileMemberId');
            }
            $naturalMedicineCount = $naturalMedicineCount->whereNull('deleted_at')->count();

            // Save current medicine cabinet if not added already
            if($naturalMedicineCount == 0){

                $MedicineCabinet = new MedicineCabinet;
                $MedicineCabinet->userId = $userId;
                $MedicineCabinet->profileMemberId = $profileMemberId;
                $MedicineCabinet->naturalMedicineId = $naturalMedicineId;
                $MedicineCabinet->frequency = $frequency;
                $MedicineCabinet->dosage = $dosage;
                $MedicineCabinet->dosageType = $dosageType;
                $MedicineCabinet->updated_at = null;

                if($MedicineCabinet->save()){

                    if(!empty($conditionId)){
                        // Check if custom condition is from input, then pass the value else pass null
                        $customConditionText = null;
                        if($conditionId == '00'){
                            $conditionId = 0;
                            $customConditionText = $request->customConditionText;
                        }
                        
                        // Store medicine cabinet condition data when medicine cabinet data is saved successfully
                        $MedicineCabinetConditions = new MedicineCabinetConditions;
                        $MedicineCabinetConditions->medicineCabinetId = $MedicineCabinet->id;
                        $MedicineCabinetConditions->conditionId = $conditionId;
                        $MedicineCabinetConditions->customConditionName = $customConditionText;
                        $MedicineCabinetConditions->updated_at = null;
                        $MedicineCabinetConditions->save(); 
                    }

                    // Commit the transaction
                    DB::commit();

                    $message = $naturalMedicineName->therapy.' added to cabinet. Tap the therapy name/icon to view therapy details if needed.';
                    session()->put('savedMedicineData', $message);
                }else{
                    // An error occured, cancel the transaction.
                    DB::rollback();
                    return $request->session()->flash('message', 'Something went wrong while saving natural medicine data. Please try again.');
                }

            }else{
                // An error occured, cancel the transaction.
                DB::rollback();
                return $request->session()->flash('error', $naturalMedicineName->therapy.' has already been saved.');
            }
        }
        // Add data for natural medicine in medicine cabinet - code end



        // Add data for rx drug in medicine cabinet - code start
        if(!empty($checkMedicineTab) && $checkMedicineTab == '2'){

            // Check if selected rx drug exist, if not then show error message
            $drugName = Drugs::select(DB::raw('CONCAT(name," - ",brand_name) AS name'))->where('id',$drugId)
            ->whereNull('deleted_at')->get()->first();
            if(empty($drugName)){
                return $request->session()->flash('error', 'Selected Rx Drug does not exist. Please try again.');
            }

            // Begin a transaction
            DB::beginTransaction();

            //Check if existing Drug Id is same as already stored in medicine cabinet table
            $rxDrugCount = MedicineCabinet::where('userId',$userId)->where('drugId',$drugId);
            if(!empty($profileMemberId)){
                $rxDrugCount = $rxDrugCount->where('profileMemberId',$profileMemberId);
            }else{
                $rxDrugCount = $rxDrugCount->whereNull('profileMemberId');
            }
            $rxDrugCount = $rxDrugCount->whereNull('deleted_at')->count();

            // Save current medicine cabinet if not added already
            if($rxDrugCount == 0){

                $MedicineCabinet = new MedicineCabinet;
                $MedicineCabinet->userId = $userId;
                $MedicineCabinet->profileMemberId = $profileMemberId;
                $MedicineCabinet->drugId = $drugId;
                $MedicineCabinet->frequency = $frequency;
                $MedicineCabinet->dosage = $dosage;
                $MedicineCabinet->dosageType = $dosageType;
                $MedicineCabinet->updated_at = null;
                  
                if($MedicineCabinet->save()){

                    if(!empty($conditionId)){

                        // Check if custom condition is from input, then pass the value else pass null
                        $customConditionText = null;
                        if($conditionId == '00'){
                            $conditionId = 0;
                            $customConditionText = $request->customConditionText;
                        }

                        // Store medicine cabinet condition data when medicine cabinet data is saved successfully
                        $MedicineCabinetConditions = new MedicineCabinetConditions;
                        $MedicineCabinetConditions->medicineCabinetId = $MedicineCabinet->id;
                        $MedicineCabinetConditions->conditionId = $conditionId;
                        $MedicineCabinetConditions->customConditionName = $customConditionText;
                        $MedicineCabinetConditions->updated_at = null;
                        $MedicineCabinetConditions->save();
                    }

                    // Commit the transaction
                    DB::commit();
                    $message = $drugName->name.' added to cabinet.';
                    session()->put('savedMedicineData', $message);
                }else{
                    // An error occured, cancel the transaction.
                    DB::rollback();
                    return $request->session()->flash('message', 'Something went wrong while saving rx drug data. Please try again.');
                }
            }else{
                // An error occured, cancel the transaction.
                DB::rollback();
                return $request->session()->flash('error', $drugName->name.' has already been saved.');

            }

        }
        // Add data for rx drug in medicine cabinet - code end


        // Add data for product in medicine cabinet - code start
        if(!empty($checkMedicineTab) && $checkMedicineTab == '3'){

            // Check if selected product exist, if not then show error message
            $productName = Product::select(DB::raw('CONCAT(product.productName," by ",product.productBrand) AS name'))->where('id',$productId)
            ->whereNull('deleted_at')->get()->first();
            if(empty($productName)){
                return $request->session()->flash('error', 'Selected product does not exist. Please try again.');
            }

            // Begin a transaction
            DB::beginTransaction();

            //Check if existing Product Id is same as already stored in medicine cabinet table
            $productCount = MedicineCabinet::where('userId',$userId)->where('productId',$productId);
            if(!empty($profileMemberId)){
                $productCount = $productCount->where('profileMemberId',$profileMemberId);
            }else{
                $productCount = $productCount->whereNull('profileMemberId');
            }
            $productCount = $productCount->whereNull('deleted_at')->count();

            // Save current medicine cabinet if not added already
            if($productCount == 0){

                $MedicineCabinet = new MedicineCabinet;
                $MedicineCabinet->userId = $userId;
                $MedicineCabinet->profileMemberId = $profileMemberId;
                $MedicineCabinet->productId = $productId;
                $MedicineCabinet->frequency = $frequency;
                $MedicineCabinet->dosage = $dosage;
                $MedicineCabinet->dosageType = $dosageType;
                $MedicineCabinet->updated_at = null;
                  
                if($MedicineCabinet->save()){

                    if(!empty($conditionId)){

                        // Check if custom condition is from input, then pass the value else pass null
                        $customConditionText = null;
                        if($conditionId == '00'){
                            $conditionId = 0;
                            $customConditionText = $request->customConditionText;
                        }

                        // Store medicine cabinet condition data when medicine cabinet data is saved successfully
                        $MedicineCabinetConditions = new MedicineCabinetConditions;
                        $MedicineCabinetConditions->medicineCabinetId = $MedicineCabinet->id;
                        $MedicineCabinetConditions->conditionId = $conditionId;
                        $MedicineCabinetConditions->customConditionName = $customConditionText;
                        $MedicineCabinetConditions->updated_at = null;
                        $MedicineCabinetConditions->save();
                    }

                    // Commit the transaction
                    DB::commit();
                    $message = 'Product '.$productName->name.' added to cabinet.';
                    session()->put('savedMedicineData', $message);
                }else{
                    // An error occured, cancel the transaction.
                    DB::rollback();
                    return $request->session()->flash('message', 'Something went wrong while saving product data. Please try again.');
                }
            }else{
                // An error occured, cancel the transaction.
                DB::rollback();
                return $request->session()->flash('error', $productName->name.' has already been saved.');

            }

        }
        // Add data for product in medicine cabinet - code end

    }

    /***
     * Display selected Natural medicine / Rx Drug data in edit page
     **/
    public function cabinetEditPage(Request $request, $medicineCabinetId){

        // Decrypts the data from listing page and gets id and type of the data
        $medicineCabinetData = Crypt::decrypt($medicineCabinetId);
        $medicineCabinetId = $medicineCabinetData['id'];
        $type = $medicineCabinetData['type'];

        $medicineCabinetData = array();

        $medicineCabinetDataArr = MedicineCabinet::where('medicine_cabinet.id',$medicineCabinetId)
        ->whereNull('medicine_cabinet.deleted_at');

        // if data is of natural medicine type then show data accordingly
        if($type == 'naturalMedicine'){

            // Fetch Natural Medicine data if exists 
            $medicineCabinetData = $medicineCabinetDataArr->select('medicine_cabinet.id as medicineCabinetId',
            'medicine_cabinet.naturalMedicineId','therapy.therapy as name',
            'therapy.canonicalName as canonicalName','medicine_cabinet.isTaking',
            'frequency','dosage','dosageType','medicine_cabinet.created_at',
            'medicine_cabinet.profileMemberId')
            ->whereNotNull('naturalMedicineId')
            ->join('therapy','medicine_cabinet.naturalMedicineId','=','therapy.id');

        }else if($type == 'rxDrugs'){

            // Fetch Rx Drugs data if exists
            $medicineCabinetData = $medicineCabinetDataArr->select('medicine_cabinet.id as medicineCabinetId',
            'medicine_cabinet.drugId',DB::raw('CONCAT(drugs.name," - ",drugs.brand_name) AS name'),
            'medicine_cabinet.isTaking','frequency','dosage','dosageType','medicine_cabinet.created_at',
            'medicine_cabinet.profileMemberId')
            ->whereNotNull('drugId')
            ->join('drugs','medicine_cabinet.drugId','=','drugs.id');

        }else if($type == 'product'){
            // Fetch Product data if exists
            $medicineCabinetData = $medicineCabinetDataArr->select('medicine_cabinet.id as medicineCabinetId',
            'medicine_cabinet.productId',DB::raw('CONCAT(product.productName," by ",product.productBrand," (",product.productSize,")") AS name'),
            'medicine_cabinet.isTaking','frequency','dosage','dosageType','medicine_cabinet.created_at',
            'medicine_cabinet.profileMemberId')
            ->whereNotNull('medicine_cabinet.productId')
            ->join('product','medicine_cabinet.productId','=','product.id');
        }

        $medicineCabinetData = $medicineCabinetData->get()->first();
        if(!empty($medicineCabinetData)){
            
            /***
             * check if this medicine cabinet edit is of profile member data then add back button to its
             * profile member's respective wellkabinet screen, else just put wellkabinet screen url 
             *  */ 
            $backButton = route('medicine-cabinet');
            $profileMemberId = ''; 
            if(!empty($medicineCabinetData->profileMemberId)){
                $backButton = route('medicine-cabinet',Crypt::encrypt($medicineCabinetData->profileMemberId));
                $profileMemberId = Crypt::encrypt($medicineCabinetData->profileMemberId);
            }

            // get all conditions data for condition dropdown
            $conditions = Condition::select('id','conditionName As name');

            $medicineCabinetConditions = array();

            $conditionsCount = '0'; // default 0 conditions to accept if not added any conditions

            // Check if current user has added any condition
            $medicineCabinetConditionsData = MedicineCabinetConditions::where('medicineCabinetId',$medicineCabinetId)
            ->select('id','conditionId','customConditionName')->get()->toArray();
            if(!empty($medicineCabinetConditionsData)){

                foreach($medicineCabinetConditionsData as $medicineCabinetConditionsDataKey => $medicineCabinetConditionsDataVal){
                    
                    $medicineCabConditions['medicineCabinetConditionId'] = $medicineCabinetConditionsDataVal['id'];
                    if(($medicineCabinetConditionsDataVal['conditionId']=='0')){
                        $medicineCabConditions['medicineCabinetConditionName'] = $medicineCabinetConditionsDataVal['customConditionName'];
                    }else{
                        $addedConditionName = Condition::where('id',$medicineCabinetConditionsDataVal['conditionId'])
                        ->pluck('conditionName')->first();
                        $medicineCabConditions['medicineCabinetConditionName'] = $addedConditionName;
                    }
                    // get selected conditions data for conditions tags
                    $medicineCabinetConditions[] = $medicineCabConditions;
                }

                $myConditions = MedicineCabinetConditions::where('medicineCabinetId',$medicineCabinetId)
                ->where('conditionId','!=','0')->pluck('conditionId')->toArray();

                // exclude selected conditions data for dropdown
                $conditions = $conditions->whereNotIn('id',$myConditions);
                
                // if already added conditions then get the count
                $conditionsCount = count($medicineCabinetConditions);
                $conditionsCount = $conditionsCount == 5 ? $conditionsCount : 5 - $conditionsCount;
            }

            // get the conditions data in array for the condition dropdown
            $conditions = $conditions->orderBy('conditionName','ASC')->whereNull('deleted_at')->get()->toArray();


            // get medicine cabinet notes data if available
            $medicineCabinetNotesData = MedicineCabinetNotes::where('medicineCabinetId',$medicineCabinetId)
            ->select('notes',DB::raw("DATE_FORMAT(created_at, '%d-%b-%Y') as date"))
            ->orderBy('created_at','DESC')->get()->toArray();
            

            // Get frequency values from master table
            $frequency = Master::select('id','name')->where('type','9')->get()->toArray();

            // Get dosage type values from master table
            $dosageType = Master::select('id','name')->where('type','10')->get()->toArray();
            
            // Add the page title name as per the medicine type
            $pageTitle = 'Edit Medicine';
            if($type == 'naturalMedicine'){
                $pageTitle = 'Edit Supplement';
            }
            if($type == 'rxDrugs'){
                $pageTitle = 'Edit Rx';
            }
            if($type == 'product'){
                $pageTitle = 'Edit Supplement Product';
            }

            return view('page.cabinet-edit',compact('backButton','pageTitle','medicineCabinetData','conditions','medicineCabinetConditions','frequency','dosageType','medicineCabinetNotesData','conditionsCount','profileMemberId'));

        }else{

            return redirect()->back()->with('error', 'Data not found for selected medicine. Please try again.');
        }

        
    }


    /*** 
     * Update Natural medicine / Rx Drug data and its condition in medicine cabinet table
    */
    public function updateMedicineCabinet(Request $request){
        // Get Logged in user id
        $userId = \Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'medicineCabinetId' => 'required',
            'notes' => 'required_if:updateFormType,==,notesForm',
            'updateFormType' => 'required',

        ]);

        //validation failed
        if ($validator->fails()) 
        {
            return back()->withErrors($validator)->withInput();
        
        }else{
            // when validation is passed, then execute below code logic

            $medicineCabinetId = $request->medicineCabinetId;

            $frequency = $request->frequency ? $request->frequency : null;
            $dosage = $request->dosage ? $request->dosage : null;
            $dosageType = $request->dosageType ? $request->dosageType : null;
            $notes = $request->notes ? $request->notes : null;

            $updateFormType = $request->updateFormType;

            DB::beginTransaction();

            $medicineCabinetUpdate = MedicineCabinet::find($medicineCabinetId);
            if(!empty($medicineCabinetUpdate)){
                
                session()->put('updateFormType', $updateFormType); // Set the session for the selected form tab value to retain the form display
                        
                // if the data is of details form then update the details in medicine_cabinet table - code start
                if($updateFormType == 'detailsForm'){

                    // Add conditions in medicine cabinet conditions table if condition added
                    if(!empty($request->condition)){
                        $conditionIds = $request->condition;
                        foreach($conditionIds as $conditionId){

                            // check if condition is custom text, then pass conditionId = 0 and pass the name in customConditionName field
                            $customConditionText = null;
                            if(!is_numeric($conditionId)){
                                $customConditionText = $conditionId;
                                $conditionId = '0';
                            }
                            // Insert the conditions data in medicine cabinet conditions table
                            $medCabConditions = new MedicineCabinetConditions;
                            $medCabConditions->medicineCabinetId = $medicineCabinetId;
                            $medCabConditions->conditionId = $conditionId;
                            $medCabConditions->customConditionName = $customConditionText;
                            $medCabConditions->updated_at = Carbon::now();
                            $medCabConditions->save();
                        }
                    }

                    $medicineCabinetUpdate->frequency = $frequency;
                    $medicineCabinetUpdate->dosage = $dosage;
                    $medicineCabinetUpdate->dosageType = $dosageType;

                    if($medicineCabinetUpdate->save()){
                        DB::commit();
                        if(isset($request->saveAndExist) && !empty($request->saveAndExist)){
                            /**
                             * Check if update is done from current user's profile member, then after updating data return to its profile member's wellkabinet screen. else return to current user's wellkabinet screen
                             **/
                            if(!empty($request->profileMemberId)){
                                return redirect()->route('medicine-cabinet',$request->profileMemberId)->with('message', 'Medicine details updated successfully');
                            }else{
                                return redirect()->route('medicine-cabinet')->with('message', 'Medicine details updated successfully');
                            }
                            
                        }
                        return redirect()->back()->with('message',"Medicine details updated successfully");
                    }else{
                        DB::rollback();
                        return redirect()->back()->with('error',"Something went wrong while saving the details. Please try again.");
    
                    }     
                }
                // if the data is of details form then update the details in medicine_cabinet table - code end


                // if the data is of notes form then insert the details in medicine_cabinet_notes table - code start
                if($updateFormType == 'notesForm'){
                    $medicineCabinetUpdateNotes = new MedicineCabinetNotes();
                    $medicineCabinetUpdateNotes->medicineCabinetId = $medicineCabinetId;
                    $medicineCabinetUpdateNotes->notes = $notes;

                    if($medicineCabinetUpdateNotes->save()){
                        DB::commit();
                        if(isset($request->saveAndExist) && !empty($request->saveAndExist) && isset($request->clickMemberId) && !empty($request->clickMemberId)){
                            /**
                             * Check if update is done from current user's profile member, then after updating data return to its profile member's wellkabinet screen. else return to current user's wellkabinet screen
                             **/
                            if(!empty($request->profileMemberId)){
                                return redirect()->route('medicine-cabinet',$request->profileMemberId)->with('message', 'Medicine notes updated successfully');
                            }
                            return redirect()->route('medicine-cabinet')->with('message', 'Medicine notes updated successfully');
                            
                        }
                        return redirect()->back()->with('message',"Medicine notes updated successfully");
                    }else{
                        DB::rollback();
                        return redirect()->back()->with('error',"Something went wrong while saving the notes. Please try again.");
    
                    }  
                }
                // if the data is of notes form then insert the details in medicine_cabinet_notes table - code start

            }else{
                DB::rollback();
                return redirect()->back()->with('error',"Id not found. Please try again.");
            }

        }
    }


    /*** 
     * Delete Natural medicine / Rx Drug data, its condition & notes if exists from the medicine_cabinet & medicine_cabinet_notes table
    */
    public function deleteMedicine(Request $request){
        try{
            // Get Logged in user id
            $userId = Auth::user()->id;
            $medicineCabinetId = $request->medicineCabinetId;    
            $medicineDeletedCheck = MedicineCabinet::where('id',$medicineCabinetId)->whereNotNull('deleted_at')->count(); 
            if($medicineDeletedCheck == 0){
                
                // delete the medicine cabinet notes if data exists
                $checkMedicineNotesData = MedicineCabinetNotes::where('medicineCabinetId',$medicineCabinetId)->count();
                if($checkMedicineNotesData != 0){
                    $deleteMedicineNotesData = MedicineCabinetNotes::where('medicineCabinetId',$medicineCabinetId)->delete();
                }

                // delete the medicine cabinet if data exists
                $deleteMedicineData = MedicineCabinet::where('id',$medicineCabinetId)->whereNull('deleted_at');
                if($deleteMedicineData->delete()){
                    $request->session()->flash('message', 'Medicine details deleted successfully.');
                    return json_encode(array('status'=>'0'));
                }
                else{
                    $request->session()->flash('error', 'Something went wrong, please try again.');
                    return json_encode(array('status'=>'1'));
                }

            }else{
                $request->session()->flash('error', 'This medicine details is already deleted.');
                return json_encode(array('status'=>'1'));
            }
            
           
        }catch (Exception $e) {
            /* Something went wrong while displaying acl details */
            $error_message = $e->getMessage();
            return json_encode([
                'message'=> $error_message,
                'status' => 1
            ]);
        }
    }

    /*** 
     * Delete selected condition from Natural medicine / Rx Drug data from the medicine_cabinet table
    */
    public function deleteMedicineCondition(Request $request){

        try{
            // Get Logged in user id
            $userId = Auth::user()->id;
            $medicineCabinetId = $request->medicineCabinetId;
            $conditionId = $request->conditionId;    
            $medicineDataCheck = MedicineCabinet::where('id',$medicineCabinetId)->whereNull('deleted_at')->count(); 
            if($medicineDataCheck != 0){
                
                DB::beginTransaction();

                // Check if conditions data exist for selected medicine cabinet id in medicine cabinet table
                $conditionIdsArr = MedicineCabinetConditions::where('medicineCabinetId',$medicineCabinetId)->pluck('conditionId');
                if(!empty($conditionIdsArr)){
      
                    // Delete the conditionId data from medicine cabinet conditions table
                    $updateConditionIdsData = MedicineCabinetConditions::where('id',$conditionId)
                    ->where('medicineCabinetId',$medicineCabinetId)
                    ->delete();

                    // if details are updated successfully then commit the record in table and show success message
                    if($updateConditionIdsData){
                        DB::commit();
                        $request->session()->flash('message', 'Condition deleted successfully.');
                        return json_encode(array('status'=>'0','message' => 'Condition deleted successfully.'));
                    }else{
                        // if details are not updated properly then show appropriate error message & rollback query transaction
                        DB::rollback();
                        $request->session()->flash('error', 'Something went wrong while deleting the condition, please try again.');
                        return json_encode(array('status'=>'1','message' => 'Something went wrong while deleting the condition, please try again.'));
                    }

                }else{
                    // If there are no conditions added by user to delete them then show no conditions found error message
                    DB::rollback();
                    $request->session()->flash('error', 'Tagged conditions data not found, please try again.');
                    return json_encode(array('status'=>'1','message' => 'Tagged conditions data not found, please try again.'));
                }

            }else{
                $request->session()->flash('error', 'Medicine details not found.');
                return json_encode(array('status'=>'1','message' => 'Medicine details not found.'));
            }
            
           
        }catch (Exception $e) {
            /* Something went wrong while deleting details */
            $error_message = $e->getMessage();
            $request->session()->flash('error', $error_message);
            return json_encode([
                'message'=> $error_message,
                'status' => 1
            ]);
        }
    }

    /*** 
     * Update "not taking" value for selected medicine of Natural medicine / Rx Drug data from the medicine_cabinet table
    */
    public function updateTakingMedicineStatus(Request $request){
        
        try{

            // Get Logged in user id
            $userId = Auth::user()->id;

            $status = $request->takingStatus;
            $medicineCabinetId = $request->medicineCabinetId;

            DB::beginTransaction();

            // Update the taking status value to 1(taking) if not already updated.
            if($status == '1'){

                $checkTakingStatus = MedicineCabinet::where('id',$medicineCabinetId)->where('isTaking','1')
                ->whereNull('deleted_at')->count();
                if($checkTakingStatus == 0){
                    $updateIsTakingStatus = MedicineCabinet::where('id',$medicineCabinetId)
                    ->update([
                        'isTaking' => $status
                    ]);
                    if($updateIsTakingStatus){
                        // if details are updated successfully then commit the record in table and show success message
                        DB::commit();
                        return json_encode(array('status'=>'0','isTakingStatus' => '1','message' => 'Medicine taking status updated successfully.'));
                    }else{
                        // if details are not updated properly then show appropriate error message & rollback query transaction
                        DB::rollback();
                        return json_encode(array('status'=>'1','message' => 'Something went wrong while updating the status, please try again.'));
                    }

                }else{
                    DB::rollback();
                    return json_encode(array('status'=>'0','message' => 'Medicine status is already updated to taking.'));
                }
            }
            // Update the taking status value to 0(Not taking) if not already updated.
            else if($status == '0'){

                $checkNotTakingStatus = MedicineCabinet::where('id',$medicineCabinetId)->where('isTaking','0')
                ->whereNull('deleted_at')->count();
                if($checkNotTakingStatus == 0){
                    $updateNotTakingStatus = MedicineCabinet::where('id',$medicineCabinetId)
                    ->update([
                        'isTaking' => $status
                    ]);
                    if($updateNotTakingStatus){
                        // if details are updated successfully then commit the record in table and show success message
                        DB::commit();
                        return json_encode(array('status'=>'0','isTakingStatus' => '0','message' => 'Medicine not taking status updated successfully.'));
                    }else{
                        // if details are not updated properly then show appropriate error message & rollback query transaction
                        DB::rollback();
                        return json_encode(array('status'=>'1','message' => 'Something went wrong while updating the status, please try again.'));
                    }

                }else{
                    DB::rollback();
                    return json_encode(array('status'=>'0','message' => 'Medicine status is already updated to not taking.'));
                }
            }
         
            
           
        }catch (Exception $e) {
            /* Something went wrong while updating details */
            $error_message = $e->getMessage();
            return json_encode([
                'message'=> $error_message,
                'status' => 1
            ]);
        }
    }

    /***
     * List the interaction of selected drug / natural medicine data in popup when clicked from interaction icon
     */
    public function getInteractionsDetails(Request $request){

        // Get Logged in user id
        $userId = Auth::user()->id;

        $drugId = $request->drugId ? $request->drugId : null;
        $naturalMedicineId = $request->naturalMedicineId ? $request->naturalMedicineId : null;
        $productId = $request->productId ? $request->productId : null;
        $profileMemberId = $request->profileMemberId ? $request->profileMemberId : null;

        // Get drugs / natural medicine ids for the interaction data
        $medicineCabinetData = MedicineCabinet::where('userId',$userId)->whereNull('deleted_at');

        $productIds = MedicineCabinet::where('medicine_cabinet.userId',$userId)->whereNotNull('medicine_cabinet.productId')->whereNull('medicine_cabinet.deleted_at');

        // Check if current medicine cabinet access is of profile member, else exclude profile member id selection
        if(!empty($profileMemberId)){
            $medicineCabinetData = $medicineCabinetData->where('profileMemberId',$profileMemberId);
            $productIds = $productIds->where('medicine_cabinet.profileMemberId',$profileMemberId);
        }
        else{
            $medicineCabinetData = $medicineCabinetData->whereNull('profileMemberId');
            $productIds = $productIds->whereNull('medicine_cabinet.profileMemberId');
        }

        $html = '';
        $htmlNew = '';
        $class = "";
        $finalArray = array();
        $circle_class = "";

        // Check if selected id is of drug
        if(!empty($drugId)){
            $naturalMedicineIds = $medicineCabinetData
            ->whereNotNull('naturalMedicineId')
            ->pluck('naturalMedicineId')->toArray();

            $getDrugData = DB::table('drugs')->where('id',$drugId)->get()->first();
            
            // if natural Medicine data exist added by logged in user then execute below code
            if(!empty($naturalMedicineIds)){
                foreach ($naturalMedicineIds as $naturalMedicineIdVal){
    
                    $getInteractionsData = DrugsInteractions::where('drugId',$drugId)
                    ->join('therapy','therapy.id','=','drugs_interactions.naturalMedicineId')
                    ->where('naturalMedicineId',$naturalMedicineIdVal)
                    ->whereNull('drugs_interactions.deleted_at')->get()->toArray();

                    if(!empty($getInteractionsData)){
                        $getInteractionsData = json_decode(json_encode($getInteractionsData),true);
                        foreach($getInteractionsData as $key => $fdata){
                            $interData = $fdata['interactionDetails'];
                            $interData = json_decode($interData,true);
                            $referenceNumbers = $interData['reference-numbers'];
                            $description = '';
                            
                            if(strtolower($interData['rating-label']) == 'major'){
                                $class = "red-card";
                                $circle_class = asset('images/major.svg');
                                $interactionPriority = '1';
                            }else if(strtolower($interData['rating-label']) == 'moderate'){
                                $class = "orange-card";
                                $circle_class = asset('images/moderate.svg');
                                $interactionPriority = '2';
                            }else if(strtolower($interData['rating-label']) == 'minor'){
                                $class = "green-card";
                                $circle_class = asset('images/minor.svg');
                                $interactionPriority = '3';
                            }else{
                                $class = "gray-card";
                                $circle_class = asset('images/none.svg');
                                $interactionPriority = '4';
                            }

                            $description = $fdata['description'];
                            
                            //Reference Start
                            $therapyReferenceDetails = NaturalMedicineReference::whereIn('number',$referenceNumbers)
                            ->whereNull('deleted_at')->get()->toArray();
                            if(!empty($therapyReferenceDetails)){
                                foreach ($therapyReferenceDetails as $therapyReferenceDetailsKey => $therapyReferenceDetailsValue) {
                                
                                    $referenceNumber = $therapyReferenceDetailsValue['number'];
                                    $referenceDescription = $therapyReferenceDetailsValue['description'];
            
                                    $clickHereToResearch = '';
                                    if(!empty($therapyReferenceDetailsValue['medicalPublicationId'])){
                                        $medicalPublicationId = $therapyReferenceDetailsValue['medicalPublicationId'];
                                        $researchApi = 'https://pubmed.ncbi.nlm.nih.gov/'.$medicalPublicationId;
                                        $clickHereToResearch = '<br/><a href='.$researchApi.' target=\'_blank\'>Click to see research</a>';
                                    }
                                    else
                                    {
                                        $clickHereToResearch = '<br/><a href="javascript:void(0)" class="text-secondary">Click to see research</a>';
                                    }
                                    $referenceDescriptionDataContent = $referenceDescription.' '.$clickHereToResearch;
                                    
                                    //Replace double quote with single quote as it was creating issue on anchor tag
                                    $referenceDescriptionDataContent = str_replace('"', "'", $referenceDescriptionDataContent);
                                    $anchorTag = '<a tabindex="0" class="dd" data-placement="top" role="button" data-toggle="popover" data-html="true" data-trigger="focus" data-content="'.$referenceDescriptionDataContent.'" target="_blank" >'.$referenceNumber.'</a>';

                                    $description = str_replace("(".$referenceNumber.",","(".$anchorTag.",",$description);

                                    $description = str_replace("(".$referenceNumber.")","(".$anchorTag.")",$description);

                                    $description = str_replace(",".$referenceNumber.",",",".$anchorTag.",",$description);

                                    $description = str_replace(",".$referenceNumber.")",",".$anchorTag.")",$description);
                                }
                            }
                            //Reference End
    
                            $data['description'] = $description;
                            $interactionsData[] = $data;
                            
                            
                            // Get level of evidence definition - Start
                            $levelOfEvidenceDefinitionVal = '';
                            $levelOfEvidenceDefinition = $interData['level-of-evidence-definition'];
                            if(!empty($levelOfEvidenceDefinition)){
                                switch ($levelOfEvidenceDefinition) {
                                    case "High-quality randomized controlled trial (RCT)":
                                        $levelOfEvidenceDefinitionVal = 'a1';
                                        break;
                                    case "High-quality meta-analysis (quantitative systematic review)":
                                        $levelOfEvidenceDefinitionVal = 'a2';
                                        break;
                                    case "Nonrandomized clinical trial":
                                        $levelOfEvidenceDefinitionVal = 'b1';
                                        break;
                                    case "Nonquantitative systematic review":
                                        $levelOfEvidenceDefinitionVal = 'b2';
                                        break;
                                    case "Lower quality RCT":
                                        $levelOfEvidenceDefinitionVal = 'b3';
                                        break;
                                    case "Clinical cohort study":
                                        $levelOfEvidenceDefinitionVal = 'b4';
                                        break;
                                    case "Case-control study":
                                        $levelOfEvidenceDefinitionVal = 'b5';
                                        break;
                                    case "Historical control":
                                        $levelOfEvidenceDefinitionVal = 'b6';
                                        break;
                                    case "Epidemiologic study":
                                        $levelOfEvidenceDefinitionVal = 'b7';
                                        break;
                                    case "Consensus":
                                        $levelOfEvidenceDefinitionVal = 'c1';
                                        break;
                                    case "Expert opinion":
                                        $levelOfEvidenceDefinitionVal = 'c2';
                                        break;
                                    case "Anecdotal evidence":
                                        $levelOfEvidenceDefinitionVal = 'd1';
                                        break;
                                    case "In vitro or animal study":
                                        $levelOfEvidenceDefinitionVal = 'd2';
                                        break;
                                    case "Theoretical based on pharmacology":
                                        $levelOfEvidenceDefinitionVal = 'd3';
                                        break;
                                }
                            }
                            // Get level of evidence definition - End


                            $temp = array();
                            $temp['therapy'] = $fdata['therapy'];
                            $temp['title'] = $interData['title'];
                            $temp['severity'] = $fdata['severity'];
                            $temp['drugName'] = $fdata['drugName']." - ".$getDrugData->brand_name;
                            $temp['drugId'] = $drugId;
                            $temp['naturalMedicineId'] = $naturalMedicineIdVal;
                            $temp['occurrence'] = $fdata['occurrence'];
                            $temp['levelOfEvidence'] = $fdata['levelOfEvidence'];
                            $temp['levelOfEvidenceDefinition'] = $levelOfEvidenceDefinitionVal;
                            $temp['description'] = $description;
                            $temp['interactionPriority'] = $interactionPriority;
                            $temp['interactionRating'] = strtolower($interData['rating-label']);
                            $temp['interactionIcon'] = $circle_class;
                            $temp['class'] = $class;
                            $temp['interactionRatingText'] = $interData['rating-text'];
                            $temp['isInteractionsFound'] = 1;
                            array_push($finalArray,$temp);
                        }
                    }
                }
                
                // Get the products data interactions if there
                $getProductIds = $productIds
                ->join('product','medicine_cabinet.productId','=','product.id')
                ->pluck('product.productId')->toArray();
                $getProductsInteractionData = Helper::getProductsInteractionWithDrug($getProductIds, $drugId,'1',$profileMemberId);
                if(!empty($getProductsInteractionData)){
                    $finalArray = array_merge($getProductsInteractionData, $finalArray);
                }

                //sort list of data of interactions with natural medicine in interaction rating priority order - code start 
                usort($finalArray, function($finalArrayOne, $finalArrayTwo) {
                    return $finalArrayOne['interactionPriority'] <=> $finalArrayTwo['interactionPriority'];
                });
                //sort list of data of interactions with natural medicine in interaction rating priority order - code end

                // Arrange array
                foreach($finalArray as $entry => $vals)
                {
                    // Burficate if the interaction data is of product interaction
                    if(!empty($vals['interaction_display_name'])){
                        $lastArray[$vals['interaction_display_name']][]=$vals;
                    }else{
                        $lastArray[$vals['therapy']][]=$vals;
                    }
                    
                }

                foreach ($lastArray as $key => $value) {

                    $no = Helper::generateRandomNumber();
                    $card_color = $value[0]['class'];
                    $html .= "<div class='card ".$card_color."'>";
                        $html .= "<div class='card-head' id='headingInteractions'".$no.">";
                            $html .= "<h2 class='mb-0 collapsed' data-toggle='collapse' data-target='#collapseInteractions".$no."' aria-expanded='false' aria-controls='collapseInteractions".$no."'>";
                                $html .= "<div class='cabinet-acco-img'><img src=".$value[0]['interactionIcon']." alt='major'></div>";
                                $html .= "<div class='int-headeing'>";
                                    
                                    if($value[0]['isInteractionsFound'] == '1'){
                                        $html .= "<div class='cabinet-acco-title'>";
                                            $html .= $key." <br><span style='color: #4f4f4f;'>Interaction Rating:<strong> ".ucfirst($value[0]['interactionRating'])."</strong></span>";
                                        $html .= "</div>";
                                        $html .= "<div class='combination'>".$value[0]['interactionRatingText']."</div>";
                                    }else{
                                        $html .= "<div class='cabinet-acco-title pt-3'>";
                                            $html .= $key." <br><span style='color: #4f4f4f;'>No interactions found</span>";
                                        $html .= "</div>";
                                    }

                                $html .= "</div>";
                            $html .= "</h2>";
                        $html .= "</div>";
                        
                        if($value[0]['isInteractionsFound'] == '1'){

                            $html .= "<div id='collapseInteractions".$no."' class='collapse' aria-labelledby='headingInteractions".$no."' data-parent='#accordionInteractions'>";
                                $html .= "<div class='card-body'>";
                                    
                                    $interactionsDataArrCount = count($value) - 1; // get the count of interaction data
                                    
                                    foreach ($value as $valueKey => $data) {
                                        $html .= "<p>";
                                            $html .= "<span><b>".$data['title']."</b></span></br></br>";
                                            $html .= "<span>Interaction Rating = ".ucfirst($data['interactionRating'])."</span></br></br>";
                                            $html .= "Severity = ".ucfirst($data['severity'])."<br>";
                                            $html .= "Occurance = ".ucfirst($data['occurrence'])."<br>";
                                            $html .= "Level of Evidence = <a href='javascript:void(0);' onClick='showLevelOfEvidencePopUp(".$data['levelOfEvidenceDefinition'].")' title='Click here to see what it means'>".$data['levelOfEvidence']."</a>";
                                        $html .= "</p>";
                                        $html .= $data['description'];

                                        // Add new line if there are more than one interaction data
                                        if($interactionsDataArrCount != $valueKey){
                                            $html .= "<br><br><br>";
                                        }
                                    }

                                $html .= "</div>";
                            $html .= "</div>";
                        }

                    $html .= "</div>";
                    
                }
            }
            // If no natural medicine data found added by logged in user then show appropriate message 
            else{

                $getProductIds = $productIds->join('product','medicine_cabinet.productId','=','product.id')
                ->pluck('product.productId')->toArray();

                $getProductsInteractionData = Helper::getProductsInteractionWithDrug($getProductIds, $drugId,'2',$profileMemberId);
                if(!empty($getProductsInteractionData)){
                    $html .= $getProductsInteractionData;
                }else{
                    $html .= "<div class='text-center'>";
                        $html .= "<h5>Found no interactions. Please add atleast one natural medicine to get the interaction details.</h5>";
                    $html .= "<div>";
                }
                
            }

            return response()->json([
                "status" => '1',
                "interactionsDataHtml" => $html
            ]);

        }

        // Check if selected id is of natural medicine
        else if(!empty($naturalMedicineId)){

            // Get drugs id from the medicine cabinet added by logged in user
            $drugIds = $medicineCabinetData            
            ->whereNotNull('drugId')
            ->pluck('drugId')->toArray();

            // if drugs data exist added by logged in user then execute below code
            if(!empty($drugIds)){
                foreach ($drugIds as $drugIdVal){
                    
                    // Get drug data
                    $getDrugData = DB::table('drugs')->where('id',$drugIdVal)->get()->first();
    
                    // Get the interactions from the drug id and natural medicine
                    $getInteractionsData = DrugsInteractions::where('drugId',$drugIdVal)
                    ->join('therapy','therapy.id','=','drugs_interactions.naturalMedicineId')
                    ->where('naturalMedicineId',$naturalMedicineId)
                    ->whereNull('drugs_interactions.deleted_at')->get()->toArray();
    
                    // If interactions exist then execute below code
                    if(!empty($getInteractionsData)){
                        $getInteractionsData = json_decode(json_encode($getInteractionsData),true);
                        foreach($getInteractionsData as $key => $fdata){
                            $interData = $fdata['interactionDetails'];
                            $interData = json_decode($interData,true);
                            $referenceNumbers = $interData['reference-numbers'];
                            $description = '';
                            
                            if(strtolower($interData['rating-label']) == 'major'){
                                $class = "red-card";
                                $circle_class = asset('images/major.svg');
                                $interactionPriority = '1';
                            }else if(strtolower($interData['rating-label']) == 'moderate'){
                                $class = "orange-card";
                                $circle_class = asset('images/moderate.svg');
                                $interactionPriority = '2';
                            }else if(strtolower($interData['rating-label']) == 'minor'){
                                $class = "green-card";
                                $circle_class = asset('images/minor.svg');
                                $interactionPriority = '3';
                            }else{
                                $class = "gray-card";
                                $circle_class = asset('images/none.svg');
                                $interactionPriority = '4';
                            }
    
                            $description = $fdata['description'];
                            
                            //Reference Start
                            $therapyReferenceDetails = NaturalMedicineReference::whereIn('number',$referenceNumbers)
                            ->whereNull('deleted_at')->get()->toArray();
                            if(!empty($therapyReferenceDetails)){
                                foreach ($therapyReferenceDetails as $therapyReferenceDetailsKey => $therapyReferenceDetailsValue) {
                                
                                    $referenceNumber = $therapyReferenceDetailsValue['number'];
                                    $referenceDescription = $therapyReferenceDetailsValue['description'];
            
                                    $clickHereToResearch = '';
                                    if(!empty($therapyReferenceDetailsValue['medicalPublicationId'])){
                                        $medicalPublicationId = $therapyReferenceDetailsValue['medicalPublicationId'];
                                        $researchApi = 'https://pubmed.ncbi.nlm.nih.gov/'.$medicalPublicationId;
                                        $clickHereToResearch = '<br/><a href='.$researchApi.' target=\'_blank\'>Click to see research</a>';
                                    }
                                    else
                                    {
                                        $clickHereToResearch = '<br/><a href="javascript:void(0)" class="text-secondary">Click to see research</a>';
                                    }
                                    $referenceDescriptionDataContent = $referenceDescription.' '.$clickHereToResearch;
                                    
                                    //Replace double quote with single quote as it was creating issue on anchor tag
                                    $referenceDescriptionDataContent = str_replace('"', "'", $referenceDescriptionDataContent);
                                    $anchorTag = '<a tabindex="0" class="dd" data-placement="top" role="button" data-toggle="popover" data-html="true" data-trigger="focus" data-content="'.$referenceDescriptionDataContent.'" target="_blank" >'.$referenceNumber.'</a>';
    
                                    $description = str_replace("(".$referenceNumber.",","(".$anchorTag.",",$description);
    
                                    $description = str_replace("(".$referenceNumber.")","(".$anchorTag.")",$description);
    
                                    $description = str_replace(",".$referenceNumber.",",",".$anchorTag.",",$description);
    
                                    $description = str_replace(",".$referenceNumber.")",",".$anchorTag.")",$description);
                                }
                            }
                            //Reference End
     
                            $data['description'] = $description;
                            $interactionsData[] = $data;
                            
                            
                            // Get level of evidence definition - Start
                            $levelOfEvidenceDefinitionVal = '';
                            $levelOfEvidenceDefinition = $interData['level-of-evidence-definition'];
                            if(!empty($levelOfEvidenceDefinition)){
                                switch ($levelOfEvidenceDefinition) {
                                    case "High-quality randomized controlled trial (RCT)":
                                        $levelOfEvidenceDefinitionVal = 'a1';
                                        break;
                                    case "High-quality meta-analysis (quantitative systematic review)":
                                        $levelOfEvidenceDefinitionVal = 'a2';
                                        break;
                                    case "Nonrandomized clinical trial":
                                        $levelOfEvidenceDefinitionVal = 'b1';
                                        break;
                                    case "Nonquantitative systematic review":
                                        $levelOfEvidenceDefinitionVal = 'b2';
                                        break;
                                    case "Lower quality RCT":
                                        $levelOfEvidenceDefinitionVal = 'b3';
                                        break;
                                    case "Clinical cohort study":
                                        $levelOfEvidenceDefinitionVal = 'b4';
                                        break;
                                    case "Case-control study":
                                        $levelOfEvidenceDefinitionVal = 'b5';
                                        break;
                                    case "Historical control":
                                        $levelOfEvidenceDefinitionVal = 'b6';
                                        break;
                                    case "Epidemiologic study":
                                        $levelOfEvidenceDefinitionVal = 'b7';
                                        break;
                                    case "Consensus":
                                        $levelOfEvidenceDefinitionVal = 'c1';
                                        break;
                                    case "Expert opinion":
                                        $levelOfEvidenceDefinitionVal = 'c2';
                                        break;
                                    case "Anecdotal evidence":
                                        $levelOfEvidenceDefinitionVal = 'd1';
                                        break;
                                    case "In vitro or animal study":
                                        $levelOfEvidenceDefinitionVal = 'd2';
                                        break;
                                    case "Theoretical based on pharmacology":
                                        $levelOfEvidenceDefinitionVal = 'd3';
                                        break;
                                }
                            }
                            // Get level of evidence definition - End
    
    
                            $temp = array();
                            $temp['therapy'] = $fdata['therapy'];
                            $temp['title'] = $interData['title'];
                            $temp['severity'] = $fdata['severity'];
                            $temp['drugName'] = $fdata['drugName']." - ".$getDrugData->brand_name;
                            $temp['drugId'] = $drugIdVal;
                            $temp['naturalMedicineId'] = $naturalMedicineId;
                            $temp['occurrence'] = $fdata['occurrence'];
                            $temp['levelOfEvidence'] = $fdata['levelOfEvidence'];
                            $temp['levelOfEvidenceDefinition'] = $levelOfEvidenceDefinitionVal;
                            $temp['description'] = $description;
                            $temp['interactionPriority'] = $interactionPriority;
                            $temp['interactionRating'] = strtolower($interData['rating-label']);
                            $temp['interactionIcon'] = $circle_class;
                            $temp['class'] = $class;
                            $temp['interactionRatingText'] = $interData['rating-text'];
                            $temp['isInteractionsFound'] = 1;
                            array_push($finalArray,$temp);
                        }
                    }
                }

                //sort list of data of interactions with Rx Drugs in interaction rating priority order - code start 
                usort($finalArray, function($finalArrayOne, $finalArrayTwo) {
                    return $finalArrayOne['interactionPriority'] <=> $finalArrayTwo['interactionPriority'];
                });
                //sort list of data of interactions with Rx Drugs in interaction rating priority order - code end

    
                // Arrange array by the drugs name
                foreach($finalArray as $entry => $vals)
                {
                    $lastArray[$vals['drugName']][]=$vals;
                }
                 
                foreach ($lastArray as $key => $value) {
     
                    $no = Helper::generateRandomNumber();
                    $card_color = $value[0]['class'];
                    $html .= "<div class='card ".$card_color."'>";
                        $html .= "<div class='card-head' id='headingInteractions'".$no.">";
                            $html .= "<h2 class='mb-0 collapsed' data-toggle='collapse' data-target='#collapseInteractions".$no."' aria-expanded='false' aria-controls='collapseInteractions".$no."'>";
                                $html .= "<div class='cabinet-acco-img'><img src=".$value[0]['interactionIcon']." alt='major'></div>";
                                $html .= "<div class='int-headeing'>";
                                    
                                    if($value[0]['isInteractionsFound'] == '1'){
                                        $html .= "<div class='cabinet-acco-title'>";
                                            $html .= $key." <br><span style='color: #4f4f4f;'>Interaction Rating:<strong> ".ucfirst($value[0]['interactionRating'])."</strong></span>";
                                        $html .= "</div>";
                                        $html .= "<div class='combination'>".$value[0]['interactionRatingText']."</div>";
                                    }else{
                                        $html .= "<div class='cabinet-acco-title pt-3'>";
                                            $html .= $key." <br><span style='color: #4f4f4f;'>No interactions found</span>";
                                        $html .= "</div>";
                                    }
    
                                $html .= "</div>";
                            $html .= "</h2>";
                        $html .= "</div>";
                        
                        if($value[0]['isInteractionsFound'] == '1'){
    
                            $html .= "<div id='collapseInteractions".$no."' class='collapse' aria-labelledby='headingInteractions".$no."' data-parent='#accordionInteractions'>";
                                $html .= "<div class='card-body'>";
                                    
                                    $interactionsDataArrCount = count($value) - 1; // get the count of interaction data
                                    
                                    foreach ($value as $valueKey => $data) {
                                        $html .= "<p>";
                                            $html .= "<span><b>".$data['title']."</b></span></br></br>";
                                            $html .= "<span>Interaction Rating = ".ucfirst($data['interactionRating'])."</span></br></br>";
                                            $html .= "Severity = ".ucfirst($data['severity'])."<br>";
                                            $html .= "Occurance = ".ucfirst($data['occurrence'])."<br>";
                                            $html .= "Level of Evidence = <a href='javascript:void(0);' onClick='showLevelOfEvidencePopUp(".$data['levelOfEvidenceDefinition'].")' title='Click here to see what it means'>".$data['levelOfEvidence']."</a>";
                                        $html .= "</p>";
                                        $html .= $data['description'];
    
                                        // Add new line if there are more than one interaction data
                                        if($interactionsDataArrCount != $valueKey){
                                            $html .= "<br><br><br>";
                                        }
                                    }
    
                                $html .= "</div>";
                            $html .= "</div>";
                        }
    
                    $html .= "</div>";
                }
                
            }
            // If no rx drugs found added by logged in user then show appropriate message 
            else{
                $html .= "<div class='text-center'>";
                    $html .= "<h5>Found no interactions. Please add atleast one Rx Drugs to get the interaction details.</h5>";
                $html .= "<div>";
            }
            
            return response()->json([
                "status" => '1',
                "interactionsDataHtml" => $html
            ]);

        }

        // Check if selected id is of product
        else if(!empty($productId)){
            
            $finalArr = array();

            // Get the therapy ids by the product id of product_therapy table
            $therapyIds = $productIds
            ->join('product','medicine_cabinet.productId','=','product.id')
            ->join('product_therapy','product.productId','=','product_therapy.productId')
            ->where('product.productId',$productId)
            ->whereNull('product_therapy.deleted_at')->pluck('therapyId')->toArray();

            // Get drugs id from the medicine cabinet added by logged in user
            $drugIds = $medicineCabinetData            
            ->whereNotNull('drugId')
            ->pluck('drugId')->toArray();

            // if drugs data exist added by logged in user then execute below code
            if(!empty($drugIds)){
                foreach ($drugIds as $drugIdVal){
                    
                    // Get drug data
                    $getDrugData = DB::table('drugs')->where('id',$drugIdVal)->get()->first();
                    
                    // Get the interactions from the drug id and natural medicine
                    $getInteractionsData = DrugsInteractions::where('drugId',$drugIdVal)
                    ->join('therapy','therapy.id','=','drugs_interactions.naturalMedicineId')
                    ->join('product_therapy','product_therapy.therapyId','=','therapy.id')
                    ->where('product_therapy.productId',$productId) 
                    ->whereIn('naturalMedicineId',$therapyIds)
                    ->whereNull('drugs_interactions.deleted_at')->get()->toArray();
    
                    
                    
                    // If interactions exist then execute below code
                    if(!empty($getInteractionsData)){
                        
                        $getInteractionsData = json_decode(json_encode($getInteractionsData),true);
                        foreach($getInteractionsData as $key => $fdata){

                            $interData = $fdata['interactionDetails'];
                            $interData = json_decode($interData,true);
                            $referenceNumbers = $interData['reference-numbers'];
                            $description = '';
                            
                            if(strtolower($interData['rating-label']) == 'major'){
                                $class = "red-card";
                                $circle_class = asset('images/major.svg');
                                $interactionPriority = '1';
                            }else if(strtolower($interData['rating-label']) == 'moderate'){
                                $class = "orange-card";
                                $circle_class = asset('images/moderate.svg');
                                $interactionPriority = '2';
                            }else if(strtolower($interData['rating-label']) == 'minor'){
                                $class = "green-card";
                                $circle_class = asset('images/minor.svg');
                                $interactionPriority = '3';
                            }else{
                                $class = "gray-card";
                                $circle_class = asset('images/none.svg');
                                $interactionPriority = '4';
                            }
    
                            $description = $fdata['description'];
                            
                            //Reference Start
                            $therapyReferenceDetails = NaturalMedicineReference::whereIn('number',$referenceNumbers)
                            ->whereNull('deleted_at')->get()->toArray();
                            if(!empty($therapyReferenceDetails)){
                                foreach ($therapyReferenceDetails as $therapyReferenceDetailsKey => $therapyReferenceDetailsValue) {
                                
                                    $referenceNumber = $therapyReferenceDetailsValue['number'];
                                    $referenceDescription = $therapyReferenceDetailsValue['description'];
            
                                    $clickHereToResearch = '';
                                    if(!empty($therapyReferenceDetailsValue['medicalPublicationId'])){
                                        $medicalPublicationId = $therapyReferenceDetailsValue['medicalPublicationId'];
                                        $researchApi = 'https://pubmed.ncbi.nlm.nih.gov/'.$medicalPublicationId;
                                        $clickHereToResearch = '<br/><a href='.$researchApi.' target=\'_blank\'>Click to see research</a>';
                                    }
                                    else
                                    {
                                        $clickHereToResearch = '<br/><a href="javascript:void(0)" class="text-secondary">Click to see research</a>';
                                    }
                                    $referenceDescriptionDataContent = $referenceDescription.' '.$clickHereToResearch;
                                    
                                    //Replace double quote with single quote as it was creating issue on anchor tag
                                    $referenceDescriptionDataContent = str_replace('"', "'", $referenceDescriptionDataContent);
                                    $anchorTag = '<a tabindex="0" class="dd" data-placement="top" role="button" data-toggle="popover" data-html="true" data-trigger="focus" data-content="'.$referenceDescriptionDataContent.'" target="_blank" >'.$referenceNumber.'</a>';
    
                                    $description = str_replace("(".$referenceNumber.",","(".$anchorTag.",",$description);
    
                                    $description = str_replace("(".$referenceNumber.")","(".$anchorTag.")",$description);
    
                                    $description = str_replace(",".$referenceNumber.",",",".$anchorTag.",",$description);
    
                                    $description = str_replace(",".$referenceNumber.")",",".$anchorTag.")",$description);
                                }
                            }
                            //Reference End
     
                            $data['description'] = $description;
                            $interactionsData[] = $data;
                            
                            
                            // Get level of evidence definition - Start
                            $levelOfEvidenceDefinitionVal = '';
                            $levelOfEvidenceDefinition = $interData['level-of-evidence-definition'];
                            if(!empty($levelOfEvidenceDefinition)){
                                switch ($levelOfEvidenceDefinition) {
                                    case "High-quality randomized controlled trial (RCT)":
                                        $levelOfEvidenceDefinitionVal = 'a1';
                                        break;
                                    case "High-quality meta-analysis (quantitative systematic review)":
                                        $levelOfEvidenceDefinitionVal = 'a2';
                                        break;
                                    case "Nonrandomized clinical trial":
                                        $levelOfEvidenceDefinitionVal = 'b1';
                                        break;
                                    case "Nonquantitative systematic review":
                                        $levelOfEvidenceDefinitionVal = 'b2';
                                        break;
                                    case "Lower quality RCT":
                                        $levelOfEvidenceDefinitionVal = 'b3';
                                        break;
                                    case "Clinical cohort study":
                                        $levelOfEvidenceDefinitionVal = 'b4';
                                        break;
                                    case "Case-control study":
                                        $levelOfEvidenceDefinitionVal = 'b5';
                                        break;
                                    case "Historical control":
                                        $levelOfEvidenceDefinitionVal = 'b6';
                                        break;
                                    case "Epidemiologic study":
                                        $levelOfEvidenceDefinitionVal = 'b7';
                                        break;
                                    case "Consensus":
                                        $levelOfEvidenceDefinitionVal = 'c1';
                                        break;
                                    case "Expert opinion":
                                        $levelOfEvidenceDefinitionVal = 'c2';
                                        break;
                                    case "Anecdotal evidence":
                                        $levelOfEvidenceDefinitionVal = 'd1';
                                        break;
                                    case "In vitro or animal study":
                                        $levelOfEvidenceDefinitionVal = 'd2';
                                        break;
                                    case "Theoretical based on pharmacology":
                                        $levelOfEvidenceDefinitionVal = 'd3';
                                        break;
                                }
                            }
                            // Get level of evidence definition - End
    
    
                            // Get the interaction data with natural medicine name
                            $interactionWithData = '';
                            $interactionWithNaturalMedicineName = Therapy::where('id',$fdata['naturalMedicineId'])->pluck('therapy')->first();
                            if(!empty($interactionWithNaturalMedicineName)){
                                $interactionWithData = ' interaction w/ '.$interactionWithNaturalMedicineName;
                            }

                            $temp = array();
                            $temp['therapy'] = $fdata['therapy'];
                            $temp['title'] = $interData['title'];
                            $temp['severity'] = $fdata['severity'];
                            $temp['drugName'] = $fdata['drugName']." - ".$getDrugData->brand_name.$interactionWithData;
                            $temp['drugId'] = $drugIdVal;
                            $temp['naturalMedicineId'] = $fdata['naturalMedicineId'];
                            $temp['interaction_display_name'] = $fdata['interaction_display_name'];
                            $temp['occurrence'] = $fdata['occurrence'];
                            $temp['levelOfEvidence'] = $fdata['levelOfEvidence'];
                            $temp['levelOfEvidenceDefinition'] = $levelOfEvidenceDefinitionVal;
                            $temp['description'] = $description;
                            $temp['interactionPriority'] = $interactionPriority;
                            $temp['interactionRating'] = strtolower($interData['rating-label']);
                            $temp['interactionIcon'] = $circle_class;
                            $temp['class'] = $class;
                            $temp['interactionRatingText'] = $interData['rating-text'];
                            $temp['isInteractionsFound'] = 1;
                            array_push($finalArr,$temp);
                        }
                    }
                }
               
                //sort list of data of interactions with Rx Drugs in interaction rating priority order - code start 
                usort($finalArr, function($finalArrayOne, $finalArrayTwo) {
                    return $finalArrayOne['interactionPriority'] <=> $finalArrayTwo['interactionPriority'];
                });
                //sort list of data of interactions with Rx Drugs in interaction rating priority order - code end
    
                // Arrange array by the drugs name
                foreach($finalArr as $entry => $vals)
                {
                    $lastArray[$vals['drugName']][]=$vals;
                }

                foreach ($lastArray as $key => $value) {
     
                    $no = Helper::generateRandomNumber();
                    $card_color = $value[0]['class'];
                    $html .= "<div class='card ".$card_color."'>";
                        $html .= "<div class='card-head' id='headingInteractions'".$no.">";
                            $html .= "<h2 class='mb-0 collapsed' data-toggle='collapse' data-target='#collapseInteractions".$no."' aria-expanded='false' aria-controls='collapseInteractions".$no."'>";
                                $html .= "<div class='cabinet-acco-img'><img src=".$value[0]['interactionIcon']." alt='major'></div>";
                                $html .= "<div class='int-headeing'>";
                                    
                                    if($value[0]['isInteractionsFound'] == '1'){

                                        $html .= "<div class='cabinet-acco-title'>";
                                            $html .= $key."<br><span style='color: #4f4f4f;'>Interaction Rating:<strong> ".ucfirst($value[0]['interactionRating'])."</strong></span>";
                                        $html .= "</div>";
                                        $html .= "<div class='combination'>".$value[0]['interactionRatingText']."</div>";
                                    }else{
                                        $html .= "<div class='cabinet-acco-title pt-3'>";
                                            $html .= $key." <br><span style='color: #4f4f4f;'>No interactions found</span>";
                                        $html .= "</div>";
                                    }
    
                                $html .= "</div>";
                            $html .= "</h2>";
                        $html .= "</div>";
                        
                        if($value[0]['isInteractionsFound'] == '1'){
    
                            $html .= "<div id='collapseInteractions".$no."' class='collapse' aria-labelledby='headingInteractions".$no."' data-parent='#accordionInteractions'>";
                                $html .= "<div class='card-body'>";

                                    $interactionsDataArrCount = count($value) - 1; // get the count of interaction data
                                    
                                    foreach ($value as $valueKey => $data) {
                                        $html .= "<p>";
                                            $html .= "<span><b>".$data['title']."</b></span></br></br>";
                                            $html .= "<span>Interaction Rating = ".ucfirst($data['interactionRating'])."</span></br></br>";
                                            $html .= "Severity = ".ucfirst($data['severity'])."<br>";
                                            $html .= "Occurance = ".ucfirst($data['occurrence'])."<br>";
                                            $html .= "Level of Evidence = <a href='javascript:void(0);' onClick='showLevelOfEvidencePopUp(".$data['levelOfEvidenceDefinition'].")' title='Click here to see what it means'>".$data['levelOfEvidence']."</a>";
                                        $html .= "</p>";
                                        $html .= $data['description'];
    
                                        // Add new line if there are more than one interaction data
                                        if($interactionsDataArrCount != $valueKey){
                                            $html .= "<br><br><br>";
                                        }
                                    }

                                $html .= "</div>";
                            $html .= "</div>";
                        }
    
                    $html .= "</div>";
                }
                
            }
            // If no rx drugs found added by logged in user then show appropriate message 
            else{
                $html .= "<div class='text-center'>";
                    $html .= "<h5>Found no interactions. Please add atleast one Rx Drugs to get the interaction details.</h5>";
                $html .= "<div>";
            }
            
            return response()->json([
                "status" => '1',
                "interactionsDataHtml" => $html
            ]);

        }
    }

   /***
     * Sends the mail notification to admin from current user regarding the supplement suggestion data
     */
    public function sendSuggestedSupplement(Request $request){

        $supplementName = $request->supplementName;
        $supplementBrandName = $request->supplementBrandName;
        $supplementSize = $request->supplementSize;

        $sendData = [
            'body' => 'Below is the new suggested supplement data <br> from user : '.Auth::user()->getUserName().' ('.Auth::user()->email .')

            <b>Supplement Name</b>: '.$supplementName.'
            <b>Supplement Brand Name</b>: '.$supplementBrandName.'
            <b>Supplement Size</b>: '.$supplementSize.'
            ',
        ];
        // Send mail to admin
        $sendEmail = Notification::route('mail','admin@wellkasa.com')->notify(new SendSupplementsSuggestionMailNotification($sendData));
        if(empty($sendEmail)){
            return response()->json(['status' => '1','message' => 'Your input has been submitted. We will contact you soon to update you on the status of your input request.']);
        }else{
            return response()->json(['status' => '0','message' => 'Something went wrong. Please try again.']);
        }
    }

}
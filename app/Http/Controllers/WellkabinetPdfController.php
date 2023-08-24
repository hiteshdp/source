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
use PDF;
use Notification;
use App\Notifications\SendWellkabinetReport;

class WellkabinetPdfController extends Controller
{

     /**
     *  This function complies wellkabinet data for pdf view &
     *  show all rx drugs name, natural medicine name & products added in wellkabinet screen.
     *
     * @return \Illuminate\Http\Response
     */
    public function pdfview($profileMemberId=null,$isSendinEmail=null,Request $request)
    {

        // Get Logged in user id
        $userId = \Auth::user()->id;

        // Get user email id
        $userEmailId = \Auth::user()->email;

        // get user name
        $userName = Auth::user()->name." ".Auth::user()->last_name;

        // Get First Name of logged in user
        $userFirstName = Auth::user()->name;

        // Check if the request of pdf is from the popup then check sender mail value 
        $toMail = $request->toMail ? $request->toMail : '';

        $medicineCabinetNotes = array(); // Store the medicine cabinet notes

        // Check if profile member id exists then decrypt profile member id and get the profile member user name
        if(!empty($profileMemberId)){
            $userName = ProfileMembers::where('addedByUserId',$userId)->where('id',$profileMemberId)
            ->select(DB::raw('CONCAT(first_name," ",last_name) As name'))->pluck('name')->first();

            $userFirstName = ProfileMembers::where('addedByUserId',$userId)->where('id',$profileMemberId)
            ->select(DB::raw('first_name As name'))->pluck('name')->first();

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
            DB::raw('CONCAT(medicine_cabinet.dosage," ",master.name) As dosage'),
            'medicine_cabinet.isTaking','medicine_cabinet.created_at')
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

                    // Get the interaction priority to sort accordingly
                    $medicineCabinetNaturalMedicineData[$medicineCabinetNaturalMedicineDataKey]['interactionPriority'] = Helper::getPriorityFromInteractionName($interactionsRatingValue);
                   
                    // Define type
                    $medicineCabinetNaturalMedicineData[$medicineCabinetNaturalMedicineDataKey]['type'] = 'naturalMedicine';
                    
                    // Get the frequency value for the dosage details
                    $naturalMedicinefrequencyValue = MedicineCabinet::where('medicine_cabinet.id',$medicineCabinetNaturalMedicineDataVal['medicineCabinetId'])
                    ->select('master.name as frequency')
                    ->leftJoin('master','medicine_cabinet.frequency','=','master.id')
                    ->pluck('frequency')->first();
                    if(!empty($naturalMedicinefrequencyValue)){
                        $medicineCabinetNaturalMedicineDosageVal = $medicineCabinetNaturalMedicineDataVal['dosage'];
                        $medicineCabinetNaturalMedicineData[$medicineCabinetNaturalMedicineDataKey]['dosage'] = $medicineCabinetNaturalMedicineDosageVal ? $medicineCabinetNaturalMedicineDosageVal.",<br><br>".$naturalMedicinefrequencyValue : $medicineCabinetNaturalMedicineDosageVal;
                    }


                    // Get the notes data
                    $hasNotes = Helper::getMedicineCabinetNotes($medicineCabinetNaturalMedicineDataVal['medicineCabinetId'],'1');
                    $medicineCabinetNaturalMedicineData[$medicineCabinetNaturalMedicineDataKey]['hasNotes'] = $hasNotes;
                    if($hasNotes !=0){
                        $medicineCabinetNotes[$medicineCabinetNaturalMedicineDataVal['name']."#".$medicineCabinetNaturalMedicineDataVal['medicineCabinetId']] = Helper::getMedicineCabinetNotes($medicineCabinetNaturalMedicineDataVal['medicineCabinetId'],'2');
                    }

                }
                $medicineCabinetNaturalMedicineDataArr = $medicineCabinetNaturalMedicineData;

                //sort list of data of natural medicine name in alphabetical order - code start 
                usort($medicineCabinetNaturalMedicineDataArr, function($finalArrayOne, $finalArrayTwo) {
                    return $finalArrayOne['interactionPriority'] <=> $finalArrayTwo['interactionPriority'];
                });
                //sort list of data of natural medicine name in alphabetical order - code end

                //sort list of taking medicine data at top order - code start 
                usort($medicineCabinetNaturalMedicineDataArr, function($finalArrayOne, $finalArrayTwo) {
                    return trim($finalArrayOne['isTaking']) < trim($finalArrayTwo['isTaking']);
                });
                //sort list of taking medicine data at top order - code end
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

                    // Get the interaction priority to sort accordingly
                    $medicineCabinetRxDrugsData[$medicineCabinetRxDrugsDataKey]['interactionPriority'] = Helper::getPriorityFromInteractionName($interactionsRatingValue);
                   
                    // Define type
                    $medicineCabinetRxDrugsData[$medicineCabinetRxDrugsDataKey]['type'] = 'rxDrugs';
                    
                    // Get the frequency value for the dosage details
                    $rxfrequencyValue = MedicineCabinet::where('medicine_cabinet.id',$medicineCabinetRxDrugsDataVal['medicineCabinetId'])
                    ->select('master.name as frequency')
                    ->leftJoin('master','medicine_cabinet.frequency','=','master.id')
                    ->pluck('frequency')->first();
                    if(!empty($rxfrequencyValue)){
                        $medicineCabinetRxDrugsDosageVal = $medicineCabinetRxDrugsDataVal['dosage'];
                        $medicineCabinetRxDrugsData[$medicineCabinetRxDrugsDataKey]['dosage'] = $medicineCabinetRxDrugsDosageVal ? $medicineCabinetRxDrugsDataVal['dosage'].",<br><br>".$rxfrequencyValue : $medicineCabinetRxDrugsDosageVal;
                    }

                    // Get the notes data
                    $hasRxDrugsNotes = Helper::getMedicineCabinetNotes($medicineCabinetRxDrugsDataVal['medicineCabinetId'],'1');
                    $medicineCabinetRxDrugsData[$medicineCabinetRxDrugsDataKey]['hasNotes'] = $hasRxDrugsNotes;
                    if($hasRxDrugsNotes !=0){
                        $medicineCabinetNotes[$medicineCabinetRxDrugsDataVal['name']."#".$medicineCabinetRxDrugsDataVal['medicineCabinetId']] = Helper::getMedicineCabinetNotes($medicineCabinetRxDrugsDataVal['medicineCabinetId'],'2');
                    }
                }
                $medicineCabinetRxDrugsDataArr = $medicineCabinetRxDrugsData;

                //sort list of data of Rx drugs name in alphabetical order - code start 
                usort($medicineCabinetRxDrugsDataArr, function($finalArrayOne, $finalArrayTwo) {
                    return trim($finalArrayOne['interactionPriority']) <=> trim($finalArrayTwo['interactionPriority']);
                });
                //sort list of data of Rx drugs name in alphabetical order - code end

                //sort list of taking medicine data at top order - code start 
                usort($medicineCabinetRxDrugsDataArr, function($finalArrayOne, $finalArrayTwo) {
                    return trim($finalArrayOne['isTaking']) < trim($finalArrayTwo['isTaking']);
                });
                //sort list of taking medicine data at top order - code end
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
                    
                    // Get the interaction priority to sort accordingly
                    $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['interactionPriority'] = Helper::getPriorityFromInteractionName($productInteractionsRatingValue);
                    
                    // Define type
                    $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['type'] = 'product';

                    // Check if isActive field is set to true for this product, if not true then disable redirection link and info popup message
                    if($medicineCabinetProductsDataVal['isActive'] == 'true'){

                        $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['imageRedirectLink'] =  $medicineCabinetProductsDataVal['imageRedirectLink'];
                    }
                    else{
                        $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['imageRedirectLink'] = '';
                    }
         
                    // Get the frequency value for the dosage details
                    $productfrequencyValue = MedicineCabinet::where('medicine_cabinet.id',$medicineCabinetProductsDataVal['medicineCabinetId'])
                    ->select('master.name as frequency')
                    ->leftJoin('master','medicine_cabinet.frequency','=','master.id')
                    ->pluck('frequency')->first();
                    if(!empty($productfrequencyValue)){
                        $medicineCabinetProductsDosageVal = $medicineCabinetProductsDataVal['dosage'];
                        $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['dosage'] = $medicineCabinetProductsDosageVal ? $medicineCabinetProductsDataVal['dosage'].",<br><br>".$productfrequencyValue : $medicineCabinetProductsDosageVal;
                    }
                    
                    // Get the notes data
                    $hasProductsNotes = Helper::getMedicineCabinetNotes($medicineCabinetProductsDataVal['medicineCabinetId'],'1');
                    $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['hasNotes'] = $hasProductsNotes;
                    if($hasProductsNotes !=0){
                        $medicineCabinetNotes[$medicineCabinetProductsDataVal['name']."#".$medicineCabinetProductsDataVal['medicineCabinetId']] = Helper::getMedicineCabinetNotes($medicineCabinetProductsDataVal['medicineCabinetId'],'2');
                    }
                }
                $medicineCabinetProductsDataArr = $medicineCabinetProductsData;

                //sort list of data of Products name in alphabetical order - code start 
                usort($medicineCabinetProductsDataArr, function($finalArrayOne, $finalArrayTwo) {
                    return trim($finalArrayOne['interactionPriority']) <=> trim($finalArrayTwo['interactionPriority']);
                });
                //sort list of data of Products name in alphabetical order - code end

                //sort list of taking medicine data at top order - code start 
                usort($medicineCabinetProductsDataArr, function($finalArrayOne, $finalArrayTwo) {
                    return trim($finalArrayOne['isTaking']) < trim($finalArrayTwo['isTaking']);
                });
                //sort list of taking medicine data at top order - code end
            }
            // Merged the data of Rx, Product & Natural medicine in exact order to display
            $medicineCabinetData = array_merge($medicineCabinetProductsDataArr,$medicineCabinetNaturalMedicineDataArr);
            $medicineCabinetData = array_merge($medicineCabinetRxDrugsDataArr, $medicineCabinetData);

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
        // Get the interactions for Rx drugs containing products and natural medicines details
        $interactionsData = $this->getInteractionsDetails($profileMemberId);


        // Get current date of file creation
        $createdOnDate = date("d M Y, H:i A");
        view()->share(compact('userFirstName','userName','createdOnDate','medicineCabinetData','interactionsData','medicineCabinetNotes')); 

        
        $pdf = PDF::loadView('page.reports.wellkabinet-pdf.index');
        // Check if the toMail value is not empty, then execute the send mail functionality else download the report
        if(!empty($toMail)){
            // Assign the report file name by the current logged in user name
            $wellkabinetReportFileName = $userName.' Wellkabinet';

            // Set the report name in the local system
            $fileName = "Wellkabinet".time()."."."pdf";
            // Store the file in the public pdf folder
            file_put_contents(public_path() . '/pdf/'.$fileName,$pdf->output());

            // Get the URL of the pdf folder
            $pdfFileUrl = url('/pdf').'/'.$fileName;

            // Send mail of the trend chart report
            $sent = Notification::route('mail' , $toMail)->notify(new SendWellkabinetReport($userName,$pdfFileUrl,$wellkabinetReportFileName));
            // Check if the report is sent successfully then display the success message, else show error message
            if(empty($sent)){
                
                // Delete the file once the mail sending process is done
                if(file_exists(public_path() . '/pdf/'.$fileName)){
                    unlink(public_path() . '/pdf/'.$fileName);
                }
                // Redirect back to same screen and display the success message in the page
                return redirect()->back()->with('message','Email sent successfully.');
            }else{
                if(file_exists(public_path() . '/pdf/'.$fileName)){
                    unlink(public_path() . '/pdf/'.$fileName);
                }
                // Redirect back to same screen and display the error message in the page
                return redirect()->back()->with('message','Something went wrong, Please try again.');
            }
            
        }else{
            return $pdf->download(str_replace(" ","_",$userName.' Wellkabinet').'.pdf');
        }
    }

    /***
     * List the interaction of selected drug / natural medicine data in popup when clicked from interaction icon
     */
    public function getInteractionsDetails($profileMemberId){

        // Get Logged in user id
        $userId = Auth::user()->id;


        // Get drugs / natural medicine ids for the interaction data
        $medicineCabinetData = MedicineCabinet::where('userId',$userId)->whereNull('deleted_at');

        $productIds = MedicineCabinet::where('medicine_cabinet.userId',$userId)->whereNotNull('medicine_cabinet.productId')
        ->whereNull('medicine_cabinet.deleted_at');

        // Check if current medicine cabinet access is of profile member, else exclude profile member id selection
        if(!empty($profileMemberId)){
            $medicineCabinetData = $medicineCabinetData->where('profileMemberId',$profileMemberId);
            $productIds = $productIds->where('medicine_cabinet.profileMemberId',$profileMemberId);
        }
        else{
            $medicineCabinetData = $medicineCabinetData->whereNull('profileMemberId');
            $productIds = $productIds->whereNull('medicine_cabinet.profileMemberId');
        }

      
        $getDrugsData = MedicineCabinet::where('medicine_cabinet.userId',$userId)->whereNotNull('medicine_cabinet.drugId');
        if(!empty($profileMemberId)){
            $getDrugsData = $getDrugsData->where('medicine_cabinet.profileMemberId',$profileMemberId);
        }else{
            $getDrugsData = $getDrugsData->whereNull('profileMemberId');
        }
        $getDrugsData = $getDrugsData->whereNull('medicine_cabinet.deleted_at')->get()->toArray();

        $html = '';
        $htmlNew = '';
        $class = "";
        $finalArray = array();
        $circle_class = "";
        
        // Get the products data interactions if there
        $getProductIds = $productIds
        ->join('product','medicine_cabinet.productId','=','product.id')
        ->pluck('product.productId')->toArray();
        if(!empty($getDrugsData)){
            foreach ($getDrugsData as $key => $value){
                $drugId = $value['drugId'];
                // Check if user has added any drug
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


                                    $temp = array();
                                    $temp['therapy'] = $fdata['therapy'];
                                    $temp['title'] = $interData['title'];
                                    $temp['severity'] = $fdata['severity'];
                                    $temp['drugName'] = $fdata['drugName']." - ".$getDrugData->brand_name;
                                    $temp['drugId'] = $drugId;
                                    $temp['naturalMedicineId'] = $naturalMedicineIdVal;
                                    $temp['occurrence'] = $fdata['occurrence'];
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
                            // unset($vals['description']); // Remove the description data
                            $lastArray[$vals['drugName']][]=$vals;
                        }
                        if(isset($lastArray)){
                            // Arrange array for product interaction and natural medicines
                            foreach($lastArray as $lastArrayKey => $lastArrayVals)
                            {
                                
                                // Show the interaction name with the data
                                foreach($lastArrayVals as $lastArrayValsKey => $lastArrayData){

                                    // Burficate if the interaction data is of product interaction
                                    if(isset($lastArrayData['interaction_display_name']) || !empty($lastArrayData['interaction_display_name'])){
                                        $lastArray[$lastArrayKey][$lastArrayData['interaction_display_name']][]=$lastArrayData;
                                    }
                                    else{
                                        // Check if the data has numeric value key then check for therapy interactions, else skip the process
                                        if(isset($lastArray[$lastArrayKey][$lastArrayValsKey]['therapy'])){
                                            $lastArray[$lastArrayKey][$lastArrayVals[$lastArrayValsKey]['therapy']][]=$lastArrayData;
                                        }
                                    }
                                    // Remove the numeric value key from the array
                                    unset($lastArray[$lastArrayKey][$lastArrayValsKey]);
                                }
                            }
                        }
                        
                    }
                    // if no natural medicine is added in medicine cabinet then take interactions from product's therapy data
                    else{
        
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
                            $lastArray[$vals['drugName']][]=$vals;
                        }
                        if(isset($lastArray)){
                            // Arrange array for product interaction and natural medicines
                            foreach($lastArray as $lastArrayKey => $lastArrayVals)
                            {
                                
                                // Show the interaction name with the data
                                foreach($lastArrayVals as $lastArrayValsKey => $lastArrayData){

                                    // Burficate if the interaction data is of product interaction
                                    if(isset($lastArrayData['interaction_display_name']) || !empty($lastArrayData['interaction_display_name'])){
                                        $lastArray[$lastArrayKey][$lastArrayData['interaction_display_name']][]=$lastArrayData;
                                    }
                                    else{
                                        // Check if the data has numeric value key then check for therapy interactions, else skip the process
                                        if(isset($lastArray[$lastArrayKey][$lastArrayValsKey]['therapy'])){
                                            $lastArray[$lastArrayKey][$lastArrayVals[$lastArrayValsKey]['therapy']][]=$lastArrayData;
                                        }
                                    }
                                    // Remove the numeric value key from the array
                                    unset($lastArray[$lastArrayKey][$lastArrayValsKey]);
                                }
                            }
                        }
                        
                    }
                    

                }
                
            }
            if(isset($lastArray)){
                return $lastArray;
            }else{
                return '';
            }
            
        }
       
    }

    

}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Crypt;
use Carbon\Carbon;
use DB;
use App\Models\Event;
use App\Models\EventConditions;
use App\Models\EventNotes;
use App\Models\Product;
use App\Models\Symptom;
use App\Models\Master;
use App\Models\Drugs;
use App\Models\MedicineCabinet;
use App\Models\MedicineCabinetNotes;
use App\Models\EventMedicineSymptom;
use App\Models\Therapy;
use Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Route;

class AddMedicineController extends Controller
{

    /**
     * Display the add medicine page with the saved medicine data and notes of logged in user
     */
    public function index(Request $request){

        try{

            $eventId = Crypt::decrypt($request->eventId); 
            $timeWindowDay = $request->timeWindowDay;

            // Get Logged in user id
            $userId = \Auth::user()->id;

            $medicineCabinetNaturalMedicineDataArr = array();
            $medicineCabinetRxDrugsDataArr = array();
            $medicineCabinetProductsDataArr = array();
            $medicineData = array();
            

            // Get data from event table based on the event id
            $eventTableData = Event::where('id',$eventId)->get()->first();

            // Get the data from the table
            $timeWindowDay = $eventTableData->timeWindowId;
            $eventDate = $eventTableData->eventDate;

            $additionalParametersForNotesUrl = [Crypt::encrypt(date('Y-m-d',strtotime($eventDate))) , 'timeWindowDay'=>$timeWindowDay];
            $notesUrl = route('add-event-notes',$additionalParametersForNotesUrl);



            $match_these = ['event_medicine_symptom.userId'=>$userId,'eventId'=>$eventId];
            $medicineCabinetDataExist = EventMedicineSymptom::where($match_these);
            $medicineCabinetDataExist = $medicineCabinetDataExist->whereNull('event_medicine_symptom.deleted_at')->get()->toArray();
            if(!empty($medicineCabinetDataExist)){
            
             
                // Fetch Natural Medicines data
                $medicineCabinetNaturalMedicineData = EventMedicineSymptom::where($match_these);
                $medicineCabinetNaturalMedicineData = $medicineCabinetNaturalMedicineData->select('event_medicine_symptom.id as eventMedicineId',
                'event_medicine_symptom.naturalMedicineId','therapy.therapy as name',
                'therapy.canonicalName as canonicalName',
                DB::raw('CONCAT(event_medicine_symptom.dosage," ",IFNULL(master.name," ")," ") As dosage'),
                'event_medicine_symptom.created_at')
                ->whereNotNull('naturalMedicineId')
                ->join('therapy','event_medicine_symptom.naturalMedicineId','=','therapy.id')
                ->leftJoin('master','event_medicine_symptom.dosageType','=','master.id')
                ->orderBy('therapy.therapy')
                ->whereNull('event_medicine_symptom.deleted_at')
                ->get()->toArray();
                if(!empty($medicineCabinetNaturalMedicineData)){
                    foreach($medicineCabinetNaturalMedicineData as $medicineCabinetNaturalMedicineDataKey => $medicineCabinetNaturalMedicineDataVal){
                        // Define type
                        $medicineCabinetNaturalMedicineData[$medicineCabinetNaturalMedicineDataKey]['type'] = 'naturalMedicine';
                    }
                    $medicineCabinetNaturalMedicineDataArr = $medicineCabinetNaturalMedicineData;

                    //sort list of data of natural medicine name in alphabetical order - code start 
                    usort($medicineCabinetNaturalMedicineDataArr, function($finalArrayOne, $finalArrayTwo) {
                        return $finalArrayOne['name'] <=> $finalArrayTwo['name'];
                    });
                    //sort list of data of natural medicine name in alphabetical order - code end
                }
            }



            // Fetch Rx Drugs data
            $medicineCabinetRxDrugsData = EventMedicineSymptom::where($match_these);
            $medicineCabinetRxDrugsData = $medicineCabinetRxDrugsData->select('event_medicine_symptom.id as eventMedicineId',
            'event_medicine_symptom.drugId',DB::raw('CONCAT(drugs.name," - ",drugs.brand_name) AS name'),
            DB::raw('CONCAT(event_medicine_symptom.dosage," ",IFNULL(master.name," ")," ") As dosage')
            ,'event_medicine_symptom.created_at')
            ->whereNotNull('drugId')
            ->join('drugs','event_medicine_symptom.drugId','=','drugs.id')
            ->leftJoin('master','event_medicine_symptom.dosageType','=','master.id')
            ->orderBy('drugs.name')
            ->whereNull('event_medicine_symptom.deleted_at')
            ->get()->toArray();

            if(!empty($medicineCabinetRxDrugsData)){
                foreach($medicineCabinetRxDrugsData as $medicineCabinetRxDrugsDataKey => $medicineCabinetRxDrugsDataVal){
                    $medicineCabinetRxDrugsData[$medicineCabinetRxDrugsDataKey]['imageName'] = asset('images').'/'.'rx-drug.svg';
                    // Define type
                    $medicineCabinetRxDrugsData[$medicineCabinetRxDrugsDataKey]['type'] = 'rxDrugs';
                    
                }
                $medicineCabinetRxDrugsDataArr = $medicineCabinetRxDrugsData;

                //sort list of data of Rx drugs name in alphabetical order - code start 
                usort($medicineCabinetRxDrugsDataArr, function($finalArrayOne, $finalArrayTwo) {
                    return trim($finalArrayOne['name']) <=> trim($finalArrayTwo['name']);
                });
                //sort list of data of Rx drugs name in alphabetical order - code end
            }


            // Fetch Products Data
            $medicineCabinetProductsData = EventMedicineSymptom::where($match_these);
            $medicineCabinetProductsData = $medicineCabinetProductsData->select('event_medicine_symptom.id as eventMedicineId',
            'product.productId as productId',DB::raw('CONCAT(product.productName," by ",product.productBrand," (",product.productSize,")") AS name'),
            DB::raw('CONCAT(event_medicine_symptom.dosage," ",IFNULL(master.name," ")," ") As dosage'),
            'event_medicine_symptom.created_at')
            ->whereNotNull('event_medicine_symptom.productId')
            ->join('product','event_medicine_symptom.productId','=','product.id')
            ->leftJoin('master','event_medicine_symptom.dosageType','=','master.id')
            ->orderBy('product.productName')
            ->whereNull('event_medicine_symptom.deleted_at')
            ->get()->toArray();

            if(!empty($medicineCabinetProductsData)){
                foreach($medicineCabinetProductsData as $medicineCabinetProductsDataKey => $medicineCabinetProductsDataVal){
                    // Define type
                    $medicineCabinetProductsData[$medicineCabinetProductsDataKey]['type'] = 'product';
                }
                $medicineCabinetProductsDataArr = $medicineCabinetProductsData;

                //sort list of data of Products name in alphabetical order - code start 
                usort($medicineCabinetProductsDataArr, function($finalArrayOne, $finalArrayTwo) {
                    return trim($finalArrayOne['name']) <=> trim($finalArrayTwo['name']);
                });
                //sort list of data of Products name in alphabetical order - code end

            }
            $medicineData = array_merge($medicineCabinetNaturalMedicineDataArr,$medicineCabinetRxDrugsDataArr);
            $medicineData = array_merge($medicineCabinetProductsDataArr, $medicineData);
 
            // Get dosage type values from master table
            $dosageType = Master::select('id','name')->where('type','10')->get()->toArray();


            return view('page.add-medicine-symptom',compact('medicineCabinetProductsData','dosageType','eventId','timeWindowDay','notesUrl','medicineData'));

        }catch(Exception $e) {
            /* Something went wrong */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
       
    }

    /**
     * Function to search the medicine (therapy,product,drugs) data.
     * 
     * @param   \Illuminate\Http\Request    $request   A request for input medicine data.
     * @return  \Illuminate\Http\Response              Returns the list of the therapy & product & drugs name by the input request name.
     */
    public function searchMedicine(Request $request){

        // Get Logged in user id
        $userId = Auth::user()->id;

        // Get the current time window day value
        $timeWindowDay = $request->timeWindowDay;

        //Get the list of product on base of search string
        $product = Product::select("id",DB::raw('CONCAT(product.productName," by ",product.productBrand," (",product.productSize,")") AS productName'))
        ->where(DB::raw('CONCAT(product.productName," by ",product.productBrand," (",product.productSize,")")'),"LIKE","%{$request->input('query')}%");

        // exclude product names which is already added in medicine cabinet by logged in user / its profile member
        $productIds = EventMedicineSymptom::where('userId',$userId)
        ->whereNull('deleted_at')->whereNotNull('productId')->where('timeWindowId','=',$timeWindowDay)->pluck('productId')->toArray();

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
        $naturalMedicineIds = EventMedicineSymptom::where('userId',$userId)
        ->whereNull('deleted_at')->whereNotNull('naturalMedicineId')->where('timeWindowId','=',$timeWindowDay)->pluck('naturalMedicineId')->toArray();

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
        $rxDrugsIds = EventMedicineSymptom::where('userId',$userId)
        ->whereNull('deleted_at')->whereNotNull('drugId')->where('timeWindowId','=',$timeWindowDay)->pluck('drugId')->toArray();

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


    /**
     * Function to save medicine symptom data.
     * 
     * @param   \Illuminate\Http\Request    $request   A request for input medicine data.
     * @return  \Illuminate\Http\Response              Returns the response message.
     */
    public function saveMedicineSymptom(Request $request){

        // Get Logged in user id
        $userId = \Auth::user()->id;
        
        // Store the form type from which data is coming
        $formType = $request->formType;

        // Store the natural medicine value from the input request exist
        $naturalMedicineId = $request->therapy_id ? $request->therapy_id : null;
        // Store the drug value from the input request exist
        $drugId = $request->drug_id ? $request->drug_id : null;
        // Store the product value from the input request exist
        $productId = $request->product_id ? $request->product_id : null;
        // Store the dosage value from the input request exist
        $dosage = $request->dosage ? $request->dosage : null;
        // Store the dosageType value from the input request exist
        $dosageType = $request->dosageType ? $request->dosageType : null;  
        // Store the event id value from the input request
        $eventId = $request->eventId ? $request->eventId : null;
        // Store the timewindow day value
        $timeWindowDay = $request->timeWindowDay;


        // Add data for add medicine tab - code start
        if(!empty($formType) && $formType == '1'){

            // If medicine id not found then display error
            if( (empty($drugId)) && (empty($naturalMedicineId)) && (empty($productId))){
                return redirect()->back()->with('error', 'Medicine not found. Please try again.');
            }

            // Begin a transaction
            DB::beginTransaction();

            // Store default data count
            $dataExistCount = 0;

            // Check if drug id exist
            if(!empty($drugId)){
                
                // Check if selected rx drug exist, if not then show error message
                $drugName = Drugs::select(DB::raw('CONCAT(name," - ",brand_name) AS name'))->where('id',$drugId)
                ->whereNull('deleted_at')->get()->first();
                if(empty($drugName)){
                    return redirect()->back()->with('error', 'Selected Rx Drug does not exist. Please try again.');
                }

                //Check if existing Drug Id is same as already stored in medicine cabinet table
                $rxDrugCount = EventMedicineSymptom::where('userId',$userId)->where('timeWindowId','=',$timeWindowDay)->where('drugId',$drugId);
                $dataExistCount = $rxDrugCount->whereNull('deleted_at')->count();
            }
            
            // Check if natural medicine id exist
            if(!empty($naturalMedicineId)){

                // Check if selected natural medicine exist, if not then show error message
                $naturalMedicineName = Therapy::select('therapy')->where('id',$naturalMedicineId)
                ->whereNull('deleted_at')->get()->first();
                if(empty($naturalMedicineName)){
                    return redirect()->back()->with('error', 'Selected natural medicine does not exist. Please try again.');
                }

                //Check if existing Natural Medicine Id is same as already stored in medicine cabinet table
                $naturalMedicineCount = EventMedicineSymptom::where('userId',$userId)->where('timeWindowId','=',$timeWindowDay)->where('naturalMedicineId',$naturalMedicineId);
                $dataExistCount = $naturalMedicineCount->whereNull('deleted_at')->count();
            }

            // Check if product id exist
            if(!empty($productId)){

                // Check if selected product exist, if not then show error message
                $productName = Product::select(DB::raw('CONCAT(product.productName," by ",product.productBrand) AS name'))->where('id',$productId)
                ->whereNull('deleted_at')->get()->first();
                if(empty($productName)){
                    return redirect()->back()->with('error', 'Selected product does not exist. Please try again.');
                }

                //Check if existing Product Id is same as already stored in medicine cabinet table
                $productCount = EventMedicineSymptom::where('userId',$userId)->where('timeWindowId','=',$timeWindowDay)->where('productId',$productId);
                $dataExistCount = $productCount->whereNull('deleted_at')->count();
            }

            // Save current medicine cabinet if not added already
            if($dataExistCount == 0){

                $eventData = Event::where('id',$eventId)->get()->first();

                $eventMedicineSymptomData = new EventMedicineSymptom;
                $eventMedicineSymptomData->eventId = $eventId;
                $eventMedicineSymptomData->userId = $userId;
                $eventMedicineSymptomData->timeWindowId = $eventData->timeWindowId;
                $eventMedicineSymptomData->drugId = $drugId;
                $eventMedicineSymptomData->naturalMedicineId = $naturalMedicineId;
                $eventMedicineSymptomData->productId = $productId;
                $eventMedicineSymptomData->dosage = $dosage;
                $eventMedicineSymptomData->dosageType = $dosageType;
                $eventMedicineSymptomData->created_at = Carbon::now();
                  
                if($eventMedicineSymptomData->save()){

                    // Commit the transaction
                    DB::commit();

                    return redirect()->back();
                }else{
                    // An error occured, cancel the transaction.
                    DB::rollback();
                    return redirect()->back()->with('message', 'Something went wrong while saving rx drug data. Please try again.');
                }
            }else{
                // An error occured, cancel the transaction.
                DB::rollback();
                return redirect()->back()->with('error', 'Medicine has already been saved.');

            }

        }
        // Add data for add medicine tab - code end
    }


    /**
     * Function to delete medicine symptom data.
     * 
     * @param   \Illuminate\Http\Request    $request   A request for event medicine data id.
     * @return  \Illuminate\Http\Response              Returns the response message.
     */
    public function deleteMedicineSymptom(Request $request){
        try{
            // Get Logged in user id
            $userId = Auth::user()->id;
            $eventMedicineId = $request->eventMedicineId;    
            $medicineDeletedCheck = EventMedicineSymptom::where('id',$eventMedicineId)->whereNotNull('deleted_at')->count(); 
            if($medicineDeletedCheck == 0){

                // delete the medicine cabinet if data exists
                $deleteMedicineData = EventMedicineSymptom::where('id',$eventMedicineId)->whereNull('deleted_at');
                if($deleteMedicineData->delete()){
                    return json_encode(array('status'=>'0'));
                }
                else{
                    $request->session()->flash('error', 'Something went wrong, please try again.');
                    return json_encode(array('status'=>'1'));
                }

            }else{
                $request->session()->flash('error', 'This medicine data is already deleted.');
                return json_encode(array('status'=>'1'));
            }
            
           
        }catch (Exception $e) {
            /* Something went wrong*/
            $error_message = $e->getMessage();
            return json_encode([
                'message'=> $error_message,
                'status' => 1
            ]);
        }
    }
}
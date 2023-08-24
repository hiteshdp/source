<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helpers as Helper;
use Carbon\Carbon;
use DB;
use Auth;
use Validator;
use Crypt;
use App\Models\Provider;
use Illuminate\Support\Facades\Session;

class UserProviderController extends Controller
{

    /*** 
     * Show the provider listing for search
    */
    public function index(Request $request){
        try {

            $providerData = DB::table('providers')
            ->select('providers.*',
            'users.name as firstName',
            'users.last_name as lastName')
            ->leftJoin('users','providers.userId','=','users.id')
            ->where('providers.isProviderApproved','1')
            ->where('providers.userId','!=',Auth::user()->id);

            $search_field_name = $request->input('query');
            $providerData = $providerData->where(function($query) use ($search_field_name) {
                $query->Where('users.name','LIKE','%'.$search_field_name.'%')
                ->orWhere('users.last_name','LIKE','%'.$search_field_name.'%');
            });

            // Exclude the providers from the list if the logged in user has already added
            $loggedInUserAddedProvider = Helper::getAddedProviderList();
            $providerData = $providerData->whereNotIn('providers.userId',$loggedInUserAddedProvider['providerIds']);
            $providerData = $providerData->limit(10)->get()->toArray();
    
            $data = array();    
            $i = 0; 
            if(!empty($providerData)){
                foreach($providerData as $key => $value){
                    // Concat first name and last name for display
                    $data[$i]['name'] = $value->firstName.' '.$value->lastName;
                    // Add the route by using id with encryption
                    $data[$i]['url'] = route('provider.details',Crypt::encrypt($value->userId));
                    $i++;
                }
            }
            return response()->json($data);

        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return response()->json($error_message);
        }
    }


    /*** 
     * Show the added provider listing by logged in user
    */
    public function getAddedProviderList(Request $request){
        try {
            $providerData = Provider::list();
            return view('page.shared-provider-access.index',compact('providerData'));
        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }


    /*** 
     * Display the selected provider detials screen
    */
    public function getProviderDetails(Request $request,$providerId){
        try {
            
            $providerId = Crypt::decrypt($providerId);

            if(!empty($providerId)){
                $providerData = Provider::fetchById($providerId);
                if(!empty($providerData)){
                    return view('page.shared-provider-access.provider-details',compact('providerData'));
                }else{
                    return redirect()->back()->with('error','Provider details not found. Please try again later.');
                }

            }else{
                return redirect()->back()->with('error','Provider details not found. Please try again later.');
            }

        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }


    /*** 
     * Display the selected provider user consent screen
    */
    public function providerConsent(Request $request,$providerId){
        try {
            
            $providerId = Crypt::decrypt($providerId);
            if(!empty($providerId)){
                $providerData = Provider::fetchById($providerId);
                if(!empty($providerData)){
                    // Store access till date of 1 year
                    $accessTillDate = date('m/d/Y',strtotime("+1 year"));
                    // Store send verification route with provider id encrypted
                    $sendVerificationCodeRoute = route('send-access-verification-code',Crypt::encrypt($providerId));
                    // Store back route for provider details screen with provider id encrypted
                    $backRoute = route('provider.details',Crypt::encrypt($providerId));
                    return view('page.shared-provider-access.provider-user-consent',compact('backRoute','providerData','accessTillDate','sendVerificationCodeRoute'));
                }else{
                    return redirect()->back()->with('error','Provider details not found. Please try again later.');
                }

            }else{
                return redirect()->back()->with('error','Provider details not found. Please try again later.');
            }

        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }

    /**
     * Fetch the provider details by the id
    */
    public function getProviderAccessDetails($providerId){
        try{
            // fetch the data by the provider id
            $providerData = Provider::fetchAccessDetailsById($providerId);
            // Check if provider data exist then display the data else show empty data
            if(!empty($providerData)){
                return response()->json(['status'=>'1','data'=>$providerData],200);
            }else{
                return response()->json(['status'=>'0','data'=>$providerData,'message'=>'Access details not found. Please try again later.'],200);
            }
        }catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return response()->json(['status'=>'0','data'=>[],'message'=>$error_message],500);
        }
       
    }

     /**
     * Fetch the provider details by the id in the popup
    */
    public function getAddedProviderDetails($providerId){
        try {
            
            $providerId = Crypt::decrypt($providerId);

            if(!empty($providerId)){
                $providerData = Provider::fetchById($providerId);
                if(!empty($providerData)){
                    return response()->json(['status'=>'1','data'=>$providerData],200);
                }else{
                    return response()->json(['status'=>'0','data'=>$providerData,'message'=>'Doctor details not found. Please try again later.'],200);
                }
            }else{
                return response()->json(['status'=>'0','data'=>[]],404);
            }

        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return response()->json(['status'=>'0','data'=>[],'message'=>$error_message],500);
        }
       
    }
    
}

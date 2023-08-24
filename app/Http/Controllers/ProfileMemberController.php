<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfileMembers;
use App\Models\User;
use App\Models\Master;
use Carbon\Carbon;
use Validator;
use Auth;
use DB;
use Crypt;
use App\Helpers\Helpers as Helper;
use Illuminate\Support\Facades\File;

class ProfileMemberController extends Controller
{

    /****
     * Intialize this method before calling other functions below
     */
    public function __construct(){
        /***
         * Check if the logged in user subscription is wellkasa plus, 
         * if not then show error message. else proceed with the screen
         * */
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            // check if not wellkasa plus user then show error message.
            if($this->user->planType == '1'){
                return redirect('my-profile')->withErrors(['msg' => 'Access denied. Please subscribe to wellkabinet.']);
            }
            // else proceed to further screen
            return $next($request); 
        });
    }
    
    /***
     * Display add profile members page 
     */
    public function index(Request $request){
        
        // Get the url to pass after save profile
        $redirectUrl = $request->route !="" ? Crypt::decrypt($request->route) : '';

        // get number of profile member remaining to be added 
        $availableCount = Auth::user()->remainingProfileMemberCount;
        
        // get gender options from master table
        $genderOptions = Master::where('type','2')->get()->toArray();

        return view('page.add-profile',compact('availableCount','genderOptions','redirectUrl'));
    }

    /***
     * Save profile member data 
     */
    public function saveProfileMember(Request $request){
       
        $userId = Auth::user()->id;

        // Validate the fields
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'gender' => 'required',
            'dateOfBirth' => 'required|date|date_format:m/d/Y',
            'avatar' => 'image|mimes:jpg,png,jpeg'
        ]);

        //validation failed
        if ($validator->fails()) 
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
            try{

                // Begin the SQL Query Transaction
                DB::beginTransaction();

                // Store the profile member data
                $profileMembers = new ProfileMembers;
                $profileMembers->addedByUserId = $userId;
                $profileMembers->first_name = $request->firstName;
                $profileMembers->last_name = $request->lastName;
                $profileMembers->gender = $request->gender;
                $profileMembers->date_of_birth = date('Y-m-d', strtotime($request->dateOfBirth));
                
                $profileMembers->age = date_diff(date_create($request->get('dateOfBirth')), date_create('today'))->y;
    
                // Check if profile picture has changed the store it in the public path
                if ($request->hasFile('avatar'))
                {   
                    if ($request->file('avatar')->isValid()) 
                    {   
                        $file = $request->file('avatar');
                        $ext = $file->extension();
                        $name = $request->file('avatar')->getClientOriginalName();
                        $name = str_replace(' ', '_', $name);
                        $fileName = time().'_'.$name;
                            
                        if($request->file('avatar')->move(public_path('uploads/avatar/').'/', $fileName))
                        {
                            $profileMembers->profile_picture = $fileName;
                        }
                    }
                }

                // Save profile member data if no error occurs
                if($profileMembers->save()){

                    // get total number of member profile added by logged in user and update to its remaining count field in users table  
                    Helper::updateRemainingProfileMembersCount($userId);
                  
                    // If no errors while saving the data then commit the query
                    DB::commit();
                    
                    // If the user has clicked add profile from dropdown then redirect back to wellkabinet screen, else to my-profile 
                    if(isset($request->redirectUrl) && !empty($request->redirectUrl)){
                        
                        return redirect()->route('medicine-cabinet',Crypt::encrypt($profileMembers['id']))->with('success', $profileMembers['first_name'].' '.$profileMembers['last_name'].' was added successfully to your account');

                    }else{
                        $message = $profileMembers['first_name'].' '.$profileMembers['last_name'].' was added successfully to your account';
                        session()->put('savedProfile', $message);
                        return redirect('my-profile');
                    }
                    
                }else{
                    // If error occurs while saving the data then rollback the query
                    DB::rollback();
                    return back()->with('error', 'Something went wrong. Please try again.');
                }

            }catch(Exception $e){
                return back()->with('error', 'Something went wrong. Please try again.');
            }
        }
    }


    /***
     * Display edit profile members page of selected profile member
     */
    public function displayEditProfilePage(Request $request, $id){
        
        // Decrypt id for the profile member id
        $id = Crypt::decrypt($id);

        // Get logged in user id
        $userId = Auth::user()->id;

        // Get member profile user details if added by logged in user
        $profileMemberData = ProfileMembers::select('profile_members.id','profile_members.first_name','profile_members.last_name',
        'master.id As gender','age','profile_picture','profile_members.date_of_birth')
        ->where(['addedByUserId' => $userId])->join('master','profile_members.gender','=','master.id')
        ->where('profile_members.id',$id)
        ->whereNull('profile_members.deleted_at')->get()->first();

        if(!empty($profileMemberData['profile_picture'])){
            $profileMemberData['profile_picture'] = url('uploads/avatar').'/'.$profileMemberData['profile_picture'];
        }else{
            $profileMemberData['profile_picture'] = '';
        }

        if(!empty($profileMemberData['date_of_birth'])){
            $profileMemberData['date_of_birth'] = date('m/d/Y', strtotime($profileMemberData['date_of_birth']));
        }

        // get gender options from master table
        $genderOptions = Master::where('type','2')->get()->toArray();


        // get number of profile member remaining to be added 
        $availableCount = Auth::user()->remainingProfileMemberCount;

        return view('page.edit-profile-member',compact('availableCount','profileMemberData','genderOptions'));
    }

    /***
     * Update profile member data 
     */
    public function updateProfileMember(Request $request){
       
        $userId = Auth::user()->id;
        
        // Validate the fields
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'gender' => 'required',
            'dateOfBirth' => 'required|date|date_format:m/d/Y',
            'avatar' => 'image|mimes:jpg,png,jpeg'
        ]);

        //validation failed
        if ($validator->fails()) 
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
            try{

                // Begin the SQL Query Transaction
                DB::beginTransaction();

                $profileMemberId = $request->profileMemberId;

                $profileMembers = ProfileMembers::find($profileMemberId);
                $profileMembers->first_name = $request->firstName;
                $profileMembers->last_name = $request->lastName;
                $profileMembers->gender = $request->gender;
                $profileMembers->date_of_birth = date('Y-m-d', strtotime($request->dateOfBirth));
                
                $profileMembers->age = date_diff(date_create($request->get('dateOfBirth')), date_create('today'))->y;
    
    
                if ($request->hasFile('avatar'))
                {   
                    if ($request->file('avatar')->isValid()) 
                    {   
                        $file = $request->file('avatar');
                        $ext = $file->extension();
                        $name = $request->file('avatar')->getClientOriginalName();
                        $name = str_replace(' ', '_', $name);
                        $fileName = time().'_'.$name;
                            
                        if($request->file('avatar')->move(public_path('uploads/avatar/').'/', $fileName))
                        {
                            // delete old uploaded photo from the public/uploads/avatar folder if exists
                            $fileToDelete = public_path() . '/uploads/avatar/' . $profileMembers->profile_picture;
                            if(File::exists($fileToDelete)){
                                File::delete($fileToDelete);
                            }

                            $profileMembers->profile_picture = $fileName;
                        }
                    }
                }

                // Update profile member data if no error occurs
                if($profileMembers->save()){

                    // update the data for profile member
                    DB::commit(); 

                    // add updated successfully message in session
                    $message = "Profile updated successfully for member ".$profileMembers['first_name'].' '.$profileMembers['last_name'];
                    session()->put('savedProfile', $message);

                    return redirect('my-profile');
                }else{
                    // If error occurs while saving the data then rollback the query
                    DB::rollback();
                    return back()->with('error', 'Something went wrong. Please try again.');
                }

            }catch(Exception $e){
                return back()->with('error', 'Something went wrong. Please try again.');
            }
        }
    }

    /***
     * Display delete profile member page with selected profile member data
     */
    public function displayDeleteProfilePage(Request $request,$id){

        // Decrypt id for the profile member id
        $id = Crypt::decrypt($id);

        // Get logged in user id
        $userId = Auth::user()->id;

        // Get member profile user details if added by logged in user
        $profileMemberData = ProfileMembers::select('profile_members.id',
        DB::raw('CONCAT(first_name," ",last_name) As name'),'master.name As gender','age','profile_picture')
        ->where(['addedByUserId' => $userId])->join('master','profile_members.gender','=','master.id')
        ->where('profile_members.id',$id)
        ->whereNull('profile_members.deleted_at')->get()->first();

        if(!empty($profileMemberData)){

            if(!empty($profileMemberData->gender)){
                switch ($profileMemberData->gender) {
                    case 'Male':
                        $profileMemberData->gender = 'M';
                        break;
                    case 'Female':
                        $profileMemberData->gender = 'F';
                        break;
                    case 'Undisclosed':
                        $profileMemberData->gender = 'Undisclosed';
                        break;
                    default:
                        $profileMemberData->gender = '';
                        break;
                }
                $profileMemberData->genderAge = $profileMemberData->gender.", ".$profileMemberData->age;
            }else{
                $profileMemberData->gender = '';
            }

            // Check profile member profile picture exists then append with path of image stored, else assign empty value
            if(!empty($profileMemberData->profile_picture)){
                $profileMemberData->profile_picture = url('uploads/avatar').'/'.$profileMemberData->profile_picture;
            }else{
                $profileMemberData->profile_picture = '';
            }

            // Add the delete profile member url with selected profile member id
            $profileMemberData->deleteUrl = route('delete-profile-member',Crypt::encrypt($profileMemberData->id));
            
        }

        return view('page.remove-profile',compact('profileMemberData'));
    }


    /***
     * Delete profile members logic code 
     */
    public function deleteProfileMember($id){

        // Decrypt id for the profile member id
        $id = Crypt::decrypt($id);

        // Get logged in user id
        $userId = Auth::user()->id;
        $deleteProfileMember = ProfileMembers::where(['addedByUserId' => $userId])
        ->where('profile_members.id',$id)
        ->whereNull('profile_members.deleted_at');

        $checkDeleteProfileMember = $deleteProfileMember->get()->first();
        $message = $checkDeleteProfileMember['first_name'].' '.$checkDeleteProfileMember['last_name'].' was removed successfully from your account';
        
        $deleteProfileMember = $deleteProfileMember->delete();

        // If profile member successfully deleted then execute below code, else redirect back to same screen and show error message
        if($deleteProfileMember){

            // Set the sucess message in deletedProfile session
            session()->put('deletedProfile', $message);
            
            // get total number of member profile added by logged in user and update to its remaining count field in users table  
            Helper::updateRemainingProfileMembersCount($userId);

            return redirect('my-profile');
        }else{
            return back()->with('error', 'Something went wrong. Please try again.');
        }

        return view('page.remove-profile');
    }
}

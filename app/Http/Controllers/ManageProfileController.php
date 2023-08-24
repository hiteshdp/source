<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use App\Models\User;
use App\Models\ProfileMembers;
use App\Models\Master;

class ManageProfileController extends Controller
{
 
    /**
     * Display a listing of the profiles added.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = Auth::user()->id;

        // Get current logged in user data - code start
        $userDetails = User::select('users.id',
        'users.gender as gender',
        'users.patientAge as age',
        'users.avatar as avatar',
        'users.patientAge as age',DB::raw('CONCAT(users.name," ",users.last_name) As name'))
        ->where('users.id',$userId)
        ->get()->first();

        if(!empty($userDetails)){
            $userDetails['genderAge'] = '';
            $genderValue = Master::select('name')->where('id',$userDetails['gender'])->get()->first();
            if(!empty($genderValue)){
                switch ($genderValue['name']){
                    case 'Male' :
                        $gender = 'M'; 
                        break;
                    case 'Female' :
                        $gender = 'F'; 
                        break;
                    case 'Undisclosed' :
                        $gender = 'Undisclosed'; 
                        break;
                    default:
                        $gender = 'N/A';
                        break;
                }
                $userDetails['genderAge'] = $gender .', '. $userDetails['age']; 
            }
            if(!empty($userDetails['avatar'])){
                $userDetails['avatar'] = url('uploads/avatar').'/'.$userDetails['avatar'];
            }
        }
        // Get current logged in user data - code end


        // Get member profile user details if added by logged in user - code start
        $profileMembers = ProfileMembers::select('profile_members.id',DB::raw('CONCAT(first_name," ",last_name) As name'),
        'master.name As gender','age','profile_picture')->where(['addedByUserId' => $userId])
        ->join('master','profile_members.gender','=','master.id')->whereNull('profile_members.deleted_at')
        ->get()->toArray();

        if(!empty($profileMembers)){
            foreach($profileMembers as $profileMembersKey => $profileMembersData){
                if(!empty($profileMembersData['gender'])){
                    switch ($profileMembersData['gender']) {
                        case 'Male':
                            $profileMembers[$profileMembersKey]['gender'] = 'M';
                            break;
                        case 'Female':
                            $profileMembers[$profileMembersKey]['gender'] = 'F';
                            break;
                        case 'Undisclosed':
                            $profileMembers[$profileMembersKey]['gender'] = 'Undisclosed';
                            break;
                        default:
                            $profileMembers[$profileMembersKey]['gender'] = 'N/A';
                            break;
                    }
                    $profileMembers[$profileMembersKey]['genderAge'] = $profileMembers[$profileMembersKey]['gender'].", ".$profileMembers[$profileMembersKey]['age'];
                }else{
                    $profileMembers[$profileMembersKey]['gender'] = '';
                }

                if(!empty($profileMembersData['profile_picture'])){
                    $profileMembers[$profileMembersKey]['profile_picture'] = url('uploads/avatar').'/'.$profileMembersData['profile_picture'];
                }else{
                    $profileMembers[$profileMembersKey]['profile_picture'] = '';
                }
            }
        }
        // Get member profile user details if added by logged in user - code end

        // Get remaining count of profile members to be added
        $availableCount = Auth::user()->remainingProfileMemberCount;

        return view('page.manage-profiles',compact('userDetails','profileMembers','availableCount'));
    }
}

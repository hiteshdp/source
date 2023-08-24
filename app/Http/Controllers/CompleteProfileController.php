<?php
/*
|-----------------------------------------------------------------------------
| LARAVEL APP BASE CLASS, MODEL CLASS, PACKAGES, HELPERS DEFINE
|-----------------------------------------------------------------------------
*/
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Models\User;
use App\Models\Master;
use App\Models\State;
use App\Models\City;
use App\Helpers\Helpers as Helper;

class CompleteProfileController extends Controller
{
     /*
    |--------------------------------------------------------------------------
    | Complete Profile Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles user's complete profile data and
    | provide city list on base state passing as argument.
    |
    */


    /**
     * This function complies display a listing of the iama Options, 
     * Journey Options, Gender & States on Profile page view
     *
     * @return  \Illuminate\Http\Response         Redirect to related response profile page view.
     */
    public function index()
    {
        $userName = Auth::user()->name.' '.Auth::user()->last_name;
       
        $availableCount = Auth::user()->remainingProfileMemberCount;

        return view('page.complete-profile',compact('userName','availableCount'));
    }

    /**
     * This function complies update comple profile information of user
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data.
     * @return  \Illuminate\Http\Response               Redirect to related response
     */
    public function updateCompleteProfile(Request $request)
    {

        //validation check of input fields
        $validator = Validator::make($request->all(), [

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
            $userId = Auth::user()->id;
            $user=User::find($userId);

            $user->gender = $request->gender;
            $user->dateOfBirth = date('Y-m-d', strtotime($request->dateOfBirth));
            $user->patientAge = date_diff(date_create($request->get('dateOfBirth')), date_create('today'))->y;
    
            //Check condtion for file is attach or not
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
                        $user->avatar = $fileName;
                    }
                }
            }
            $user->updatedCompleteProfile = '1';
            $user->save();
            return redirect('medicine-cabinet')->with('message', 'Profile updated successfully');
        }
        return view('page.complete-profile');
    }

    /**
     * This function complies get the list of city on base of request pass
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data as state id.
     * @return  \Illuminate\Http\Response               Redirect to related response as json data
     */
    public function getCity(Request $request)
    {
        //Get city list check where state is match with request state.
        $data['cities'] = City::where("state_id",$request->state_id)
                    ->get(["name","id"]);
        return response()->json($data);
    }
}

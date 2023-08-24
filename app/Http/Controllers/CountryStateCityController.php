<?php
/*
|-----------------------------------------------------------------------------
| LARAVEL APP BASE CLASS, PACKAGES DEFINE
|-----------------------------------------------------------------------------
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Country,State,City};

class CountryStateCityController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Country State City Controller
    |--------------------------------------------------------------------------
    |
    | This controller has only get city list & show in response data in JSON format.
    |
    */

    // public function index()
    // {
    //     $data['countries'] = Country::get(["name","id"]);
    //     return view('country-state-city',$data);
    // }
    // public function getState()
    // {
    //     $data = State::where("country_id",'231')
    //                 ->select("name","id")->get()->toArray();
    //     return response()->json($data);
       
    // }
    
    /**
     * This function complies get the list of city on base of request pass
     *
     * @param   \Illuminate\Http\Request    $request    A request object pass through form data as state id.
     * @return  \Illuminate\Http\Response               Redirect to related response as json data
     */
    public function getCity(Request $request)
    {
        //Get city list check where state is match with request state.
        $data['cities'] = City::where("state_id",$request->state_id)->orderBy('name')
                    ->get(["name","id"]);
        return response()->json($data);
    }
 
}

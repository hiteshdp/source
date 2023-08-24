<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helpers as Helper;
use Carbon\Carbon;
use DB;
use Auth;
use Validator;
use Crypt;
use App\Models\QuizIntroScreen;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\QuizRecommendationScreen;
use App\Models\QuizConfiguration;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class MigraineController extends Controller
{
    /**
     * Redirect to migraine tracker page while check if there is any unique_id_value 
     * to remove from session when clicked on don't save
     */
    public function redirectToIndex(Request $request){

        // Check if the unique id value exists from the url
        if( !empty($request->unique_id_value) && $request->unique_id_value !='0'){
            // Check if Session has values
            if(Session::has('unique_id_value')){
                // Store the session values in array
                $sessionValues = Session::get('unique_id_value');
                foreach ($sessionValues as $key => $value) {
                    // Check if the unique id value is same from the array then remove it
                    if($value == $request->unique_id_value){
                        unset($sessionValues[$key]);
                    }
                }
                // Store the updated session values after removing the value
                Session::put('unique_id_value',$sessionValues);
            }

            Session::forget('is_migraine_quiz');
            Session::put('is_migraine_quiz','1');

        }
        // Redirect to migraine tracker screen
        return redirect()->route('migrainemight');
    }
        
}
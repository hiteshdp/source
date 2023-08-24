<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Therapy;
use App\Models\Condition;
use App\Models\UserSymptoms;
use App\Models\Symptom;


class SingleSearchController extends Controller
{
    /**
     * Function to get Therapy & Condition Names from autocomplete search of wordpress page.
     * 
     * @param   \Illuminate\Http\Request    $request   A request pass for condition or therapy name.
     * @return  \Illuminate\Http\Response              Returns the list of the condition or therapy name with its respective url to redirect to its detail page.
     */
    public function autocompleteSearch(Request $request)
    {

        $therapy = Therapy::select("id","therapy","canonicalName")
                ->where("therapy","LIKE","%{$request->input('query')}%")
                ->groupBy('therapy')
                ->whereNull('deleted_at')
                ->get();
        
        $data = array();    
        $i = 0;            
        foreach ($therapy as $key => $thpy)
        {
            $data[$i]['Id'] = $thpy->id;
            $data[$i]['Type'] = "Therapy";
            $data[$i]['Name'] = $thpy->therapy;
            $data[$i]['canonicalUrl'] = route('therapy',$thpy->canonicalName);
            $i++;
        }

        //Get the conditoin along with Therapies and add into the array
        $condition = Condition::select("id","conditionName","canonicalName")
                ->where("conditionName","LIKE","%{$request->input('query')}%")
                ->where('displayInSearch','1')
                ->groupBy('conditionName')
                ->whereNull('deleted_at')
                ->get();
        
        foreach ($condition as $key => $cond)
        {
            $data[$i]['Id'] = $cond->id;
            $data[$i]['Type'] = "Condition";
            $data[$i]['Name'] = $cond->conditionName;
            $data[$i]['canonicalUrl'] = route('condition',$cond->canonicalName);
            $i++;
        }

        return response()->json($data);
    }

    /**
     * API Function to display the symptoms list from the symptom name search
     * 
     * @param   \Illuminate\Http\Request    $request   A request pass for symptom name.
     * @return  \Illuminate\Http\Response              Returns the list of the symptoms.
     */
    public function getSymptomsList(Request $request)
    {
        // Get the current logged in user id
        $userId = $request->userId;
        // Get the current logged in user's profile member id
        $profileMemberId = $request->profileMemberId;

        // Get the symptoms list by the input given by the user
        $symptomsData = Symptom::select("symptom.id","symptom.symptomName as name")
        ->where("symptom.symptomName","LIKE","%{$request->input('query')}%");

        // Fetch the symptom ids added by the logged in user or its profile member id - Code Start
        $userSymptomsIds = UserSymptoms::getAllSelectedSymptomsId($userId,$profileMemberId);
        // Fetch the symptom ids added by the logged in user or its profile member id - Code End
        
        // Exclude the symptoms list already added by the user or its profile member
        $symptomsData = $symptomsData->whereNotIn('symptom.id',$userSymptomsIds)->groupBy('symptom.symptomName')->whereNull('symptom.deleted_at')->limit(10)->get();

        // Return the data result in json format
        return response()->json($symptomsData);
    }
}

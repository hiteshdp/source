<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSymptoms extends Model
{
    use HasFactory;
    protected $table = 'user_symptoms';

    /***
     * Get logged in user or its profile member's added symptoms list which enabled
     */
    public static function symptomsList($profileMemberId=NULL){
        $userSymptomsList = UserSymptoms::where('user_symptoms.userId',\Auth::user()->id)
        ->leftJoin('symptom','user_symptoms.symptomId','=','symptom.id');
         // Check if profile member id selection is there, if there then display the data accordingly else exclude the selection
        if(!empty($profileMemberId)){
            $userSymptomsList = $userSymptomsList->where('user_symptoms.profileMemberId',$profileMemberId);
        }else{
            $userSymptomsList = $userSymptomsList->whereNull('user_symptoms.profileMemberId');
        }
        $userSymptomsList = $userSymptomsList->where('user_symptoms.status','1')->whereNull('symptom.deleted_at')->whereNull('user_symptoms.deleted_at')->get()->toArray();
        return $userSymptomsList;
    }

    /***
     * Get logged in user or its profile member's added symptoms list regardless the status
     */
    public static function allSymptomsList($profileMemberId=NULL){
        $userSymptomsList = UserSymptoms::select('symptom.id AS id','user_symptoms.id AS userSymptomId','symptom.symptomName AS symptomName','user_symptoms.status AS status')->where('user_symptoms.userId',\Auth::user()->id)
        ->leftJoin('symptom','user_symptoms.symptomId','=','symptom.id');
         // Check if profile member id selection is there, if there then display the data accordingly else exclude the selection
        if(!empty($profileMemberId)){
            $userSymptomsList = $userSymptomsList->where('user_symptoms.profileMemberId',$profileMemberId);
        }else{
            $userSymptomsList = $userSymptomsList->whereNull('user_symptoms.profileMemberId');
        }
        $userSymptomsList = $userSymptomsList->whereNull('symptom.deleted_at')->whereNull('user_symptoms.deleted_at')->orderBy('user_symptoms.id','DESC')->get()->toArray();
        return $userSymptomsList;
    }

    /***
     * Get the logged in users added symptoms ids based on user id and profile member id if there
     */
    public static function getSelectedSymptomsId($userId,$profileMemberId=NULL){
        // Fetch the symptom ids added by the logged in user or its profile member id - Code Start
        $userSymptomsIds = UserSymptoms::where('userId',$userId);
        // Check if profileMemberId exists, then get the data accordingly or exclude for this same
        if(!empty($profileMemberId)){
            $userSymptomsIds = $userSymptomsIds->where('user_symptoms.profileMemberId',$profileMemberId);
        }else{
            $userSymptomsIds = $userSymptomsIds->whereNull('user_symptoms.profileMemberId');
        }
        $userSymptomsIds = $userSymptomsIds->where('user_symptoms.status','1')->whereNull('user_symptoms.deleted_at')->pluck('symptomId')->toArray();
        // Fetch the symptom ids added by the logged in user or its profile member id - Code End

        return $userSymptomsIds;
    }

    /***
     * Get the logged in users added symptoms ids based on user id and profile member id if there regardless the status
     */
    public static function getAllSelectedSymptomsId($userId,$profileMemberId=NULL){
        // Fetch the symptom ids added by the logged in user or its profile member id - Code Start
        $userSymptomsIds = UserSymptoms::where('userId',$userId);
        // Check if profileMemberId exists, then get the data accordingly or exclude for this same
        if(!empty($profileMemberId)){
            $userSymptomsIds = $userSymptomsIds->where('user_symptoms.profileMemberId',$profileMemberId);
        }else{
            $userSymptomsIds = $userSymptomsIds->whereNull('user_symptoms.profileMemberId');
        }
        $userSymptomsIds = $userSymptomsIds->whereNull('user_symptoms.deleted_at')->pluck('symptomId')->toArray();
        // Fetch the symptom ids added by the logged in user or its profile member id - Code End

        return $userSymptomsIds;
    }
}

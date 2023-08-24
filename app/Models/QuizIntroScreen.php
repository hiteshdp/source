<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Crypt;
use Config;

class QuizIntroScreen extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'quiz_intro_screen';

    /***
     * Get the quiz list from quiz_intro_screen table
     */
    public static function allQuizList(){
        // Get all the data from the quiz_intro_screen table
        $data = DB::table('quiz_intro_screen')->select('id','title','condition_name','quiz_name')->get()->toArray();
        foreach($data as $key => $value){
            $condition_name = $value->condition_name;
            $quiz_name = $value->condition_name;
            if (!empty($condition_name) && !empty($quiz_name)) {
                // Add the route by using id with encryption
                $data[$key]->url = route('quiz/intro',['condition_name'=>$condition_name, 'quiz_name'=>$quiz_name]);   
            }
        }
        return $data;
    }

    /**
     * Get the quiz last screen action check
     * ( display score / 
     *   display score & recommendation screen / 
     *   display recommendation screen only /
     *   don't display recommendations screen & redirect as per given url
     * )
     */
    public static function getActionCheck($id){
        // Get the data for the current quiz id
        $data = DB::table('quiz_intro_screen')->select('id','show_score','show_recco_screen')
        ->where('id',$id)->get()->first();
        $data = json_decode(json_encode($data),true);
        return $data;
    }

    /**
     * Get the midasTest quiz URL
     */
    public static function getMidasTestQuizURL(){
        $data = DB::table('quiz_intro_screen')->select('id','condition_name','quiz_name')
        ->where('id',Config::get('constants.MidasTestId'))->get()->first();
        $route_data = route('quiz/intro',['condition_name'=>$data->condition_name,'quiz_name'=>$data->quiz_name]);
        return $route_data;
    } 

    /**
     * Get the Hit6 quiz URL
     */
    public static function getHitSixQuizURL(){
        $data = DB::table('quiz_intro_screen')->select('id','condition_name','quiz_name')
        ->where('id',Config::get('constants.HitSixTestId'))->get()->first();
        $route_data = route('quiz/intro',['condition_name'=>$data->condition_name,'quiz_name'=>$data->quiz_name]);
        return $route_data;
    } 
}

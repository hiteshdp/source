<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class QuizTransition extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'quiz_transition';

    /**
     * Get the transition details data from the 
     * transition table based on the current question is selected
     */
    public static function getTransitionData($qid){

        $data = DB::table('quiz_question')
        ->select('quiz_transition.*','quiz_intro_screen.condition_name','quiz_intro_screen.quiz_name')
        ->where('quiz_transition.id',$qid)
        ->join('quiz_transition','quiz_question.transition_id','=','quiz_transition.id')
        ->join('quiz_intro_screen','quiz_question.intro_screen_id','=','quiz_intro_screen.id')
        ->get()->first();
        if(!empty($data)){
            // Convert the delay value to seconds if value exists
            $data->delay = !empty($data->delay) ? $data->delay*1000 : '';
        }
        return $data;
    }
}

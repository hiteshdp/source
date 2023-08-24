<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
class QuizScore extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'quiz_score';

    /**
     * Get message for the quiz attempted based on the score 
     */
    public static function showScoreMessage($quizId,$score){

        $msg = "";
        $message = DB::table('quiz_score')->where('quiz_id',$quizId)
        ->where(function ($query) use ($score) {
            $query->where('score_range_low', '<=', (int)$score);
            $query->where('score_range_high', '>=', (int)$score);
        })
        ->get()->first();
        if(!empty($message)){
            $msg = $message->score_description;
        }
        return $msg;
    }
}

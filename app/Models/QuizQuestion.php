<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizQuestion extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'quiz_question';

    /**
     * Get the total count based on intro screen id from given question id 
     */
    public static function totalQuizQuestions($intro_id){
        $totalNumberOfQuestions = QuizQuestion::where('intro_screen_id',$intro_id)->distinct()->count(['percent_progress']);
        return $totalNumberOfQuestions;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class QuizConfiguration extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'quiz_configuration';

    /***
     * Get the configuration for intro screen
     */
    public static function introScreen($introScreenId){
        $data = DB::table('quiz_configuration')
        ->select('id','question_text_color','button_color','button_text_color','background_color_screen')
        ->where('intro_screen_id',$introScreenId)->whereNull('deleted_at')->get()->first();
        if(!empty($data)){
            $data->question_text_color = !empty($data->question_text_color) ? $data->question_text_color : '#181818';
            $data->button_color = !empty($data->button_color) ? $data->button_color : '#7380B4';
            $data->button_text_color = !empty($data->button_text_color) ? $data->button_text_color : '#FFF';
            $data->background_color_screen = !empty($data->background_color_screen) ? $data->background_color_screen : '#ffffff00';
        }else{
            $data['question_text_color'] =  '#181818';
            $data['button_color'] = '#7380B4';
            $data['button_text_color'] = '#FFF';
            $data['background_color_screen'] = '#ffffff00';

            $data = (object)json_decode(json_encode($data),true);
        }
        return $data;
    }


    /***
     * Get the configuration for quiz screen
     */
    public static function quizScreen($introScreenId){
        $data = DB::table('quiz_configuration')
        ->select('id','question_text_color','answer_text_color','button_color','button_text_color','background_color_screen','arrow_color','thanks_message')
        ->where('intro_screen_id',$introScreenId)->whereNull('deleted_at')->get()->first();
        if(!empty($data)){
            $data->question_text_color = !empty($data->question_text_color) ? $data->question_text_color : '#181818';
            $data->answer_text_color = !empty($data->answer_text_color) ? $data->answer_text_color : '#181818';
            $data->button_color = !empty($data->button_color) ? $data->button_color : '#7380B4';
            $data->button_text_color = !empty($data->button_text_color) ? $data->button_text_color : '#FFF';
            $data->background_color_screen = !empty($data->background_color_screen) ? $data->background_color_screen : '#ffffff00';
            $data->arrow_color = !empty($data->arrow_color) ? $data->arrow_color : '#181818';
        }else{
            $data['question_text_color'] =  '#181818';
            $data['answer_text_color'] =  '#181818';
            $data['button_color'] = '#7380B4';
            $data['button_text_color'] = '#FFF';
            $data['background_color_screen'] = '#ffffff00';
            $data['arrow_color'] = '#181818';
            $data['thanks_message'] = '';

            $data = (object)json_decode(json_encode($data),true);
        }
        return $data;
    }

}

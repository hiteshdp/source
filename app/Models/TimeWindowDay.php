<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class TimeWindowDay extends Model
{
    use HasFactory;
    protected $table = 'time_window_day';

    /***
     * get the hashtag name based on the time window day value for the notes display
     */
    public static function getTimeWindowHashTag($timeWindowDay){

        $hashTagName = DB::table('time_window_day')->select('label')->where('id', '=', $timeWindowDay)->get()->pluck('label')->first();
        if(!empty($hashTagName)){
            return "#".$hashTagName;
        }
        return '';
    }
}

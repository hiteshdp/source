<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class EventNotes extends Model
{
    use HasFactory;
    protected $table = 'event_notes';


    /***
     * get logged in user's added notes by given date and profile member id
     */
    public static function getEventNotesByUser($eventDate,$profileMemberId=NULL){
        $userId = \Auth::user()->id;
        $notes = EventNotes::select('event_notes.id',DB::raw('DATE_FORMAT(event_notes.created_at, "%d-%b-%Y  %H:%i") AS date'),
        'notes as description','time_window_day.label as timeWindowDay')
        ->where('event_notes.userId', $userId)
        ->leftJoin('time_window_day','event_notes.timeWindowDay','=','time_window_day.id');
        // Get the profile member id if given, else check for userId with profileMemberId null only
        if(!empty($profileMemberId)){
            $notes = $notes->where('event_notes.profileMemberId', $profileMemberId);
        }else{
            $notes = $notes->whereNull('event_notes.profileMemberId');
        }
        $notes =  $notes->where('event_notes.eventDate', $eventDate)->orderBy('event_notes.id','DESC')->get()->toArray();
        foreach ($notes as $key => $value) {
            $notes[$key]['description'] = $value['description']." <b>#".$value['timeWindowDay']."</b>";
        }
        return $notes;
    }

}

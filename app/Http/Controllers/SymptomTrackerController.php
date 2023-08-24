<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Crypt;
use Carbon\Carbon;
use DB;
use App\Models\Event;
use App\Models\EventConditions;
use App\Models\EventNotes;
use App\Models\ConditionSymptom;
use App\Models\Severity;
use App\Models\Symptom;
use App\Models\TimeWindowDay;
use App\Models\EventSymptoms;
use App\Models\ProfileMembers;
use App\Models\UserSymptoms;
use App\Models\Duration;
use Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\Helpers;
use PDF;
use Notification;
use App\Notifications\SendSymptomTrackerReport;
use App\Notifications\SendSymptomSuggestionMailNotification;
use Illuminate\Validation\Rule;

class SymptomTrackerController extends Controller
{
 
    /**
     * This function complies load view of event date selection page.
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function eventSelection(Request $request, $profileMemberId=NULL){
        try{

            // Check if session has event date value then remove the data from the session
            if(Session::get('eventDate')!=''){
                Session::forget('eventDate');
                Session::forget('profileMemberId');
            }

            // Reset the chart Date value if exists in the Session
            if(Session::get('chartDate')!=''){
                Session::forget('chartDate');
            }

            $userId = Auth::user()->id;

            // get user name
            $userName = Auth::user()->getUserName();

            // Check if profile member id exists then decrypt profile member id and get the profile member user name
            if(!empty($profileMemberId)){
                $profileMemberId = Crypt::decrypt($profileMemberId);
                $userName = ProfileMembers::where('addedByUserId',$userId)->where('id',$profileMemberId)
                ->select(DB::raw('CONCAT(first_name," ",last_name) As name'))->pluck('name')->first();
                // if the profile member id is not of added by current logged in user then display error message
                if(empty($userName)){
                    return redirect()->route('event-selection')->with('error','Profile Member Not Found.');
                }
            }

            // Show profile member names with route
            $userProfileMembersData = [];
            if(Auth::user()->getSubscriptionStatus()){
                $userProfileMembersData = Auth::user()->getProfileMembersWithMedicineCabinetData();
                if(!empty($userProfileMembersData)){
                    $primaryUser[0] = array('id'=>Auth::user()->id,'name'=>Auth::user()->name." ".Auth::user()->last_name,'event_selection_url'=>route('event-selection'));
                    $userProfileMembersData = array_merge($primaryUser,$userProfileMembersData);
                }
            }


            // Redirect to manage symptom screen with the profile member id selection if there when no symptoms is added - code start
            $checkRedirectToManageSymptomScreen = Helpers::redirectToManageSymptomListScreen($profileMemberId);
            if($checkRedirectToManageSymptomScreen['isRedirect'] == '1'){
                return $checkRedirectToManageSymptomScreen['route'];
            }
            // Redirect to manage symptom screen with the profile member id selection if there when no symptoms is added - code end

            // Get the previous logged event dates of current user
            $eventDates = Event::select(DB::raw('DATE_FORMAT(eventDate, "%m/%d/%Y") AS eventDate'),'id')->where('userId',$userId);
            // Check if profile member id selection is there, if there then display the data accordingly else exclude the selection
            if(!empty($profileMemberId)){
                $eventDates = $eventDates->where('profileMemberId',$profileMemberId);
            }else{
                $eventDates = $eventDates->whereNull('profileMemberId');
            }
            $eventDates = $eventDates->whereNull('deleted_at')->orderBy('event.id','DESC')->get()->toArray();

            // Get the color codes by the highest severity with the event date & profile member id in array
            $eventDates = Helpers::getHighlightColorEventDates($eventDates,$profileMemberId);

            // Get the color code of the added notes by current user or its profile member with the event date in array
            $notesDates = Helpers::getNotesAddedColor($userId,$profileMemberId);

            // Pass current date as default for current chart date value
            $currentChartDate = date('Y-m-d');
            $nextChartDate = '';
            $previousChartDate = '';
            $noOfDays = '30';
            $daysPVal = '-'.$noOfDays.' days';
            $daysNVal = '+'.$noOfDays.' days';

            // Get the requested the event date
            $requestedEventDate = isset($request->eventChartDate) && $request->eventChartDate!='' ? Crypt::decrypt($request->eventChartDate) : '';

            // Check if the date is requested from the URL, then pass the given date as current date value else pass null 
            if(isset($requestedEventDate) && $requestedEventDate!='')
            {
                //Set current date date
                $currentChartDate = $requestedEventDate;
            }
            // Set the previous 30 days from the given current date
            $previousChartDate = date('Y-m-d', strtotime($currentChartDate.$daysPVal));
            // If given current date is not matched with today's date, then pass the next 30 days date in next chart date value 
            if (strtotime($currentChartDate) != strtotime(date('Y-m-d'))) {
                // Set next 30 days of date from given current chart date value
                $nextChartDate = date('Y-m-d', strtotime($currentChartDate.$daysNVal));
            }
            
            // Get the trend chart data for the existing logged in user or it's profile member & it's number of days
            $trendChartData = Helpers::getTrendChartData($userId,$profileMemberId,'30',$currentChartDate);

            // Store the severity names listing array from the get trend chart data
            $severityNames = $trendChartData['severityNames'];
            // Store the symptom names listing array from the get trend chart data
            $symptomNames = $trendChartData['symptomNames'];
            // Store the last event dates array from the get trend chart data recorded by logged in user
            $lastDaysDateArray = $trendChartData['lastDaysDateArray'];
            // Store the trend chart data array from the get trend chart data
            $trendChartData = $trendChartData['trendChartData'];
            

            // Added previous day route without profile member selection
            $previousDateRoute = route('event-selection',['eventChartDate'=>Crypt::encrypt($previousChartDate)]);
            // Added next day route without profile member selection
            $nextDateRoute = route('event-selection',['eventChartDate'=>Crypt::encrypt($nextChartDate)]);

            // Check if profile member selection is there, then add with the profile member data access
            if(!empty($profileMemberId)){
                // Encrypt the profile member id
                $encryptedProfileMemberId = Crypt::encrypt($profileMemberId);
                // Pass the encrypted the profile member id in the previous day route with the previous date
                $previousDateRoute = route('event-selection',['profileMemberId'=>$encryptedProfileMemberId,'eventChartDate'=>Crypt::encrypt($previousChartDate)]);
                // Pass the encrypted the profile member id in the next day route with the next date
                $nextDateRoute = route('event-selection',['profileMemberId'=>$encryptedProfileMemberId,'eventChartDate'=>Crypt::encrypt($nextChartDate)]);
            }

            return view('page.event-selection',compact('userName','userProfileMembersData','profileMemberId','eventDates','notesDates','severityNames','lastDaysDateArray','trendChartData','symptomNames','currentChartDate','nextChartDate','previousDateRoute','nextDateRoute'));
        }catch (Exception $e) {
            /* Something went wrong */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }

    /**
     * This function fetchs the eventDate value and santize properly to symptom tracker page.
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function symptomSession(Request $request){
        try{
            /***
             * Validate the required event date format and restrict selection of future date
             */
            $validator = Validator::make($request->all(), [
                'eventOfDate' => 'required|date_format:m/d/Y|before_or_equal:'.date('m/d/Y')
            ]);
            //validation failed
            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput();
            }
            // Store event date into a session
            $date = $request->eventOfDate ? $request->eventOfDate : '';
            Session::put('eventDate', $date);
            // Check if session has stored event date then pass profile member id in session if exists and redirect to symptom tracker screen
            if(Session::get('eventDate')!=''){
                // Fetch profile member id from the event selection page and store it in the profileMemberId session
                $request->profileMemberId ? Session::put('profileMemberId',$request->profileMemberId) : Session::forget('profileMemberId');
                $userId = Auth::user()->id;

                //Get the event date and fetch the timewindow for it.
                $timeWindowData = DB::table('event')->select('id','timeWindowId','created_at','updated_at')->whereDate('eventDate',date('Y-m-d',strtotime($date)))->where('userId',$userId);
                if(!empty($request->profileMemberId)){
                    $timeWindowData = $timeWindowData->where('profileMemberId',$request->profileMemberId);
                }else{
                    $timeWindowData = $timeWindowData->whereNull('profileMemberId');
                }
                $timeWindowData = $timeWindowData->orderBy('timeWindowId','DESC')->get()->toArray();

                // Check if the time window data exist
                if(!empty($timeWindowData))
                {
                    foreach($timeWindowData as $timeWindowDayKey => $timeWindowDayValue)
                    {
                        // Fetch the highest severity for the timewindow screen
                        $timeWindowPriority = DB::table('event_symptoms')
                        ->leftJoin('severity','severity.id','=','event_symptoms.severityId');
                        // Add the selection for user based symptom listing - Code Start
                        $timeWindowPriority = $timeWindowPriority->leftJoin('user_symptoms','user_symptoms.symptomId','=','event_symptoms.symptomId')
                        ->where('user_symptoms.userId',$userId);
                        // Check if profileMemberId exists, then get the data accordingly or exclude for this same
                        if(!empty($profileMemberId)){
                            $timeWindowPriority = $timeWindowPriority->where('user_symptoms.profileMemberId',$profileMemberId);
                        }else{
                            $timeWindowPriority = $timeWindowPriority->whereNull('user_symptoms.profileMemberId');
                        }
                        // Add the selection for user based symptom listing - Code Start
                        $timeWindowPriority = $timeWindowPriority->where('user_symptoms.status','1')->where('event_symptoms.eventId', $timeWindowDayValue->id)
                        ->max('severity.severityPriority');

                        /**
                         * Check if the time window severity value exists then add to the time window day array,  
                         * else use the default value as 1 i.e, none
                         *  */ 
                        if($timeWindowPriority != '')
                        {
                            $timeWindowData[$timeWindowDayKey]->severityPriority = $timeWindowPriority;
                        }
                        else
                        {
                            $timeWindowData[$timeWindowDayKey]->severityPriority = '1';
                        }
                    }

                    // Fetch the data from timewindow array with maximum severity 
                    $finalTimeWindowData = Helpers::getMaxSeverity($timeWindowData);
                    // Get the time window day value to redirect to its symptom tracker's time window screen
                    $selectedTimeWindow = $finalTimeWindowData->timeWindowId;
                }
                
                /**
                 * If the timewindow day value exists then pass it to the URL for the redirection, 
                 * else redirect to the default selection of first time window data
                 */
                if(isset($selectedTimeWindow) && $selectedTimeWindow != '')
                {
                    return redirect()->route('symptom-tracker',['timeWindowDay'=>$selectedTimeWindow]);
                }
                else
                {
                    return redirect()->route('symptom-tracker');
                }
                
            }else{
                // If event date selection not found then redirect back to event-selection page
                return view('page.event-selection');
            }
            
        }catch (Exception $e) {
            /* Something went wrong */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }

    /**
     * This function complies load view of symptom tracker page as given the event date selection.
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function symptomTracker(Request $request){
        try{
            $date = Session::get('eventDate');

            $profileMemberId = '';
            // Check the profile member value from current page or previous selection
            if($request->profileMemberId == 'selfUser'){
                Session::forget('profileMemberId');
            }else{
                $profileMemberId = Session::get('profileMemberId') ? Session::get('profileMemberId') : '';
                if(!empty($request->profileMemberId)){
                   Session::forget('profileMemberId');
                   $profileMemberId = Crypt::decrypt($request->profileMemberId);
                   Session::put('profileMemberId',$profileMemberId);
                   $profileMemberId = Session::get('profileMemberId');
                }
            }
            
            $userId = Auth::user()->id;

            // get user name
            $userName = Auth::user()->getUserName();

            // If no profile member selected then use this URL as back to redirection button
            $redirectBackURL = route('event-selection'); 

            // Check if profile member id exists then get the profile member user name
            if(!empty($profileMemberId)){
                $userName = ProfileMembers::where('addedByUserId',$userId)->where('id',$profileMemberId)
                ->select(DB::raw('CONCAT(first_name," ",last_name) As name'))->pluck('name')->first();
                // if the profile member id is not of added by current logged in user then display error message
                if(empty($userName)){
                    return redirect()->route('symptom-tracker')->with('error','Profile Member Not Found.');
                }
                // If profile member selection is there then redirect back with the profile member selection
                $redirectBackURL = route('event-selection',Crypt::encrypt($profileMemberId)); 
            }

            // Show profile member names with route
            $userProfileMembersData = [];
            if(Auth::user()->getSubscriptionStatus()){
                $userProfileMembersData = Auth::user()->getProfileMembersWithMedicineCabinetData();
                if(!empty($userProfileMembersData)){
                    $primaryUser[0] = array('id'=>Auth::user()->id,'name'=>Auth::user()->name." ".Auth::user()->last_name,'symptom_tracker_url'=>route('symptom-tracker','selfUser'));
                    $userProfileMembersData = array_merge($primaryUser,$userProfileMembersData);
                }
            }

            // Redirect to manage symptom screen with the profile member id selection if there when no symptoms is added - code start
            $checkRedirectToManageSymptomScreen = Helpers::redirectToManageSymptomListScreen($profileMemberId);
            if($checkRedirectToManageSymptomScreen['isRedirect'] == '1'){
                return $checkRedirectToManageSymptomScreen['route'];
            }
            // Redirect to manage symptom screen with the profile member id selection if there when no symptoms is added - code end

            // Get the previous logged event dates of current user
            $eventDates = Event::select(DB::raw('DATE_FORMAT(eventDate, "%m/%d/%Y") AS eventDate'),'id')->where('userId',$userId);
            // Check if profile member id selection is there, if there then display the data accordingly else exclude the selection
            if(!empty($profileMemberId)){
                $eventDates = $eventDates->where('profileMemberId',$profileMemberId);
            }else{
                $eventDates = $eventDates->whereNull('profileMemberId');
            }
            $eventDates = $eventDates->whereNull('deleted_at')->orderBy('event.id','DESC')->get()->toArray();
            
            // Get the color codes by the highest severity with the event date & profile member id in array
            $eventDates = Helpers::getHighlightColorEventDates($eventDates,$profileMemberId);

            // Get the color code of the added notes by current user or its profile member with the event date in array
            $notesDates = Helpers::getNotesAddedColor($userId,$profileMemberId);

            // display default (AM) time window data
            $timeWindowDaySelected = $request->timeWindowDay ? $request->timeWindowDay : '1'; 
            $conditionId = $request->conditionId ? $request->conditionId : '304';
            if($date!=''){           

                // Fetch the data for already added symptom tracker records
                $fetchEventData = Event::where('userId',$userId);
                // Check if profileMemberId exists, then get the data accordingly or exclude for this same
                if(!empty($profileMemberId)){
                    $fetchEventData = $fetchEventData->where('profileMemberId',$profileMemberId);
                }else{
                    $fetchEventData = $fetchEventData->whereNull('profileMemberId');
                }
                $fetchEventData = $fetchEventData->where('timeWindowId',$timeWindowDaySelected)
                ->whereDate('eventDate',date('Y-m-d',strtotime($date)))->get()->last();

                /**
                 * Check if the selected event date and time window has been added by current user then, display pre-selected the data accordingly.
                 *  else do not show selected values in symptom tracker screen
                 */
                $hasSelectedEventData =  '0';
                $selectedEventId = '';
                if(!empty($fetchEventData)){
                    $hasSelectedEventData =  '1';
                    $selectedEventId = $fetchEventData['id'];
                }

                // Fetch the notes count for current event date selected by logged in user
                $eventNotesCount = count(EventNotes::getEventNotesByUser(date('Y-m-d',strtotime($date)),$profileMemberId));

                // Get the label names for the time window display
                $timeWindowDay = TimeWindowDay::select('id','label')->whereNull('deleted_at')->get()->toArray();
                if(!empty($timeWindowDay)){
                    foreach ($timeWindowDay as $timeWindowDayKey => $timeWindowDayValue){
                        $timeWindowDay[$timeWindowDayKey]['isSelected'] = '0'; // default value as not selected
                        // If the user has already selected time window day value for this event condition then pass acknowledgement accordingly
                        if(!empty($fetchEventData)){
                            if($timeWindowDayValue['id'] == $fetchEventData['timeWindowId']){
                                $timeWindowDay[$timeWindowDayKey]['isSelected'] = '1'; // pass value as selected for this time window details
                            }
                        }
                        $lableClass = '';
                        switch ($timeWindowDayValue['id']){
                            case $timeWindowDayValue['id'] == '1':
                                $lableClass = 'first-label';
                                break;
                            case $timeWindowDayValue['id'] == '2':
                                $lableClass = 'second-label';
                                break;
                            case $timeWindowDayValue['id'] == '3':
                                $lableClass = 'third-label';
                                break;
                            case $timeWindowDayValue['id'] == '4':
                                $lableClass = 'fourth-label';
                                break;      
                        }
                        $timeWindowDay[$timeWindowDayKey]['labelClass'] = $lableClass;

                    }
                }

                $trackerDetails = array();

                // Get Condition Name
                $conditionsData = ConditionSymptom::leftJoin('conditions','condition_symptom.conditionId','=','conditions.id')
                ->select('condition_symptom.id as conditionSymptomId','conditions.id as conditionId',
                'conditions.conditionName as conditionName')
                ->where('conditions.id',$conditionId)
                ->groupBy('condition_symptom.conditionId')
                ->whereNull('condition_symptom.deleted_at')->get()->toArray();
                if(!empty($conditionsData)){

                    foreach ($conditionsData as $conditionsDataKey =>  $conditionsDataValue){

                    
                        $conditionData['conditionSymptomId'] = $conditionsDataValue['conditionSymptomId'];
                        $conditionData['conditionId'] = $conditionsDataValue['conditionId'];
                        $conditionData['conditionName'] = $conditionsDataValue['conditionName'];
                        

                        // Fetch Symptoms Names of selected condition id
                        $symptomsData = Symptom::leftJoin('condition_symptom','condition_symptom.symptomId','=','symptom.id');
                        // Add the selection for user based symptom listing - Code Start
                        $symptomsData = $symptomsData->leftJoin('user_symptoms','user_symptoms.symptomId','=','symptom.id')->where('user_symptoms.userId',$userId);
                        // Check if profileMemberId exists, then get the data accordingly or exclude for this same
                        if(!empty($profileMemberId)){
                            $symptomsData = $symptomsData->where('user_symptoms.profileMemberId',$profileMemberId);
                        }else{
                            $symptomsData = $symptomsData->whereNull('user_symptoms.profileMemberId');
                        }
                        // Add the selection for user based symptom listing - Code End
                        $symptomsData = $symptomsData->select('symptom.id as symptomId','symptom.symptomName as symptomName','symptom.symptomIcon as symptomIcon','symptom.symptomSubText as symptomSubText')
                        ->where('condition_symptom.conditionId',$conditionsDataValue['conditionId'])->where('user_symptoms.status','1')->whereNull('symptom.deleted_at')
                        ->orderBy('symptom.created_at','ASC')
                        ->get()->toArray();

                        if(!empty($symptomsData)){
                            
                            foreach ($symptomsData as $symptomsDataKey => $symptomsDataValue){

                                $addSelectedSymptomDurationDurationId = '1';
                                $addSelectedSymptomDurationIcon = 'none-duration.svg';   
                                $addSelectedSymptomDurationDurationName = 'None';
                                $addSelectedSymptomDurationData = EventSymptoms::leftJoin('duration','event_symptoms.durationId','=','duration.id')
                                ->select('duration.name as durationName','durationId','icon')
                                ->where(['event_symptoms.eventId'=>$selectedEventId, 'event_symptoms.symptomId'=>$symptomsDataValue['symptomId']])
                                ->get()->first();
                                if(isset($addSelectedSymptomDurationData['durationId']) && isset($addSelectedSymptomDurationData['icon'])){
                                    $addSelectedSymptomDurationDurationId = $addSelectedSymptomDurationData['durationId'];
                                    $addSelectedSymptomDurationIcon = $addSelectedSymptomDurationData['icon'];   
                                    $addSelectedSymptomDurationDurationName = $addSelectedSymptomDurationData['durationName'];
                                }
                                $symptomsData[$symptomsDataKey]['durationName'] = $addSelectedSymptomDurationDurationName;
                                $symptomsData[$symptomsDataKey]['durationId'] = $addSelectedSymptomDurationDurationId;
                                $symptomsData[$symptomsDataKey]['durationIcon'] = $addSelectedSymptomDurationIcon;

                                // Fetch Severity Names from symptom id
                                $severityData = Severity::leftJoin('symptom','severity.symptomId','=','symptom.id')
                                ->select('severity.id as severityId','severity.severityLabel as severity','severity.severityColor as severityColor')
                                ->where('severity.symptomId',$symptomsDataValue['symptomId'])->whereNull('severity.deleted_at')->get()->toArray();
                                
                                // Fetch user selected symptom severity if already event date and severity details is added
                                if(!empty($severityData)){
                                    foreach($severityData as $severityDataKey => $severityDataValue){
                                        // default no selected severity value
                                        $severityData[$severityDataKey]['isSelected'] = '0'; 
                                        if(!empty($fetchEventData)){
                                            $fetchUserSelectedSeverityData = EventSymptoms::where('eventId',$fetchEventData['id'])
                                            ->where('symptomId',$symptomsDataValue['symptomId'])
                                            ->where('severityId',$severityDataValue['severityId'])->get()->last();
                                          
                                            if(!empty($fetchUserSelectedSeverityData)){
                                                // if severity is already selected then pass 1 as a value for acknowledgement
                                                $severityData[$severityDataKey]['isSelected'] = '1'; 
                                            }
                                            
                                        }
                                    }

                                    // Check if severity data is not empty for the current symptom then pass the records
                                    $symptomsData[$symptomsDataKey]['severityData'] = $severityData;
                                    $conditionData['symptomsData'] = $symptomsData;
                                }
                            }
                        }
                        // Store all the conditions data into array
                        $trackerDetails[] = $conditionData;
                    }

                    // Fetch severity names listing
                    $severityListing = Helpers::severityListing();

                    $additionalParametersForNotesUrl = [Crypt::encrypt(date('Y-m-d',strtotime($date))) , 'timeWindowDay'=>$timeWindowDaySelected];
                    $addNotesUrl = route('add-event-notes',$additionalParametersForNotesUrl);

                    // Get the duration data for the select
                    $duration = Duration::select('id','name','icon')->whereNull('deleted_at')->get()->toArray();
                    

                    // If the data is fetched successfully then redirect to symptom tracker screen
                    return view('page.symptom-tracker',compact('userName','profileMemberId','userProfileMembersData','redirectBackURL','conditionId','date','trackerDetails','timeWindowDay','hasSelectedEventData','selectedEventId','eventNotesCount','eventDates','notesDates','severityListing','addNotesUrl','duration','symptomsData'));

                }

                // Redirect to event selection page if condition is not found with error message
                return redirect()->route('event-selection')->withErrors('Condition not found, Please try again later.');

            }
            // Redirect to event selection page if date is not selected
            return redirect()->route('event-selection');

        }catch (Exception $e) {
            /* Something went wrong */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }

    /**
     * This function checks if given time window data has value, if there then show the response accordingly
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function checkTimeWindowEvent(Request $request){
        
        try{

            $userId = Auth::user()->id;
            $conditionId = $request->conditionId;
            $timeWindowDaySelected = $request->timeWindowDay;
            $eventDate = $request->eventDate;
            $profileMemberId = $request->profileMemberId;

            // Fetch the data for already added symptom tracker records
            $fetchEventData = Event::leftJoin('event_conditions','event_conditions.eventId','=','event.id')
            ->select('event.*')->where('event_conditions.conditionId',$conditionId)
            ->where('event.userId',$userId);
            // Check profileMemberId if selected, else exclude the selection
            if(!empty($profileMemberId)){
                $fetchEventData = $fetchEventData->where('event.profileMemberId',$profileMemberId);
            }else{
                $fetchEventData = $fetchEventData->whereNull('event.profileMemberId');
            }
            $fetchEventData = $fetchEventData->where('event.timeWindowId',$timeWindowDaySelected)
            ->whereDate('event.eventDate',date('Y-m-d',strtotime($eventDate)))->groupBy('event_conditions.conditionId')->get()->first();

            // Check for notes added by given event date
            $fetchEventNotesData = EventNotes::whereDate('eventDate',date('Y-m-d',strtotime($eventDate)))
            ->where('userId',$userId);
            // Check profileMemberId if selected, else exclude the selection
            if(!empty($profileMemberId)){
                $fetchEventNotesData = $fetchEventNotesData->where('profileMemberId',$profileMemberId);
            }else{
                $fetchEventNotesData = $fetchEventNotesData->whereNull('profileMemberId');
            }
            $fetchEventNotesData = $fetchEventNotesData->where('timeWindowDay',$timeWindowDaySelected);
            $fetchEventNotesData = $fetchEventNotesData->get()->first();

            $additionalParametersForNotesUrl = [Crypt::encrypt(date('Y-m-d',strtotime($eventDate))) , 'timeWindowDay'=>$timeWindowDaySelected];
            $addNotesUrl = route('add-event-notes',$additionalParametersForNotesUrl);

            //Check if severities tracked or notes are added for given event date then pass status as '1' else, '0'
            if(!empty($fetchEventData) || !empty($fetchEventNotesData)){
                return response()->json(['status' => '1', 'addNotesUrl' => $addNotesUrl]);
            }
            return response()->json(['status' => '0', 'addNotesUrl' => $addNotesUrl]);

        }catch (Exception $e) {
            /* Something went wrong */
            $error_message = $e->getMessage();
            return response()->json(['status' => '0', 'addNotesUrl' => $addNotesUrl, 'message' => $error_message]);
        }
    }
    


    /**
     * This function saves/updates the selected condition's symptoms & its severity into the database.
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function saveTracker(Request $request){
        try{
            /***
             * Validate the required event date format and restrict selection of future date
             */
            $validator = Validator::make($request->all(), [
                'conditionId' => 'required',
                'eventDate' => 'required|date_format:m/d/Y|before_or_equal:'.date('m/d/Y'),
                'time_window_day' => 'required',
                'symptom_severities' => 'required'
            ]);
            //validation failed
            if ($validator->fails()) 
            {
                return redirect()->route('event-selection')->withErrors($validator);
            }

            $userId = Auth::user()->id;
            $profileMemberId = !empty($request->profileMemberId) ? $request->profileMemberId : null;
            $conditionId = $request->conditionId;
            $eventDate = $request->eventDate ? date('Y-m-d H:i:s', strtotime($request->eventDate)) : '';
            $timeWindowDay = $request->time_window_day ? $request->time_window_day : '';
            $symptomsSeverityArray = $request->symptom_severities;
            // event id from edit screen
            $selectedEventId = $request->selectedEventId ? $request->selectedEventId : '';  
            // check redirect to symptom tracker screen value if exists
            $redirectBackToSymptomTracker = $request->redirectBackToSymptomTracker ? $request->redirectBackToSymptomTracker : ''; 
            // check redirect to selected time window url if exists
            $redirectToSelectedTimeWindowValue = $request->redirectToSelectedTimeWindowValue ? $request->redirectToSelectedTimeWindowValue : ''; 
            // Check if redirection is from add notes screen
            $redirectToAddNotesValue = $request->redirectToAddNotes ? $request->redirectToAddNotes : '0';
            // Check if redirection is from wellkasa tab screen
            $redirectToWellkabinetValue = $request->redirectToWellkabinet ? $request->redirectToWellkabinet : '0'; 
            // Check the selection is from profile member dropdown
            $redirectToSelectedProfile = $request->redirectToSelectedProfile == '1' ? $request->redirectToSelectedProfileURL : ''; 
            // Check if the opencalendar value is there to redirect back to symptom tracker screen with calendar to open up
            $openCalendar = $request->openCalendar == '1' ? $request->openCalendar : ''; 

            DB::beginTransaction();

            $isInsertSuccess = 0;

            // Update event data if event id exist
            if(!empty($selectedEventId)){
                // Update the current time of the event data
                $updateEventData = Event::where('id', $selectedEventId)->update([
                    'updated_at' => Carbon::now()
                ]);
                // if successfully updated then delete the data related to current event id from event_symptoms & event_conditions table
                if($updateEventData == '1'){
                    EventSymptoms::where('eventId', $selectedEventId)->delete();
                    EventConditions::where('eventId', $selectedEventId)->delete();
                }
                // Get current event id as the reference to insert into its related table
                $eventId = $selectedEventId;

            }else{

                // Insert into event table
                $eventData = new Event();
                $eventData->userId = $userId;
                $eventData->profileMemberId = $profileMemberId;
                $eventData->timeWindowId = $timeWindowDay;
                $eventData->eventDate = $eventDate;
                $eventData->created_at = Carbon::now();
                if($eventData->save()){
                    // Get new event id from created data
                    $eventId = $eventData->id;
                }
            }


            foreach ($symptomsSeverityArray as $key => $value) {

                // Check if symptom severity has a value then insert the data
                if(isset($value[$value['symptomId'].'_severity'])){

                    // Insert into event symptoms table
                    $eventSymptoms = new EventSymptoms();
                    $eventSymptoms->eventId = $eventId;
                    $eventSymptoms->symptomId = $value['symptomId'];
                    $eventSymptoms->durationId = $value[$value['symptomId'].'_duration'];
                    $eventSymptoms->severityId = $value[$value['symptomId'].'_severity'];
                    $eventSymptoms->created_at = Carbon::now();
                    if($eventSymptoms->save()){

                        $eventSymptomId = $eventSymptoms->id;

                        // Insert into event conditions table
                        $eventConditions = new EventConditions();
                        $eventConditions->eventId = $eventId;
                        $eventConditions->eventSymptomId = $eventSymptomId;
                        $eventConditions->conditionId = $conditionId;
                        $eventConditions->created_at = Carbon::now();
                        if($eventConditions->save()){
                            // if data inserted successfully then update the insert check value to 1
                            $isInsertSuccess++;
                        }else{
                            // if any error occurs while inserting the data then update the insert check value to 0
                            $isInsertSuccess = '0';
                            break;
                        }
                        
                    }else{
                        // if any error occurs while inserting the data then update the insert check value to 0
                        $isInsertSuccess = '0';
                        break;
                    }
                }   
            }
            
            // Redirect to the route with the profile member id selection if there
            $route = route('event-selection');
            $medicineCabinetRoute = route('medicine-cabinet');
            if(!empty($request->profileMemberId)){
                $route = route('event-selection',Crypt::encrypt($request->profileMemberId));
                $medicineCabinetRoute = route('medicine-cabinet',Crypt::encrypt($request->profileMemberId));
            }

            // If data not inserted properly then rollback the insert transaction and show error message.
            if($isInsertSuccess == 0){
                // If no errors while saving the data then commit the query
                DB::rollback();
                return redirect($route)->withErrors('Something went wrong. Please try adding data again.');
            }else{
                // If error occurs while saving the data then rollback the query
                DB::commit();

                // Store the success message in a variable as global
                $successMessage = 'Your symptom events have been recorded. Thank you!';

                return redirect()->route('add-medicine-symptom',[Crypt::encrypt($eventId),'timeWindowDay'=>$timeWindowDay]);

                // Save the data & redirect to symptom tracker screen
                if($redirectBackToSymptomTracker == '1' || $openCalendar == '1'){
                    Session::put('eventDate', date('m/d/Y', strtotime($eventDate))); // Store the event date in session to display selected event date
                    Session::put('openCalendar', $openCalendar); // Store the openCalendar flag in session to open calendar after screen loads
                    // Check if redirection has specified timewindow value then pass that in URL else use current time window value
                    $redirectToSelectedTimeWindowValue = $redirectToSelectedTimeWindowValue ? $redirectToSelectedTimeWindowValue : $timeWindowDay; 
                    return redirect()->route('symptom-tracker',['timeWindowDay'=>$redirectToSelectedTimeWindowValue]);
                }

                // Check if redirection is from add notes button then save the data and redirect to add notes screen
                if($redirectToAddNotesValue == '1'){
                    return redirect()->route('add-event-notes',[Crypt::encrypt(date('Y-m-d',strtotime($request->eventDate))),'timeWindowDayValue'=>$timeWindowDay]);
                }

                // Check if redirection is from wellkabinet tab then save the data and redirect to wellkabinet screen
                if($redirectToWellkabinetValue == '1'){
                    return redirect($medicineCabinetRoute);
                }

                // Check if redirection is from profile member dropdown then save the data and redirect to selected profile member screen
                if(!empty($redirectToSelectedProfile)){
                    return redirect($redirectToSelectedProfile);
                }

                // If all data inserted successfully then show success message & redirect to event selection screen. 
                return redirect($route);
            }


        }catch (Exception $e) {
            /* Something went wrong */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);

        }
    }

    /**
     * This function displays the notes page for selected event date with list of notes if added.
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function displayEventNotePage(Request $request,$date){
        try{

            // Decrypt the date
            $date = Crypt::decrypt($date);

            $checkDateFormat = ['date' => $date];
            /***
             * Validate the required event date format
             */
            $validator = Validator::make($checkDateFormat, ['date' => 'required|date_format:Y-m-d']);
            //validation failed
            if ($validator->fails()) 
            {
                return back()->withErrors($validator);
            }

            // Get the profileMemberId from the session
            $profileMemberId = Session::get('profileMemberId');

            // Get the time window day value
            $timeWindowDay = $request->timeWindowDay ? $request->timeWindowDay : '1';

            // Get the time window day hashtag based on the time window day id
            $timeWindowHashTag = TimeWindowDay::getTimeWindowHashTag($timeWindowDay);

            // Fetch the event notes from the selected date & profileMemberId if exists
            $eventNotes = EventNotes::getEventNotesByUser($date,$profileMemberId);

            // Get Logged in user id
            $userId = \Auth::user()->id;

            // Fetch the event recored data for the current date and timewindowday
            $eventTableData = Event::where('eventDate',date('Y-m-d 00:00:00',strtotime($date)))->where('userId',$userId)->where('timeWindowId',$timeWindowDay)->get()->first();
            if(!empty($eventTableData)){
                $addMedicineSymptomRoute = route('add-medicine-symptom',[Crypt::encrypt($eventTableData->id),'timeWindowDay'=>$timeWindowDay]);
            }else{
                $addMedicineSymptomRoute = "";
            }

            // display the event notes page and display notes list if available
            return view('page.event-notes',compact('eventNotes','date','timeWindowDay','timeWindowHashTag','addMedicineSymptomRoute'));
            

        }catch (Exception $e) {
            /* Something went wrong */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);

        }
    }

    /**
     * This function to save the notes page for selected event date
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function saveEventNote(Request $request){
        try{

            /***
             * Validate the required fields
             */
            $validator = Validator::make($request->all(), [
                'userId' => 'required|integer',
                'eventDate' => 'required|date_format:Y-m-d',
                'notes' => 'required',
                'timeWindowDay' => 'required'

            ]);
            //validation failed
            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput();
            }

            // Begin the SQL Query Transaction
            DB::beginTransaction();

            // encrypt the event date to redirect back to the URL
            $date = Crypt::encrypt($request->eventDate);
            // Get the time window day value from the hidden form values
            $timeWindowDay = $request->timeWindowDay ? $request->timeWindowDay : '';

            // Added the date and time window day selected value in the parameters for add event notes page
            $additionalParametersForRoute = [$date,'timeWindowDay'=>$timeWindowDay];

            // Get profileMemberId from the session
            $profileMemberId = Session::get('profileMemberId') ? Session::get('profileMemberId') : null;

            $eventNotesInsertData = new EventNotes();
            $eventNotesInsertData->userId = $request->userId;
            $eventNotesInsertData->notes = $request->notes;
            $eventNotesInsertData->profileMemberId = $profileMemberId;            
            $eventNotesInsertData->eventDate = $request->eventDate;
            $eventNotesInsertData->timeWindowDay = $timeWindowDay;
            $eventNotesInsertData->created_at = Carbon::now();
            $eventNotesInsertData->save();
            if($eventNotesInsertData){
                // If no errors while saving the data then commit the query
                DB::commit();
                return redirect()->route('add-event-notes',$additionalParametersForRoute);
            }else{
                // If error occurs while saving the data then rollback the query
                DB::rollback();
                return redirect()->route('add-event-notes',$additionalParametersForRoute)->with('error','Failed to save note, Please try again later');
            }
            

        }catch (Exception $e) {
            /* Something went wrong */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);

        }
    }


    /**
     * This function to get the note of the selected data by its id
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function editEventNote(Request $request, $id){
        try{

            // Decrypt the id
            $id = Crypt::decrypt($id);

            /***
             * Validate the required fields
             */
            $checkIdFormat = ['id' => $id];
            $validator = Validator::make($checkIdFormat, [
                'id' => 'required|integer',
            ]);

            //validation failed
            if ($validator->fails()) 
            {
                return back()->withErrors($validator);
            }

            // Fetch the event notes data by the id
            $eventNotesData = EventNotes::where('id',$id)->get()->first();
            if(!empty($eventNotesData)){
                return view('page.edit-event-notes',compact('eventNotesData'));
            }else{
                return redirect()->back()->with('error','Notes id not found. Please try again later');
            }

            

        }catch (Exception $e) {
            /* Something went wrong */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);

        }
    }

    /**
     * This function is to update the note by its id
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function updateEventNote(Request $request){
        try{

            /***
             * Validate the required fields
             */
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'notes' => 'required'
            ]);

            //validation failed
            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput();
            }

            // Begin the SQL Query Transaction
            DB::beginTransaction();

            // Update data with returning the values inserted
            $data = tap(DB::table('event_notes')->where('id',$request->id))->update([
                'notes' => $request->notes,
                'updated_at' => Carbon::now()
            ])->first();
            // Check if data has some values, i.e, data has been updated then show success message and commit the changes else rollback query with error message
            if($data){
                // Commit the changes
                DB::commit(); 
                // Fetch the date to redirect back to add event note page with the date
                $date = Crypt::encrypt(date('Y-m-d',strtotime($data->eventDate))); 
                $timeWindowDay = $request->timeWindowDay ? $request->timeWindowDay : '';
                $additionalParametersForNotesUrl = [$date, 'timeWindowDay' => $timeWindowDay];
                return redirect()->route('add-event-notes',$additionalParametersForNotesUrl);

            }else{
                // Rollback the current SQL query and return back to its current page with error message
                DB::rollback(); 
                return redirect()->back()->with('error','Something wen\'t wrong, Please try again later.');
            }
            

        }catch (Exception $e) {
            /* Something went wrong */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);

        }
    }

    /*** 
     * Delete Notes from event_notes table by the id provided
    */
    public function deleteEventNote(Request $request){
        try{
            // Get the event notes id from the input request
            $eventNotesId = $request->notesId;   
            // Check if selected event note id exists in the table, then execute below code else show error message 
            $notesDeletedCheck = EventNotes::where('id',$eventNotesId)->whereNotNull('deleted_at')->count(); 
            if($notesDeletedCheck == 0){                
                // delete the event notes if data exists
                $deleteEventNotesData = EventNotes::where('id',$eventNotesId)->delete();
                if($deleteEventNotesData != 0){
                    return json_encode(array('status'=>'0'));
                }
            }else{
                $request->session()->flash('error', 'This note is already deleted.');
                return json_encode(array('status'=>'1'));
            }
        }catch (Exception $e) {
            /* Something went wrong while displaying acl details */
            $error_message = $e->getMessage();
            return json_encode([
                'message'=> $error_message,
                'status' => 1
            ]);
        }
    }


    /**
     * This function is to download the trend chart of logged in user or 
     * it's profile member if id is given
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function saveTrendChartPDF($profileMemberId=null,Request $request){

        // Get Logged in user id
        $userId = \Auth::user()->id;

        // Get user email id
        $userEmailId = \Auth::user()->email;

        // get user name
        $userName = Auth::user()->name." ".Auth::user()->last_name;

        // Get First Name of logged in user
        $userFirstName = Auth::user()->name;

        // Check if the request of pdf is from the popup then check toMail value 
        $toMail = $request->toMail ? $request->toMail : '';
        

        //No.of days of difference to get the report
        $noOfDays = '30';

        // Check if profile member id exists then decrypt profile member id and get the profile member user name
        if(!empty($profileMemberId)){
            $userName = ProfileMembers::where('addedByUserId',$userId)->where('id',$profileMemberId)
            ->select(DB::raw('CONCAT(first_name," ",last_name) As name'))->pluck('name')->first();

            $userFirstName = ProfileMembers::where('addedByUserId',$userId)->where('id',$profileMemberId)
            ->select(DB::raw('first_name As name'))->pluck('name')->first();

            // if the profile member id is not of added by current logged in user then display error message
            if(empty($userName)){
                return redirect()->route('event-selection')->with('error','Profile Member Not Found.');
            }
        }


        // Get the previous logged event dates of current user
        $eventDates = Event::select(DB::raw('DATE_FORMAT(eventDate, "%m/%d/%Y") AS eventDate'),'id')->where('userId',$userId);
        // Check if profile member id selection is there, if there then display the data accordingly else exclude the selection
        if(!empty($profileMemberId)){
            $eventDates = $eventDates->where('profileMemberId',$profileMemberId);
        }else{
            $eventDates = $eventDates->whereNull('profileMemberId');
        }
        $eventDates = $eventDates->whereNull('deleted_at')->orderBy('event.id','DESC')->get()->toArray();
        
        // Get the first 30 days previous from current date trend chart data for the existing logged in user or it's profile member & it's number of days
        $firstTrendChartData = Helpers::getTrendChartData($userId,$profileMemberId,$noOfDays,date('Y-m-d'));

        // Store the first date end from the trend chart to calculate next 30 days data
        $firstTrendChartDate = $firstTrendChartData['newPreviousDate'];
        // Store the severity names listing array from the get trend chart data
        $firstSeverityNames = $firstTrendChartData['severityNames'];
        // Store the symptom names listing array from the get trend chart data
        $firstSymptomNames = $firstTrendChartData['symptomNames'];
        // Store the last event dates array from the get trend chart data recorded by logged in user
        $firstLastDaysDateArray = $firstTrendChartData['lastDaysDateArray'];
        // Store the trend chart data array from the get trend chart data
        $firstTrendChartData = $firstTrendChartData['trendChartData'];

        // Get the second 30 days previous from first trend chart date to display trend chart data for the existing logged in user or it's profile member & it's number of days
        $secondTrendChartData = Helpers::getTrendChartData($userId,$profileMemberId,$noOfDays,$firstTrendChartDate);

        // Store the second date end from the trend chart to calculate next 30 days data
        $secondTrendChartDate = $secondTrendChartData['newPreviousDate'];
        // Store the severity names listing array from the get trend chart data
        $secondSeverityNames = $secondTrendChartData['severityNames'];
        // Store the symptom names listing array from the get trend chart data
        $secondSymptomNames = $secondTrendChartData['symptomNames'];
        // Store the last event dates array from the get trend chart data recorded by logged in user
        $secondLastDaysDateArray = $secondTrendChartData['lastDaysDateArray'];
        // Store the trend chart data array from the get trend chart data
        $secondTrendChartData = $secondTrendChartData['trendChartData'];

        // Get the third 30 days previous from second trend chart date to display trend chart data for the existing logged in user or it's profile member & it's number of days
        $thirdTrendChartData = Helpers::getTrendChartData($userId,$profileMemberId,$noOfDays,$secondTrendChartDate);

        // Store the severity names listing array from the get trend chart data
        $thirdSeverityNames = $thirdTrendChartData['severityNames'];
        // Store the symptom names listing array from the get trend chart data
        $thirdSymptomNames = $thirdTrendChartData['symptomNames'];
        // Store the last event dates array from the get trend chart data recorded by logged in user
        $thirdLastDaysDateArray = $thirdTrendChartData['lastDaysDateArray'];
        // Store the trend chart data array from the get trend chart data
        $thirdTrendChartData = $thirdTrendChartData['trendChartData'];

        // Get the color codes by the highest severity with the event date & profile member id in array
        $eventDates = Helpers::getHighlightColorEventDates($eventDates,$profileMemberId);

        // Get current date of file creation
        $createdOnDate = date("d M Y, H:i A");

        $titleName = 'Symptom Tracker';
        // Update the title name if user is migraine user
        if(Auth::user()->isUserMigraineUser()){
            $titleName = 'Migraine Tracker';
        }

        view()->share(compact('userFirstName','titleName','userName','createdOnDate','profileMemberId','eventDates','firstSeverityNames','secondSeverityNames','thirdSeverityNames','firstLastDaysDateArray','secondLastDaysDateArray','thirdLastDaysDateArray','firstTrendChartData','secondTrendChartData','thirdTrendChartData','firstSymptomNames','secondSymptomNames','thirdSymptomNames')); 


        $pdf = PDF::loadView('page.reports.trend-chart-pdf.index');

        // Check if the toMail value is not empty, then execute the send mail functionality else download the report
        if(!empty($toMail)){
            // Assign the report file name by the current logged in user name
            $reportFileName = $userName.' Trend Chart';

            // Set the report name in the local system
            $fileName = "WellnessReport".time()."."."pdf";
            // Store the file in the public pdf folder
            file_put_contents(public_path() . '/pdf/'.$fileName,$pdf->output());

            // Get the URL of the pdf folder
            $pdfFileUrl = url('/pdf').'/'.$fileName;

            // Send mail of the trend chart report
            $sent = Notification::route('mail' , $toMail)->notify(new SendSymptomTrackerReport($userName,$pdfFileUrl,$reportFileName));
            // Check if the report is sent successfully then display the success message, else show error message
            if(empty($sent)){
                
                // Delete the file once the mail sending process is done
                if(file_exists(public_path() . '/pdf/'.$fileName)){
                    unlink(public_path() . '/pdf/'.$fileName);
                }
                // Redirect back to same screen and display the success message in the page
                return redirect()->back()->with('message','Email sent successfully.');
            }else{
                if(file_exists(public_path() . '/pdf/'.$fileName)){
                    unlink(public_path() . '/pdf/'.$fileName);
                }
                // Redirect back to same screen and display the error message in the page
                return redirect()->back()->with('message','Something went wrong, Please try again.');
            }
            
        }else{
            // If the request is from the "Download Report" button then download the report
            return $pdf->download(str_replace(" ","_",$userName.' Trend Chart').'.pdf');
        }
    }

    /**
     * This function is to display logged in user symptom list data
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function manageSymptomList(Request $request,$profileMemberId=NULL){

        // Get the logged in user id
        $userId = Auth::user()->id;

        // get user name
        $userName = Auth::user()->getUserName();

        // Check if profile member id exists then decrypt profile member id and get the profile member user name
        if(!empty($profileMemberId)){
            $profileMemberId = Crypt::decrypt($profileMemberId);
            $userName = ProfileMembers::where('addedByUserId',$userId)->where('id',$profileMemberId)
            ->select(DB::raw('CONCAT(first_name," ",last_name) As name'))->pluck('name')->first();
            // if the profile member id is not of added by current logged in user then display error message
            if(empty($userName)){
                return redirect()->route('manage-symptom-list')->with('error','Profile Member Not Found.');
            }
        }

        // Show profile member names with route
        $userProfileMembersData = [];
        if(Auth::user()->getSubscriptionStatus()){
        $userProfileMembersData = Auth::user()->getProfileMembersWithMedicineCabinetData();
            if(!empty($userProfileMembersData)){
                $primaryUser[0] = array('id'=>Auth::user()->id,'name'=>Auth::user()->name." ".Auth::user()->last_name,'manage_symptom_list'=>route('manage-symptom-list'));
                $userProfileMembersData = array_merge($primaryUser,$userProfileMembersData);
            }
        }

        // Get the symptoms list associated with the user id & profile member id if exists
        $userSymptomsList = UserSymptoms::allSymptomsList($profileMemberId);

        // Get symptoms values from symptom table alphabetically order
        $allSymptomsList = Symptom::select('id','symptomName as name')->orderBy('symptomName','ASC')->whereNull('deleted_at')->get()->toArray();

        // Exclude the symptoms list
        $exculdeSymptomIds = [];
        if(!empty($userSymptomsList)){
            // Get the symptom ids from the array
            $exculdeSymptomIds = array_column($userSymptomsList,'id');
        }

        return view('page.manage-symptom-list',compact('userName','userProfileMembersData','profileMemberId','userSymptomsList','allSymptomsList','exculdeSymptomIds'));        
    }


    /**
     * Store the user selected symptom data in user_symptom table 
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function saveSymptom(Request $request){
        try{

            /***
             * Validate the required input values
             */
            $validator = Validator::make($request->all(), [
                'symptomName' => 'required'
            ]);
            //validation failed
            if ($validator->fails()){
                return back()->withErrors($validator)->withInput();
            }

            // Get the logged in user id
            $userId = Auth::user()->id;
            // Get profile member id
            $profileMemberId = $request->profileMemberId ? $request->profileMemberId : null;
            // Get the symptom Id value
            $symptomId = $request->symptomId;
            // Fetch the symptom name 
            $symptomName = $request->symptomName;
            // Get the redirectToEventSelectionScreen value if selected from confirmation popup
            $redirectToEventSelectionScreen = $request->redirectToEventSelectionScreen ? '1' : '0';

            // Check if given symptom id exist in the symptom table
            $checkExistingSymptom = Symptom::where('id', $symptomId)->count();
            if($checkExistingSymptom==0){
                return redirect()->back()->with('error','Please select symptom name from the list');
            }

            // Get symptom ids already added by the user
            $alreadyAddedSymptoms = UserSymptoms::getAllSelectedSymptomsId($userId,$profileMemberId);
            
            // Check if already added symptom then show error message
            if(in_array($symptomId,$alreadyAddedSymptoms)){
                return redirect()->back()->with('error', $symptomName.' symptom is already added');
            }

            // Begin the SQL Query Transaction
            DB::beginTransaction();

            // Check if the redirection is selected from the confirmation popup
            if($redirectToEventSelectionScreen !='0'){
                // If save button is selected from the confirmation popup and profileMemberId is there 
                if($profileMemberId != ''){
                    $redirectURL = route('event-selection',\Crypt::encrypt($profileMemberId));    
                }else{
                    // If save button is selected from the confirmation popup and profileMemberId is not there
                    $redirectURL = route('event-selection');
                }
            }else{
                if($profileMemberId != ''){
                // If save button is not selected from the confirmation popup and profileMemberId is there
                    $redirectURL = route('manage-symptom-list',\Crypt::encrypt($profileMemberId)); 
                }else{
                    // If save button is not selected from the confirmation popup and profileMemberId is not there
                    $redirectURL = route('manage-symptom-list');
                }
            }

            // Insert into user symptom table
            $userSymptomData = new UserSymptoms();
            $userSymptomData->userId = $userId;
            $userSymptomData->profileMemberId = $profileMemberId;
            $userSymptomData->symptomId = $symptomId;
            $userSymptomData->created_at = Carbon::now();
            if($userSymptomData->save()){
                // Commit the changes
                DB::commit(); 
                // Redirect to the screen with the success message
                return redirect($redirectURL);
            }else{
                // Rollback the current SQL query and return back to its current page with error message
                DB::rollback(); 
                return redirect()->back()->with('error','Something wen\'t wrong while saving symptom. Please try again later.');
            }

        }catch (Exception $e) {
            /* Something went wrong */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }


    /**
     * Update the symptom status to display for symptom tracker 
     *
     * @return  \Illuminate\Http\Response               Redirect to related response as route path.
     */
    public function changeSymptomStatus(Request $request){

        try{

            // Get Logged in user id
            $userId = Auth::user()->id;
            // Get the status for the symptom visible
            $status = $request->status;
            // Get the current user symptom id
            $userSymptomId = $request->userSymptomId;

            // Begin the SQL Query Transaction
            DB::beginTransaction();

            // Update the taking status value to 1(taking) if not already updated.
            if($status == '1'){

                $updateIsCheckedStatus = UserSymptoms::where('id',$userSymptomId)
                ->update([
                    'status' => $status
                ]);
                if($updateIsCheckedStatus){
                    // if details are updated successfully then commit the record in table and show success message
                    DB::commit();
                    return json_encode(array('status'=>'0','changeStatus' => '1','message' => 'Symptom enabled successfully.'));
                }else{
                    // if details are not updated properly then show appropriate error message & rollback query transaction
                    DB::rollback();
                    return json_encode(array('status'=>'1','message' => 'Something went wrong while updating the status, please try again.'));
                }
                
            }
            // Update the taking status value to 0(Not taking) if not already updated.
            else if($status == '0'){

                $updateNotCheckedStatus = UserSymptoms::where('id',$userSymptomId)
                ->update([
                    'status' => $status
                ]);
                if($updateNotCheckedStatus){
                    // if details are updated successfully then commit the record in table and show success message
                    DB::commit();
                    return json_encode(array('status'=>'0','changeStatus' => '0','message' => 'Symptom disabled successfully.'));
                }else{
                    // if details are not updated properly then show appropriate error message & rollback query transaction
                    DB::rollback();
                    return json_encode(array('status'=>'1','message' => 'Something went wrong while updating the status, please try again.'));
                }
            }
           
        }catch (Exception $e) {
            /* Something went wrong while updating details */
            $error_message = $e->getMessage();
            return json_encode([
                'message'=> $error_message,
                'status' => 1
            ]);
        }

    }

    /***
     * Sends the mail notification to admin from current user regarding the supplement symptom data
     */
    public function sendSuggestedSymptom(Request $request){

        // Fetch the symptom name from the form
        $symptomName = $request->symptomName;

        // Check if the symptom name is empty then pass the error message and skip mail functionality
        if(empty($symptomName)){
            return response()->json(['status' => '0','message' => 'Symptom name is required. Please try again.']);
        }
        // Set the message format with the symptom name given to send mail
        $sendData = [
            'body' => 'Below is the new suggested symptom data <br> from user : '.Auth::user()->getUserName().' ('.Auth::user()->email .')

            <b>Symptom Name</b>: '.$symptomName.'
            ',
        ];
        // Send mail to admin
        $sendEmail = Notification::route('mail','admin@wellkasa.com')->notify(new SendSymptomSuggestionMailNotification($sendData));
        if(empty($sendEmail)){
            return response()->json(['status' => '1','message' => 'Your input has been submitted. We will contact you soon to update you on the status of your input request.']);
        }else{
            return response()->json(['status' => '0','message' => 'Something went wrong. Please try again.']);
        }
    }
        
}
@extends('layout.default')


@section('content')
<div class="container750">

    <div class="symptom-tracker">
    <div class="cabinet-accordion cabinet-header-new {{ \Auth::user()->isUserMigraineUser() ? 'quiz-flow' : ''}}">

            @if(\Auth::user()->isUserMigraineUser())
               
            @else
                <div class="cabinet-title-header">
                    <h1>Design your wellness</h1>
                    <div class="cabinet-title-header">
                        @if(!empty($userProfileMembersData))
                            <span class="dropdown" title="Select dropdown to change profile member">
                                <button class="btn btn-secondary wellkabinet-dropdown-toggle p-0" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span>{{$userName}}</span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                    @foreach($userProfileMembersData as $userProfileMembersDataValue)
                                        <a class="dropdown-item" data-session="{{isset($userProfileMembersDataValue['session_forget']) ? $userProfileMembersDataValue['session_forget'] : ''}}" href="{{$userProfileMembersDataValue['symptom_tracker_url']}}">{{$userProfileMembersDataValue['name']}}</a>
                                    @endforeach
                                    <!-------------- Check if user has subscription and has remaining profile member to add, then show add profile option - code start -------------------->   
                                    @if(Auth::user()->planType == '2' && Auth::user()->remainingProfileMemberCount != 0)
                                    <a class="dropdown-item" href="{{route('add-profile',['route' => \Crypt::encrypt('1')])}}" title="Add new profile member">Add Profile ({{Auth::user()->remainingProfileMemberCount}} Available)</a>
                                    @endif
                                    <!-------------- Check if user has subscription and has remaining profile member to add, then show add profile option - code end -------------------->   
                                </div>
                            </span>
                        @else
                        <span>{{$userName}}</span>
                        @endif
                    </div> 
                </div>

                <!------------- Tab selection - Start -------------------->
                <div class="row mt-md-3 mt-4 mb-md-5 mb-4">
                    <div class="col-md-6 col-6">
                        <a class="btn-border" href="{{$profileMemberId ? route('medicine-cabinet',\Crypt::encrypt($profileMemberId)) : route('medicine-cabinet')}}" id="wellkabinetTab"><img width="70" height="47"  src="{{asset('images/WellkasaLogo.png')}}" alt="WellkasaLogo"> Wellkabinet</a>
                    </div>
                    <div class="col-md-6 col-6">
                        <a class="btn-border btn-active" href="javascript:void(0);"><img width="70" height="47"  src="{{asset('images/symptom.png')}}" alt="Symptom Tracker"> Symptom Tracker</a>
                    </div>
                </div>
                <!------------- Tab selection - End -------------------->

            @endif

        </div>

        
        <div class="symptom-dashboard pb-4"> 
            <a class="dashboard-link" href="javascript:void(0);" id="add-symptoms" title="Redirect to event selection screen"> <img src="{{asset('images/arrow-left.svg')}}" class="mr-1 mb-1"> Dashboard</a>  
            <div class="position-relative">  
                <a href="{{$addNotesUrl}}" class="add-event-notes-file" id="add-notes"><img src="{{asset('images/file.svg')}}" class="mr-1 mb-1"> Add Notes/Meds<span id="eventNotesCount" class="d-none">{{$eventNotesCount}}</span></img></a>
                <a href="javascript:void(0);" class="add-event-notes-file ml-2 changeDate" id="changeDate"><img src="{{asset('images/calendar.svg')}}" class="mr-1 mb-1"> Change Date</img></a>
                <div class="dashboard-datepicker" id="datepicker"></div>
                <!-------------------  Event Date and profilemember id form submit values - Start  ------------------------>
                {!! Form::open(['url' => 'symptom-session', 'class'=>'login-signup-form', 'id'=>'save-event-date', 'method'=>'POST']) !!}
                    <div class="">
                        <input class="form-control " type="hidden" id="eventOfDate" name="eventOfDate" value="{{old('eventOfDate')}}" placeholder="Please select the event date" readonly="true" autocomplete="off">
                    </div>
                    <div class="">
                        <input type="hidden" id="profileMemberId" name="profileMemberId" value="{{$profileMemberId}}">
                            <button type="submit" id="trackNow" class="btn btn-gradient w-100 font-weight-bold d-none">
                                {{ __('Track Symptoms') }}
                            </button>                
                    </div>
                {!! Form::close() !!}
                <!-------------------  Event Date and profilemember id form submit values - End  ------------------------>
            </div>
        </div>
        {!! Form::open(['url' => 'save-symptom-tracker', 'class'=>'login-signup-form', 'id'=>'save-symptom-data', 'method'=>'post']) !!}
            <div class="symptom-tracker-header">
                <div class="sth-date">{{date('m/d/y',strtotime($date))}}</div> <!--// Selected Event Date Display --->
                <!-- Time window day values display - start --->
                @if(!empty($timeWindowDay))
                    <div class="symptom-day">
                        <ul class="symptom-day-list">
                            @foreach($timeWindowDay as $timeWindowDayKey => $timeWindowDayData)
                                <li class="{{$timeWindowDayData['labelClass']}}"> 
                                    <label class="symptom-container">
                                    <input type="radio" name="time_window_day" id="time_window_day" class="time_window_day{{$timeWindowDayData['id']}}" value="{{$timeWindowDayData['id']}}" {{$timeWindowDayData['isSelected'] == '1' ? "checked='checked'" : ($timeWindowDayData['id'] == '1' ?  "checked='checked'" : '' )}}>
                                    <span class="checkmark"> <span class="symptom-name-header">{{$timeWindowDayData['label']}}</span> </span>
                                    </label>
                                </li>
                            @endforeach
                            <!------------- Info icon for time window day selection - start ------------------->
                            <a tabindex="0" class="dd" data-placement="top" role="button" data-toggle="popover" data-html="true" data-trigger="focus" data-content=" <b>Morning</b>: 6am-12pm, <br> <b>Afternoon</b>: 12pm-6pm, <br> <b>Evening</b>: 6pm-12am, <br> <b>Night</b>: 12am-6am " target="_blank" >
                                <img class="info-icon" width="14" height="14" src="{{asset('images/info.svg')}}" alt="Info">
                            </a>
                            <!------------- Info icon for time window day selection - end ------------------->
                        </ul>
                       
                        <div class="text-center time_window_error_div" style="display:none;">
                            <small id="time_window_day-error" class="error" for="time_window_day"></small>
                        </div>
                    </div>
                @endif
                <!-- Time window day values display - end --->
            </div>
            <div class="symptom-tracker-wrapper">
           
                     <!-- Severity legend names display - start --->
                     @if(!empty($severityListing))
                        <div class="symptom-tracker-list-inner mb-3 pb-3 pt-3">
                            <!--- Severity Name -->
                            <div class="symptom-name-new">
                            <label class="symptom-container mr-5" style="cursor: auto;">
                                <span class="checkmark check-label"><span class="symptom-name">Symptoms</span></span>
                            </label>
                            </div>
                            <!--- Severity Name -->
                            <div class="symptom-label-new">
                            @foreach($severityListing as $severityListingKey => $severityListingData)
                                <!-- Hide none severity and display others from symptoms list - start -->
                                <label class="symptom-container severity {{$severityListing[$severityListingKey]}}  {{$severityListingKey == 'None' ? 'd-none' : ''}}" style="cursor: auto;">
                                    <span class="checkmark"><span class="symptom-name">{{$severityListingKey}}</span></span>
                                </label>
                                
                                <!-- Hide none severity and display others from symptoms list - end -->
                            @endforeach
                            
                            <label class="symptom-container severity blue-bg" style="cursor: auto;">
                                    <span class="checkmark"><span class="symptom-name">Duration</span></span>
                                </label>
                            </div>
                        </div>
                        @endif
                  
                <div class="symptom-tracker-list {{count($symptomsData) >= 10 ? 'scrollbar-symptom' : ''}}">
                    <!-- Symptoms and severity display - start --->
                    @if(!empty($trackerDetails) && isset($trackerDetails[0]['symptomsData']))

                        <!-- Severity legend names display - start --->
                        @if(!empty($severityListing))
                        <div class="symptom-tracker-list-inner mb-3 pb-3 pt-2 d-none">
                            @foreach($severityListing as $severityListingKey => $severityListingData)
                                <!-- Hide none severity and display others from symptoms list - start -->
                                @if($severityListingKey != 'None')
                                <label class="symptom-container severity {{$severityListing[$severityListingKey]}}" style="cursor: auto;">
                                    <span class="checkmark"><span class="symptom-name">{{$severityListingKey}}</span></span>
                                </label>
                                @endif
                                <!-- Hide none severity and display others from symptoms list - end -->
                            @endforeach
                        </div>
                        @endif
                        <!-- Severity legend names display - end --->   

                        @foreach ($trackerDetails as $trackerDetailsKey => $trackerDetailsData)
                            
                            @if(!empty($trackerDetailsData['symptomsData']))
                                
                                @foreach ($trackerDetailsData['symptomsData'] as $trackerSymptomsDetailsKey => $trackerSymptomsDetailsData)
                                <div class="st-list mb-2">
                                    <input type="hidden" name="symptom_severities[][symptomId]" id="symptomId" value="{{$trackerSymptomsDetailsData['symptomId']}}">

                                    <div class="st-left">
                                        @if(!empty($trackerSymptomsDetailsData['symptomIcon']))
                                            <img class="symptom-icon mr-md-2" src="{{asset('images/'.$trackerSymptomsDetailsData['symptomIcon'])}}" width="35" height="46">
                                        @endif
                                        <div class="symptom-label-main align-items-center justify-content-center">

                                            <!-- Show the sub text in the popup if data exist, else show text only - start -->
                                            @if(!empty($trackerSymptomsDetailsData['symptomSubText']))
                                                <!-- Show the sub text in the popup data exist - start -->
                                                <a tabindex="0" role="button" data-toggle="popover" data-placement="auto" data-trigger="focus" style="text-decoration: underline;" data-content="{{$trackerSymptomsDetailsData['symptomSubText']}}">
                                                {{$trackerSymptomsDetailsData['symptomName']}}
                                                </a>
                                                <!-- Show the sub text in the popup data exist - end -->
                                            @else
                                                {{$trackerSymptomsDetailsData['symptomName']}}
                                            @endif
                                            <!-- Show the sub text in the popup if data exist, else show text only - start -->
                                            
                                        </div>
                                    
                                    
                                    </div>
                                    <div class="symptom-tracker-list-inner-new symptom-tracker-list-inner mb-lg-2">
                                        @foreach ($trackerSymptomsDetailsData['severityData'] as $trackerSeverityDetailsKey => $trackerSeverityDetailsData)
                                        
                                            <!-- Hide none severity and display others from symptoms list - start -->
                                                <label class="symptom-container new-symptom-lable severity {{$trackerSeverityDetailsData['severityColor']}} {{$trackerSeverityDetailsData['severity'] == 'None' ? 'd-none' : ''}}" >
                                                    <input type="radio" 
                                                    name="symptom_severities[{{$trackerSymptomsDetailsKey}}][{{$trackerSymptomsDetailsData['symptomId']}}_severity]" 
                                                    value="{{$trackerSeverityDetailsData['severityId']}}" class="{{ucfirst($trackerSeverityDetailsData['severity']) == 'None' ? 'firstSeverityOption' : ''}} severitychecks severity-duration{{$trackerSymptomsDetailsKey}}{{$trackerSeverityDetailsData['severity']}} severity-duration{{$trackerSymptomsDetailsKey}}" data-symptom-name="{{$trackerSymptomsDetailsData['symptomName']}}"
                                                    <?php 
                                                        if($trackerSeverityDetailsData['isSelected'] == '1') {
                                                            echo "checked='checked'";
                                                        } else if ($trackerSeverityDetailsData['severity'] == "None") { 
                                                            echo "checked='checked'";
                                                        }
                                                    ?>>
                                                    <span class="checkmark"></span>
                                                </label> 
                                            <!-- Hide none severity and display others from symptoms list - end -->
                                            
                                        @endforeach
                                        <span class="dropdown duration-dropdown duration-dropdwon-new">
                                            <!-- Duration data icon display - start -->
                                            <a href="javascript:void(0);" title="Click here to update duration"  class="ml-1 dropdown-toggle" role="button" data-toggle="dropdown" aria-expanded="false">
                                                <span id="durationIconView{{$trackerSymptomsDetailsData['symptomId']}}" class="selected-duration duration-list{{$trackerSymptomsDetailsKey}}">{{$trackerSymptomsDetailsData['durationName']}}</span>
                                            </a>
                                            @if(!empty($duration))
                                                <div class="dropdown-menu" >
                                                    @foreach($duration as $durationKey => $durationData)
                                                    <label class="duration-container" onclick="durationUpdate('{{$trackerSymptomsDetailsData['symptomId'].'_'.$durationData['id']}}_duration');">
                                                        <span value="{{$durationData['id']}}" id="durationSelection_{{$trackerSymptomsDetailsData['symptomId'].'_'.$durationData['id']}}_duration">{{$durationData['name']}}</span>
                                                        <input type="radio" class="duration-radio{{$durationKey}} duration-radio" name="symptom_severities[{{$trackerSymptomsDetailsKey}}][{{$trackerSymptomsDetailsData['symptomId']}}_duration]" value="{{$durationData['id']}}" {{$trackerSymptomsDetailsData['durationId'] == $durationData['id'] ? "checked='checked'" : ''}}>
                                                        <span class="duration-checkmark"></span>
                                                    </label>
                                                    @endforeach
                                                </div>
                                            @endif 
                                           
                                            <!-- Duration data icon display - emd -->
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                                
                            @endif

                        @endforeach
                    @else
                        <div class="text-center min-h-500 align-items-center justify-content-center d-flex">
                            No records found
                        </div>
                    @endif
                    <!-- Symptoms and severity display - end --->
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12" id="buttons-div">
                    <input type="hidden" name="selectedEventId" id="selectedEventId" value="{{$selectedEventId}}">
                    <input type="hidden" name="eventDate" id="eventDate" value="{{$date}}">
                    <input type="hidden" name="conditionId" id="conditionId" value="{{$conditionId}}">
                    <input type="hidden" name="redirectBackToSymptomTracker" id="redirectBackToSymptomTracker" value="1">
                    <input type="hidden" name="redirectToSelectedTimeWindowValue" id="redirectToSelectedTimeWindowValue" value="">
                    <input type="hidden" name="redirectToAddNotes" id="redirectToAddNotes" value="0">
                    <input type="hidden" name="redirectToWellkabinet" id="redirectToWellkabinet" value="0">
                    <input type="hidden" name="redirectToSelectedProfile" id="redirectToSelectedProfile" value="0">
                    <input type="hidden" name="redirectToSelectedProfileURL" id="redirectToSelectedProfileURL" value="">
                    <input type="hidden" name="openCalendar" id="openCalendar" value="">
                    <input type="hidden" name="noValuesFromAPICheck" id="noValuesFromAPICheck" value="">
                    <input type="hidden" name="profileMemberId" id="profileMemberId" value="{{$profileMemberId}}">
                    <!---------- If tracker details exist then only show save button - start ------------------->
                    @if(!empty($trackerDetails) && isset($trackerDetails[0]['symptomsData']))
                        <button type="submit" id="submitData" class="btn btn-gradient w-100 font-weight-bold symptomTrackerSaveBtn" {{$hasSelectedEventData=='1' ? "disabled=disabled;" : ""}}>
                            {{ __('Save Symptoms') }}
                        </button>
                    @endif   
                    <!---------- If tracker details exist then only show save button - end --------------------->              
                </div>
            </div>
        {!! Form::close() !!}
    </div>
    </div>

    
</div>

<!-- Save symptoms data confirmation modal popup code start -->
<div class="modal fade" id="saveSymptomsConfirmation">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">You have unsaved changes.</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <input type="hidden" name="selectedTimeWindowValue" id="selectedTimeWindowValue" value="">
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default btn-warning" id="noDataFromPopUp" data-dismiss="modal">Don't Save</button>
        <button type="button"  id="yesDataFromPopUp" class="btn btn-success modalYes">Save</button>
      </div>
    </div>
  </div>
</div>
<!-- Save symptoms data confirmation modal popup code end -->

@endsection

@push('styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
.event a {
    background-color: #42B373 !important;
    background-image :none !important;
    color: #ffffff !important;
}
.eventHighlight-red a{
    background-color: #FA4028 !important;
    background-image :none !important;
    color: #ffffff !important;
}

.eventHighlight-pink a{
    background-color: #EDA1A3 !important;
    background-image :none !important;
    color: #454545 !important;
}

.eventHighlight-yellow a{
    background-color: #FDCA40 !important;
    background-image :none !important;
    color: #454545 !important;
}

.eventHighlight-green a{
    background-color: #E2F0D9 !important;
    background-image :none !important;
    color: #454545 !important;
}

.eventHighlight-grey a{
    background-color: #D6DCE5 !important;
    background-image :none !important;
    color: #ffffff !important;
}

/* css to center the text align in dates of the calendar */
.ui-datepicker-calendar a{
    text-align: center!important;
}
.ui-datepicker-calendar span{
    text-align: center!important;
}
.btn-gradient {
    font-size: 15px !important;
}
</style>
@endpush

@push('scripts')
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/0.10.0/lodash.min.js"></script>
<script src="{{asset('js/jquery-ui.js')}}"></script>

<script type="text/javascript">

$(document).ready(function()
{   

    //when there is an AJAX request and the user is not authenticated then reload the page - code start
    $(document).ajaxError(function (event, xhr, settings, error) {
        if(xhr.status == 401 || xhr.status == 419) {
            alert("Your session has timed out. Please login.")
            window.location.reload();
        }
    });
    //when there is an AJAX request and the user is not authenticated then reload the page - code end

    // ------------------- Change Date Datepicker code - start ------------------- //
    var end_date = new Date ("{{date('m/d/Y')}}");
    // Check if previous event dates selected, then highligh those dates in calendar
    var eventDates = {};
    let eventDatesArray = {!! isset($eventDates) ? json_encode($eventDates) : '""' !!}
    if(eventDatesArray!=''){
        $.each(eventDatesArray, function(key,val) {   
            // Convert the dates to json decode and pass in the array
            eventDates[ new Date( JSON.parse(val.eventDate) ).toString()] = new Date( JSON.parse(val.eventDate) ).toString();
            // Store the highlight color code based on event date
            eventDates[new Date( JSON.parse(val.eventDate) ).toString()+'color'] = "eventHighlight-"+val.highlightColor;
        });
    }

    // Check if previous event notes dates added, then highligh those dates in calendar
    var eventNotesDates = {};
    let eventNotesDatesArray = {!! isset($notesDates) ? json_encode($notesDates) : '""' !!}
    if(eventNotesDatesArray!=''){
        $.each(eventNotesDatesArray, function(key,val) {   
            // Convert the dates to json decode and pass in the array
            eventNotesDates[ new Date( JSON.parse(val.eventDate) ).toString()] = new Date( JSON.parse(val.eventDate) ).toString();
            // Store the highlight color of notes added indication
            eventNotesDates[new Date( JSON.parse(val.eventDate) ).toString()+'isNotesAdded'] = val.notesColor;
        });
    }

    // Hide the datepicker display
    hideCalendar();
    // On click of change date button, hide/show datepicker display
    $("#changeDate").click(function(){
        $("#openCalendar").val('1'); 
        /**  if severity is changed as per from previous selection, 
         * then while clicking on add notes prompt the confirmation to save current data*/
        let verifyChange = verifyCurrentFromPreviousFormValues();
        if(verifyChange === 1){
            localStorage.setItem("openCalendarRedirection", '1');
            $("#saveSymptomsConfirmation").modal('show'); // show confirmation popup
            $("#redirectBackToSymptomTracker").val(''); // add check to not redirect back to symptom tracker screen
            return false;
        }
        $("#datepicker").toggle();
        
        /***
         * Check if date picker calendar is open then add active class in change date button 
         * else remove active class
         * */
        if($("#datepicker").is(":visible")){
            $("#changeDate").addClass("blue-btn");
        }else{
            $("#changeDate").removeClass("blue-btn");
        }

    }); 

    $("#datepicker").datepicker({ 
        maxDate: end_date, // disable future dates from today
        beforeShowDay: function( date ) {
            // Highlight the dates in calendar if previous dates are available
            var highlight = eventDates[date];
            var eventNotesHighlight = eventNotesDates[date];

            if( highlight!='' || eventNotesHighlight!='' ) {
                // Store dynamic color code by the date
                return [true, eventDates[date+"color"]+" "+eventNotesDates[date+"isNotesAdded"], highlight];
            } else {
                return [true, '', ''];
            }
        },
        onSelect: function(date) { 
            // Get the event date selection and add it in the input for the form submit
            $("#eventOfDate").val(date);
            // Submit the form with all values
            $("#save-event-date").submit();        
        } 
    });

    // Check if there is redirection back to same page with calendar open flag
    let openCalendarFlag = "{{ Session::get('openCalendar')}}";
    openCalendarFlag = openCalendarFlag !='' ? openCalendarFlag : (localStorage.getItem("openCalendarRedirection")!="" ? localStorage.getItem("openCalendarRedirection") : '');
    if(openCalendarFlag == '1'){
        // If the flag is set to 1 from the openCalendar session then open the calendar 
        showCalendar();
        // Once opened the calendar then delete the session value of the openCalendar
        "{{ Session::forget('openCalendar')}}"
        localStorage.removeItem("openCalendarRedirection");
    }
    
    // Hide calendar when clicked outside anywhere in the html document
    $('html').click(function(event) {
      if(event.target != ""){
         setTimeout(() => { // Checks if calendar is visible after 1 second
            // if visible and class name is not empty wherever clicked the execute this code
            if($("#datepicker").is(":visible") === true && event.target.innerText){
                if($(event.target).hasClass("changeDate") || $(event.target).parent().hasClass('hasDatepicker') == true || $(event.target).parent().hasClass('ui-widget-header') == true || $(event.target).parent().hasClass('ui-corner-all') == true || $(event.target).parent().hasClass('ui-datepicker-title') == true){
                    // if class is clicked then stop behaviour of hide/show
                    event.stopPropagation();
                }else{
                    // else hide the calendar
                    hideCalendar();
                }
            }
         }, 100);
      }    
   });

    // ------------------- Change Date Datepicker code - start ------------------- //

    $('[data-toggle="popover"]').popover(); // enables the popover behaviour defined in the HTML

    localStorage.removeItem("previousFormValues"); // Remove previous stored form values
    localStorage.removeItem("currentFormValues"); // Remove current stored form values

    // Store current form values as reference once anything changed
    storePreviousFormValues();

    disableSaveButton(); // default disable save button

    getCheckedSelectionTimeWindowValue(); // Preserve the selection of time window value from request parameter

    // On form submit check validations on time window & severity selection
    $('#submitData').on('click', function(event){

        let isTimeWindowChecked = $('input[name=time_window_day]:checked').val()
        if($.isNumeric(isTimeWindowChecked) == false){
            // Show proper error message if time window is not selected
            $("#time_window_day-error").html('');
            $("#time_window_day-error").html('Please select a time window day');
            $(".time_window_error_div").show();

            // Scroll up to the section
            $('.symptom-tracker, html, body').animate({
                scrollTop: $(".symptom-day").offset().top-90
            }, 900);
            return false;
        }

        /**
         * Check if duration is selected other than none but 
         * severity is not selected then show error alert to select the severity
         */
        checkDurationSeverityChecked();


        let isSymptomSeverityChecked = $('.symptom-tracker-list-inner input[type=radio]:checked').val();
        if($.isNumeric(isSymptomSeverityChecked) == false || isSymptomSeverityChecked == '') {
            // Show proper error message if atleast one severity is not selected
            alert('Please select atleast one severity');
            return false;
        }
    });

    // On time window day selection remove the validation message
    $('input[name=time_window_day]').click(function (e) {

        // Check if duration selected but severity is not, then show error alert
        if(checkDurationSeverityCheckedStatus() == 0){
            checkDurationSeverityChecked();
            return false;
        }

        // if severity is changed as per from previous selection, then while switching to other day time prompt the confirmation to save current data
        let verifyChange = verifyCurrentFromPreviousFormValues();
        if(verifyChange === 1){
            $("#saveSymptomsConfirmation").modal('show'); // show confirmation popup
            $("#selectedTimeWindowValue").val($(this).val()); // pass current time window value to hidden popup form
            $("#redirectBackToSymptomTracker").val('1'); // add check to redirect back to symptom tracker screen
            return false;
        }

        $("#time_window_day-error").html('');
        $(".time_window_error_div").hide();

        var time_window_day_val = $(this).val();

        // Check the selected time window date has some value, if there then reload the page and display the record
        checkSymptomsSeverityValues(time_window_day_val);

    });

    /**
     * On click of plus icon implement below code
     */
    $("#add-symptoms").click(function(){
        /**  if severity is changed as per from previous selection, 
         * then while switching to other day time prompt the confirmation to save current data*/
        let verifyChange = verifyCurrentFromPreviousFormValues();
        if(verifyChange === 1){
            $("#saveSymptomsConfirmation").modal('show'); // show confirmation popup
            $("#redirectBackToSymptomTracker").val(''); // add check to not redirect back to symptom tracker screen
            return false;
        }
        redirectToEventSelectionScreen(); // Redirect to event selection screen
    });

    /**
     * On click of add notes, Implement below code
     */
    $("#add-notes").click(function(){

        $("#redirectToAddNotes").val('1'); 
        /**  if severity is changed as per from previous selection, 
         * then while clicking on add notes prompt the confirmation to save current data*/
        let verifyChange = verifyCurrentFromPreviousFormValues();
        if(verifyChange === 1){
            $("#saveSymptomsConfirmation").modal('show'); // show confirmation popup
            $("#redirectBackToSymptomTracker").val(''); // add check to not redirect back to symptom tracker screen
            return false;
        }
    });


    /**
     * On click of wellkabinet tab, Implement below code
     */
    $("#wellkabinetTab").click(function(){

        $("#redirectToWellkabinet").val('1'); 
        /**  if severity is changed as per from previous selection, 
         * then while clicking on wellkabinet tab, prompt the confirmation to save current data*/
        let verifyChange = verifyCurrentFromPreviousFormValues();
        if(verifyChange === 1){
            $("#saveSymptomsConfirmation").modal('show'); // show confirmation popup
            $("#redirectBackToSymptomTracker").val(''); // add check to not redirect back to symptom tracker screen
            return false;
        }
    });

    /**
     * On click of profile member dropdown, Implement below code
     */
    $(".dropdown-item").click(function(){

        // Get selected profile member URL
        let selectedProfileURL = $(this).attr("href");

        $("#redirectToSelectedProfile").val('1'); 
        // Store the URL in the attribute to get the URL redirection
        $("#redirectToSelectedProfileURL").val(selectedProfileURL)

        /**  if severity is changed as per from previous selection, 
         * then while clicking on profile member selection, prompt the confirmation to save current data*/
        let verifyChange = verifyCurrentFromPreviousFormValues();
        if(verifyChange === 1){
            $("#saveSymptomsConfirmation").modal('show'); // show confirmation popup
            $("#redirectBackToSymptomTracker").val(''); // add check to not redirect back to symptom tracker screen
            return false;
        }
    });

    //--------------------- Confirmation popup - code start -------------------------//
    /**
     * on click of yes button from popup 
     */
    $('#yesDataFromPopUp').on('click', function(){
        $("#redirectToSelectedTimeWindowValue").val($("#selectedTimeWindowValue").val()); // Redirect to selected time window value
        $('#save-symptom-data').submit();
    });

    /**
     * Cancel data from popup 
     */
    $('#noDataFromPopUp').on('click', function(){
        
        // Check if the "continue without saving button" is clicked from wellkabinet tab then redirect to wellkabinet screen
        let wellkabinetRedirection = $("#redirectToWellkabinet").val(); 
        if(wellkabinetRedirection == "1"){
            redirectToWellkabinetScreen();
            return false;
        }

        // Check if the "continue without saving button" is clicked from add notes button then redirect to add notes screen
        let addNotesRedirection = $("#redirectToAddNotes").val(); 
        if(addNotesRedirection == "1"){
            redirectToAddNotesScreen();
            return false;
        }


        // Check if the "continue without saving button" is clicked from profile member selection then redirect to selected profile member screen
        let profileRedirection = $("#redirectToSelectedProfile").val(); 
        if(profileRedirection == "1"){
            redirectToSelectedProfileScreen();
            return false;
        }

        // Check if the "continue without saving button" is clicked from change date calendar then redirect to same screen and open calendar
        let openCalendarRedirection = $("#openCalendar").val()!="" ? $("#openCalendar").val() : (localStorage.getItem("openCalendarRedirection")!="" ? localStorage.getItem("openCalendarRedirection") : ''); 
        if(openCalendarRedirection == "1"){
            // Get the selected time window day value
            let selectTimeWindowDayValue = $("input[name=time_window_day]:checked").val();
            // Use that value to redirect to the time window day value
            $(".time_window_day"+selectTimeWindowDayValue).prop("checked", true).trigger("click");

            // Check if open calendar redirection localStorage value exist without page reload then open calendar after 1 second
            setTimeout(() => {
                // Check if the severity data exist on any time window selection, if exist then execute the below code 
                let isValueFromAPICheck = $("#noValuesFromAPICheck").val();
                if(isValueFromAPICheck == '1'){
                    let openCalendarFlag = localStorage.getItem("openCalendarRedirection")!="" ? localStorage.getItem("openCalendarRedirection") : '';
                    if(openCalendarFlag == '1'){
                        // If the flag is set to 1 from the openCalendar session then open the calendar 
                        showCalendar();
                        // Once opened the calendar then delete the session value of the openCalendar
                        localStorage.removeItem("openCalendarRedirection");
                    }
                }
            }, "1000");
        }

        var twValue = $("#selectedTimeWindowValue").val(); // get the time window value from the popup
        if(twValue !=''){
            $(".symptom-container input[type='radio'][value='"+twValue+"']").prop('checked', true); // set the selection of time window value
            disableSaveButton(); // disable the save button by default
            checkSymptomsSeverityValues(twValue); // call ajax function to check if data exist on given time window value & redirect
        }else{
            redirectToEventSelectionScreen(); // Redirect to event selection screen
        }
    });

    /**
     * On close of popup empty the selected timewindow value, to store everytime new value and verify accordingly
     */
    $("#saveSymptomsConfirmation").on("hidden.bs.modal", function () {
        $("#selectedTimeWindowValue").val('');
        $("#redirectToAddNotes").val('0'); 
        $("#redirectToWellkabinet").val('0'); 
        $("#redirectToSelectedProfile").val('0');
        $("#redirectToSelectedProfileURL").val('');
        $("#openCalendar").val('');  
        
    });
    //--------------------- Confirmation popup - code end -------------------------//



});

    // Change the duration based on selection check update
    function durationUpdate(id){
        let selectedDurationValue = id;
        // Get the symptom id to change the duration icon for
        let symptomId = id.split('_')[0];
        // Get the selected duration image url
        let imageUrl = $("#"+id).attr('src');
        // Set the selected duration image url to symptom specific duration icon
        $("#durationIconView"+symptomId).attr('src',imageUrl);
        $("#durationIconView"+symptomId).html($('#durationSelection_'+selectedDurationValue).text());

        // Store the current changes from the selected duration values
        storeCurrentFormValues();
        // Verify form data from previous stored data
        verifyCurrentFromPreviousFormValues();
    }

    // Check the selected time window date has some value, if there then reload the page and display the record
    function checkSymptomsSeverityValues(time_window_day_val){
            
        var conditionId = $("#conditionId").val();
        var eventDate = $("#eventDate").val();
        var route = '{{route("check-time-window-event")}}';
        var profileMemberId = $("#profileMemberId").val();

        $.ajax({
            url: route,
            type: 'POST',
            data: { 'conditionId': conditionId, 'eventDate': eventDate, 'timeWindowDay': time_window_day_val, 'profileMemberId': profileMemberId},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            success: function (result) {

                //Remove timewindowday param from url
                window.history.pushState(null, null, window.location.pathname);

                // If record has some value then reload the page to get the details
                if(result.status=='1'){
                    var urls = new URL(window.location.href);

                    // Append the timeWindowDay param with the value
                    urls.searchParams.append("timeWindowDay", time_window_day_val);
                    window.history.pushState(null, null, urls);
                    window.location.href = urls;

                    return false;
                }

                // Get the url for the add notes along-with the time window day selection and update the URL in the 'Add Notes/Meds' button
                $("#add-notes").attr('href',result.addNotesUrl);

                // if data not found then reset the values of the severities
                $('.firstSeverityOption').prop('checked', true);
                $('.firstSeverityOption').attr('checked', 'checked');
                

                // Remove checked from all severity options (because none & major severity removed)
                // $('.severitychecks').prop('checked',false);
                // $('.severitychecks').removeAttr('checked');

                // Reset the duration values for all the symptoms
                resetDuration();
            
                // Remove previous stored form values
                localStorage.removeItem("previousFormValues");
                // Remove current stored form values 
                localStorage.removeItem("currentFormValues");
                // Delete the edit event id if this time window is new data 
                $("#selectedEventId").val(''); 

                // Store current form values as reference once anything changed
                storePreviousFormValues(); 

                // Store a flag value when data exist from the selected time window day
                $("#noValuesFromAPICheck").val('1');
            },error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                alert(err.Message);
            }
        });
    }

    // On severity click check if any input values are changed to enable/disable save button
    $(".symptom-tracker-list-inner .symptom-container input[type='radio']").click(function(){
        storeCurrentFormValues();
        verifyCurrentFromPreviousFormValues();
    });

    /*** 
     * Store the form values in localStorage
    */
    function storePreviousFormValues(){
        let previousFormValues = JSON.stringify($('#save-symptom-data').find(':input[type=radio]:not(:disabled)').serializeArray());
        localStorage.setItem("previousFormValues", previousFormValues);
    }

    /*** 
     * Store the current form values in localStorage
    */
    function storeCurrentFormValues(){
        let currentFormValues = JSON.stringify($('#save-symptom-data').find(':input[type=radio]:not(:disabled)').serializeArray());
        localStorage.setItem("currentFormValues", currentFormValues);
    }

    /*** 
     * Verify the form values if changed then compare the similarity and prompt the confirmation box
    */
    function verifyCurrentFromPreviousFormValues(){

        disableSaveButton();
        let status = 0;

        let formValues = JSON.parse(localStorage.getItem("currentFormValues"));
        let previousFormValues = JSON.parse(localStorage.getItem("previousFormValues"));
        if( (localStorage.hasOwnProperty("currentFormValues")) == true && (localStorage.hasOwnProperty("previousFormValues"))==true){
            if( _.isEqual(formValues, previousFormValues) === false ){
                status = 1;
                enableSaveButton();                
            }
        }
        return status;
    }

    // Disable save symptom button
    function disableSaveButton(){
        $("#submitData").prop('disabled',true);
        $("#submitData").attr('disabled','disabled');
    }

    // Enable save symptom button
    function enableSaveButton(){
        $("#submitData").prop('disabled',false);
        $("#submitData").removeAttr('disabled');
    }

    // Redirects to event selection screen
    function redirectToEventSelectionScreen(){
        redirect_route = "{{ $redirectBackURL }}";
        return window.location.href = redirect_route;
    }

    // Preserve the selection of time window value from request parameter once redirected after saving the tracker data
    function getCheckedSelectionTimeWindowValue(){
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const timeWindowDayValueFromParam = urlParams.get('timeWindowDay');
        return $("input[name=time_window_day][value="+timeWindowDayValueFromParam+"]").prop('checked', true);
    }

    // Redirects to add notes screen
    function redirectToAddNotesScreen(){
        // Get the URL from the add notes button and use as redirection
        redirect_route = $("#add-notes").attr('href');
        return window.location.href = redirect_route;
    }

    // Redirects to wellkabinet screen
    function redirectToWellkabinetScreen(){
        // Get the URL from the wellkabinet tab and use as redirection
        redirect_route = $("#wellkabinetTab").attr('href');
        return window.location.href = redirect_route;
    }

    // Redirects to selected profile member screen
    function redirectToSelectedProfileScreen(){
        // Get the URL from the drop down of selected profile member and use as redirection
        redirect_route = $("#redirectToSelectedProfileURL").val();
        return window.location.href = redirect_route;
    }

    // Open/Display Calendar function
    function showCalendar(){
        // show the calendar
        $("#datepicker").show();
        // Once calendar is opened add the active class in the change date button
        $("#changeDate").addClass("blue-btn");
    }

    // Close/Hide Calendar function
    function hideCalendar(){
        // hide the calendar
        $("#datepicker").hide();
        // Once calendar is hide remove the active class from the change date button
        $("#changeDate").removeClass("blue-btn");
    }

    // Reset the duration selected value with default "none" icon set
    function resetDuration(){
        // Remove the checked value for the duration options
        $(".duration-radio").prop("checked", false).removeAttr('checked');
        
        // Default none option is checked for all symptoms duration 
        $(".duration-radio0").prop("checked", true);

        // Set all the duration text to default none value
        $(".selected-duration").text('None');
    }

    /**
     * Check if duration is selected other than none but 
     * severity is not selected then show error alert to select the severity
     */
    function checkDurationSeverityChecked(){
        let isSuccess = 1;
        let durations = $(".selected-duration").length;
        for (let index = 0; index < durations; index++) {
            if($(".duration-list"+index).text()!='None' && $(".severity-duration"+index+"None").is(':checked') == true){
                isSuccess = 0;
                break;
            }            
        }
        if(isSuccess==0){
            alert('Found duration selected without severity for some symptom - please fix to save');
            event.preventDefault();
        }
    }

    /**
     * Return the duration selected without severity checked status
     */
    function checkDurationSeverityCheckedStatus(){
        let isSuccess = 1;
        let durations = $(".selected-duration").length;
        for (let index = 0; index < durations; index++) {
            if($(".duration-list"+index).text()!='None' && $(".severity-duration"+index+"None").is(':checked') == true){
                isSuccess = 0;
                break;
            }            
        }
        return isSuccess;
    }
</script>
@endpush
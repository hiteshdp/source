@extends('layout.default')


@section('content')
<div class="container750">
    <div class="cabinet-accordion cabinet-header-new">
    @if(\Auth::user()->isWellabinetUser())       
        <div class="cabinet-title-header">
            <h1>Design your wellness</h1>
            <div class="event-title-header-inner">
                @if(!empty($userProfileMembersData))
                    <span class="dropdown" title="Select dropdown to change profile member">
                        <button class="btn btn-secondary wellkabinet-dropdown-toggle p-0" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span>{{$userName}}</span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            @foreach($userProfileMembersData as $userProfileMembersDataValue)
                            <a class="dropdown-item" href="{{$userProfileMembersDataValue['event_selection_url']}}">{{$userProfileMembersDataValue['name']}}</a>
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
        <div class="row mt-3 mb-4">
            <div class="col-md-6 col-6">
                <a class="btn-border" href="{{$profileMemberId ? route('medicine-cabinet',\Crypt::encrypt($profileMemberId)) : route('medicine-cabinet')}}"><img width="70" height="47"  src="{{asset('images/WellkasaLogo.png')}}" alt="WellkasaLogo"> Wellkabinet</a>
            </div>
            <div class="col-md-6 col-6">
                <a class="btn-border btn-active" href="{{$profileMemberId ? route('event-selection',\Crypt::encrypt($profileMemberId)) : route('event-selection')}}"><img width="70" height="47"  src="{{asset('images/symptom.png')}}" alt="Symptom Tracker"> Symptom Tracker</a>
            </div>
        </div>
        <!------------- Tab selection - End -------------------->
    @endif

    </div>
    <div class="position-relative">
        <div class="container300 mt-lg-4">

            <!-- Hide tracker demo and save buttons - code start -->
            @if(1!=1)
            <div class="row mb-0 mt-5 d-none">
                <div class="col-md-6 col-6 d-none">
                    <a class="d-inline-block color-white btn-blue p-2 w-100" href="javascript:void(0);">Tracker Demo</a>
                </div>
                <div class="col-md-6 col-6 d-none">
                    <a tabindex="0" type="button" class="d-inline-block color-white btn-blue p-2 w-100" id="save-app" data-toggle="popover" role="button" data-trigger="focus" title="Install App">Save</a>
                </div>
            </div>
            @endif
            <!-- Hide tracker demo and save buttons - code end -->

            <!-- Calendar title - div start -->
            <div class="text-center justify-content-between">
                <span>Select a date & start tracking</span>
            </div>
            <!-- Calendar title - div end -->

            {!! Form::open(['url' => 'symptom-session', 'class'=>'login-signup-form', 'id'=>'save-event-date', 'method'=>'POST']) !!}
                <div class="event-date-picker">
                    <div class="date-container pt-3">
                        
                        <input class="form-control pl-3" type="hidden" id="eventOfDate" name="eventOfDate" value="{{old('eventOfDate')}}" placeholder="Please select the event date" readonly="true" autocomplete="off">
                        <div id="eventOfDatePicker"> </div>
                    </div>
                     <!-- legend div start  -->
                    <div class="legend">
                        <div class="legend-list">
                            <span class="note-color"></span>
                            <span class="legend-title">Notes</span>
                        </div>
                        @foreach($severityNames as $severityName)
                            <!-- Hide none & major severity and display others from symptoms list - start -->
                            @if(!in_array($severityName,['None','Major']))
                            <div class="legend-list">
                                <span class="{{strtolower($severityName)}}-color"></span>
                                <span class="legend-title">{{$severityName}}</span>
                            </div>
                            @endif
                            <!-- Hide none & major severity and display others from symptoms list - end -->
                        @endforeach
                    </div>
                    <!-- legend div End  -->
                    
                </div>
                <div class="form-group mb-3">
                    <div class="col-12">
                        <input type="hidden" id="profileMemberId" name="profileMemberId" value="{{$profileMemberId}}">
                        <button type="submit" id="trackNow" class="btn btn-gradient w-100 font-weight-bold d-none">
                            {{ __('Track Symptoms') }}
                        </button>                
                    </div>
                </div>
                
            {!! Form::close() !!}
        </div>
    </div>
    <!-- Trend Graph start  -->

        <!-- Trend Graph Title Name - start -->
        <div class="text-center pb-3 pt-4 trend-graph-title">
            <!-- Show previous arrows - start -->
            <a class="arrow-left" href="{{ $previousDateRoute }}" title="Click here to view previous 30 days data"><i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i></a>
            <!-- Show previous arrows - end -->
            <span style="font-weight: 900;">Trend Chart <br> {{date('m/d/y',strtotime(reset($lastDaysDateArray)))}} - {{date('m/d/y',strtotime(end($lastDaysDateArray)))}}</span>
           
            <!-- Show next arrows - start -->
            <a class="arrow-right {{ $nextChartDate == ''  ? 'disable-arrow-click' : ''}}" href="{{ $nextChartDate == ''  ? '#' : $nextDateRoute }}" title="Click here to view next 30 days data"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i></a>
            <!-- Show next arrows - end -->
        </div>
        <!-- Trend Graph Title Name - end -->

        <div class="graph-view  pb-0">
            @if(empty($trendChartData))
                <!-- Check if the chart data is available, if not then display below message - start -->
                <div class="no-data">
                    {{trans('messages.graph_data_not_available')}}
                </div>   
                
                <a href="{{$profileMemberId ? route('manage-symptom-list',\Crypt::encrypt($profileMemberId)) : route('manage-symptom-list')}}">
                    <img class="symptom-toggle" width="35" height="30"  src="{{asset('images/toggle.png')}}" alt="Symptom Tracker">         
                </a>

                <!-- Check if the chart data is available, if not then display below message - end -->
                <!-- If trend data exist then display symptom by severity row repetition start -->
                @if(!empty($symptomNames))
                    @foreach($symptomNames as $data)
                        <div class="graph-list">
                            <!-- Display symptom name div start -->
                            <div class="graph-left">
                                {{$data->symptomName}}
                            </div>
                            <!-- Display symptom name div end -->
                        </div>
                    @endforeach
                @endif
                <!-- If trend data exist then display symptom by severity row repetition end -->
            @else
                <a href="{{$profileMemberId ? route('manage-symptom-list',\Crypt::encrypt($profileMemberId)) : route('manage-symptom-list')}}">
                    <img class="symptom-toggle" width="35" height="30"  src="{{asset('images/toggle.png')}}" alt="Symptom Tracker">         
                </a>
                <!-- If trend data exist then display symptom by severity row repetition start -->
                @foreach($symptomNames as $data)
                    <div class="graph-list">
                        <!-- Display symptom name div start -->
                        <div class="graph-left">
                            {{$data->symptomName}}
                        </div>
                        <!-- Display symptom name div end -->
                        <!---- Severities data plots - start ---->
                        <div class="graph-right">
                            @foreach($lastDaysDateArray as $date)
                            <div class="border-right">
                                <!---- Display severity by date and symptom name data - div start ---->
                                <div class="{{strtolower($trendChartData[$date][$data->symptomName])}} stacked-bar"></div> 
                                <!---- Display severity by date and symptom name data - div end ---->
                            </div>
                            @endforeach
                        </div> 
                        <!---- Severities data plots - end ----> 
                    </div>
                @endforeach
                <!-- If trend data exist then display symptom by severity row repetition end -->
            @endif

            <!-- Display 7 days dates listing - start -->
            <div class="graph-date">
                @if (!$agent->isMobile())
                    <!--- Display dates list for desktop view only - start --->
                    @foreach($lastDaysDateArray as $date)
                        <div class="date-list">
                            <div class="date-view">{{date('d',strtotime($date))}}</div>
                        </div>
                    @endforeach
                    <!--- Display dates list for desktop view only - end --->
                @endif
            </div> 
            <!-- Display 7 days dates listing - end -->
        </div>
    <!-- Trend Graph end  -->
    <div class="row mb-3 mt-5">
        @if(!empty($trendChartData))
        <div class="col-md-6 col-6 text-right">
        <a class="d-inline btn-report download-report" href="javascript:void(0);" >
        <svg class="m-0 mb-1" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 10.5C6 10.3674 6.05268 10.2402 6.14645 10.1464C6.24021 10.0527 6.36739 10 6.5 10C6.63261 10 6.75979 10.0527 6.85355 10.1464C6.94732 10.2402 7 10.3674 7 10.5C7 10.6326 6.94732 10.7598 6.85355 10.8536C6.75979 10.9473 6.63261 11 6.5 11C6.36739 11 6.24021 10.9473 6.14645 10.8536C6.05268 10.7598 6 10.6326 6 10.5ZM6.5 12C6.36739 12 6.24021 12.0527 6.14645 12.1464C6.05268 12.2402 6 12.3674 6 12.5C6 12.6326 6.05268 12.7598 6.14645 12.8536C6.24021 12.9473 6.36739 13 6.5 13C6.63261 13 6.75979 12.9473 6.85355 12.8536C6.94732 12.7598 7 12.6326 7 12.5C7 12.3674 6.94732 12.2402 6.85355 12.1464C6.75979 12.0527 6.63261 12 6.5 12ZM6 14.5C6 14.3674 6.05268 14.2402 6.14645 14.1464C6.24021 14.0527 6.36739 14 6.5 14C6.63261 14 6.75979 14.0527 6.85355 14.1464C6.94732 14.2402 7 14.3674 7 14.5C7 14.6326 6.94732 14.7598 6.85355 14.8536C6.75979 14.9473 6.63261 15 6.5 15C6.36739 15 6.24021 14.9473 6.14645 14.8536C6.05268 14.7598 6 14.6326 6 14.5ZM8.5 10C8.36739 10 8.24021 10.0527 8.14645 10.1464C8.05268 10.2402 8 10.3674 8 10.5C8 10.6326 8.05268 10.7598 8.14645 10.8536C8.24021 10.9473 8.36739 11 8.5 11H13.5C13.6326 11 13.7598 10.9473 13.8536 10.8536C13.9473 10.7598 14 10.6326 14 10.5C14 10.3674 13.9473 10.2402 13.8536 10.1464C13.7598 10.0527 13.6326 10 13.5 10H8.5ZM8 12.5C8 12.3674 8.05268 12.2402 8.14645 12.1464C8.24021 12.0527 8.36739 12 8.5 12H13.5C13.6326 12 13.7598 12.0527 13.8536 12.1464C13.9473 12.2402 14 12.3674 14 12.5C14 12.6326 13.9473 12.7598 13.8536 12.8536C13.7598 12.9473 13.6326 13 13.5 13H8.5C8.36739 13 8.24021 12.9473 8.14645 12.8536C8.05268 12.7598 8 12.6326 8 12.5ZM8.5 14C8.36739 14 8.24021 14.0527 8.14645 14.1464C8.05268 14.2402 8 14.3674 8 14.5C8 14.6326 8.05268 14.7598 8.14645 14.8536C8.24021 14.9473 8.36739 15 8.5 15H13.5C13.6326 15 13.7598 14.9473 13.8536 14.8536C13.9473 14.7598 14 14.6326 14 14.5C14 14.3674 13.9473 14.2402 13.8536 14.1464C13.7598 14.0527 13.6326 14 13.5 14H8.5ZM6 2C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V16C4 16.5304 4.21071 17.0391 4.58579 17.4142C4.96086 17.7893 5.46957 18 6 18H14C14.5304 18 15.0391 17.7893 15.4142 17.4142C15.7893 17.0391 16 16.5304 16 16V7.414C15.9997 7.01631 15.8414 6.63503 15.56 6.354L11.646 2.439C11.3648 2.15798 10.9835 2.00008 10.586 2H6ZM5 4C5 3.73478 5.10536 3.48043 5.29289 3.29289C5.48043 3.10536 5.73478 3 6 3H10V6.5C10 6.89782 10.158 7.27936 10.4393 7.56066C10.7206 7.84196 11.1022 8 11.5 8H15V16C15 16.2652 14.8946 16.5196 14.7071 16.7071C14.5196 16.8946 14.2652 17 14 17H6C5.73478 17 5.48043 16.8946 5.29289 16.7071C5.10536 16.5196 5 16.2652 5 16V4ZM14.793 7H11.5C11.3674 7 11.2402 6.94732 11.1464 6.85355C11.0527 6.75979 11 6.63261 11 6.5V3.207L14.793 7Z" fill="white"/>
                </svg>Download Report</a>
           
    </div>
            <div class="col-md-6 col-6">
                <a class="d-inline btn-report" href="javascript:void(0);" onClick="sendMailPopup(this)" data-send-mail="{{route('trend-chart-pdf',$profileMemberId ? $profileMemberId:'')}}">
                    <img src="{{asset('images/email-icon.svg')}}" width="15" height="18" class="vertical-align-bottom">Email Report</a>
            </div>
        @endif
    </div>

    <!-- Display the tabs if user role is migraine tracker -->
    @if(\Auth::user()->isUserMigraineUser())
        @include('page.tabs.index')
    @endif
    <!-- Display the tabs if user role is migraine tracker -->
    
</div>

<!---Modal pop up for trend chart report html code - start --->
<div class="modal fade" id="messageTrendChartReportPopUp">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="trendchart-report-select-modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="trendchart-report-select-modal-body">
       
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for trend chart report html code - end --->

<!---Modal pop up for send email with attachment code start---->
<div class="modal fade sendReportMailModalPopup" id="sendReportMailModalPopup"  data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="sendmail-modal-title">Email Report</h4>
        <button type="button" class="close" id="sendMailModalClose" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="sendmail-modal-body">
        
        <form action="" id="sendMailForm" class="floating">
          
          <div class="form-group ">    
              <input id="toMail" type="email" placeholder=" " class="form-control" name="toMail" value="" required>
              <label for="toMail" class="float-label">{{ __("Recepient's Email") }}</label>
          </div>
          
          <div class="form-group mb-2">    
              <label for="pdfAttached"><i class="fa fa-file-pdf-o mr-2" aria-hidden="true"></i><em>{{ __('PDF report will be attached with this email') }}</em></label>
          </div>

          <div class="row">
              <div class="col-6">
                  <div class="form-btn mt-4 mb-4">
                      <button type="submit" class="btn btn-green w-100" id="sendMailButton">Send Mail</a>
                  </div>
              </div>
              <div class="col-6">
                  <div class="form-btn mt-4 mb-4">
                      <button type="reset" class="btn btn-green w-100" id="resetButton">Reset</a>
                  </div>
              </div>
          </div>
          <div class="form-group mb-2" style="text-align: center;">
            <label id="loadingMsg"></label>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for send email with attachment code end---->
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
.quiz-flow{
    max-width: 500px !important;
}
</style>
@endpush

@push('scripts')
<script src="{{asset('js/jquery-ui.js')}}"></script>

<script>
$(document).ready(function(){

    // Fetch the mobile version check
    let isMobileView = "{!! $agent->isMobile() !!}";

    // If screen is mobile version then display message from if block, else display message for desktop from else block
    if(isMobileView){
       var message = '<p>Check the <b>Add to Home Screen</b> option in your web browser.';
    }else{
        var message = '<p>Click install button next to the website url from your browser. After clicking the button, below popup will appear. Then click on install.<br> <img src="{{asset("images/save-app-image.png")}}" class="mr-3" alt="Sample Image" width="250" height="160">';
    }
   
    $('#save-app').popover({
        placement : 'auto',
        html : true,
        content : message
    });
});

var end_date = new Date ("{{date('m/d/Y')}}");
$(function(){

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

    $('#eventOfDatePicker').datepicker({
        maxDate: end_date, // disable future dates from today
        beforeShowDay: function( date ) {
            // Highlight the dates in calendar if previous dates are available
            var eventDatesHighlight = eventDates[date];
            var eventNotesHighlight = eventNotesDates[date];

            if( eventDatesHighlight!='' || eventNotesHighlight!='' ) {
                // Store dynamic color code by the date
                return [true, eventDates[date+"color"]+" "+eventNotesDates[date+"isNotesAdded"], eventDatesHighlight];
            } else {
                return [true, '', ''];
            }
        },
        onSelect: function(date) {
            // Get the event date selection and add it in the input for the form submit
            $("#eventOfDate").val(date);
            // Submit the form with all values
            $("#save-event-date").submit();
        },
        

    });

});

// event date range validation
$.validator.addMethod("dateRange", function(value, element, params) {
    try {
        var date = new Date(value);
        if (date <= params.from) {
        return true;
        }
    } catch (e) {}
    return false;
}, 'Please select event date less than equals to {{date("m/d/Y")}}. Please try again.');

//----------------- Create Report functionality - Start ---------------- //
$(".download-report").click(function () {

    // Check if not migraine user then apply restriction based on subscription 
    let isMigraineUser = "{{\Auth::user()->isUserMigraineUser()}}";
    if(!isMigraineUser){
        var subscriptionStatus = "{{\Auth::user()->getSubscriptionStatus()}}";
        if(!subscriptionStatus){

        // Set modal title
        $('#trendchart-report-select-modal-title').html('Permission denied!');
        
        // Set body
        $('#trendchart-report-select-modal-body').html('<p><a class="dd" href="https://wellkasa.com/products/wellkabinet" target="_blank">Renew subscription to generate PDF</a></p>');

        // Show Modal
        $('#messageTrendChartReportPopUp').modal('show');
        return false;
        }
    }
    
    
    var profileMemberId = $("#profileMemberId").val();
    // Check if profile member id exists, if exists then generate trend chart PDF accordingly
    if(profileMemberId!=''){
       window.location.href = "{{ route('trend-chart-pdf',$profileMemberId) }}"
    }else{
       window.location.href = "{{ route('trend-chart-pdf') }}"
    }
});
//----------------- Create Report functionality - End ---------------- //

//----------------- Email Report functionality - Start ---------------- //
// Send mail popup
function sendMailPopup(id){
  // get the url to send the mail with report id
  let route = $(id).attr("data-send-mail");
  
  // assign route in action attr of the form
  $("#sendMailForm").attr("action",route);
  
  // Show Modal
  $('#sendReportMailModalPopup').modal('show');
  
  // reset to email field value
  $('#toMail').val('');

}

// disable buttons and modal popup hide when email is sending
$("#sendMailButton").on("click", function(){
  // check if form is validated, then execute below code
    if($('#sendMailForm')[0].checkValidity()){
        // disable modal popup
        $('#sendReportMailModalPopup').modal({backdrop: 'static', keyboard: false});
        // execute disable buttons after 1 sec
        setTimeout(function() { 
            // disable all input and buttons in modal popup
            $("#sendMailButton").attr("disabled",true);
            $("#resetButton").attr("disabled",true);
            $("#toMail").attr("disabled",true);
            $("#sendMailModalClose").attr("disabled",true);
            $("#sendMailModalCloseButton").attr("disabled",true);  
            $("#loadingMsg").text("Please wait... while your email is being sent!");  
        }, 100);
        $("#loadingMsg").text('');
    }
});
//----------------- Email Report functionality - End ---------------- //

</script>
@endpush
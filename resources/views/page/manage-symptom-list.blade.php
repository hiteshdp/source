@extends('layout.default')


@section('content')
<div class="container750">
    <div class="cabinet-accordion cabinet-header-new {{ \Auth::user()->isUserMigraineUser() ? 'quiz-flow' : ''}}">

    @if(!\Auth::user()->isWellabinetUser())
        <h1 class="configure-your-tracker">Configure your Tracker</h1>
    @else
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
                            <a class="dropdown-item" href="{{$userProfileMembersDataValue['manage_symptom_list']}}">{{$userProfileMembersDataValue['name']}}</a>
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
                <a class="btn-border btn-active" href="javascript:void(0);"><img width="70" height="47"  src="{{asset('images/symptom.png')}}" alt="Symptom Tracker"> Symptom Tracker</a>
            </div>
        </div>
        <!------------- Tab selection - End -------------------->
    @endif

        
        <div class="manage-symptom-list">


            <!---- Display add symptom input box - Start ----->
            <div class="manage-symptom-footer mb-3 text-center">
                <form id="add-symptom-form" method="post" action="{{route('save-symptom')}}">
                    <div class="top-add-form align-items-start">
                    @csrf
                    <div class="manage-symptom-add">
                        <!---- Display symptom search dropdown - Start ----->
                            <div class=" gradient-dropdown text-left">
                                <select id="symptomId" name="symptomId" class="form-control select2 ">
                                @if(!empty($allSymptomsList))
                                    <option value="" disabled selected>Select symptom to add</option>
                                    @foreach($allSymptomsList as $allSymptomsListValue)
                                        @if(!in_array($allSymptomsListValue['id'],$exculdeSymptomIds))
                                            <option value="{{$allSymptomsListValue['id']}}">
                                                {{$allSymptomsListValue['name']}}
                                            </option>
                                        @endif
                                    @endforeach
                                @else
                                    <option value="">No records found</option>
                                @endif
                                </select>
                                <label id="symptomId-error" class="error" for="symptomId" style="display: none;"></label>
                            </div>
                        <!---- Display symptom search dropdown - End ----->
                        <input type="hidden" value="" name="symptomName" id="symptomName">
                        <input type="hidden" value="{{$profileMemberId ? $profileMemberId : ''}}" name="profileMemberId" id="profileMemberId">
                        <input type="hidden" value="" name="redirectToEventSelectionScreen" id="redirectToEventSelectionScreen">
                    </div>
                    <div class="manage-symptom-save-btn ml-2">
                        <button class=" btn-gradient w-100 border-0 d-block" type="submit" id="save-symptom">Add</button>
                    </div>
                </div>
                <div class="text-left pl-1 pt-1">
                    <a class="d-inline" id="suggest-symptom" href="javascript:void(0);"> Suggest Missing Symptom </a>
                </div>
                </form>
            </div>
            <!---- Display add symptom input box - End ----->

            <div class="manage-symptom-title text-center justify-content-center">
                <h2 class="mb-0">{{ !empty($userSymptomsList) ? "My Symptoms" : "Please add at least one symptom to start tracking symptoms" }}</h2> 
                
            </div>
            <div class="manage-symptom-tracker-list">
                <!---- Check if data is available then show the symptoms listing - Start ----->
                @if(!empty($userSymptomsList))

                    <!---- Show the symptoms list with the toggle button - Start ----->
                    @foreach($userSymptomsList as $data)
                        <div class="manage-list">
                            <div class="manage-symptom-name">
                            {{$data['symptomName']}}
                            </div>
                            <div class="cab-act-list position-relative">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input symptomCheckbox" id="customSwitch{{$data['id']}}" {{$data['status'] == '1' ? 'checked' :''}} onclick="updateSymptomStatus(this,'{{$data['userSymptomId']}}');" data-id="{{$data['userSymptomId']}}" data-status="{{$data['status']}}">
                                    <label class="custom-control-label" for="customSwitch{{$data['id']}}"></label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <!---- Show the symptoms list with the toggle button - End ----->

                @endif
                <!---- Check if data is available then show the symptoms listing - End ----->
            </div>

            <div class="manage-symptom-save-btn text-center mt-2">
                <a class="continue-symptom-tracker-btn pt-3 close-add-drugs-medicine-popup" href="{{$profileMemberId ? route('event-selection',\Crypt::encrypt($profileMemberId)) : route('event-selection') }}" id="continueBtn">Continue to Track Symptoms</a>
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
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default btn-warning" id="noDataFromPopUp" data-dismiss="modal">Don't Save</button>
                <button type="button"  id="yesDataFromPopUp" class="btn btn-success modalYes">Save</button>
            </div>
            </div>
        </div>
    </div>
<!-- Save symptoms data confirmation modal popup code end -->
   

<!-- Suggest symptom name form - start -->
<div class="suggest-symptom-popup suggest-supplement-tab mobile-popup" id="suggestSymptomForm" style="display: none;"> 
 <!------- Suggest Supplement Header - Start --------->
 <ul class="nav nav-tabs" id="custom-suggest-supplement-tab" role="tablist">
      <li class="nav-item">
         <a title="" class="nav-link pt-3 pl-1" id="naturalmedicine-tab"  href="#" role="tab" aria-controls="naturalmedicine" aria-selected="true"></a> 
      </li>
      <li class="nav-item">
         <a title="" class="nav-link pt-3 pl-1 add-supplement-title" id="naturalmedicine-tab"  href="#" role="tab" aria-controls="naturalmedicine" aria-selected="true"><span> Suggest Symptom</span></a> 
      </li>
      <li class="nav-item">
         <a title="" class="nav-link  pt-3" id="rxdrug-tab"  href="#" role="tab" aria-controls="rxdrug" aria-selected="false"></a> 
      </li>
   </ul>
   <!------- Suggest Supplement Header - End --------->
   <div class="tab-content" id="myTabContent">
        <form id="suggest-symptom-form" method="post">
            <div class="form-group">
                <small class="form-text text-muted pb-1">Symptom Name (i.e. : Dehydration)</small>
                <input type="text" autocomplete="off" class="form-control" id="symptom-name" name="symptom-name" aria-describedby="" value="" placeholder="Type symptom name">
            </div>
            <button type="submit" id="send-suggestion" class="btn-gradient w-100 border-0 mt-2">Submit Your Suggestion</button>
            <div class="text-center w-100">
                <a class="cancel-link pt-3" id="suggestSymptomFormClose" href="javascript:void(0);">Cancel</a>
            </div>
        </form>
   </div>
</div>
<!-- Suggest symptom name form - end -->

</div>
@endsection

@push('styles')
    <!-- Added select 2 css for dosage & dosageType dropdown -->
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
    <style type="text/css">
        .error {
            color: #ff0505 !important;
            font-weight: normal !important;
        }
        .select2-container { z-index: 5 !important; }
        .select2-results { overflow-x: hidden !important; }
        #suggest-symptom{ font-size: 14px; }
        #symptom-name-error{
            position: absolute;
        }
    </style>

@endpush

@push('scripts')
<script src="{{asset('js/jquery-ui.js')}}"></script>
<!-- Added select 2 js for dosage & dosageType dropdown -->
<script src="{{ asset('js/select2.min.js') }}" defer></script>

<script>

$(document).ready(function(){

    //Select2 option for frequency & dosageType dropdown
    $('.select2').select2({
        closeOnSelect: true,
        "language": {
            "noResults": function(searchedTerm) {
                // If the there no records then add "no-data-select2" class to adjust the search dropdown
                $(".select2-results").addClass('no-data-select2');
                return "No Result Found";
            }
        },
    }).on('select2:close', (elm) => {
        // Get the symptom selected id
        var selectedSymptomId = $('#symptomId').val();
        // Check if the id is selected then add the data accordingly
        if(selectedSymptomId){
            // Get the selected symptom name
            var selectedSymptomName = $('#symptomId option:selected').text();
            // Trim the whitespace from selected symptom name
            var symptomName = selectedSymptomName.trim();
            // Store the selected symptom name to the symptom name input
            $("#symptomName").val(symptomName);
            // Store the symptom id from selected symptom name
            $("#symptomId").val(selectedSymptomId);
            // Hide the error to select the symptom name below the dropdown
            $("#symptomId-error").hide();

        }else{
            // Clear the input fields including symptom name and id if no symptom is selected from the dropdown
            clearInputFields();
        }
    });

    // On search symptom check if there is results then remove "no-data-select2" class
    $('body').on('keyup', '.select2-search__field', function() {
        if($(".select2-results__options li").length > 1){
            $(".select2-results").removeClass('no-data-select2');
        }else{
            $(".select2-results").addClass('no-data-select2');
        }
    });

    // Function to clear input values from the form
    function clearInputFields(){
        $('#symptomName').attr('value', '');
        $("#symptomId").val('').attr('value', '');
    }

    
    // validate symptom name input form - code start
    $('#add-symptom-form').validate({
        rules:{
            symptomId : {
                required : true,
                normalizer: function(value) {
                    // Trim the value of the input
                    return $.trim(value);
                }
            }
        },
        messages:{
            symptomId : {
                required : "Please enter symptom name.",
            }
        }
    });
    // validate symptom name input form - code end

    // Remove the input value of symptom field if search symptom has no data
    $("#symptomId").on("input", function() {
        // Check if input value is empty 
        if($(this).val() == ''){
            // Remove the value from the input if input box is empty
            $(this).val('').attr('value', '');
        }
        else{
            // Clear input values when no matching symptom is selected
            clearInputFields();
        }
    });

    // On click of continue button
    $('#continueBtn').on('click', function(){
        var inputValue = $("#symptomId").val();
        if(inputValue){
            $("#saveSymptomsConfirmation").modal('show');
            return false;
        }
    });
    // On click save from the popup button submit form
    $("#yesDataFromPopUp").on('click', function(){
        // Pass the redirection value of the screen to event selection after saving the data
        $("#redirectToEventSelectionScreen").val('1');
        $("#add-symptom-form").submit();
    });
    // On click don't save from the popup button redirect to event selection page
    $("#noDataFromPopUp").on('click', function(){
        // Pass the empty value for redirection of the screen to current page
        $("#redirectToEventSelectionScreen").val('');
        // Get the URL from the continueBtn and redirect accordingly
        window.location.href = $("#continueBtn").attr('href');
    });


    /*********** Suggest Symptom - code start ***********/

        // Suggest symptom screen open
        $('#suggest-symptom').on('click', function() {
            $("#suggestSymptomForm").show();
        });
        // Suggest symptom screen close
        $('#suggestSymptomFormClose').on('click', function() {
            // Clear input value for symptom name field
            $("#symptom-name").val('');
            $("#suggestSymptomForm").hide();
        });

        // validate suggest symptom form - code start
        $('#suggest-symptom-form').validate({
            rules:{
                "symptom-name" : {
                required : true,
                normalizer: function(value) {
                    // Trim the value of the input
                    return $.trim(value);
                },
                maxlength : 100
            }
        },
        messages:{
            "symptom-name" : {
                required : "Please enter symptom name.",
                maxlength : "Maximum only 100 characters allowed.",
            }
        }
        });
        // validate suggest symptom form - code end

        // Send suggest symptom data form - code start
        $('#suggest-symptom-form').on('submit', function (e) {
        
            // prevent any activity to submit the form
            e.preventDefault();

            // Set the value from the validate the form 
            var symptomFormValidated = $('#suggest-symptom-form').valid();

            // check if form is validated then send data
            if(symptomFormValidated === true){
                // Get the value from the symptom name fields
                var suggestedSymptomName = $('#symptom-name').val();
                // Pass the value in the data form for ajax submit value
                var data = { symptomName: suggestedSymptomName };
                // Convert the form values into json format
                var jsonString = JSON.stringify(data);
                $.ajax({
                    url: "{{route('send-suggested-symptom')}}",
                    type: "POST",
                    data: jsonString,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    contentType: 'application/json; charset=utf-8',
                    beforeSend: function(){
                        // While data being submit show below message on the send button and make it disable
                        $("#send-suggestion").text('Sending email.. Please Wait..').attr('disabled', 'disabled');
                        $("#suggestSymptomFormClose").addClass('disable-cancel-btn');
                    },
                    complete: function(){
                        // After the process is done then enable the click of the button with below text in the button
                        $("#send-suggestion").text('Email your input request').attr("disabled", false);
                        $("#suggestSymptomFormClose").removeClass('disable-cancel-btn');

                    },
                    success: function (response)
                    {
                        // On success, show the response in the alert message 
                        $('#responseMessage').html('<div class="alert alert-info mb-0"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span>'+response.message+'</span></div>');
                        $('#responseMessage').show();
                        // Show the above response message for 5 seconds
                        setTimeout(function() { $('#responseMessage').hide();  }, 5000);

                        // Clear input value for symptom name field
                        $("#symptom-name").val('');
                        // Close the form
                        $("#suggestSymptomForm").hide();
                    }
                });
            }
        });
        // Send suggest symptom data form - code end

    /*********** Suggest Symptom - code end ***********/


});

//Check if symptom selected or not
function checkSymptomValue(){

    // Remove validation if option selected
    $('#symptomName').on('select2:closing', function (e){
        if($("#symptomName").val() != ''){
            $("#symptomName-error").text('');
            $("#symptomName-error").addClass('d-none');
        }
    });

}

// //----------------------------------  Update Symptom Status API - Code Start  --------------------------- //

function updateSymptomStatus(id,dataArrKey){

    var cardId = 'customSwitch'+dataArrKey;
    let checkboxId = $(id).attr('id');
    let symptomStatusValue = $(id).attr('data-status');
    let userSymptomId = $(id).attr('data-id');

    // revoke check/uncheck the checkbox till the status is changed
    if(symptomStatusValue == '0'){
        $("#"+checkboxId).prop('checked',false);
    }else{
        $("#"+checkboxId).prop('checked',true);
    }

    var status = symptomStatusValue;
    status = status == '0' ? '1' : '0'; // if value is 0 (disable) then consider to 1 (enable) for symptom value
    var csrf_token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: "{{route('update-symptom-status')}}",
        type : 'put',
        dataType: "json",
        "data":{ _token: csrf_token,"status":status,"userSymptomId":userSymptomId},
        success: function(res){
            // If the status value is 0 (i.e, Success) then show success message and enable/disable checkbox accordingly.
            if(res.status == 0){
               
                // check if symptom status is updated then get return value to update in the checkbox
                if(res.status!=''){
                    // update the status in the checkbox
                    $("#"+checkboxId).attr('data-status',res.changeStatus);

                    // get the status and convert to boolean value to check/uncheck checkbox
                    var status = res.changeStatus == '1' ? true : false;
                    $("#"+checkboxId).prop('checked',status);
                    if(status){ // If status is 1 then add taking data filter class
                        $("#"+cardId).attr('data-status','1');
                    }else{ // If status is 1 then removed taking data filter class
                        $("#"+cardId).attr('data-status','0');
                    }
                    
                }
                
            }
            else
            {
                // If the status value is not 0 (i.e, Fail) then show error message.
                $('#responseMessage').show();
                $('#responseMessage').html('<div class="alert alert-danger mb-0"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span>'+res.message+'</span></div>');
                // Show message till 5 seconds
                setTimeout(function() { $('#responseMessage').hide();  }, 5000);
            }
        }
    }); 
}
// //----------------------------------  Update Symptom Status API - Code End  --------------------------- //

</script>
@endpush
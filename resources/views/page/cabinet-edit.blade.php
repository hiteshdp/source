@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp


@section('content')
<div class="loader-warp loaderDiv">
   <div class="loader"></div>
</div>
<div class="container750">
    <div class="cabinet-header mobile">
        <div class="tourlogo">
        <a href="{{$backButton}}"><img  src="{{asset('images/arrow-back.svg')}}" alt="arrow"> </a>
            
        </div>    
        <div class="cabinet-title mobile">
            <h1>{{$pageTitle}}</h1>
            <span>{{$medicineCabinetData['name']}}</span>
        </div>  
        <div class="cabinet-add position-relative">
            <a href="javascript:void(0);" onClick="deleteMedicineCabinet({{$medicineCabinetData['medicineCabinetId']}})">
                <img   src="{{asset('images/bluedelete.svg')}}" alt="bluedelete"> 
            </a>
            
        </div> 
    </div>
    <div class="edit-cabinet-medicine">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
         <li class="nav-item">
            <a class="nav-link" id="details-tab" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true">Details</a>
         </li>
         <li class="nav-item">
            <a class="nav-link" id="notes-tab" data-toggle="tab" href="#notes" role="tab" aria-controls="notes" aria-selected="false">Notes</a>
         </li>
      
      </ul>
      <div class="tab-content" id="myTabContent">
         <!--- Details tab form start ---->
         <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
            <form class="edit-meicine-field mt-3" id="detailsForm" action="{{route('update-medicine-cabinet')}}" method="post">
               @csrf

               <div class="floating multi form-group mb-2">
                  <div class="form-group select-condition-form-group mb-0">
                     <label id="select-condition-placeholder" for="condition">Start typing condition to tag</label>
                     <select placeholder=" " class="form-control js-select" name="condition[]" id="condition" multiple="multiple">
                        @if($conditionsCount != 5)
                           @foreach($conditions as $value)
                              @if(!empty($medicineCabinetData['conditionIds']) && !in_array($value['id'],json_decode($medicineCabinetData['conditionIds'],true)))
                                 <option value="{{$value['id']}}" {{ (collect(old('condition'))->contains($value['id'])) ? 'selected':'' }}>{{$value['name']}}</option>
                              @else
                                 <option value="{{$value['id']}}" {{ (collect(old('condition'))->contains($value['id'])) ? 'selected':'' }}>{{$value['name']}}</option>
                              @endif
                           @endforeach
                        @else
                           <option value="" disabled>You can add only 5 Conditions, Please delete some to add new ones.</option>
                        @endif
                     </select>
                     <span id="select-conditions-error" class="error d-none" for="condition"></span>
                  </div>
               </div>
               

               <!-- Tagged Conditions Div start -->
               @if(!empty($medicineCabinetConditions))
                  <div class="form-group tagged-condition mt-3 mb-0">
                     <label  for="condition">Tagged Conditions</label>
                     <ul id="conditionTags">
                        @foreach($medicineCabinetConditions as $conditionValue)
                        <li>{{$conditionValue['medicineCabinetConditionName']}}  <a href="#" onClick="deleteCondition({{$conditionValue['medicineCabinetConditionId']}})"><img src="{{asset('images/bluedelete.svg')}}" alt="bluedelete" width="15" heigth="10"></a></li>
                        @endforeach
                     </ul>
                  </div>
               @endif
               <!-- Tagged Conditions Div end -->

               <!------ Currently taking field start ------>
               <!-- <div class="Currently mt-3" style="display: flex;justify-content: space-between;align-items: center;">
               <h2 class="detail-title">Currently taking this medicine</h2>
               <div class="cab-act-list position-relative">
                  <div class="custom-control custom-switch">
                     <span id="isTakingStatus" class="isTakingStatus">{{$medicineCabinetData['isTaking'] == '1' ? 'Yes' : 'No'}}</span>
                     <input type="checkbox" onclick="updateTakingMedicineStatus(this,'{{$medicineCabinetData['medicineCabinetId']}}');" data-taking-status="{{$medicineCabinetData['isTaking']}}" data-medicine-id="{{$medicineCabinetData['medicineCabinetId']}}" class="custom-control-input" id="customSwitch{{$medicineCabinetData['medicineCabinetId']}}" {{$medicineCabinetData['isTaking'] == '1' ? 'checked' :''}}>
                     <label class="custom-control-label" for="customSwitch{{$medicineCabinetData['medicineCabinetId']}}">Taking</label>
                  </div>
               </div>
               </div> -->
               <!------ Currently taking field end ------>

               
              
               <div class="form-row tagged-condition mt-4">
               <div class="form-group col-12 col-md-4 mb-0">
                  <div class="form-group mb-2 tagged-condition gradient-dropdown">
                     <label  for="frequency">Frequency</label> 
                     <select class="form-control select2" name="frequency" id="frequency">
                     {{old('frequency')}}
                        @if(!empty($frequency))
                           <option value="" disabled selected>Please select</option>
                           @foreach($frequency as $frequencyValue)
                              <option value="{{$frequencyValue['id']}}" id="{{$frequencyValue['id']}}" 
                                 {{ !empty(old('frequency')) ? (old('frequency') == $frequencyValue['id'] ? 'selected' : '') : ($medicineCabinetData['frequency'] == $frequencyValue['id'] ? 'selected' : '') }} >
                     
                                 {{$frequencyValue['name']}}
                     
                              </option>
                           @endforeach
                        @else
                           <option value="">Found no records for frequency</option>
                        @endif
                     </select>
                     <label id="frequency-error" class="error mb-0" for="frequency" style="display: none;"></label>
                  </div>
               </div>
                  <div class="form-group col-6 col-md-4 mb-3">
                     <label for="dosage">Dosage</label>
                     <input type="text" class="form-control" placeholder="Enter your dosage" name="dosage" id="dosage" value="{{ !empty(old('dosage')) ? (old('dosage')) : $medicineCabinetData['dosage']}}">
                  </div>
                  <div class="form-group col-6 col-md-4 gradient-dropdown mb-3">
                     <label class="d-inherit" for="dosageType">Dosage Type</label>
                     <select id="dosageType" name="dosageType" class="form-control select2 ">
                     @if(!empty($dosageType))
                        <option value="" disabled selected>Please select</option>
                        @foreach($dosageType as $dosageTypeValue)
                           <option value="{{$dosageTypeValue['id']}}"
                              {{ !empty(old('dosageType')) ? (old('dosageType') == $dosageTypeValue['id'] ? 'selected' : '') : ($medicineCabinetData['dosageType'] == $dosageTypeValue['id'] ? 'selected' : '') }} >
                        
                              {{$dosageTypeValue['name']}}
                        
                           </option>
                        @endforeach
                     @else
                        <option value="">Found no records for dosage type</option>
                     @endif
                     </select>
                     <label id="dosageType-error" class="error" for="dosageType" style="display: none;"></label>
                  </div>
                  
               </div>
               <input type="hidden" name="medicineCabinetId" value="{{$medicineCabinetData['medicineCabinetId']}}">
               <input type="hidden" name="oldConditionIds" id="oldConditionIds" value="{{$medicineCabinetData['conditionIds']}}">
               <input type="hidden" name="clickMemberId" id="clickMemberId" value=""/>
               <input type="hidden" name="profileMemberId" id="profileMemberId" value="{{$profileMemberId}}"/>
               <input type="hidden" name="updateFormType" value="detailsForm">
               <div class="save-exit-buttons mt-lg-3">
                  <button type="submit" id="updateDetailsForm" onClick="updateCabinetClick()" name="save"  value="save" class="btn-gradient w-25 border-0">Save</button>  
                  <button type="submit" id="updateDetailsForm" onClick="updateCabinetClick()" name="saveAndExist"  value="saveAndExist" class="btn-gradient w-25 border-0">Save & Exit</button> 
               </div>
            </form>
         </div>
         <!--- Details tab form end ---->

         <!--- Notes tab form start ---->
         <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
            <form class="edit-meicine-field mt-3" id="notesForm" action="{{route('update-medicine-cabinet')}}" method="post">
               @csrf
               <div class="form-group">
                  <label for="notes"></label>
                  <textarea class="form-control" id="notes" name="notes" placeholder="Add notes" rows="3"></textarea>
               </div>
               <input type="hidden" name="medicineCabinetId" value="{{$medicineCabinetData['medicineCabinetId']}}">
               <input type="hidden" name="oldConditionIds" id="oldConditionIds" value="{{$medicineCabinetData['conditionIds']}}">
               <input type="hidden" name="updateFormType" value="notesForm">
              <button type="submit" class="btn-gradient w-50 mb-4 border-0">Update</button>
               @if(!empty($medicineCabinetNotesData))
                  <h2 class="detail-title pb-2">Previous Notes</h2>
                  <div class=" note-details scroll-popup notes-scroll">
                     @foreach ($medicineCabinetNotesData as $medicineCabinetNotesDataValue)
                        <div class="notes-list">
                           <h3>{{$medicineCabinetNotesDataValue['date']}}</h3>
                           <p>{{$medicineCabinetNotesDataValue['notes']}}</p> 
                        </div>
                     @endforeach
                  </div>
               @endif
            </form>
         </div>
         <!--- Notes tab form end ---->

      </div>
   </div>
</div>

<!------ Delete medicine pop up window - code start ------->
<div class="modal fade" id="deleteMedicineConfirmation">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="medicine-modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="medicine-modal-body">
       
      </div>
      <input type="hidden" name="medicineModalId" value="" id="medicineModalId">
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button"  id="modalMedicineYes" class="btn btn-green modalMedicineYes">Yes</button>
      </div>
    </div>
  </div>
</div>
<!------ Delete medicine pop up window - code end ------->

<!------ Delete condition pop up window - code start ------->
<div class="modal fade" id="deleteConditionConfirmation">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="condition-modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="condition-modal-body">
       
      </div>
      <input type="hidden" name="conditionModalId" value="" id="conditionModalId">
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button"  id="modalConditionYes" class="btn btn-green modalConditionYes">Yes</button>
      </div>
    </div>
  </div>
</div>
<!------ Delete condition pop up window - code end ------->

@endsection

@push('styles')
<!-- Added select 2 css for dosage & dosageType dropdown -->
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
<style type="text/css">
.error {
   color: #ff0505 !important;
   font-weight: normal !important;
}
</style>

@endpush

@push('scripts')

<!-- Added select 2 js for dosage & dosageType dropdown -->
<script src="{{ asset('js/select2.min.js') }}" defer></script>

<script>
   $(document).ready(function()
   {
      $('.loaderDiv').hide();

      // setActiveTab();
      displayActiveTabView();

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
      });

      // On search data from dropdown check if there is results then remove "no-data-select2" class
      $('body').on('keyup', '.select2-search__field', function() {
         if($(".select2-results__options li").length > 1){
            $(".select2-results").removeClass('no-data-select2');
         }else{
            $(".select2-results").addClass('no-data-select2');
         }
      });

      // hide error when frequency option is selected
      $('#frequency').on('change', function() {
         if ($('#frequency').val() != ""){
            $('#frequency-error').hide();
         }
      });

      // hide error when dosageType option is selected
      $('#dosageType').on('change', function() {
         if ($('#dosageType').val() != ""){
            $('#dosageType-error').hide();
         }
      });

      var conditionsCount = "{{$conditionsCount == '0' ? '5' : $conditionsCount}}";
      //Select2  option for condition dropdown  
      $('.js-select').select2({
         closeOnSelect: true,
         tags: true,
         maximumSelectionLength: conditionsCount,
         language: {
            maximumSelected: function (e) {
            var t = "You can only select " + e.maximum + " conditions at a time";
            return t;
            }
         }
      }).on('select2:open', (elm) => {
         const targetLabel = $(elm.target).prev('#select-condition-placeholder');
         targetLabel.addClass('selected');

         // Check after 1 sec if there are options in conditions dropdown then change the css accordingly
         setTimeout(() => {
            let optionLength = $('#select2-condition-results li').length;
            if(optionLength > 2){
               var style ="height:200px !important;";
               $(".select2-results").attr("style", style);
            }else{
               var style ="height:auto !important;";
               $(".select2-results").attr("style", style);
            }
         }, 100);

      }).on('select2:close', (elm) => {
         const target = $(elm.target);
         const targetLabel = target.prev('#select-condition-placeholder');
         const targetOptions = $(elm.target.selectedOptions);
         if (targetOptions.length === 0) {
            targetLabel.removeAttr('class');
         }
      });
      // for click on label of select2 dropdown then show label above dropdown
      $('#select-condition-placeholder').on('click', function(){      
         $(this).addClass('selected');
         $('#condition').select2('open');
      });

      // details tab form validation check
      $('#detailsForm').validate({    
         rules:{
            frequency : {
               required : false,
            },
            dosage : { 
               required : false,
               number : true,
               min : true,
            },
            dosageType : {
               required : false,
            },
         },
         messages:{
            frequency : {
               required : "Please select frequency.",
            },
            dosage : {
               required : "Please enter dosage.",
               number : "Please enter only numeric values.",
               min : "Minimum dosage value should be greater than equals to 1."
            },
            dosageType : {
               required : "Please select dosage type.",
            },
         }     
      });

      // jQuery Validator method for required but not blank.
      $.validator.addMethod('requiredNotBlank', function(value, element) {
         return $.validator.methods.required.call(this, $.trim(value), element);
      }, 'Blank space not allowed. Please enter notes.');

      // notes tab form validation check
      $('#notesForm').validate({    
         rules:{
            notes : {
               required : true,
               requiredNotBlank: true,
            },
         },
         messages:{
            notes : {
               required : "Please enter notes.",
            },
         }     
      });


      //Check if conditions selected or not
      function checkConditionsValue(){
      
         // Check if conditions tag does not exist
         if($("#conditionTags li").length == 0){
            // Check if condition is empty
            if($("#condition").val() == ''){
               $("#select-conditions-error").text('');
               $("#select-conditions-error").text('Please select condition.');
               $("#select-conditions-error").removeClass('d-none');
               event.preventDefault();
            }else{
               // Condition is selected then remove validation message
               $("#select-conditions-error").text('');
               $("#select-conditions-error").addClass('d-none');
            }
         }
         // Remove validation if option selected
         $('#condition').on('select2:closing', function (e){
            if($("#condition").val() != ''){
               $("#select-conditions-error").text('');
               $("#select-conditions-error").addClass('d-none');
            }
         });
      
      }

   });

   //----------------------------------  Delete Medicine Cabinet Pop up --------------------------- //

   function deleteMedicineCabinet(id){
      // Set modal title
      $('#medicine-modal-title').html('Delete Medicine Confirmation');
      
      // Set body
      $('#medicine-modal-body').html('Are you sure you want to delete this medicine details?');

      // Show Modal
      $('#deleteMedicineConfirmation').modal('show');
      
      // Set hidden input type value 
      $('#medicineModalId').val(id);
   }

   //----------------------------------  Delete medicine API  --------------------------- //

   // Call Ajax for Delete medicine
   $('.modalMedicineYes').on('click',function()
   {   
      
      var modalId = $('#medicineModalId').val();
      var csrf_token = $('meta[name="csrf-token"]').attr('content');
      var redirect_route = "{{ route('medicine-cabinet') }}"; 
      let profileMemberId = "{{$profileMemberId}}";
      if(profileMemberId!=''){
         redirect_route = "{{ route('medicine-cabinet',$profileMemberId) }}";
      }
      $.ajax({
         url: "{{route('delete-medicine')}}",
         type : 'delete',
         dataType: "json",
         "data":{ _token: csrf_token,"medicineCabinetId":modalId},
         success: function(res){
            if(res.status == 0){
               $('#deleteMedicineConfirmation').modal('hide');
               window.location.href = redirect_route
            }
            else
            {
               window.location.href = redirect_route                      
            }
         }
      });    
      
   });


   //----------------------------------  Delete Condition Pop up --------------------------- //

   function deleteCondition(id){
      // Set modal title
      $('#condition-modal-title').html('Delete Condition Tag Confirmation');
      
      // Set body
      $('#condition-modal-body').html('Are you sure you want to delete this condition tag?');

      // Show Modal
      $('#deleteConditionConfirmation').modal('show');
      
      // Set hidden input type value 
      $('#conditionModalId').val(id);
   }

   //----------------------------------  Delete Condition API  --------------------------- //

   // Call Ajax for Delete Condition
   $('.modalConditionYes').on('click',function()
   {   
      
      var modalId = $('#conditionModalId').val();
      var medicineCabinetId = "{{$medicineCabinetData['medicineCabinetId']}}";
      var csrf_token = $('meta[name="csrf-token"]').attr('content');
      $.ajax({
         url: "{{route('delete-medicine-condition')}}",
         type : 'delete',
         dataType: "json",
         "data":{ _token: csrf_token,"conditionId":modalId,"medicineCabinetId":medicineCabinetId},
         success: function(res){
            if(res.status == 0){
               $('#deleteConditionConfirmation').modal('hide');
               window.location.reload();
            }
            else
            {
               window.location.reload();                     
            }
         }
      });    
      
   });

   function updateCabinetClick(){
      // Get active profile if from localstorage
     var clickMemberId = localStorage.getItem("clickMemberId");
      $('#clickMemberId').val(clickMemberId);
   }


//----------------------------------  Update Taking Medicine Status API  --------------------------- //

   function updateTakingMedicineStatus(id,dataArrKey){

      let checkboxId = $(id).attr('id');
      let medicineTakingStatusValue = $(id).attr('data-taking-status');
      let medicineCabinetIdValue = $(id).attr('data-medicine-id');

      // revoke check/uncheck the checkbox till the status is changed
      if(medicineTakingStatusValue == '0'){
         $("#"+checkboxId).prop('checked',false);
      }else{
         $("#"+checkboxId).prop('checked',true);
      }

      var status = medicineTakingStatusValue;
      status = status == '0' ? '1' : '0'; // if value is 0 (not taking) then consider to 1 (taking) for taking medicine value
      var medicineCabinetId = medicineCabinetIdValue;
      var csrf_token = $('meta[name="csrf-token"]').attr('content');
      $.ajax({
         url: "{{route('update-taking-medicine-status')}}",
         type : 'put',
         dataType: "json",
         "data":{ _token: csrf_token,"takingStatus":status,"medicineCabinetId":medicineCabinetId},
         success: function(res){
            if(res.status == 0){
               $('#responseMessage').show();
               $('#responseMessage').html('<div class="alert alert-info mb-0"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span>'+res.message+'</span></div>');
               
               // check if taking status is updated then get return value to update in the checkbox
               if(res.isTakingStatus!=''){
                  
                  // update the status in the checkbox
                  $("#"+checkboxId).attr('data-taking-status',res.isTakingStatus);

                  // get the status and convert to boolean value to check/uncheck checkbox
                  var isTakingStatus = res.isTakingStatus == '1' ? true : false;
                  $("#"+checkboxId).prop('checked',isTakingStatus);
                  
               }
               $("#isTakingStatus").load(location.href + " #isTakingStatus");
               setTimeout(function() { $('#responseMessage').hide();  }, 5000);                    

            }
            else
            {
               $('#responseMessage').show();
               $('#responseMessage').html('<div class="alert alert-danger mb-0"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span>'+res.message+'</span></div>');
               setTimeout(function() { $('#responseMessage').hide();  }, 5000);                    

            }

         }
      });    

   }

//----------------------------------  Retain selected tab display view --------------------------- //

   function displayActiveTabView(){
      $('.loaderDiv').show();
      let current_tab = "{{Session::get('updateFormType')}}"; // get the tab name in session
      
      if(current_tab!=""){
         if (current_tab == "detailsForm"){
            $('#details-tab').click();
            $('.loaderDiv').hide();
         }
         if (current_tab == "notesForm"){
            $('#notes-tab').click();
            $('.loaderDiv').hide();
         }
      }
      else
      {
         $('#details-tab').click();
         $('.loaderDiv').hide();
      }
      "{{Session::forget('updateFormType')}}" // flush the selected tab value from session after display
   }
</script>
@endpush
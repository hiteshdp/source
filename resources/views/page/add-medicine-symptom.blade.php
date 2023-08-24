@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp


@section('content')
<div class="container750">
    <div class="cabinet-header mobile justify-content-start">
    <div class="cabinet-title mobile w-100">
            <h1>Add any medicine or therapy use with this episode</h1>
        </div>
        <div class="tourlogo">
            <a href="{{route('symptom-tracker',['timeWindowDay'=>$timeWindowDay])}}" title="Back to symptom tracking">
            <img  src="{{asset('images/close.svg')}}" alt="arrow"> 
            </a>   
        </div>    
          
        
    </div>
    <div class="edit-cabinet-medicine">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
         <li class="nav-item">
            <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true">Add <br class="d-none"> Medicine/Therapy</a>
         </li>
         <li class="nav-item">
            <a class="nav-link" id="notes-tab" href="{{$notesUrl}}">Add<br class="d-none"> Notes</a>
         </li>
      
      </ul>
      <div class="tab-content" id="myTabContent">
         <!--- Details tab form start ---->
         <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
            <form class="edit-meicine-field mt-3 form-row" id="detailsForm" action="{{route('save-medicine-symptom')}}" method="post">
               @csrf
               
               <div class="floating multi col-6 col-md-4 mb-3">
                  <div class="form-group">
                     <label class="font-weight-bold" for="medicine">Medicine/Therapy</label>
                     <input type="text" autocomplete="off" class="form-control" id="medicine" name="medicine" value="" placeholder="Search" >
                     <label id="medicine-error" class="error" for="medicine" style="display: none;"></label>
                     <input type="hidden" id="therapy_id" name="therapy_id" value="">
                     <input type="hidden" id="drug_id" name="drug_id" value="">
                     <input type="hidden" id="product_id" name="product_id" value="">

                     
                  </div>
               </div>
            
               <div class="form-group col-6 col-md-4 mb-3">
                  <label class="font-weight-bold" for="dosage">Dosage</label>
                  <input type="text" class="form-control" placeholder="Enter your dosage" name="dosage" id="dosage" value="">
               </div>
               
               <div class="form-group col-6 col-md-4 gradient-dropdown mb-3">
                  <label class="d-inherit font-weight-bold" for="dosageType">Dosage Type</label>
                  <select id="dosageType" name="dosageType" class="form-control select2 ">
                     <option value="" disabled selected>Please select</option>
                     @if(!empty($dosageType))
                        @foreach($dosageType as $dosageTypeValue)
                           <option value="{{$dosageTypeValue['id']}}" id="{{$dosageTypeValue['id']}}" >
                              {{$dosageTypeValue['name']}}
                           </option>
                        @endforeach
                     @else
                        <option value="">Found no records for dosage type</option>
                     @endif
                  </select>
                  <label id="dosageType-error" class="error" for="dosageType" style="display: none;"></label>
               </div>

               
               <a class="add-medicine" href="javascript:void(0);" id="save-medicine"><img src="{{asset('images/add-medicine.svg')}}" alt="Add Medicine" width="35" heigth="35"></a>

               <!-- Tagged medicine Div start -->
                  @if(!empty($medicineData))
                  <div class="form-group col-md-12 tagged-condition mt-3 mb-0">
                     <ul id="medicineTags" class="medicine-tag">
                        @foreach($medicineData as $medicineDataValue)
                           <li> {{$medicineDataValue['name']}} {{$medicineDataValue['dosage']}} <a href="javascript:void(0);" onClick="deleteMedicine({{$medicineDataValue['eventMedicineId']}})">
                              <img src="{{asset('images/bluedelete.svg')}}" alt="bluedelete" width="15" heigth="10"></a>
                             </li>
                        @endforeach

                     </ul>
                  </div>
                  @endif
               <!-- Tagged medicine Div end -->

               <input type="hidden" name="eventId" value="{{$eventId}}">
               <input type="hidden" name="formType" value="1">
               <input type="hidden" id="timeWindowDay" name="timeWindowDay" value="{{$timeWindowDay}}">
               
            </form>
         </div>
         <!--- Details tab form end ---->

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


@endsection

@push('styles')
<!-- Added select 2 css for dosage & dosageType dropdown -->
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
<style type="text/css">
.error {
   color: #ff0505 !important;
   font-weight: normal !important;
}
.select2-results{
   overflow-x: hidden;
}
</style>

@endpush

@push('scripts')

<!-- Added select 2 js for dosage & dosageType dropdown -->
<script src="{{ asset('js/select2.min.js') }}" defer></script>

<script>
   $(document).ready(function()
   {

      // On click of plus button add the data
      $('#save-medicine').on('click', function (e) {

         e.preventDefault();
      
         var formValidated = $('#detailsForm').valid();

         // check if form is validated then send data
         if(formValidated === true){
            // Check if added medicine is not from the list then show error, else submit the form
            if($("#medicine").attr('value').length==0){
               $('#medicine-error').show();
               $('#medicine-error').html('Please select medicine from the search drop-down list.');
            }else{
               $("#detailsForm").submit();
            }
         }
      });


      // details tab form validation check
      $('#detailsForm').validate({    
         rules:{
            medicine : {
               required : true,
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
            medicine : {
               required : "Please select medicine.",
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


      //Select2 option for dosageType dropdown
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

      // hide error when dosageType option is selected
      $('#dosageType').on('change', function() {
         if ($('#dosageType').val() != ""){
            $('#dosageType-error').hide();
         }
      });

      // Auto complete ajax call for medicine dropdown - code start
      var medicine_list_url = "{{ route('search-medicine') }}";
      $('input#medicine').typeahead({
         items:'all',
         source: function (query, process) {
            return $.ajax({
               url: medicine_list_url,
               type: 'get',
               data: { query: query, timeWindowDay : $("#timeWindowDay").val() },
               dataType: 'json',
               success: function (result) {
                  var resultList = result.map(function (item) {
                        var aItem = { id: item.id, name: item.name };
                        return JSON.stringify(aItem);
                  });
                  // Check if search has no options then clear input
                  if($.isEmptyObject(resultList) == true){
                     $('input#medicine').attr('value', '');
                     $('#therapy_id').val('');
                     $('#drug_id').val('');
                     $('#product_id').val('');
                  }
                  return process(resultList);
               }
            });
         },

         matcher: function (obj) {
            var item = JSON.parse(obj);
            return ~item.name.toLowerCase().indexOf(this.query.toLowerCase())
         },

         sorter: function (items) {          
            var beginswith = [], caseSensitive = [], caseInsensitive = [], item;
            while (aItem = items.shift()) {
               var item = JSON.parse(aItem);
               if (!item.name.toLowerCase().indexOf(this.query.toLowerCase())) beginswith.push(JSON.stringify(item));
               else if (~item.name.indexOf(this.query)) caseSensitive.push(JSON.stringify(item));
               else caseInsensitive.push(JSON.stringify(item));
            }
            return beginswith.concat(caseSensitive, caseInsensitive)
         },

         highlighter: function (obj) {
            var item = JSON.parse(obj);
            var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
            return item.name.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
               return '<strong>' + match + '</strong>'
            })
         },

         updater: function (obj) {
            var item = JSON.parse(obj);
            $('input#medicine').attr('value', item.id);
            
            $('#therapy_id').val('');
            $('#drug_id').val('');
            $('#product_id').val('');
            
            let selectedMedicineId = item.id;
            if(selectedMedicineId.includes('therapy')){        
               let id = selectedMedicineId.replace('-therapy','');
               $('#therapy_id').val(id);
            }
            if(selectedMedicineId.includes('rxdrug')){
               let id = selectedMedicineId.replace('-rxdrug','');
               $('#drug_id').val(id);
            }
            if(selectedMedicineId.includes('product')){
               let id = selectedMedicineId.replace('-product','');
               $('#product_id').val(id);
            }

            return item.name;
         }
      });
      // Auto complete ajax call for medicine dropdown - code end

   });


   //----------------------------------  Delete Medicine Pop up --------------------------- //

   function deleteMedicine(id){
       // Set modal title
       $('#medicine-modal-title').html('Delete Medicine Confirmation');
      
      // Set body
      $('#medicine-modal-body').html('Are you sure you want to delete this medicine details?');

      // Show Modal
      $('#deleteMedicineConfirmation').modal('show');
      
      // Set hidden input type value 
      $('#medicineModalId').val(id);
   }

   //----------------------------------  Delete Medicine API  --------------------------- //

   // Call Ajax for Delete Condition
   $('.modalMedicineYes').on('click',function()
   {   
      
      var modalId = $('#medicineModalId').val();
      var csrf_token = $('meta[name="csrf-token"]').attr('content');    
      $.ajax({
         url: "{{route('delete-medicine-symptom')}}",
         type : 'delete',
         dataType: "json",
         "data":{ _token: csrf_token,"eventMedicineId":modalId},
         success: function(res){
            if(res.status == 0){
               window.location.reload();
            }
            else
            {
               window.location.reload();
            }
         }
      });    
      
   });
</script>
@endpush
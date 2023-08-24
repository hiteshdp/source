@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp


@section('content')

<div class="container750">
    <div class="cabinet-header mobile title-note">
        <div class="tourlogo">
            <a href="{{route('symptom-tracker',['timeWindowDay'=>$timeWindowDay])}}">
                <img  src="{{asset('images/close.svg')}}" alt="arrow"> 
            </a>    
        </div>
        <!-- Show title name when add medicine tab is not visible - start -->
        @if(empty($addMedicineSymptomRoute))
            <div class="cabinet-title mobile">
                <h1>Add Notes</h1>
            </div>
        @endif
        <!-- Show title name when add medicine tab is not visible - end -->
    </div>
    <div class="edit-cabinet-medicine">

        <!-- Show the add medicine tab only when symptom data are recorded earlier - code start -->
        @if(!empty($addMedicineSymptomRoute))
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                <a class="nav-link " id="details-tab" href="{{$addMedicineSymptomRoute}}">Add <br class="d-none"> Medicine/Therapy</a>
                </li>
                <li class="nav-item">
                <a class="nav-link active" id="notes-tab" data-toggle="tab" href="#notes" role="tab" aria-controls="notes" aria-selected="false">Add <br class="d-none"> Notes</a>
                </li>
            </ul>
        @endif
        <!-- Show the add medicine tab only when symptom data are recorded earlier - code end -->

    </div>
    <div class="edit-cabinet-medicine">
        <form class="edit-meicine-field mt-3" id="add-event-notes-form" action="{{route('save-event-notes')}}" method="post">
            @csrf
            <div class="form-group">
                <label for="notes"></label>
                <textarea class="form-control" name="notes" placeholder="E.g. I woke up with headache today." rows="3"></textarea>
                <a class="popup-info mt-2 d-inline-block" href="javascript:void(0)" onclick="showNotesExamplePopUp();">Example of useful notes <img src="{{asset('images/info.svg')}}" width="16" height="16" alt="Migraine Might" title="Migraine Might"> </a>
            </div>
            <!-- Notes example modal popup click point - code start -->
            
            <!-- Notes example modal popup click point - code end -->
            <input type="hidden" name="userId" value="{{\Auth::user()->id}}">
            <input type="hidden" name="eventDate" value="{{$date}}">
            <input type="hidden" name="timeWindowDay" value="{{$timeWindowDay}}">
            <div class="text-center"><button type="submit" class="btn-gradient w-50 mb-4 border-0">Save</button></div>
            @if(!empty($eventNotes))
                <h2 class="detail-title pb-2">Previous Notes</h2>
                <div class=" note-details scroll-popup notes-scroll">
                    @foreach ($eventNotes as $eventNotesDataValue)
                    <div class="notes-list">
                        <h3>{{$eventNotesDataValue['date']}} (UTC)
                            <div class="row">
                                <!-- Edit button - start -->
                                <a href="{{route('edit-event-notes',\Crypt::encrypt($eventNotesDataValue['id']))}}"> 
                                    <img src="{{asset('images/blue-edit.svg')}}" alt="blue-edit">
                                </a>
                                <!-- Edit button - end -->

                                <!-- Delete button - start -->
                                <a href="javascript:void(0);" class="ml-3" onClick="deleteNote({{$eventNotesDataValue['id']}})">
                                    <img src="{{asset('images/bluedelete.svg')}}" alt="bluedelete" style="width: 20px; height: 17px;">
                                </a>
                                <!-- Delete button - end -->
                            </div>
                        </h3>
                        <p> {!! nl2br($eventNotesDataValue['description']) !!} </p> 
                    </div>
                    @endforeach
                </div>
            @endif
        </form>
   </div>
</div>


<!------ Delete notes pop up window - code start ------->
<div class="modal fade" id="deleteNotesConfirmation">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="notes-modal-title">Delete Note Confirmation</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="notes-modal-body">Are you sure you want to delete this note?</div>
      <input type="hidden" name="notesId" value="" id="notesId">
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button"  id="modalConditionYes" class="btn btn-green modalConditionYes">Yes</button>
      </div>
    </div>
  </div>
</div>
<!------ Delete notes pop up window - code end ------->


<!---Modal pop up for notes example - code start --->
<div class="modal fade info-note-popup" id="notesPopUp">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow bg-white rounded">
      <div class="modal-header">
        <h4 class="modal-title w-100 text-center" id="notesPopUp-modal-title"><img src="{{asset('images/popup-logo.png')}}" width="300" height="63" alt="Migraine Might" title="Migraine Might"> </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><svg width="22" height="22" viewBox="0 0 23 23" fill="#fff" xmlns="http://www.w3.org/2000/svg"> <path d="M12.7439 11.1157L21.955 1.90455C22.1371 1.69199 22.2322 1.41857 22.2214 1.13893C22.2106 0.859291 22.0947 0.594026 21.8968 0.396143C21.6989 0.19826 21.4336 0.0823348 21.154 0.0715335C20.8744 0.0607322 20.6009 0.155851 20.3884 0.33788L11.1773 9.54899L1.96616 0.326769C1.75693 0.117542 1.47316 0 1.17727 0C0.88138 0 0.597608 0.117542 0.388382 0.326769C0.179155 0.535995 0.0616131 0.819767 0.0616131 1.11566C0.0616131 1.41155 0.179155 1.69532 0.388382 1.90455L9.6106 11.1157L0.388382 20.3268C0.272069 20.4264 0.177602 20.549 0.110909 20.6868C0.0442165 20.8247 0.00673854 20.9748 0.00082799 21.1278C-0.00508256 21.2808 0.0207013 21.4334 0.0765619 21.576C0.132423 21.7186 0.217154 21.8481 0.325437 21.9564C0.43372 22.0647 0.563217 22.1494 0.7058 22.2053C0.848384 22.2611 1.00098 22.2869 1.154 22.281C1.30702 22.2751 1.45717 22.2376 1.59501 22.1709C1.73286 22.1042 1.85544 22.0098 1.95505 21.8934L11.1773 12.6823L20.3884 21.8934C20.6009 22.0755 20.8744 22.1706 21.154 22.1598C21.4336 22.149 21.6989 22.0331 21.8968 21.8352C22.0947 21.6373 22.2106 21.372 22.2214 21.0924C22.2322 20.8127 22.1371 20.5393 21.955 20.3268L12.7439 11.1157Z" fill="#fff"/></svg></span>
        </button>
      </div>
      <div class="modal-body" id="notesPopUp-modal-body">
        <div class="bg-blue mb-3">
            Add “Flags” on your migraine journey including
            new symptoms, treatments, dosages triggers &
            your thoughts at the time. These will be
            summarized with your migraine report for you
            and healthcare team.
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
             <p>Started new preventative medication A</p> 
          </div>
          <div class="col-md-6 mb-3">
             <p>Reduced dose of B to 20 mg due to less severe headache</p>  
          </div>
          <div class="col-md-6 mb-3">
             <p>Holding off on device C due to skin irritation</p> 
          </div>
          <div class="col-md-6 mb-3">
             <p>Started new herbal supplement D for acute treatment</p>  
          </div>
          <div class="col-md-6 mb-3">
             <p>Able to reduce 400 mg ibuprofen to 1/week</p> 
          </div>
          <div class="col-md-6 mb-3">
             <p>Had strange aura after dinner<br class="d-mobile-none"> out ? MSG</p>  
          </div>
          <div class="col-md-6 mb-3">
             <p>Started acupuncture</p> 
          </div>
          <div class="col-md-6 mb-3">
             <p>Able to exercise without headaches!</p>  
          </div>
          <div class="col-md-12">
             <p>Finished biofeedback - really helped me be aware of my shallow breathing which gets worse right before a headache</p> 
          </div>
      </div>
          
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for notes example - code end--->
@endsection

@push('styles')
<!-- Added select 2 css for dosage & dosageType dropdown -->
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
<style type="text/css">
.error {
   color: #ff0505 !important;
   font-weight: normal !important;
}
#notes-error { width: 100%;  margin: 0; }
</style>
@endpush

@push('scripts')
<script type="text/javascript">
    // validate form - code start
   $('#add-event-notes-form').validate({
      rules:{
        "notes" : {
            required : true,
            normalizer: function(value) {
                // Trim the value of the input
                return $.trim(value);
            },
        }
      },
      messages:{
        "notes" : {
            required : "Please enter notes.",
        },
      }
   });

    //----------------------------------  Delete Notes Pop up --------------------------- //
    function deleteNote(id){
        // Show Modal
        $('#deleteNotesConfirmation').modal('show');
        // Set hidden input type value 
        $('#notesId').val(id);
    }

    //----------------------------------  Delete Note API  --------------------------- //
   // Call Ajax for Delete note
   $('.modalConditionYes').on('click',function()
   {   
      var notesId = $('#notesId').val();
      var csrf_token = $('meta[name="csrf-token"]').attr('content');
      $.ajax({
         url: "{{route('delete-event-note')}}",
         type : 'delete',
         dataType: "json",
         "data":{ _token: csrf_token,"notesId":notesId},
         success: function(res){
            if(res.status == 0){
               $('#deleteNotesConfirmation').modal('hide');
               window.location.reload();
            }
            else
            {
               window.location.reload();                     
            }
         }
      });    
   });

   // Display notes pop up
   function showNotesExamplePopUp(){
       $('#notesPopUp').modal('show');
   }
</script>
@endpush
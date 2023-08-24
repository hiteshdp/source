@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp


@section('content')

<div class="container750">
    <div class="cabinet-header mobile title-note">
        <div class="tourlogo">
            <a href="{{route('add-event-notes',[\Crypt::encrypt(date('Y-m-d',strtotime($eventNotesData->eventDate))), 'timeWindowDay' => $eventNotesData->timeWindowDay])}}">
                <img src="{{asset('images/close.svg')}}" alt="arrow"> 
            </a>    
        </div>    
        <div class="cabinet-title mobile">
            <h1>Edit Notes</h1>
        </div>  
    </div>
    <div class="edit-cabinet-medicine">
        <form class="edit-meicine-field mt-3" id="update-event-notes-form" action="{{route('update-event-notes')}}" method="post">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="notes"></label>
                <textarea class="form-control" name="notes" placeholder="E.g. I woke up with headache today." rows="3">{{$eventNotesData->notes}}</textarea>
            </div>
            <input type="hidden" name="id" value="{{$eventNotesData->id}}">
            <input type="hidden" name="timeWindowDay" value="{{$eventNotesData->timeWindowDay}}">
            <div class="text-center"><button type="submit" class="btn-gradient w-50 mb-4 border-0">Update</button></div>
        </form>
   </div>
</div>

@endsection


@push('scripts')
<script type="text/javascript">
    // validate form - code start
   $('#update-event-notes-form').validate({
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
</script>
@endpush
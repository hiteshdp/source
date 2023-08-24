@extends('layout.default')

@section('title', __('Wellkasa - Complete profile'))
@section('meta-keywords', __('Wellkasa - Complete profile'))
@section('meta-news-keywords', __('Wellkasa - Complete profile'))
@section('meta-description', __('Wellkasa - Complete profile'))
<div class="alert alert-success mb-0" style="display: none;">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
    <span>Please complete the profile to access all features of WellKabinet&#8482;</span>
</div>
@section('content')
<div class="container">
    <div class="container750 user-profile-wrapper mt-0 mt-md-5" id="myTabContent">
      <div class="edit-profile position-relative">
         <div class="container300">
            <div class="cabinet-title mb-4">
               <h1 class="h3 text-center mt-2 mb-1">Complete your profile</h1>
               <span>{{$userName}}</span>
            </div>
                <!--- Complete form start --->
                {!! Form::open(['url' => 'update-complete-profile', 'class'=>'login-signup-form', 'id'=>'addprofileForm', 'files' => true, 'method'=>'post']) !!}

                    <div class="form-group mb-4">
                        <label class="label-title w-100">Sex at birth</label>
                        <input type='radio' id='male' value='4' {{ old('gender') == '4' ? 'checked' : '' }} name='gender'>
                        <label for='male'>Male</label>
                        <input type='radio' id='female' value='5' {{ old('gender') == '5' ? 'checked' : '' }}  name='gender'>
                        <label for='female'>Female</label>
                        <input type='radio' id='undisclosed' value='6' {{ old('gender') == '6' ? 'checked' : '' }}  name='gender'>
                        <label for='undisclosed'>Undisclosed</label>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="label-title">Date of birth</label>
                        <input class="form-control pl-3" type="text" id="dateOfBirth" name="dateOfBirth" value="{{old('dateOfBirth')}}" autocomplete="off">
                    </div>

                    <div class="form-group mb-4">
                        <label class="label-title w-100">Profile picture (optional)</label>
                        <div class="addprofile-img position-relative">
                            <img id="preview" class="img-thumbnail rounded-circle p-0" src="" onerror="this.onerror=null;this.src='{{ asset("images/upload.svg") }}';" alt="user" title="user" style="width: 70px; height: 70px;">
                            <input type="file" class="myprofile-phpto" id="avatar" name="avatar" accept="image/*">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="submit" id="continue" class="btn btn-gradient w-100 font-weight-bold">
                                {{ __('Continue to WellKabinet') }}
                            </button>                
                        </div>
                    </div>
                    <input type="hidden" name="isPassMiddleware" value="1">
                {!! Form::close() !!}
                <!--- Complete form end --->
         </div>
      </div>
   </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('css/bootstrap-datetimepicker.min.css')}}"/>
@endpush

@push('scripts')
<script src="{{asset('js/moment.min.js')}}"></script>
<script src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script>

<script>
var start_date = new Date ("{{config('constants.startDateRange')}}");
var end_date = new Date ("{{config('constants.endDateRange')}}");

$(function(){
    $('#dateOfBirth').datetimepicker({
        format: 'MM/DD/YYYY',
        weekStart: 1,
        minDate: start_date,
        maxDate: end_date,
        defaultDate: end_date,
        minView: 2,
        maxView: 5,
        changeMonth: true,
        changeYear: true,
        pickTime: false,
        autoclose: true,
    })
});

</script>
@endpush


@push('scripts')
<!-- Code to show selected Files -->
<script>

    // Date of birth date range validation
    $.validator.addMethod("dateRange", function(value, element, params) {
        try {
            var date = new Date(value);
            if (date >= params.from && date <= params.to) {
            return true;
            }
        } catch (e) {}
        return false;
    }, 'Please select date of birth between {{config('constants.startDateRange')}} - {{config('constants.endDateRange')}}. Please try again.');

    $(document).ready(function()
    {
        clearTimeout(alertMsg); // clear 5 seconds timeout for alert message
        $('.alert').addClass('text-center font-weight-bold'); // align alert message in center & text in bold 
        $('#addprofileForm').validate({
            rules:{
                firstName : {
                    required : true,
                    maxlength : 50,
                },
                lastName : {
                    required : true,
                    maxlength : 50,
                },
                gender : {
                    required : true
                },
                dateOfBirth : {
                    required : true,
                    date : 'mm-dd-yyyy',
                    dateRange: {
                        from: start_date.setDate(start_date.getDate() - 1),
                        to: end_date
                    }
                },
            },
            messages:{
                firstName : {
                    required : "Please enter first name.",
                    maxlength : "First name should not be more than 50 characters"
                },
                lastName : {
                    required : "Please enter last name.",
                    maxlength : "Last name should not be more than 50 characters"
                },
                gender : {
                    required : "Please select a sex.",
                },
                dateOfBirth : {
                    required : "Please select a date of birth.",
                    date : "Invalid format. ( date format should be like : MM/DD/YYYY )",
                },
            }
        });
    });


    $(document).on("click", ".browse", function() {
        var file = $(this).parents().find(".file");
        file.trigger("click");
    });

    $('input[type="file"]').change(function(e) {
        var fileInput = 
            document.getElementById('avatar');
            
        var filePath = fileInput.value;
        
        // Allowing file type
        var allowedExtensions = 
                /(\.jpg|\.jpeg|\.png)$/i;
            
        if (!allowedExtensions.exec(filePath)) {
            alert('Only image type jpg/png/jpeg is allowed');
            fileInput.value = '';
            return false;
        }else{
            var fileName = e.target.files[0].name;
            $("#file").val(fileName);

            var reader = new FileReader();
            reader.onload = function(e) {
                // get loaded data and render thumbnail.
                document.getElementById("preview").src = e.target.result;
            };
            // read the image file as a data URL.
            reader.readAsDataURL(this.files[0]);
        }
        
    });
</script>
@endpush

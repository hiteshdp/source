@extends('layout.default')

@section('title', __('Wellkasa - Update Profile'))
@section('meta-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-news-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-description', __('Wellkasa - Understanding the use prudent use of antimicrobials and their Health Canada categories'))

@section('content')
<div class="container">
    <div class="container750 user-profile-wrapper mt-0 mt-md-5" id="myTabContent">
      <div class="edit-profile position-relative">
         <a href="{{route('my-profile')}}"><img class="back-icon" src="{{asset('images/arrow-back-fill.svg')}}" alt="back"></a>
         <div class="container300">
               <h1 class="h3 text-center mt-4">Edit Profile</h1>
               <p class="text-left text-center">If you change your email, you will receive a numeric code on the new email for verifications</p>
               <!--- Register form start --->
               {!! Form::open(['url' => 'update-profile', 'class'=>'login-signup-form', 'id'=>'editprofileForm', 'method'=>'post']) !!}
                    <div class="form-group mt-2 position-relative">
                        <input id="firstName" placeholder=" "  type="text" class="form-control" name="firstName" value="{{ old('firstName') ? old('firstName') : $userDetails['firstName'] }}" autofocus required>
                        <img class="input-icons" src="{{asset('images/icon_user.svg')}}" alt="firstName"> 
                        <label for="firstName" class="float-label">{{ __('First Name') }}</label>
                    </div>

                    <div class="form-group mt-2 position-relative">
                        <input id="lastName" placeholder=" "  type="text" class="form-control" name="lastName" value="{{ old('lastName') ? old('lastName') : $userDetails['lastName'] }}" required>
                        <img class="input-icons" src="{{asset('images/icon_user.svg')}}" alt="lastName"> 
                        <label for="lastName" class="float-label">{{ __('Last Name') }}</label>
                    </div>

                    <div class="form-group mt-2 position-relative">
                        <input id="email" placeholder=" "  type="email" class="form-control" name="email" value="{{ old('email') ? old('email') : $userDetails['email'] }}" required>
                        <img class="input-icons" src="{{asset('images/icon_mail.svg')}}" alt="email"> 
                        <label for="email" class="float-label">{{ __('Email') }}</label>
                        
                        @error('email')
                            <span class="invalid-feedback signupEmailError" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <label class="label-title">Sex at birth</label>
                    <div class="form-group">
                        @if(!empty($genderOptions))
                            @foreach($genderOptions as $value)
                                <input type='radio' id="{{$value['name']}}" value="{{$value['id']}}" {{ old('gender') ? (old('gender') == $value['id'] ? 'checked' : '') : ($userDetails['gender'] == $value['id'] ? 'checked' : '') }} name='gender'>
                                <label for="{{$value['name']}}">{{$value['name']}}</label>
                            @endforeach
                        @endif
                    </div>
                    <label class="label-title">Date of birth</label>
                    <div class="form-group">
                        <input class="form-control pl-3" type="text" id="dateOfBirth" name="dateOfBirth" value="{{$userDetails['dateOfBirth']}}" autocomplete="off">
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="submit" id="continue" class="btn btn-gradient w-100 font-weight-bold">
                                {{ __('Save & continue') }}
                            </button>                
                        </div>
                    </div>
                {!! Form::close() !!}
                <!--- Register form end --->
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

<!-- Code to show selected Files -->
<script>
    $(document).ready(function()
    {

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

        // Validates Email Pattern
        jQuery.validator.addMethod("emailPattern", function(value, element) {
            if(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/.test(value)){
                return true;
            }else{
                return false;
            };
            
        },'Please enter a valid email address');

        $('#editprofileForm').validate({
            rules:{
                firstName : {
                    required : true,
                    maxlength : 50,
                },
                lastName : {
                    required : true,
                    maxlength : 50,
                },
                email : {
                    required : true,
                    maxlength : 50,
                    email : true,
                    emailPattern : true
                },
                gender : {
                    required : true
                },
                dateOfBirth : {
                    required : true,
                    date: true,
                    dateRange: {
                        from: start_date.setDate(start_date.getDate() - 1),
                        to: end_date
                    }
                }
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
                email : {
                    required : "Please enter email.",
                    email : "Please enter a valid email address.",
                    maxlength : "Email address should not be more than 50 characters"
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

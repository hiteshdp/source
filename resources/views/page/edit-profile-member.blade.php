@extends('layout.default')

@section('title', __('Wellkasa - Update Profile'))
@section('meta-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-news-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-description', __('Wellkasa - Understanding the use prudent use of antimicrobials and their Health Canada categories'))

@section('content')
<div class="container">
    <div class="container750 user-profile-wrapper mt-0 mt-md-5" id="myTabContent">
      <div class="edit-profile position-relative">
         <a href="{{route('my-profile')}}"><img class="back-icon profile-close" src="{{asset('images/closex.svg')}}" alt="back"></a>
         <div class="container300">
               <h1 class="h3 text-center mt-2 mb-3">Edit profile</h1>
               
               <!--- Edit profile form start --->
                {!! Form::open(['url' => 'update-profile-member', 'class'=>'login-signup-form', 'id'=>'addprofileForm', 'files' => true, 'method'=>'put']) !!}
                    <label class="label-title w-100">Name</label>
                    <div class="form-group mt-2 position-relative">
                        <input id="firstName" placeholder=" "  type="text" class="form-control" name="firstName" value="{{ old('firstName') ? old('firstName') : $profileMemberData['first_name'] }}" required autocomplete="off" autofocus>
                        <img class="input-icons" src="{{asset('images/icon_user.svg')}}" alt="firstName"> 
                        <label for="firstName" class="float-label">{{ __('First Name') }}</label>
                    </div>

                    <div class="form-group mt-2 mb-4 position-relative">
                        <input id="lastName" placeholder=" "  type="text" class="form-control" name="lastName" value="{{ old('lastName') ? old('lastName') : $profileMemberData['last_name'] }}" required autocomplete="off" autofocus>
                        <img class="input-icons" src="{{asset('images/icon_user.svg')}}" alt="lastName"> 
                        <label for="lastName" class="float-label">{{ __('Last Name') }}</label>
                    </div>

                    <div class="form-group mb-4">
                        <label class="label-title w-100">Sex at birth</label>
                        @if(!empty($genderOptions))
                            @foreach($genderOptions as $value)
                                <input type='radio' id="{{$value['name']}}" value="{{$value['id']}}" {{ old('gender') ? (old('gender') == $value['id'] ? 'checked' : '') : ($profileMemberData['gender'] == $value['id'] ? 'checked' : '')}} name='gender'>
                                <label for="{{$value['name']}}">{{$value['name']}}</label>
                            @endforeach
                        @endif
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="label-title">Date of birth</label>
                        <input class="form-control pl-3" type="text" id="dateOfBirth" name="dateOfBirth" value="{{$profileMemberData['date_of_birth']}}" autocomplete="off">
                    </div>

                    <div class="form-group mb-4">
                        <label class="label-title w-100">Profile picture (optional)</label>
                        <div class="addprofile-img position-relative">
                            <img id="preview" class="img-thumbnail rounded-circle p-0" src="{{$profileMemberData['profile_picture']}}" onerror="this.onerror=null;this.src='{{ asset("images/upload.svg") }}';" alt="user" title="user" style="width: 70px; height: 70px;">
                            <input type="file" class="myprofile-phpto" id="avatar" name="avatar" accept="image/*">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <input type="hidden" id="profileMemberId" name="profileMemberId" value="{{$profileMemberData['id']}}">
                            <button type="submit" id="continue" class="btn btn-gradient w-100 font-weight-bold">
                                {{ __('Update') }}
                            </button>                
                        </div>
                    </div>
                {!! Form::close() !!}
                <!--- Edit profile form end --->
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
        maxDate: new Date(),
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
    }, 'Please select date of birth between {{config('constants.startDateRange')}} till today. Please try again.');

    $(document).ready(function()
    {
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
                        to: new Date()
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

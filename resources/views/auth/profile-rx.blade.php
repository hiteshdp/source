@extends('layout.default')

@section('title', __('Wellkasa - Update Profile'))
@section('meta-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-news-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-description', __('Wellkasa - Understanding the use prudent use of antimicrobials and their Health Canada categories'))

@section('content')
<div class="container container750 rx-profile">
    <div class="edit-profile floating">
    
        {!! Form::open(['url' => 'update-profile-rx', 'id'=>'editrxprofileform','files' => true,'method'=>'post']) !!}
        <div class="text-center  mt-3"><img  src="{{asset('images/healthcare.svg')}}" alt="healthcare"></div>
        <h1 class="h3 text-center pt-3">{{ __('My Rx Profile') }}</h1>
        <p class="text-center">Help us get to know you better</p>

        @if(Session::has('profileSuccess'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('profileSuccess') }}
            </div>
        @endif
        <div class="form-group">
            <div class="form-group">
                <label for="avatar" class="d-none">Avatar</label>
                <div class="form-group upload-img">
                    <img src="{{asset('uploads/avatar/'.$userDetails['avatar'])}}" onerror="this.onerror=null;this.src='{{ asset("images/user.jpg") }}';" id="preview" class="img-thumbnail" alt="user" title="user">
                    <input type="file" class="file" id="avatar" name="avatar" accept="image/*">
                </div>
            </div>
        </div>

        <div class="edit-profile-form mt-4 pt-4">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="firstName_lastName" value="{{$userDetails['firstName'].' '.$userDetails['lastName']}}" placeholder=" " name="firstName_lastName" required>
                        <label for="firstName_lastName" class="float-label" >First name & Last name<span class='error'>*</span></label>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">                        
                        <input type="text" class="form-control read-only" readonly  id="email" value="{{$userDetails['email']}}" placeholder=" " name="email">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <select placeholder=" " name="state" id="state" class="form-control" >
                            <option value="" disabled selected>Select State</option>
                            @foreach($states as $statesValues)
                                <option value="{{$statesValues['id']}}" {{ $userDetails['state'] == $statesValues['id'] ? 'selected' : ''}}>{{$statesValues['name']}}</option>
                            @endforeach
                        </select>
                        <label for="state" class="float-label" >State<span class='error'>*</span></label>
                    </div>
                </div>
                <div class="col-12 col-md-6">   
                    <div class="form-group">
                        <select placeholder=" " class="form-control select2" name="city" id="city"></select>
                        <label id="city-error" class="error" style="display:none;" for="city"></label>
                        <label for="city" class="float-label">City<span class='error'>*</span></label>
                    </div>
                </div>
                <div class="col-12 col-md-6">  
                    <div class="form-group">
                        <select placeholder=" " name="gender" id="gender" class="form-control">
                            <option value="">Select sex at birth</option>
                            @foreach($genderOptions as $genderOptionsValues)
                                <option value="{{$genderOptionsValues['id']}}" {{$userDetails['gender'] == $genderOptionsValues['id'] ? 'selected' : ''}}>{{$genderOptionsValues['name']}}</option>
                            @endforeach
                        </select>
                        <label class="float-label" for="gender">Sex at Birth<span class='error'>*</span></label>
                    </div>
                </div>
                <div class="col-12 col-md-6">  
                    <div class="form-group">
                        <input type="text" class="form-control" id="ageRange" value="{{$userDetails['ageRange']}}" placeholder=" " name="ageRange" required>
                        <label for="ageRange" class="float-label" >Age (Years)<span class='error'>*</span></label>
                    </div>
                </div> 
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <select placeholder=" " name="practitionerType" id="practitionerType" class="form-control" >
                            <option value="" selected disabled>Select Practitioner Type</option>
                            @foreach($practitionerTypeOptions as $practitionerTypeOptionsValues)
                                <option value="{{$practitionerTypeOptionsValues['id']}}" {{$userDetails['practitionerType'] == $practitionerTypeOptionsValues['id'] ? 'selected' : ''}}>{{$practitionerTypeOptionsValues['name']}}</option>
                            @endforeach
                        </select>
                        <label for="practitionerType" class="float-label" >Practitioner Type<span class='error'>*</span></label>
                    </div>
                </div>
                <div class="col-12 col-md-6">  
                    <div class="form-group">
                        <select placeholder=" " name="speciality" id="speciality" class="form-control">
                        <option value="" selected disabled>Select Speciality</option>
                        @foreach($specialityOptions as $specialityOptionsValues)
                            <option value="{{$specialityOptionsValues['id']}}" {{$userDetails['speciality'] == $specialityOptionsValues['id'] ? 'selected' : ''}}>{{$specialityOptionsValues['name']}}</option>
                        @endforeach
                        </select>
                        <label for="speciality" class="float-label" >Speciality<span class='error'>*</span></label>
                    </div>
                </div> 
                <div class="col-12 col-md-6">  
                    <div class="form-group">
                        <select placeholder=" " name="myAffiliationIs" id="myAffiliationIs" class="form-control">
                        <option value="" selected disabled>Select My affiliation is</option>
                        @foreach($myAffiliationIsOptions as $myAffiliationIsOptionsValues)
                            <option value="{{$myAffiliationIsOptionsValues['id']}}" {{$userDetails['myAffiliationIs'] == $myAffiliationIsOptionsValues['id'] ? 'selected' : ''}}>{{$myAffiliationIsOptionsValues['name']}}</option>
                        @endforeach
                        </select>
                        <label for="myAffiliationIs" class="float-label" >My affiliation is<span class='error'>*</span></label>
                    </div>
                </div> 
                <div class="col-12 col-md-6">  
                    <div class="form-group">
                        <select placeholder=" " name="integrativeCareExperience" id="integrativeCareExperience" class="form-control">
                        <option value="" selected disabled>Select Integrative care experience</option>
                        @foreach($integrativeCareExperienceOptions as $integrativeCareExperienceOptionsValues)
                            <option value="{{$integrativeCareExperienceOptionsValues['id']}}" {{$userDetails['integrativeCareExperience'] == $integrativeCareExperienceOptionsValues['id'] ? 'selected' : ''}}>{{$integrativeCareExperienceOptionsValues['name']}}</option>
                        @endforeach   
                        </select>
                        <label for="integrativeCareExperience" class="float-label" >Integrative care experience<span class='error'>*</span></label>
                    </div>
                </div>
                
                <!-- <div class="col-12 col-md-6">
                    <span class="text-secondary"><span class='error'>*</span>All fields are required</span>
                </div> -->
            </div>
        </div>

        <input type="hidden" name="cityValue" id="cityValue" value="{{$userDetails['city']}}"></select>
        
        <div class="row">
            @if ($agent->isMobile())
                <div class="col-12 col-md-6">
                <div class="form-btn mt-4 mb-4">
                        <button type="submit"  class="btn btn-green w-100">Update Profile</button>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-btn mt-4 mb-4">
                        <a class="btn btn-green w-100" href="{{route('my-profile')}}">Cancel</a>
                    </div>
                </div>
            @else
                <div class="col-12 col-md-6 d-none">
                    <div class="form-btn mt-4 mb-4">
                        <a class="btn btn-green w-100" href="{{route('my-profile')}}">Cancel</a>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-btn mt-4 mb-4">
                        <button type="submit"  class="btn btn-green w-100">Save</button>
                    </div>                                                      
                </div>
            @endif
        </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection


@push('styles')
<!-- Added select 2 for city dropdown -->
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')

<!-- Added select 2 for city dropdown -->
<script src="{{ asset('js/select2.min.js') }}" defer></script>

<!-- Code to show selected Files -->
<script>
    $(document).ready(function()
    {
        //Select2  option for city dropdown
        $('.select2').select2({
            closeOnSelect: true
        }).on('select2:select', function (e) {
            // if value selected in city dropdown then hide error message
            $("#city-error").hide();
        });


        // Validates Email Pattern
        jQuery.validator.addMethod("emailPattern", function(value, element) {
            if(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/.test(value)){
                return true;
            }else{
                return false;
            };
            
        },'Please enter a valid email address');

        $('#editrxprofileform').validate({
            rules:{
                firstName_lastName : {
                    required : true,
                    maxlength : 50,
                },
                // email : {
                //     required : true,
                //     maxlength : 50,
                //     email : true,
                //     emailPattern : true
                // },
                state : {
                    required : true
                },
                city : {
                    required : true
                },
                gender : {
                    required : true
                },
                ageRange : {
                    required : true,
                    number: true,
                    max : 119,
                    min : 22
                },
                practitionerType : {
                    required : true
                },
                speciality : {
                    required : true
                },
                myAffiliationIs : {
                    required : true
                },
                integrativeCareExperience : {
                    required : true
                },
            },
            messages:{
                firstName_lastName : {
                    required : "Please enter first name & last name.",
                    maxlength : "First name & last name should not be more than 50 characters"
                },
                // email : {
                //     required : "Please enter email.",
                //     email : "Please enter a valid email address.",
                //     maxlength : "Email address should not be more than 50 characters"
                // },
                state : {
                    required : "Please select state.",
                },
                city : {
                    required : "Please select city.",
                },
                gender : {
                    required : "Please select a sex.",
                },
                ageRange : {
                    required : "Please enter age.",
                    number: "Please enter numbers only",
                    max : "Age should be less than 120",
                    min : "Age should be greater than 21"
                },
                practitionerType : {
                    required : "Please select practitioner type.",
                },
                speciality : {
                    required : "Please select speciality.",
                },
                myAffiliationIs : {
                    required : "Please select my affiliation is.",
                },
                integrativeCareExperience : {
                    required : "Please select integrative care experience.",
                },
            }
        });

        // Display default selected city
        if($("#state").val()!=''){
            var state_id = $("#state").val();
            var stored_city_id = $("#cityValue").val();
            if(stored_city_id != ''){
                $("#city").html('');
                $.ajax({
                    url: "{{url('get-cities-by-state')}}",
                    type: "POST",
                    data: {
                        state_id: state_id,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#city').html('<option value="">Select City</option>');
                        $.each(result.cities, function(key, value) {
                            if(stored_city_id == value.id){
                                isSelected = "selected";
                            }else{
                                isSelected = "";
                            }
                            $("#city").append('<option value="' + value.id + '" '+isSelected+' <?php old("city") ? old("city") : "" ?> >' + value.name + '</option>');
                        });
                    }
                });
            }
            
        }

        // Display cities from state dropdown
        $('#state').on('change', function() {
            var state_id = this.value;
            $("#city").html('');
            $.ajax({
                url: "{{url('get-cities-by-state')}}",
                type: "POST",
                data: {
                    state_id: state_id,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#city').html('<option value="">Select City</option>');
                    $.each(result.cities, function(key, value) {
                        $("#city").append('<option value="' + value.id + '" <?php old("city") ? old("city") : "" ?>>' + value.name + '</option>');
                    });
                }
            });
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

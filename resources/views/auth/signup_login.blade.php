@extends('layout.default')

@section('title', __('Wellkasa - Login'))
@section('meta-keywords', __('Wellkasa, Integrative care, evidence informed, natural medicines'))
@section('meta-news-keywords', __('Wellkasa, Integrative care, evidence informed, natural medicines'))
@section('meta-description', __('Wellkasa Login - start your integrative care Journey'))

@section('content')
<div id="mainTab" class="container small-container mid-container">
    <h1 class="logo-login">
        <img  src="{{asset('images/mobile-new-logo.png')}}" alt="Wellkasa_Logo" class="m-3 mb-4"> 
    </h1>

    <!-- tab start -->
    <div class="signup-login-tab">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="signup-tab" data-toggle="tab" href="#signup" role="tab" aria-controls="signup" aria-selected="true">Sign Up</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="false">Login</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
           

            <!--- Sign up tab start ---->
            <div class="tab-pane fade" id="signup" role="tabpanel" aria-labelledby="signup-tab">
                <form method="POST" class="floating" id="registerForm" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group">
                        <input id="firstName" type="text" placeholder=" " class="form-control @error('firstName') is-invalid @enderror" name="firstName" value="{{ old('firstName') }}" autocomplete="firstName" required autofocus>
                        <label for="firstName" class="float-label">{{ __('First Name') }}</label>
                        @error('firstName')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group ">
                        <input id="lastName" type="text" placeholder=" " class="form-control @error('lastName') is-invalid @enderror" name="lastName" required value="{{ old('lastName') }}"  autocomplete="lastName" autofocus>
                        <label for="lastName" class="float-label">{{ __('Last Name') }}</label>
                        @error('lastName')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group ">
                        <input id="email" placeholder=" " type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                        <label for="email" class="float-label">{{ __('E-Mail Address') }}</label>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group ">
                        <input id="password" type="password" placeholder=" " class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" autocomplete="password" required>
                        <label for="password" class="float-label">{{ __('Password') }}</label>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input id="password_confirmation" placeholder=" " type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" value="{{ old('password_confirmation') }}"   autocomplete="password_confirmation" required>
                        <label for="password_confirmation" class="float-label">{{ __('Confirm Password') }}</label>
                        @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <div class="form-check form-group mb-1">
                        <input type="checkbox" class="form-check-input @error('terms_of_use') is-invalid @enderror" name="terms_of_use" id="terms_of_use" {{ old('terms_of_use') == 'on' ? 'checked' : '' }} required>                                
                        <label class="form-check-label" for="terms_of_use">I agree to the following:</label> <a class="btn-link" href="https://wellkasa.app/terms-conditions" target="_blank"> Terms of use*</a>
                        <div class="form-group mb-1">
                            <label id="terms_of_use-error" class="error" for="terms_of_use"></label>
                        </div>
                        @error('terms_of_use')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-gradient w-100 font-weight-bold">
                                {{ __('Register') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!--- Sign up tab end ---->

            <!--- Login up tab start ---->
            <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
            <form class="mt-5 floating login-signup-form" method="POST" id="login-form" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                    
                        <input id="email" placeholder=" "  type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        <!-- <span class="highlight"></span> -->
                        <img class="input-icons" src="{{asset('images/icon_mail.svg')}}" alt="email"> 
                        <label for="email" class="float-label">{{ __('Email') }}</label>
                        
                        @error('email')
                            <span class="invalid-feedback loginEmailError" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        
                    </div>

                    <div class="form-group">
                        <div class="input-group" id="show_hide_password">
                            <input id="password" placeholder=" " type="password" class="form-control" name="password" required autocomplete="current-password">
                            <!-- <span class="highlight"></span> -->
                            <img class="input-icons" src="{{asset('images/icon_lock.svg')}}" alt="email">
                            <label for="password" class="float-label">{{ __('Password') }}</label>
                            <div class="input-group-addon">
                                <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                            </div>
                            @error('password')
                                <span class="invalid-feedback loginPasswordError" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>                      

                    

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                <label class="form-check-label" for="exampleCheck1">Remember me</label>
                            </div>
                        </div>
                        <div class="col-6 login-help">
                            <a href="{{ route('password.request') }}">Forgot password?</a>
                        </div>   
                    </div>   
                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="submit" id="loginSubmit" class="btn btn-gradient w-100 font-weight-bold">
                                {{ __('Sign In') }}
                            </button>                
                        </div>
                    </div>

                    <div class="sideline mb-3 d-none">Or Continue with</div>
                                
                </form>
                <!--- Hide Social Media Sign Up - Start --->
                @if(1==2)
                <div class="form-group row mb-1">
                    <div class="col-md-6 pr-1">
                        <a class="btn login-btn google-btn pl-2" href="{{ route('social.oauth', 'google') }}"><img src="{{asset('images/icon_google.svg')}}" alt="google logo"> <span>Google</span> </a>
                    </div>
                    <div class="col-md-6 pl-1">
                        <a class="btn login-btn fb-btn pl-2" href="{{ route('social.oauth', 'facebook') }}"><img src="{{asset('images/icon_facebook.svg')}}" alt="facebook logo"> <span>Facebook</span> </a>
                    </div>
                </div>  
                @endif
                <!--- Hide Social Media Sign Up - End --->
            </div>
            <!--- Login up tab end ---->
        </div>
    </div>
    <!-- tab end -->

    <div class="privacy-link">
        By Signing up, I agree to Wellkasa’s <a href="https://wellkasa.com/policies/privacy-policy"><b>Privacy Policy</b></a>
    </div>

</div>

<!--- Annual Subscription plan information for paitent/caregiver Screen div start --->
<div id="infoForPaitentCaregiver" class="container550 confirm-details" style="display:none;">
    <h1 class="logo-login">
        <img  src="{{asset('images/Wellkasa_Logo.svg')}}" alt="Wellkasa_Logo"> 
    </h1>
    <!--center tab start -->
    <div class="confirm-text">
        
        <!-- back arrow button start -->
        <a href="javascript:void(0);" class="backToSignUpScreen"><img class="conf-back"  src="{{asset('images/eva-arrowback.svg')}}" alt="eva-arrowback"></a> 
        <!-- back arrow button end -->

        <h2>
            Patient/Caregiver login needs subscription. <br>
            If you already have one, please <a href="{{url('login')}}">LOGIN</a>
        </h2>

        <p class="mt-3 p-0">
           <span> 
               You can buy annual subscription by clicking <a href="https://wellkasa.com/products/wellkabinet">Buy Subscription</a>.
            </span>
        </p>
        
        <p class="mt-3 mb-0 p-0">
            By clicking on above link, you will be redirected to wellkasa shopify website.
        </p>

        <br>
        <span>The subscription offers you following benefits:</span>
        <ul>
            <li>
                Free medicine cabinet to keep track of your prescription drugs and natural medicines
            </li>
            <li>
                Easy way to add conditions, dosage and intake routines
            </li>
            <li>
                Free Interaction Checker for the items you entered in WellKabinet&#8482;
            </li>
            <li>
                Free chronological journaling of your experience
            </li>
            <li>
                Free access to research evidence
            </li>
        </ul>
        <br/>

        <!-- back button start -->
        <a href="javascript:void(0);" class="backToSignUpScreen backblack-btn">BACK</a>
        <!-- back button end -->

    </div>
    <!--center tab end -->
</div>       
<!--- Annual Subscription plan information for paitent/caregiver Screen div end --->


<!--- Confirm healthcare provider credentials Screen div start --->
<div id="confirmScreen" class="container550 confirm-details" style="display:none;">
    <h1 class="logo-login">
        <img  src="{{asset('images/Wellkasa_Logo.svg')}}" alt="Wellkasa_Logo"> 
    </h1>
    <!--center tab start -->
    <div class="confirm-text">
        
        <!-- back arrow button start -->
        <a href="javascript:void(0);" class="backToSignUpScreen"><img class="conf-back"  src="{{asset('images/eva-arrowback.svg')}}" alt="eva-arrowback"></a> 
        <!-- back arrow button end -->

        <h2 >Confirm your credentials</h2>
        <p>
            I confirm that I am a licensed medical professional in the United States with at least one of the following designations: MD, DO, Physicians Assistant, Nurse Practitioner, Registered Nurse, Chiropractor, Naturopathic Doctor, Physical/Occupational Therapist, Pharmacist, Nutritionist/Dietician, Medical practitioner of Acupuncture, Acupressure, Massage Therapy, Meditation, Reiki, Tai Chi or Yoga). I recognize and affirm that providing false information here will lead to cancellation of my account by Wellkasa Inc.
        </p>
        
        <!-- continue button start -->
        <div class="form-group row">
            <div class="col-md-12">
                <button id="continueConfirm" class="btn btn-green w-100 font-weight-bold">
                    {{ __('Continue') }}
                </button>                
            </div>
        </div>
        <!-- continue button end -->

        <!-- back button start -->
        <a href="javascript:void(0);" class="backToSignUpScreen backblack-btn">BACK</a>
        <!-- back button end -->

    </div>
    <!--center tab end -->
</div>       
<!--- Confirm healthcare provider credentials Screen div end --->
 

<!--- Annual Subscription plan information for healthcare provider Screen div start --->
<div id="infoForHealthCareProvider" class="container550 confirm-details" style="display:none;">
    <h1 class="logo-login">
        <img  src="{{asset('images/Wellkasa_Logo.svg')}}" alt="Wellkasa_Logo"> 
    </h1>
    <!--center tab start -->
    <div class="confirm-text">
        
        <!-- back arrow button start -->
        <a href="javascript:void(0);" class="backToSignConfirmScreen"><img class="conf-back"  src="{{asset('images/eva-arrowback.svg')}}" alt="eva-arrowback"></a> 
        <!-- back arrow button end -->

        <h2>
            Healthcare Provider login needs subscription. <br>
            If you already have one, please <a href="{{url('login')}}">LOGIN</a>
        </h2>

        <p class="mt-3 p-0">
           <span> 
               You can buy annual subscription by clicking <a href="https://wellkasa.com/products/wellkasa-rx">Buy Subscription</a>.
            </span>
        </p>

        <p class="mt-3 mb-0 p-0">
            By clicking on above link, you will be redirected to wellkasa shopify website.
        </p>
        
        <br>
        <span>The subscription offers you following benefits:</span>
        <ul>
            <li>
                Customizable Integrative Protocols
            </li>
            <li>
                Easy way to add conditions to your protocols and add desired notes
            </li>
            <li>
                Free Interaction Checker for your protocols
            </li>
            <li>
                Free access to research evidence
            </li>
        </ul>
        <br/>

        <!-- back button start -->
        <a href="javascript:void(0);" class="backToSignConfirmScreen backblack-btn">BACK</a>
        <!-- back button end -->

    </div>
    <!--center tab end -->
</div>       
<!--- Annual Subscription plan information for healthcare provider Screen div end --->


@endsection
@push('scripts')
<script>

    $(document).ready(function(){
        
        $('#login-form').validate({
            rules:{
                email : {
                    required : true,
                    maxlength : 50
                },
                password : {
                    required : true,
                }
            },
            messages:{
                email : {
                    required : "Please enter email.",
                    maxlength : "Email address should not be more than 50 characters."
                },
                password : {
                    required : "Please enter password.",
                }
            }
        });

        // Validates Email Pattern
        jQuery.validator.addMethod("emailPattern", function(value, element) {
            if(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/.test(value)){
                return true;
            }else{
                return false;
            };
        },'Please enter a valid email address');

        // Validates domain in email
        jQuery.validator.addMethod("noMailinatorDomain", function(value, element) {
            if(value.includes('@mailinator')){
                return false;
            }else{
                return true;
            };
        },'Email domain should not be of "mailinator.com"');

        // Validates domain in email
        jQuery.validator.addMethod("noMailinaterDomain", function(value, element) {
            if(value.includes('@mailinater')){
                return false;
            }else{
                return true;
            };
        },'Email domain should not be of "mailinater.com"');

        // Validates domain in email
        jQuery.validator.addMethod("noMailinator2Domain", function(value, element) {
            if(value.includes('@mailinator2')){
                return false;
            }else{
                return true;
            };
        },'Email domain should not be of "mailinator2.com"');

        // Validates Password Pattern
        jQuery.validator.addMethod("passwordPattern", function(value, element) {
            if(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#$^+=!*()@%&]).{8,}$/.test(value)){
                return true;
            }else{
                return false;
            };
        },'Password must be minimum 8 characters containing atleast 1 Lowercase letter, 1 Captial letter, 1 Special Character (i.e, # @ $ % & * !) and 1 Number.');

        $('#registerForm').validate({
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
                    email :true,
                    emailPattern : true,
                    maxlength : 50,
                    noMailinator2Domain :true,
                    noMailinatorDomain : true,
                    noMailinaterDomain : true,
                },
                password : { 
                    required : true,
                    passwordPattern : true,
                    minlength : 8,
                    
                },
                password_confirmation : { 
                    passwordPattern : true,
                    minlength : 8,
                    equalTo : "#password"
                },
                terms_of_use : {
                    required : true
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
                    email: "Please enter a valid email address.",
                    maxlength : "Email address should not be more than 50 characters"
                },
                password : {
                    required : "Please enter password.",
                    minlength : "Password should be maximum 8 characters.",
                },
                password_confirmation : {
                    required : "Please enter confirm password.",
                    minlength : "Password should be maximum 8 characters.",
                    equalTo : "Password and confirm password does not match"
                },
                terms_of_use : {
                    required : "Please check the terms of use."
                }
            }
        });

    });


    $(document).ready(function()
    {

        // on click paitent caregiver redirect in a new tab to shopify page
        $(".patientCaregiverRole").click(function(){
            window.open('https://wellkasa.com/products/wellkabinet', '_blank');
        });

        // on click healthcare provider show confirm your credentials screen
        $(".healthCareProviderRole").click(function(){
            $("#confirmScreen").show(); // show confirm your credentials screen
            $("#mainTab").hide(); // hide main sign up & login tab screen
        });

        // on back button click show sign up tab screen and hide confirm your credentials screen
        $(".backToSignUpScreen").click(function(){
            $("#infoForPaitentCaregiver").hide(); // show confirm information for subscription screen
            $("#confirmScreen").hide(); // hide confirm your credentials screen
            $("#mainTab").show(); // show main sign up & login tab screen
        });

        // on click of continue button redirect in a new tab to shopify page
        $("#continueConfirm").click(function(){
            window.open('https://wellkasa.com/products/wellkasa-rx', '_blank');
            
        });
        // on click after selection of health care provider role, the back button displays confirmation screen of it
        $(".backToSignConfirmScreen").click(function(){
            $("#infoForPaitentCaregiver").hide(); // show confirm information for subscription screen
            $("#infoForHealthCareProvider").hide();
            $("#confirmScreen").show(); // hide confirm your credentials screen
        });

        $(".changeSelectedRole").click(function(){
            $(".selectRoleDiv").show();
            $("#selectedRolePatientDiv").hide();
            $("#selectedRoleHealthCareDiv").hide();
            $("#roleId").val('');
        });



        $('#login-form').validate({
            rules:{
                email : {
                    required : true,
                    maxlength : 50
                },
                password : {
                    required : true,
                }
            },
            messages:{
                email : {
                    required : "Please enter email.",
                    maxlength : "Email address should not be more than 50 characters."
                },
                password : {
                    required : "Please enter password.",
                }
            }
        });

    });
</script>

<script>
    $(document).ready(function() {
    $("#show_hide_password a").on('click', function(event) {
        event.preventDefault();
        if($('#show_hide_password input').attr("type") == "text"){
            $('#show_hide_password input').attr('type', 'password');
            $('#show_hide_password i').addClass( "fa-eye-slash" );
            $('#show_hide_password i').removeClass( "fa-eye" );
        }else if($('#show_hide_password input').attr("type") == "password"){
            $('#show_hide_password input').attr('type', 'text');
            $('#show_hide_password i').removeClass( "fa-eye-slash" );
            $('#show_hide_password i').addClass( "fa-eye" );
        }
    });
});
</script>
@endpush

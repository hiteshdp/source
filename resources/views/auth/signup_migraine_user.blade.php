@extends('layout.default')

@section('title', __('Wellkasa - Login'))
@section('meta-keywords', __('Wellkasa, Integrative care, evidence informed, natural medicines'))
@section('meta-news-keywords', __('Wellkasa, Integrative care, evidence informed, natural medicines'))
@section('meta-description', __('Wellkasa Login - start your integrative care Journey'))

@section('content')
<div id="mainTab" class="container small-container mid-container">
    <h1 class="logo-login mt-5 pt-4"></h1>

    <!-- tab start -->
    <div class="signup-login-tab">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            @if(!isset($hideSignUpTab))
            <li class="nav-item active">
                <a class="nav-link active" id="signup-tab" data-toggle="tab" href="#signup" role="tab" aria-controls="signup" aria-selected="true">Sign Up</a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="false">Login</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
           
            <!--- Sign up tab start ---->
            <div class="tab-pane fade {{ !isset($hideSignUpTab) ? 'show active' : ''}}" id="signup" role="tabpanel" aria-labelledby="signup-tab">
                
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
            <div class="tab-pane fade" id="login" role="tabpanel" aria-labelledby="login-tab">
                <form class="mt-4 floating login-signup-form" method="POST" id="login-form" action="{{ route('login') }}">
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
            </div>
            <!--- Login up tab end ---->
        </div>
    </div>
    <!-- tab end -->

    <div class="privacy-link">
        By Signing up, I agree to Wellkasa’s <a href="https://wellkasa.com/policies/privacy-policy"><b>Privacy Policy</b></a>
    </div>

</div>


@endsection
@push('scripts')
<script>
    $(document).ready(function()
    {
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

        var url = window.location.href;
        var activeTab = url.substring(url.indexOf("?") + 1);
        if(activeTab!=''){
            $('a[href="#'+ activeTab +'"]').tab('show');
        }
    });
</script>
@endpush
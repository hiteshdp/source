@extends('layout.default')

@section('title', __('Register with Wellkasa to Begin your Integrative Care Journey.'))
@section('meta-keywords', __('Wellkasa, Integrative care, evidence informed, natural medicines'))
@section('meta-news-keywords', __('Wellkasa, Integrative care, evidence informed, natural medicines'))
@section('meta-description', __('Register with Wellkasa to begin your integrative care journey'))

@section('content')
<div class="container small-container">
<!-- <h1 class="logo-text">Complementary & Integrative Medicine (CIM)</h1> -->
    <h1 class="login-title">Free Sign Up</h1>
    
                    
    <h2 class="register-title mb-4">Already have a account? <a class="uderline" href="{{ route('login') }}">{{ __('Login Here') }}</a> </h2>
    <!-- <div class="tab-title pb-3 pt-2">{{ __('Register') }}</div> -->

    <div class="register">
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
                    <button type="submit" class="btn btn-green w-100">
                        {{ __('Register') }}
                    </button>
                </div>
            </div>
            <!-- <div class="form-group row mt-4 mb-4">
                <div class="col-md-6 col-6 border-right">
                    <a class="btn login-btn" href="{{ route('social.oauth', 'google') }}"><img class="mr-1" width="30" src="{{asset('images/google.svg')}}" alt="google logo"> <span> Use Google<br>account to Sign In</span> </a>
                </div>
                <div class="col-md-6 col-6">
                    <a class="btn login-btn" href="{{ route('social.oauth', 'facebook') }}"><img class="mr-1" width="30" src="{{asset('images/facebook.svg')}}" alt="facebook logo"> <span> Use Facebook<br>account to Sign In</span> </a>
                </div>
                </div> -->
                <div class="form-group row mt-1">
                            <div class="col-md-12">
                                <a class="btn login-btn pl-2" href="{{ route('social.oauth', 'google') }}"><img class="float-left mr-2" width="30" src="{{asset('images/google-new.svg')}}" alt="google logo"> <span> Continue with Google</span> </a>
                            </div>
                            <div class="col-md-12">
                                <a class="btn login-btn pl-2" href="{{ route('social.oauth', 'facebook') }}"><img class="float-left mr-2" width="30" src="{{asset('images/facebook-new.svg')}}" alt="facebook logo"> <span> Continue with Facebook</span> </a>
                            </div>
                         </div>  

            
        </form>
    </div>

</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function()
    {
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
@endpush
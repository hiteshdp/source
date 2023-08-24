@extends('layout.default')

@section('title', __('Wellkasa - Login'))
@section('meta-keywords', __('Wellkasa, Integrative care, evidence informed, natural medicines'))
@section('meta-news-keywords', __('Wellkasa, Integrative care, evidence informed, natural medicines'))
@section('meta-description', __('Wellkasa Login - start your integrative care Journey'))

@section('content')
<div class="container small-container mid-container">
    <!-- <h1 class="logo-text">Complementary & Integrative Medicine (CIM)</h1> -->
    <!-- <h1 class="login-title">myWellkasa</h1> -->
    <h1 class="login-title">Login</h1>
    <h2 class="register-title  mb-5">New user?  <a href="{{ route('register') }}">Sign up here</a> </h2>
    <!-- <div class="tab-title pb-2 pt-2">{{ __('Login') }}</div> -->
    
   


<form class="mb-4 floating" method="POST" id="login-form" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group mt-2">
                            
                                <input id="email" placeholder=" "  type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                <!-- <span class="highlight"></span> -->
                                <label for="email" class="float-label">{{ __('Username') }}</label>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                
                            
                        </div>

                        <div class="form-group">
                            
                                <input id="password" placeholder=" " type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                <!-- <span class="highlight"></span> -->
                                <label for="password" class="float-label">{{ __('Password') }}</label>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            
                        </div>

                       

                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-green w-100">
                                    {{ __('Login') }}
                                </button>

                               
                               
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 login-help">
                                 <a href="{{ route('password.request') }}">Need help logging in?</a>
                            </div>   
                        </div>   
                        <!-- <div class="row"> 
                            <div class="col-5 col-lg-6 text-center">
                                 <a class="btn-link" href="{{ route('register') }}">{{ __('Register Here') }}</a>
                            </div>
                            <div class="col-7 col-lg-6 text-center">
                            @if (Route::has('password.request'))
                                    <a class="btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                            
                         </div>   -->
                        <!-- <div class="form-group row mt-3 mb-3">
                            <div class="col-md-6 col-6 border-right">
                                <a class="btn login-btn" href="{{ route('social.oauth', 'google') }}"><img class="mr-1" width="30" src="{{asset('images/google.svg')}}" alt="google logo"> <span> Use Google<br>account to Sign In</span> </a>
                            </div>
                            <div class="col-md-6 col-6">
                                <a class="btn login-btn" href="{{ route('social.oauth', 'facebook') }}"><img class="mr-1" width="30" src="{{asset('images/facebook.svg')}}" alt="facebook logo"> <span> Use Facebook<br>account to Sign In</span> </a>
                            </div>
                         </div>   -->
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
@endsection
@push('scripts')
<!-- Code to show selected Files -->
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
        },'Please enter a valid email address.');

        $('#login-form').validate({
            rules:{
                email : {
                    required : true,
                    maxlength : 50,
                    emailPattern : true
                },
                password : {
                    required : true,
                }
            },
            messages:{
                email : {
                    required : "Please enter username.",
                    maxlength : "Email address should not be more than 50 characters."
                },
                password : {
                    required : "Please enter password.",
                }
            }
        });
    });
</script>
@endpush

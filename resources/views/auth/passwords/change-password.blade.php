@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp

@section('title', __('Wellkasa - Change Password'))
@section('meta-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-news-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-description', __('Wellkasa - Understanding the use prudent use of antimicrobials and their Health Canada categories'))

@section('content')

<div class="container container750 small-container">
<!--<h1 class="logo-text">Complementary & Integrative Medicine (CIM)</h1>-->
    <h1 class="login-title">{{ __('Change Password') }}</h1>
    <!-- <div class="tab-title pb-0 pt-4">{{ __('Change Password') }}</div> -->

<div class="change-pass floating">
                
                <div class="change-pass-form">
                    <form method="POST" id="change-password-form" action="{{ route('change.password') }}">
                        @csrf
                        <div class="form-group">
                            
                            
                                <input type="password" placeholder=" " class="form-control @error('current_password') is-invalid @enderror"  name="current_password" autocomplete="current_password" value="{{old('current_password') ? old('current_password') : '' }}">
                                <label for="password" class="float-label">Current Password</label>
                                @error('current_password')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                                @enderror
                        </div>

                        <div class="form-group ">
                            
                            
                                <input type="password" placeholder=" " class="form-control @error('password') is-invalid @enderror" name="password" id="password" autocomplete="password" value="{{old('password') ? old('password') : '' }}">
                                <label for="password" class="float-label">New Password</label>
                                @error('password')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                                @enderror
                            
                        </div>

                        <div class="form-group ">
                            
                            
                                <input type="password" placeholder=" " class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" autocomplete="password_confirmation" value="{{old('password_confirmation') ? old('password_confirmation') : '' }}">
                                <label for="password" class="float-label">Password Confirmation</label>
                                @error('password_confirmation')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                                @enderror
                            
                        </div>

                        <div class="form-group  mb-0">
                            
                                <button type="submit" class="btn btn-green pl-5 pr-5 w-100">
                                    Change Password
                                </button>
                            
                        </div>
                        <div class="col-md-12 login-help mt-4">
                            @if(Auth::user()->isUserHealthCareProvider())
                                <a href="{{route('my-profile-rx')}}">Cancel</a>
                            @else
                                <a href="{{route('my-profile')}}">Cancel</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
</div>
@endsection
@push('scripts')
<!-- Code to show selected Files -->
<script>
    $(document).ready(function()
    {
        // Validates Password Pattern
        jQuery.validator.addMethod("passwordPattern", function(value, element) {
            if(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#$^+=!*()@%&]).{8,}$/.test(value)){
                return true;
            }else{
                return false;
            };
        },'Password must be minimum 8 characters containing atleast 1 Lowercase letter, 1 Captial letter, 1 Special Character (i.e, # @ $ % & * !) and 1 Number.');

        $('#change-password-form').validate({
            rules:{
                current_password : {
                    required : true,
                },
                password : {
                    required : true,
                    passwordPattern : true
                },
                password_confirmation : {
                    required : true,
                    passwordPattern : true,
                    equalTo : "#password"
                }
            },
            messages:{
                current_password : {
                    required : "The current password field is required.",
                },
                password : {
                    required : "The password field is required.",
                },
                password_confirmation : {
                    required : "The password confirmation field is required.",
                    equalTo : "Password and confirm password does not match.",
                }
            }
        });
    });
</script>
@endpush

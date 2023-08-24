@extends('layout.default')

@section('title', __('Wellkasa - Verify Email'))
@section('meta-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-news-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-description', __('Wellkasa - Understanding the use prudent use of antimicrobials and their Health Canada categories'))

@section('content')
<div class="container small-container mid-container">
    <div class="signup-login-tab verify_email mt-5">
        <div class="tab-content">
            <h1 class="login-title">{{ __('Verify your email') }}</h1>
            <div class="text-center verify-text pt-4">
                @if (session('resent'))
                    <div class="alert alert-success" role="alert">
                        <p>{{ __('A new verification code has been sent to your email address.') }}</p>
                    </div>
                @endif
                <p>We have sent a 6 digit code to<br> your email<span> {{Auth::user()->email}}<span></p>
                <form class="d-inline" id="resendEmail" method="POST" action="{{ route('resend-verify-email-code') }}" novalidate="novalidate">
                    @csrf
                    <p>Didn't get the email? Click on the below <br>button to resend verification code.</p>    
                    <button type="submit" class="btn btn-gradient mb-3">Re-send Email Verification</button>
                </form>
                <p>Please check your email and <br> enter it below</p>
                <form class="d-inline" id="verifyCodeForm" method="POST" action="{{ route('verify-email-code') }}">
                    @csrf
                    <div class="verify-div">
                        <div class="divOuter">
                            <div class="divInner">
                                <input class="partitioned" type="text" maxlength="6" name="verification_code" id="verification_code" value="{{old('verification_code')}}" />
                            </div>
                        </div>
                        <button type="submit" class="btn btn-gradient mb-4">{{ __('Verify') }}</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function()
    {
        $('#verifyCodeForm').validate({
            rules:{
                verification_code : { 
                    required : true,
                    number : true,
                    maxlength : 6,
                    minlength : 6
                }
            },
            messages:{
                verification_code : {
                    required : "Please enter verification code.",
                    number : "Only numbers allowed.",
                    maxlength : "Verification code should be maximum 6 characters.",
                    minlength : "Verification code should be minimum 6 characters."
                    
                }
            }
        });
    });
</script>
@endpush
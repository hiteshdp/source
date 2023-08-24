@extends('layout.default')

@section('content')

        <div class="container750 min-450">
        <div class="providers-header border-0 mb-3">
            <h2>{{ __('We sent a code to the registered email.') }}</h2>
        </div>
            <div class="verify-form">
               
                <p class="verify-text">Enter the verification code</p>
                <form class="d-inline" id="verifyCodeForm" method="POST" action="{{ $verifyInputCodeRoute }}">
                    @csrf
                    <div class="verify-email">
                        
                                <input type="hidden" name="is_resend" value="">
                                <input class="form" type="text" maxlength="6" name="verification_code" id="verification_code" value="{{old('verification_code')}}" placeholder="6 digit code" autocomplete="off" />
                                <button type="submit" class="btn btn-gradient mb-2">{{ __('Submit') }}</button>
                                <label id="verification_code-error" class="error" style="display:none;" for="verification_code"></label>
                           
                            
                    </div>       
                </form>
                <!-- Resend email - code start -->
                
                    <div class="verify-email">
                        <form class="d-inline" id="resendEmail" method="POST" action="{{ $sendCodeRoute }}" novalidate="novalidate">
                            @csrf
                            <input type="hidden" name="is_resend" value="1">
                            <button type="submit" class="btn-link">Resend Code</button>
                            
                        </form>
                    </div>
               
                <!-- Resend email - code end -->

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
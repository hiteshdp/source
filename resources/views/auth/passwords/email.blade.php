@extends('layout.default')

@section('title', __('Wellkasa - Reset Password'))

@section('content')
<div class="container small-container mid-container">
<!-- <h1 class="logo-text">Complementary & Integrative Medicine (CIM)</h1> -->
    <h1 class="login-title">{{ __('Reset Password') }}</h1>
    
    <div class="reset-pass ">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <!-- <div class="tab-title pb-3 pt-2">{{ __('Reset Password') }}</div> -->
                    <form method="POST" class="floating mt-4" id="resetPasswordForm" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group">
                            

                            
                                <input id="email" placeholder=" " type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                <label for="email" class="float-label">{{ __('E-Mail Address') }}</label>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            
                        </div>

                        <div class="form-group row mb-4 mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-green w-100">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                                <div class="col-md-12 login-help mt-4">
                                <a href="{{ route('login') }}">Back to Login</a>
                                </div>
                            </div>
                            
                        </div>
                    </form>
                </div>

   
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

        $('#resetPasswordForm').validate({
            rules:{
                email : {
                    required : true,
                    maxlength : 50,
                    emailPattern : true
                },
            },
            messages:{
                email : {
                    required : "Please enter email.",
                    maxlength : "Email address should not be more than 50 characters."
                }
            }
        }); 
    });

</script>
@endpush

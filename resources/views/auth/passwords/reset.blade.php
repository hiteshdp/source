@extends('layout.default')

@section('title', __('Wellkasa - Reset Password'))

@section('content')
<div class="container small-container mid-container">
    <h1 class="login-title">myWellkasa</h1>
            <div class="reset-pass ">
            <div class="tab-title pb-2 pt-2">{{ __('Reset Password') }}</div>
                    <form method="POST" class="floating" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group">
                            

                            
                                <input id="email" type="email" placeholder=" " class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                                <label for="email" class="float-label">{{ __('E-Mail Address') }}</label>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            
                        </div>

                        <div class="form-group ">
                            

                            
                                <input id="password" placeholder=" " type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                <label for="password" class="float-label">{{ __('Password') }}</label>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            
                        </div>

                        <div class="form-group">
                            

                            
                                <input id="password-confirm"  placeholder=" " type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                <label for="password-confirm" class="float-label">{{ __('Confirm Password') }}</label>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-green w-100">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
            </div>

   
</div>

@endsection

@extends('layout.default')

@section('title', __('Wellkasa - Verify Email'))
@section('meta-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-news-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-description', __('Wellkasa - Understanding the use prudent use of antimicrobials and their Health Canada categories'))

@section('content')
<div class="container">
<!-- <h1 class="logo-text">Complementary & Integrative Medicine (CIM)</h1> -->
<h1 class="login-title">{{ __('Verify Your Email Address') }}</h1>
<div class="text-center verify-text pt-4">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            <p>{{ __('A fresh verification link has been sent to your email address.') }}</p>
                        </div>
                    @endif
                    <p>{{ __('Before proceeding, please check your email for a verification link.') }}</p>
                    <p>{{ __('If you did not receive the email') }},</p>
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-green mb-4">{{ __('click here to request another.') }}</button>
                    </form>
                    <p class="pb-2"><strong>Please check for the verification email in your SPAM folder in case you do not find the email in your mailbox within 15 mins. If you still can't find the email, please contact us at <a href="mailto:admin@wellkasa.com" title="mailto:admin@wellkasa.com">admin@wellkasa.com</a> and we will help you.</strong></p>
                </div>
</div>
@endsection

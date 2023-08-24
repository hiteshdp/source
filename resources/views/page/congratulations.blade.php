@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp


@section('content')
    
    <div class="container750">
        <div class="container300 text-center mt-5">
            <img src="{{asset('images/congratulations.svg')}}" alt="congratulations">
            <h2 class="user-name">Congratulations {{Auth::user()->name}}!</h2>
            <div class="congrat-message">
                You are now a <strong>@if(Auth::user()->planType == 1){{'Wellkasa Basic'}}@else{{'Wellkabinet'&#8482;}} @endif member!</strong> Let us get you set up.
            </div>
            <a href="{{route('complete-profile')}}" type="submit" class="btn-gradient w-100 mt-5 border-0">Complete profile</a>
            <!-- <button type="submit" class="btn-gradient w-100 mt-5 border-0">Complete profile</button> -->
        </div>
    </div>
@endsection
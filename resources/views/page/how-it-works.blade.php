@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp

@section('title', __('Wellkasa - How it works'))
@section('meta-keywords', __('Wellkasa - How it works'))
@section('meta-news-keywords', __('Wellkasa - How it works'))
@section('meta-description', __('Wellkasa - How it works'))

@section('content')
<div class="middle-part ">
<div class="container floating mid-container">
   <div class="home-new-hero-text">
        <h1 class="home-main-title text-center">How it works</h1>
        <div class="small-container-video">
            <div class="row video-part mb-5">
                <div class="col-12 col-md-12">
                <iframe width="100%" height="450" src="https://www.youtube.com/embed/trfa0x6MXak?rel=0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
        </div>
   </div>
</div>
@endsection

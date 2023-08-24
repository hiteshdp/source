@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp
                
@section('meta-keywords', __('integrative care, startup, journey, Wellkasa, mission '))
@section('meta-news-keywords', __('integrative care, startup, journey, Wellkasa, mission '))
@section('meta-description', __('Wellkasa is committed to empowering you to confidently engage in your inetgrative care journey.'))
@section('title', __('Learn about Wellkasa Startup Journey'))

@section('content')

<div class="our-story">
  <div class="container">
    <div class="page-title">
      <h1>Our story</h1>
    </div>
    <div class="post-links">
      <ul class="d-flex m-0 p-0 justify-content-center">
        <li><a href="{{ route('blogs') }}">All Posts</a></li>
        <li><a href="{{ route('company-news') }}">Company News</a></li>
        <li class="active"><a href="{{ route('/blogs/startup-journey') }}">Startup Journey</a></li>
      </ul>
    </div>
    <div class="row">
      <div class="col-md-6 mb-5">
        <a class="post-column" href="{{ route('our-storyepisode') }}">
          <div class="post-image">
            <img class="mt-0 img-fluid" src="{{asset('images/episode-1.jpg')}}" alt="Launching Wellkasa">
          </div>
          <div class="post-description p-4">
            <div class="post-date">
              <span>August 2, 2021</span>
              <span>|</span>
              <span>Startup Journey</span>
            </div>
            <h3>Episode 1: Launching Wellkasa</h3>
            <p>Clarity Despite Cancer</p>
            <span class="continue-reading">Continue Reading</span>
          </div>
        </a>
      </div>
      <div class="col-md-6 mb-5">
        <a class="post-column" href="{{ route('/blogs/road-to-wellkasa') }}">
          <div class="post-image">
            <img class="mt-0 img-fluid" src="{{asset('images/episode-2.jpg')}}" alt="Launching Wellkasa">
          </div>
          <div class="post-description p-4">
            <div class="post-date">
              <span>July 17, 2021</span>
              <span>|</span>
              <span>Startup Journey</span>
            </div>
            <h3>Episode 0: The Road to Wellkasa</h3>
            <p>Below we detail the train of events leading to Wellkasa’s mission.</p>
            <span class="continue-reading">Continue Reading</span>
          </div>
        </a>
      </div>
      <div class="col-md-6">
        <a class="post-column" href="{{ route('/blogs/wellkasa-is-live') }}">
          <div class="post-image text-center">
            <img class="mt-0 img-fluid" src="{{asset('images/well-img.jpg')}}" alt="Launching Wellkasa">
          </div>
          <div class="post-description p-4">
            <div class="post-date">
              <span>August 17, 2021</span>
              <span>|</span>
              <span>Startup Journey</span>
            </div>
            <h3>Episode 2: Wellkasa is Live</h3>
            <p> See the Science on Effectiveness of a Natural Medicine in Less than a Minute</p>
            <span class="continue-reading">Continue Reading</span>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>

@endsection

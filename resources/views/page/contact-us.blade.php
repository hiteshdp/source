@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp
                
​@section('title', __('Contact Wellkasa to help with your integrative care journey'))
@section('meta-keywords', __('natural medicines, find natural medicines, buy supplements, track effectiveness, prevention, symptom relief, chronic conditions, quality of life, feel better, physician grade supplements, member relations, help, contact, Wellkasa'))
@section('meta-news-keywords', __('natural medicines, find natural medicines, buy supplements, track effectiveness, prevention, symptom relief, chronic conditions, quality of life, feel better, physician grade supplements, member relations, help, contact, Wellkasa'))
@section('meta-description', __('Contact Wellkasa to help with your integrative care journey'))
@section('og-url', Request::url())
@section('og-title', __('Contact Wellkasa to help with your integrative care journey'))
@section('og-description', __('Contact Wellkasa to help with your integrative care journey.'))

@section('content')
<div class="middle-part contactus">
  <div class="container">
    <div class="text-center">
      <h1 class="pb-3" > Contact Us</h1>
    </div>
      <div class="row align-items-center justify-content-center mt-5">
      
      <div class="col-12 col-md-8 order-2">
          <div class="contact-info">
            <h3 class="contact-title">Hi there!</h3>
            <p>I’m Melissa – a mom of 2 in Phoenix AZ. I have been a caregiver for most of my life and I love helping people with chronic conditions live their best life.</p>
            <p>If you are looking for safe and effective natural medicines for prevention or for symptom relief, I am here to help you:</p>

            <ul class="m-0">
              <li><p><strong>Find</strong> natural medicines for your symptoms</p></li>
              <li><p><strong>Buy</strong> physician grade supplements at competitive price</p></li>
              <li><p><strong>Track</strong> that these natural medicines indeed help you feel better</p></li>
            </ul>
            <p>So, if you have a question or a suggestion.</p>
            <div class="d-flex align-items-center flex-wrap flex-md-nowrap">
              <a class="btn btn-info mr-0 mr-md-3 mb-3 mb-md-0" href="mailto:help@wellkasa.com"><strong>Email me</strong><div>help@wellkasa.com</div></a>
              <a class="btn btn-call" href="tel:+1480-779-8266"><strong>Call me</strong><div>+1480-779-8266</div><div>M-F 9 AM – 5 PM US PST </div></a>
            </div>
            
          </div>
        </div>
        <div class="col-12 col-md-4 order-1 text-center">
          <img class="img-fluid member-img"  src="{{asset('images/melissa-mehrotra.png')}}" alt="contactus" title="contactus">
          <h5 class="mt-4"><strong>Melissa Mehrotra</strong></h5>
          <p>Wellkasa Member Relations</p>
      </div>
      </div>  
      
  </div>
</div>  
@endsection
@push('styles')
<style type="text/css">
    .read-more-show{
      cursor:pointer;
      color: #ed8323;
    }
    .read-more-hide{
      cursor:pointer;
      color: #ed8323;
    }

    .hide_content{
      display: none;
    }
</style>
@endpush
@push('scripts')
<script type="text/javascript">
</script>
@endpush
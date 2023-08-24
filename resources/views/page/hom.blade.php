@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp
                
@section('title', __('Wellkasa - Research Integrative and Natural Medicine - Personalized and Free!'))
@section('meta-keywords', __('Wellkasa, natural, personalized, supplements, herbs, nutrition, integrative, mind-body, evidence, interaction checker'))
@section('meta-news-keywords', __('Wellkasa, natural, personalized, supplements, herbs, nutrition, integrative, mind-body, evidence, interaction checker'))
@section('meta-description', __('Find evidence-informed nutrition, herbs, supplements, and mind-body therapies. Build personalized integrative care plans for you and your loved ones.'))

@section('twitter-url', Request::url())
@section('twitter-title', __('Wellkasa - Research Integrative and Natural Medicine - Personalized & Free!'))
@section('twitter-description'){!!'Wellkasa - Research Engine for evidence based information on effectiveness of nutrition, supplements, &amp; natural therapies for many diseases and conditions.'!!}@stop

@section('og-url', Request::url())
@section('og-title', __('Wellkasa - Research Integrative and Natural Medicine - Personalized & Free!'))
@section('fb-title', __('Wellkasa - Research Integrative and Natural Medicine - Personalized & Free!'))
@section('og-description'){!!'Wellkasa - Research Engine for evidence based information on effectiveness of nutrition, supplements, &amp; natural therapies for many diseases and conditions.'!!}@stop

@section('content')
<div class="bg-white py-5">
   <div class="container">
      <div class="row">
         <div class="col-md-6">
            <div class="migrain-top-left">
               <img width="285"  src="{{asset('images/home/migraine-logo.svg')}}" alt="Migraine Logo" title="Migraine Logo">
               <h1>Migraine wellness companion</h1>
               <p>MIGRAINE AI & SYMPTOM TRACKER</p>
               <a class="btn btn-gradient mt-4 font-weight-bold" href="#">FREE SIGN UP</a>
            </div>
         </div>
         <div class="col-md-6">
            <img class="img-fluid"  src="{{asset('images/home/happy-people.jpg')}}" alt="Happy People" title="Happy People">
            
         </div>
      </div>
   </div>
</div>
<div class="light-blue-bg py-5">
   <div class="container">
         <div class="row">
            <div class="col-md-6">
               fd
            </div>
            <div class="col-md-6">
               fd
            </div>
         </div>
   </div>
</div>
<div class="middle-part">
<nav class="float-action-button">
      <a href="https://www.linkedin.com/shareArticle?url={{route('hom').'/home'}}&title='Wellkasa - Research Integrative and Natural Medicine - Personalized & Free!'" class="buttons" title="linkedin" data-toggle="tooltip" data-placement="left">
            <img  src="{{asset('images/in.png')}}" alt="linkedin">
        </a>
         <!-- <a href="#" class="buttons" title="instagram" data-toggle="tooltip" data-placement="left">
            <img  src="{{asset('images/insta.png')}}" alt="instagram">
        </a> -->
       <a href="https://twitter.com/share?url={{route('hom').'/home'}}&text=Wellkasa - Research Integrative and Natural Medicine - Personalized & Free!" class="buttons" title="Twitter" data-toggle="tooltip" data-placement="left">
         <img  src="{{asset('images/twit.png')}}" alt="twit">
        </a>
       <a href="https://www.facebook.com/sharer.php?u={{route('hom').'/home'}}" class="buttons" title="Facebook" data-toggle="tooltip" data-placement="left">
            <img  src="{{asset('images/fb.png')}}" alt="fb">
        </a>
      <a href="#" class="buttons main-button" title="Share" data-toggle="tooltip" data-placement="left">
      
          <!-- <i class="fa fa-times"></i>
          <i class="fa fa-share-alt"></i> -->
          <img class="fa fa-times" src="{{asset('images/close.png')}}" alt="close">
          <img class="fa fa-share-alt" src="{{asset('images/share.png')}}" alt="share">
          
      </a>
</nav>
<div class="container ">
   <div class="home-new-hero-text">
      
      <div class="research-box">
         <h2>Powered by research </h2>
         <p>Find evidence-informed nutrition, herbs, supplements and mind-body 
therapies and build personalized integrative care plans for you and your 
loved ones</p>
         <div class="row">
            <div class="col-4 col-md-4">
               <div class="icon-list-view">
               <img  src="{{asset('images/medical-conditions.svg')}}" alt="Medical Conditions">
                  <div class="numbers">
                  993
                  </div>
                  <span>Medical <br>Conditions</span>
               </div>
            </div>
            <div class="col-4 col-md-4">
               <div class="icon-list-view">
               <img   src="{{asset('images/natural-medicines.svg')}}" alt="Natural Medicines">   
               <div class="numbers">
                  1,458
                  </div>
                  
                  <span>Natural<br> Medicines</span>
               </div>
            </div>
            <div class="col-4 col-md-4">
               <div class="icon-list-view">
               <img  src="{{asset('images/research-papers.svg')}}" alt="Research Papers">
                  <div class="numbers">
                     19,953
                  </div>
                  
                  <span>Research <br>Papers</span>
               </div>
            </div>
         </div>
      </div>
      
      <div class="small-container-video pt-5 d-none">
         <div class="hand-icons pb-3">
            <img  src="{{asset('images/hand.svg')}}" alt="hand">
         </div>
            <div class="natural-med-text">
               <h3>Check your drug - natural medicine interactions</h3>
               <p>Add at least one drug and one natural medicine to view interactions</p>
            </div>
            <div class="right-search round-search text-left mb-0">
         <div class="input-group rounded mt-1">
            <span class="input-group-text border-0 add-filled" id="search-addon">
               <img src="{{asset('images/carbon-add-filled.svg')}}" alt="search" title="search">
            </span>
            <input type="search" class="form-control  therapy" placeholder=" " aria-label="Search"
               aria-describedby="search-addon" />
            <label for="email" class="float-label mob-font">Start by typing a drug or a natural medicine name</label>  
         </div>
         <div class="powered-by">
            Powered by: <img src="{{asset('images/trc.png')}}" alt="search" title="trc">
         </div>   
      </div>
         
      </div>
     
   </div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Most Searched Diseases, Conditions & Natural Medicines</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><img  src="{{asset('images/close-nav.svg')}}" alt="close"></span>
        </button>
      </div>
      <div class="modal-body">
      <h2>Most Searched Diseases & Conditions:</h2>
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


@endpush
@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp
                
@section('meta-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-news-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-description', __('Wellkasa - Understanding the use prudent use of antimicrobials and their Health Canada categories'))
@section('meta-abstract', __('Wellkasa - Understanding the use prudent use of antimicrobials and their Health Canada categories'))
@section('meta-name', __('Wellkasa - Integrative Cancer Care, Evidence Based Medicine'))

@section('twitter-url', Request::url())
@section('twitter-title', __('Wellkasa - Understanding antimicrobials and wellkasa'))
@section('twitter-description', __('Wellkasa - Understanding the use prudent use of antimicrobials and their Health Canada categories'))

@section('og-url', Request::url())
@section('og-title', __('Wellkasa - Understanding antimicrobials and wellkasa'))
@section('fb-title', __('Wellkasa - Understanding antimicrobials and wellkasa'))
@section('og-description', __('Wellkasa - Understanding the use prudent use of antimicrobials and their Health Canada categories'))

@section('content')
<div class="container">
   <ul class="nav nav-tabs getcaretab d-none" id="myTab" role="tablist">
      <li class="nav-item">
         <a class="nav-link text-center" href="{{route('my-profile')}}" aria-selected="true"> <img src="{{asset('images/myprofile.svg')}}" alt="myprofile" title="myprofile"> <span class="tab-name">My <br>Profile </span> </a>
      </li>
      <li class="nav-item">
         <a class="nav-link text-center" id="therapy-tab" data-toggle="tab" href="#" role="tab" aria-controls="therapy" aria-selected="false"><img src="{{asset('images/mycareteam.svg')}}" alt="mycareteam" title="mycareteam"> <span class="tab-name">My <br>Care Team </span></a>
      </li>
      <li class="nav-item">
         <a class="nav-link text-center" id="getcare-tab" data-toggle="tab" href="#" role="tab" aria-controls="getcare" aria-selected="false"><img src="{{asset('images/myappointments.svg')}}" alt="myappointments" title="myappointments"> <span class="tab-name">My <br>Appointments </span></a>
      </li>
      <li class="nav-item">
         <a class="nav-link text-center active" id="getcare-tab" data-toggle="tab" href="#" role="tab" aria-controls="mytherapies" aria-selected="false"><img class="svg" src="{{asset('images/mytherapies.svg')}}" alt="mytherapies" title="mytherapies"> <span class="tab-name">My <br>Therapies </span></a>
      </li>
   </ul>
   <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade" id="therapy" role="tabpanel" aria-labelledby="therapy-tab">...</div>
      <div class="tab-pane fade" id="getcare" role="tabpanel" aria-labelledby="getcare-tab">...</div>
      <div class="tab-pane fade show active" id="mytherapies" role="tabpanel" aria-labelledby="mytherapies-tab">
         <div class="media d-none">
            <img  class="mr-4 rounded-circle" src="{{asset('uploads/avatar/'.Auth::user()->avatar)}}" onerror="this.onerror=null;this.src='{{ asset("images/user.jpg") }}';" alt="user" title="user" style="width: 120px; height: 120px">
            <div class="media-body">
               <h2 class="profile-title">{{ Auth::user()->name." ".Auth::user()->last_name }}</h2>
            </div>
         </div>
         <div class="row pb-3">
           <?php echo"<pre>"; print_r($allTherapy);?>
         </div>
      <!-- <a data-toggle="tooltip" data-placement="left" title="Add Integrative Therapy" class="add-btn" href="#"><img  src="{{asset('images/add.svg')}}" alt="add" title="add"></a> -->
      
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
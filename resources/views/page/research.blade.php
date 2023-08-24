@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp
                
@section('meta-keywords', __(''))
@section('meta-news-keywords', __(''))
@section('meta-description', __(''))
@section('title', __(''))

@section('content')
<style>
    .footer { margin-top: 0px }
</style>
<div class="home-banner">
    <div class="container">
   <div class="floating">
        <h1 > FIND<br>
            Safe & Effective Supplements<br>
            Diets & Mind-Body Therapies
             </h1>
        <div class="right-search round-search text-left pt-2 mb-0">
            <div class="input-group rounded mt-1">
                <span class="input-group-text border-0" id="search-addon">
                    <img width="32"  src="{{asset('images/search-blue.svg')}}" alt="search" title="search">
                </span>
                <input type="search" class="form-control  therapy" placeholder=" " aria-label="Search" aria-describedby="search-addon" />
                <label for="email" class="float-label mob-font">Search for a disease, condition or therapy</label>  
            </div>
            <div class="small-text">
                <div class="deseases-list">
                    <strong>Example:</strong> 
                    <a href="{{route('therapy','ashwagandha')}}">Ashwagandha,</a> 
                    <a href="{{route('condition','breast-cancer')}}">Breast Cancer,</a>
                    <a href="{{route('therapy','melatonin')}}">Melatonin,</a>
                    <!-- <a href="{{route('therapy','turmeric')}}">Turmeric,</a> -->
                    <a href="{{route('therapy','vitamin-d')}}">Vitamin D,</a>
                    <a href="{{route('therapy','yoga')}}">Yoga,</a>
                    <a href="{{route('condition','migraine-headache')}}">Migraine Headache</a>
                </div>
            </div>
        </div>
        <!-- <h2> Access science on natural medicines <br> <span>FREE</span></h2> -->
    </div>
    </div>
</div>
<div class="light-blue-bg">
    <div class="container-fluid">
        <div class="row">
        <div class="col-md-6 border-right">
            <div class="research-box ">
                <h2>Powered by Research </h2>
                <p class="mt-4 mb-4">Within seconds, find simplified research evidence to understand turmeric benefits,  melatonin side effects or natural therapies that support wellness when managing chemotherapy side effects
                    </p>
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
        </div>
        <div class="col-md-6">
        <div class="research-box research-box-right">
                <h2>Wellkasa Seal of Trust </h2>
                <div class="mt-4 mb-4"><img  src="{{asset('images/wellcasa-seal-big.svg')}}" alt="wellcasa-seal-big"></div>
                <div class="row">
                    <div class="col-4 col-md-4">
                    <div class="icon-list-view">
                    <img  src="{{asset('images/trust-2.svg')}}" alt="trust">
                       
                        <span>Doctor Designed <br>Wellness Plans</span>
                    </div>
                    </div>
                    <div class="col-4 col-md-4">
                    <div class="icon-list-view">
                    <img   src="{{asset('images/scientific-evidence.svg')}}" alt="scientific-evidence">   
                        <span>Backed by Scientific Evidence</span>
                    </div>
                    </div>
                    <div class="col-4 col-md-4">
                        <div class="icon-list-view">
                         <img  src="{{asset('images/trust-3.svg')}}" alt="trust-3">
                                               
                            <span>Physician Grade Discounted Supplements</span>
                        </div>
                    </div>
                    <div class="col-4 col-md-4">
                        <div class="icon-list-view">
                         <img  src="{{asset('images/trust-4.svg')}}" alt="trust-4">
                        <span>Effective Dosage</span>
                    </div>
                    </div>
                    
                </div>
            </div>
        </div>
        </div>
     </div>
    </div>
</div>

@endsection

<!-- Single search bar logic code - start -->
@push('scripts')
<script type="text/javascript">

  // Auto complete ajax call 
  var path_therapy = "{{ route('autocomplete-therapy') }}";    
  $('input.therapy').typeahead({
    items:'all',
    source: function (query, process) {
          return $.ajax({
              url: path_therapy,
              type: 'get',
              data: { query: query },
              dataType: 'json',
              success: function (result) {
                var resultList = result.map(function (item) {
                      var aItem = { id: item.Id, name: item.Name , canonicalName: item.canonicalName };
                      return JSON.stringify(aItem);
                });
                return process(resultList);
              }
          });
    },

    matcher: function (obj) {
          var item = JSON.parse(obj);
          return ~item.name.toLowerCase().indexOf(this.query.toLowerCase())
    },

    sorter: function (items) {          
        var beginswith = [], caseSensitive = [], caseInsensitive = [], item;
          while (aItem = items.shift()) {
              var item = JSON.parse(aItem);
              if (!item.name.toLowerCase().indexOf(this.query.toLowerCase())) beginswith.push(JSON.stringify(item));
              else if (~item.name.indexOf(this.query)) caseSensitive.push(JSON.stringify(item));
              else caseInsensitive.push(JSON.stringify(item));
          }
          return beginswith.concat(caseSensitive, caseInsensitive)
    },

    highlighter: function (obj) {
          var item = JSON.parse(obj);
          var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
          return item.name.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
              return '<strong>' + match + '</strong>'
          })
    },

    updater: function (obj) {
          var item = JSON.parse(obj);
          $('input.therapy').attr('value', item.id);
          //Merged Logic to identify that it's condition or Therapy and then redirect accordingly - Start
          if(item.id.includes("therapy"))
          {
              item.id = item.id.replace("-therapy", "");
              window.location.href = "{{route('therapy', '')}}"+"/"+item.canonicalName;
          }
          else
          {
              item.id = item.id.replace("-condition", "");
              window.location.href = "{{route('condition', '')}}"+"/"+item.canonicalName;
          }
          //Merged Logic to identify that it's condition or Therapy and then redirect accordingly - End
          return item.name;
    }

  });
  
</script>
@endpush
<!-- Single search bar logic code - end -->
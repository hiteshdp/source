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
    <div class="container text-center rx">
       
        
         <!-- tab start-->
         <ul class="nav nav-tabs tab-group" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="integrative-protocols-tab" data-toggle="tab" href="#protocolstab" role="tab" aria-controls="protocolstab" aria-selected="true">Integrative Protocols</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="interaction-checker-tab" data-toggle="tab" href="#inchecker" role="tab" aria-controls="inchecker" aria-selected="false">Interaction Checker</a>
        </li>
        
        </ul>
        <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="protocolstab" role="tabpanel" aria-labelledby="integrative-protocols-tab">fdsf</div>
        <div class="tab-pane fade" id="inchecker" role="tabpanel" aria-labelledby="interaction-checker-tab">ssss</div>
        
        </div>
        <!-- tab end -->
        <div class="floating">
            <!-- <h1 class="text-center ">Interaction Checker</h1> -->
            <form method="post" id="reportInteraction">
               <!-- Single search bar code - start -->
                <div class="right-search round-search mb-0 text-left">
                <div class="input-group rounded mt-1 ">
                <span class="input-group-text border-0" id="search-addon">
                        <img width="18"  src="{{asset('images/search-blue.svg')}}" alt="search" title="search">
                    </span>
                    <input type="search" class="form-control  therapy " placeholder=" " aria-label="Search"
                    aria-describedby="search-addon" />
                    <label for="email" class="float-label mob-font">Search for a disease, condition or therapy</label> 
                </div>
                <div class="small-text pl-3  pt-2">Didn't find what you were looking? Submit your suggestion by clicking the chat icon bottom right.  </div>
                </div>
                <!-- Single search bar code - end -->
            </form>
        </div>
        
       
            
                <div class="filter-bar">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            Select all
                        </label>
                    </div>
                    <ul class="navbar-nav">
                        <li class="dropdown">
                            <a class="dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by condition
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                            </ul>
                        </li>
                    </ul>
                    <button class="btn-report"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 10.5C6 10.3674 6.05268 10.2402 6.14645 10.1464C6.24021 10.0527 6.36739 10 6.5 10C6.63261 10 6.75979 10.0527 6.85355 10.1464C6.94732 10.2402 7 10.3674 7 10.5C7 10.6326 6.94732 10.7598 6.85355 10.8536C6.75979 10.9473 6.63261 11 6.5 11C6.36739 11 6.24021 10.9473 6.14645 10.8536C6.05268 10.7598 6 10.6326 6 10.5ZM6.5 12C6.36739 12 6.24021 12.0527 6.14645 12.1464C6.05268 12.2402 6 12.3674 6 12.5C6 12.6326 6.05268 12.7598 6.14645 12.8536C6.24021 12.9473 6.36739 13 6.5 13C6.63261 13 6.75979 12.9473 6.85355 12.8536C6.94732 12.7598 7 12.6326 7 12.5C7 12.3674 6.94732 12.2402 6.85355 12.1464C6.75979 12.0527 6.63261 12 6.5 12ZM6 14.5C6 14.3674 6.05268 14.2402 6.14645 14.1464C6.24021 14.0527 6.36739 14 6.5 14C6.63261 14 6.75979 14.0527 6.85355 14.1464C6.94732 14.2402 7 14.3674 7 14.5C7 14.6326 6.94732 14.7598 6.85355 14.8536C6.75979 14.9473 6.63261 15 6.5 15C6.36739 15 6.24021 14.9473 6.14645 14.8536C6.05268 14.7598 6 14.6326 6 14.5ZM8.5 10C8.36739 10 8.24021 10.0527 8.14645 10.1464C8.05268 10.2402 8 10.3674 8 10.5C8 10.6326 8.05268 10.7598 8.14645 10.8536C8.24021 10.9473 8.36739 11 8.5 11H13.5C13.6326 11 13.7598 10.9473 13.8536 10.8536C13.9473 10.7598 14 10.6326 14 10.5C14 10.3674 13.9473 10.2402 13.8536 10.1464C13.7598 10.0527 13.6326 10 13.5 10H8.5ZM8 12.5C8 12.3674 8.05268 12.2402 8.14645 12.1464C8.24021 12.0527 8.36739 12 8.5 12H13.5C13.6326 12 13.7598 12.0527 13.8536 12.1464C13.9473 12.2402 14 12.3674 14 12.5C14 12.6326 13.9473 12.7598 13.8536 12.8536C13.7598 12.9473 13.6326 13 13.5 13H8.5C8.36739 13 8.24021 12.9473 8.14645 12.8536C8.05268 12.7598 8 12.6326 8 12.5ZM8.5 14C8.36739 14 8.24021 14.0527 8.14645 14.1464C8.05268 14.2402 8 14.3674 8 14.5C8 14.6326 8.05268 14.7598 8.14645 14.8536C8.24021 14.9473 8.36739 15 8.5 15H13.5C13.6326 15 13.7598 14.9473 13.8536 14.8536C13.9473 14.7598 14 14.6326 14 14.5C14 14.3674 13.9473 14.2402 13.8536 14.1464C13.7598 14.0527 13.6326 14 13.5 14H8.5ZM6 2C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V16C4 16.5304 4.21071 17.0391 4.58579 17.4142C4.96086 17.7893 5.46957 18 6 18H14C14.5304 18 15.0391 17.7893 15.4142 17.4142C15.7893 17.0391 16 16.5304 16 16V7.414C15.9997 7.01631 15.8414 6.63503 15.56 6.354L11.646 2.439C11.3648 2.15798 10.9835 2.00008 10.586 2H6ZM5 4C5 3.73478 5.10536 3.48043 5.29289 3.29289C5.48043 3.10536 5.73478 3 6 3H10V6.5C10 6.89782 10.158 7.27936 10.4393 7.56066C10.7206 7.84196 11.1022 8 11.5 8H15V16C15 16.2652 14.8946 16.5196 14.7071 16.7071C14.5196 16.8946 14.2652 17 14 17H6C5.73478 17 5.48043 16.8946 5.29289 16.7071C5.10536 16.5196 5 16.2652 5 16V4ZM14.793 7H11.5C11.3674 7 11.2402 6.94732 11.1464 6.85355C11.0527 6.75979 11 6.63261 11 6.5V3.207L14.793 7Z" fill="white"/>
                        </svg>Create Report</button>
                </div>
            
        

        
           
                <div class="accordion intergrative-accordion" id="accordionExample">
                <div class="card">
                    <div class="card-head" id="headingOne">
                    <h2 class="mb-0" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <div class="accordion-title">Beta carotene (2) <img  src="{{asset('images/pill1.svg')}}" alt="pill1"></div> 
                    </h2>
                    <div class="add-accordion"> <a href="#"><img  src="{{asset('images/add-acc.svg')}}" alt="add"> Add condition</a>
                        <div class="addcondition-info">
                        <img  src="{{asset('images/yellow-info.svg')}}" alt="yellow-info"> 
                            <p>Click add condition to select a condition and add  notes about Beta cartones effectiveness</p>
                            <div class="info-link-popup">
                                <a href="#">Add now</a> <a href="#">Later</a>
                            </div>
                        </div>   
                    </div>
                    </div>

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body">
                        <div class="acc-repeated">
                            <div class="acc-top-action">
                                <label class="custom-checkbox">Aging skin
                                    <input type="checkbox" checked="checked">
                                    <span class="checkmark"></span>
                                </label>
                                <div class="acc-action">
                                    <a href="#"><img  src="{{asset('images/acc-edit.png')}}" alt="acc-edit"></a>
                                    <a href="#"><img  src="{{asset('images/acc-delete.png')}}" alt="acc-delete"></a>
                                </div>   
                            </div>
                            <div class="acc-progress">
                                    <div class="acc-efficany"><img  src="{{asset('images/trc-acc.png')}}" alt="trc-acc"> Known Efficacy</div> 
                                    <span>Inconclusive</span> 
                                    <ul class="acc-progress-list" id="progressbar" style="height: auto !important;">
                                        
                                        <li class="INEFFECTIVE " id="confirm"></li>
                                        <li class="LIKELY_INEFFECTIVE" id="confirm"></li>
                                        <li class="POSSIBLY_INEFFECTIVE active" id="confirm"></li>
                                        <li class="INSUFFICIENT_RELIABLE_EVIDENCE_to_RATE" id="confirm"></li>
                                        <li class="POSSIBLY_EFFECTIVE" id="confirm"></li>
                                        <li class="LIKELY_EFFECTIVE" id="confirm"></li>
                                        <li class="EFFECTIVE" id="confirm"></li>
                                    </ul>
                                    
                            </div>
                            <div class="acc-pro-text">Beta carotene is found to be effective for men between 35-45....</div>
                        </div>
                        <div class="acc-repeated">
                            <div class="acc-top-action">
                                <label class="custom-checkbox">Aging skin
                                    <input type="checkbox" checked="checked">
                                    <span class="checkmark"></span>
                                </label>
                                <div class="acc-action">
                                    <a href="#"><img  src="{{asset('images/acc-edit.png')}}" alt="acc-edit"></a>
                                    <a href="#"><img  src="{{asset('images/acc-delete.png')}}" alt="acc-delete"></a>
                                </div>   
                            </div>
                            <div class="acc-progress">
                                    <div class="acc-efficany"><img  src="{{asset('images/trc-acc.png')}}" alt="trc-acc"> Known Efficacy</div> 
                                    <span>Inconclusive</span> 
                                    <ul class="acc-progress-list" id="progressbar" style="height: auto !important;">
                                        
                                        <li class="INEFFECTIVE " id="confirm"></li>
                                        <li class="LIKELY_INEFFECTIVE" id="confirm"></li>
                                        <li class="POSSIBLY_INEFFECTIVE active" id="confirm"></li>
                                        <li class="INSUFFICIENT_RELIABLE_EVIDENCE_to_RATE" id="confirm"></li>
                                        <li class="POSSIBLY_EFFECTIVE" id="confirm"></li>
                                        <li class="LIKELY_EFFECTIVE" id="confirm"></li>
                                        <li class="EFFECTIVE" id="confirm"></li>
                                    </ul>
                                    
                            </div>
                            <div class="acc-pro-text">Beta carotene is found to be effective for men between 35-45....</div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-head" id="headingTwo">
                    <h2 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Collapsible Group Item #2
                    </h2>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                    <div class="card-body">
                        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                    </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-head" id="headingThree">
                    <h2 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Collapsible Group Item #3
                    </h2>
                    </div>
                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                    <div class="card-body">
                        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                    </div>
                    </div>
                 </div>
             </div>
         
    
    
 
</div>

@endsection

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
              window.location.href = "{{route('therapy-integrative', '')}}"+"/"+item.canonicalName;
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
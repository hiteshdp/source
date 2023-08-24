@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp

@section('title', __(!empty($metaTitle) ? $metaTitle : 'Natural medicines effective for '.$condition['conditionName']))
@section('meta-keywords', __(!empty($metaKeywords) ? $metaKeywords : $condition['conditionName'].', possibly effective, ineffective, inconclusive, evidence, efficacy'))
@section('meta-news-keywords', __(!empty($metaNewsKeywords) ? $metaNewsKeywords : $condition['conditionName'].', possibly effective, ineffective, inconclusive, evidence, efficacy'))
@section('meta-description', __(!empty($metaDescription) ? $metaDescription : 'Find evidence on safety and effectivenss of natural therapies for '.$condition['conditionName'].'.'))

@section('og-url', Request::url())
@section('og-title', __(!empty($metaOgTitle) ? $metaOgTitle : 'Natural medicines effective for '.$condition['conditionName']))
@section('og-description', __(!empty($metaOgDescription) ? $metaOgDescription : 'Find evidence on safety and effectivenss of natural therapies for '.$condition['conditionName']))

@section('content')
<div class="container floating pt-3">
    <!-- <h1 class="h3 mt-4 mb-3">Looking for a specific therapy?</h1>
  <div class="form-group position-relative">
    <input type="sideeffect" class="form-control therapy" id="sideeffect" aria-describedby="emailHelp" placeholder=" " autocomplete="off">
    <label class="float-label" for="sideeffect">Search by therapy name</label>
  </div> -->
    
    <!-- Single search bar code - start -->
    <div class="right-search round-search mb-0 text-left">
        <div class="input-group rounded mt-1">
        <span class="input-group-text border-0" id="search-addon">
                <img width="18"  src="{{asset('images/search-blue.svg')}}" alt="search" title="search">
            </span>
            <input type="search" class="form-control  therapy" placeholder=" " aria-label="Search"
            aria-describedby="search-addon" />
            <label for="email" class="float-label mob-font">Search natural medicines by name or medical condition</label>
           
        </div>
        <div class="small-text pl-3 pt-2">Didn't find what you were looking? Submit your suggestion by clicking the chat icon bottom right.  </div>
    </div>
    <!-- Single search bar code - end -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <!--<h2 class="tab-title mt-3 mb-3">Mucositis and mouth sores</h2>
  <p>Mucositis is the painful inflammation and ulceration of the mucous membranes lining the digestive tract, usually as an adverse effect of chemotherapy and radiotherapy treatment for cancer. Mucositis can occur anywhere along the gastrointestinal (GI) tract, but oral mucositis refers to the particular inflammation and ulceration that occurs in the mouth. Oral mucositis is a common and often debilitating complication of cancer treatment</p>-->

    @if(count($finalArray) > 0)
  <h1 class="h4 text-center mt-4 mb-3">{{ $condition['conditionName']}} Efficacy Chart</h1>
    <!--Accordion wrapper-->
  <div class="accordion md-accordion" id="accordionEx" role="tablist" aria-multiselectable="true">
        @php $n = 0 @endphp
        @foreach ($finalArray as $key => $value)
        <div class="info-evi">
            @if($key == 'INCONCLUSIVE EVIDENCE')
                <a tabindex="0" class="evidence-info ml-3" data-placement="top" role="button" data-toggle="popover" data-html="true" data-trigger="focus" data-content="Inconclusive Evidence category is associated with therapeutic uses where there is not enough evidence, positive or negative, for Natural Medicines<sup>TM</sup> to assign a rating. In such cases, either the strength of the evidence is not strong enough to rate or there are conflicting research studies with different findings. Wellkasa recommends users to review the research evidence provided and discuss with a qualified medical provider before using any therapies with Inconclusive Evidence."><img class="mb-1" width="18" height="18" src="{{asset('images/info.svg')}}" alt="Info"></a>
            @endif
        </div> 
        <!-- Accordion card -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header" role="tab" id="headingTwo2">
            <a class="collapsed" data-toggle="collapse" data-parent="#accordionEx" href="#collapseTwo{{$n = $n+1}}"
                aria-expanded="false" aria-controls="collapseTwo{{$n}}">
                <h3 class="mb-0">
                <span class="{{$effectiveness_color[$key]}}"></span> <span class="acc-title"> {{ $key }} </span> 
                </h3>
            </a>
            </div>

            <!-- Card body -->
            <div id="collapseTwo{{$n}}" class="collapse" role="tabpanel" aria-labelledby="headingTwo2"
                data-parent="#accordionEx">
            <div class="card-body">
                <ul class="side-effect-list">
                    <?php
                        $valueSort = array_column($value, 'therapy');
                        array_multisort($valueSort, SORT_ASC, $value);
                    ?>
                    @foreach ($value as $v_key => $v_value)
                    <li>
                        <a href="{{route('therapy', $v_value['canonicalName'])}}"> {{ $v_value['therapy'] }}</a>
                    </li>
                    @endforeach
                </ul>
                <?php
                    $valueCount = count($value);
                    if ($valueCount > 3) { ?>
                        <a class="showMore">Show more</a>
                        <a class="showLess">Show less</a>
                <?php
                    }
                ?>
            </div>
            </div>
        </div>
        <!-- Accordion card -->
        @endforeach
    @else
        <div class="alert alert-warning">
            <strong>Sorry!</strong> No Therapy Found.
        </div>
    @endif
  </div>
<!-- Accordion wrapper -->
    <div class="text-center mt-4 mb-4 d-flex flex-column">
        <a href="{{config('constants.Footer_TRC_URL')}}" target="_blank">  
        <img width="290" height="60" src="{{asset('images/trclogo.png')}}" alt="trclogo" title="trclogo">
        </a>
        <span>Licensed from Therapeutic Research Center, LLC</span>
        <span>Copyright © 1995-{{date('Y')}} by Therapeutic Research Center, LLC. All Rights Reserved.</span>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">

    // Check after 1 sec to check if page is loaded from back button then reload again to take condition value in session - code start
    setTimeout(() => {
        window.onpageshow = function (event) {
            if (event.persisted) {
                window.location.reload(); //reload page if it has been loaded from cache
            }
        };
    }, 100);
    // Check after 1 sec to check if page is loaded from back button then reload again to take condition value in session - code end


    // Display details from info icon next to inconclusive evidence for label
    $(function () {
        $('[data-toggle="popover"]').popover()
    })
    
    //<!-- Single search bar logic code - start -->
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
    //<!-- Single search bar logic code - end -->

    // Show More-Less Functionality starts
    $(document).ready(function () {

        //Use this inside your document ready jQuery 
        // $(window).on('popstate', function() {
        //     location.reload(true);
        // });
        

        // Display first accordion default
        $("a[href='#collapseTwo1']").trigger("click");

        $('.showLess').hide();

        var myRow = 0;
        $(".side-effect-list").each(function (){
            myRow = myRow+1;
            $(this).attr("id", "myList" + myRow);     
        })

        // Taking all UL inside lists
        $('.card-body > ul').each(function(){
            // Hiding all list elements except first 3
            $(this).find('li').slice(3).hide();
        });
    
        // Handling clicks to "show more" links
        $('.showMore').on('click', function() {

            // Searching for previous UL in it
            var $list = $(this).prev('ul');

            // displaying all the remaining list elements
            $list.find('li:hidden').slice(0,10000).show();

            //If last list item is already visible - hide "show more" link
            if ($list.find('li:last-child').is(':visible')) {
                $list.next('.showMore').hide();
                $(this).next('.showLess').show();
            }
        })


        // Handling clicks to "show more" links
        $('.showLess').on('click', function() {

            $(this).prev().prev('.card-body > ul').each(function(){
                // Hiding all list elements except first 3
                $(this).find('li').slice(3).hide();
                $(this).next().next('.showLess').hide();
                $(this).next('.showMore').show();
            });
        })
    });
    // Show More-Less Functionality ends
</script>
@endpush
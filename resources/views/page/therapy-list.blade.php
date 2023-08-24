@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp
                
@section('title', __('Wellkasa - Therapies List'))
@section('meta-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-news-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-description', __('Wellkasa - Understanding the use prudent use of antimicrobials and their Health Canada categories'))

@section('content')
<div class="container">
   <ul class="nav nav-tabs d-none" id="myTab" role="tablist">
      <li class="nav-item">
         <a class="nav-link " id="diagnosis-tab" data-toggle="tab" href="#diagnosis" role="tab" aria-controls="diagnosis" aria-selected="true">Diagnosis & Care Team</a>
      </li>
      <li class="nav-item">
         <a class="nav-link active" id="therapy-tab" data-toggle="tab" href="#therapy" role="tab" aria-controls="therapy" aria-selected="false">Find Therapy </a>
      </li>
      <li class="nav-item">
         <a class="nav-link" id="getcare-tab" data-toggle="tab" href="#getcare" role="tab" aria-controls="getcare" aria-selected="false">Get care</a>
      </li>
   </ul>
   <div class="tab-content floating" id="myTabContent">
      <div class="tab-pane fade " id="diagnosis" role="tabpanel" aria-labelledby="diagnosis-tab">...</div>
      <div class="tab-pane fade show active" id="therapy" role="tabpanel" aria-labelledby="therapy-tab">
      
      <h1 class="h3 pt-2 pb-2">Browse Therapies</h1>
      <div class="form-group">
        <input type="sideeffect" class="form-control therapy" id="sideeffect" aria-describedby="emailHelp" placeholder=" " autocomplete="off">
        <label class="float-label" for="sideeffect">Search by therapy name</label>
      </div>
      <div class="browse-list-view">
      <ul class="side-effect-list list-view">
            @foreach ($therapy as $key => $value)
            <li>
                <a  href="{{route('therapy', $value->id)}}">{{ $value->therapy }}</a>
            </li>
            @endforeach
        </ul>
        <ul class="alphabet-short">
        <li><a class="alphabets act" href="#" data-letter="ALL">ALL</a></li>  
            @foreach (range('A', 'Z') as $column)
            <li>
                <a class="alphabets" style="cursor: pointer;" data-value="0" data-letter="{{ $column }}">{{ $column }}</a>
            </li>
            @endforeach
        </ul>
        </div>    
      </div>
      <div class="tab-pane fade" id="getcare" role="tabpanel" aria-labelledby="getcare-tab">...</div>
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

    // Sort listing of therapy by alphabet selected 
    jQuery(".alphabets").click(function(){
        jQuery(".alphabets").removeClass("act");
        jQuery(this).addClass("act");

        let url = "<?= route('therapy-list')?>";
        let letter = $(this).attr("data-letter");
        $.ajax({
            url:url,
            method:'GET',
            data:{'letter' : letter},
            success:function(data){
                if(data != ''){
                    jQuery(".side-effect-list").html('');
                    jQuery(".side-effect-list").html(data);
                }else{
                    jQuery(".side-effect-list").html('<h3> No Therapy List Available With The Letter '+letter+'. </h3>');
                }
            }
        })
    });

</script>
@endpush
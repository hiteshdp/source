@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp
                
@section('meta-keywords', __('Wellkasa, Integrative therapies, natural medicines'))
@section('meta-news-keywords', __('Wellkasa, Integrative therapies, natural medicines'))
@section('meta-description', __('Home for your personalized evidence informed integrative care journey'))
@section('title', __('Home for your personalized evidence informed integrative care journey.'))


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
         <div class="page-title text-center">
            <h1>My Wellkasa</h1>
         </div>
         <div class="row pb-3 floating">
            <div class="col-md-12">
               <!-- Single search bar code - start -->
               <div class="right-search round-search  mb-0 text-left w-100 form-group mobsearch">
                  <div class="input-group rounded mt-1">
                  <span class="input-group-text border-0" id="search-addon">
                        <img width="18"  src="{{asset('images/search-blue.svg')}}" alt="search" title="search">
                     </span>
                     <input type="search" class="form-control  therapy" placeholder=" " aria-label="Search"
                     aria-describedby="search-addon" />
                     <label for="email" class="float-label">Search natural medicines by name or medical condition</label>
                    
                  </div>
                  <div class="small-text pl-3 pt-2">Didn't find what you were looking? Submit your suggestion by clicking the chat icon bottom right.  </div>
               </div>
               
               <!-- Single search bar code - end -->
               
            </div>
            <!-- <div class="col-8 d-none">
           
               <a data-toggle="tooltip" data-placement="top" title="Find Integrative Therapy" class="btn btn-green" href="#">Find Integrative Therapy</a>
              
            </div> -->
            <div class="col-12 col-md-8">
            <h4 class="mt-2 mob-font-inner">My Integrative Therapies</h4>
            </div>
            <div class="col-12 col-md-4 text-right">
            <div class="dropdown">
               <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               Sort by: A-Z
               </button>
               <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <a class="dropdown-item d-none" href="{{route('search-wellkasa', 'ratingsdesc')}}">Effectiveness Rating <img class="ml-1"  width="15" src="{{asset('images/desc.svg')}}" alt="user" title="user"></a>
                  <!-- <a class="dropdown-item" href="{{ route('search-wellkasa','ratingsasc') }}">Rating Asc</a> -->
                  <a class="dropdown-item" href="{{ route('search-wellkasa','therapyasc') }}">Therapy Name A-Z</a>
                  <a class="dropdown-item" href="{{ route('search-wellkasa','therapydesc') }}">Therapy Name Z-A</a>
               </div>
               </div>
            </div>
         </div>
         
         @if(sizeof($my_therapy)!=0)
            @foreach ($my_therapy as $key => $value)
               <div class="rating-star-block">
                  <div class="media mb-3">
                     <img  class="mr-4 d-none" src="{{asset('images/img01.png')}}" alt="user" title="user">
                     <div class="media-body">
                        <div class="row">
                           <div class="col-8 col-sm-6">
                              <h2 class="type-title"><a class="type-title" href="{{route('therapy',$value['therapy']['canonicalName'])}}">{{$value['therapy']['therapy']}}</a> </h2>
                           </div>
                           <div class="col-4 col-sm-6 text-right">
                              <a class="edit mr-3" href="{{route('edit-therapy',$value['id'])}}"><img  src="{{asset('images/edit.svg')}}" alt="edit" title="edit"></a>
                              <a class="edit" style="cursor:pointer;" onClick="deleteTherapy({{$value['id']}})"><img  src="{{asset('images/delete.png')}}" alt="delete" title="delete"></a>
                           </div>
                        </div>
                        <span class="type-text"> Type:  {{!empty($value['therapy']['therapyType']) ? $value['therapy']['therapyType'] : '-'}}</span><br>
                        <span class="type-text"> Added On:  {{ date('d M Y' , strtotime($value['created_at'])) }} </span><br>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-12 col-md-12 d-none">
                        <ul class="rating-sart mb-3">
                           <li class="mr-1">My Effectiveness</li>
                           <?php
                              for($x=1;$x<=($value['ratings']);$x++) {
                                 ?><li class="mr-1"><a><img width="20" src="{{asset('images/star-act.svg')}}" alt="star ratings" title="star"></a></li><?php
                              }
                              $remainingstars = 5-($value['ratings']);
                              for ($y=0; $y<$remainingstars; $y++) {
                                 ?><li class="mr-1"><a><img width="20" src="{{asset('images/star.svg')}}" alt="star" title="star"></a></li><?php
                              }
                           ?>
                        </ul>
                     </div>
                     <div class="col-12 col-md-12 mb-12">
                        @if(!empty($value['conditions']))
                           @foreach($value['conditions'] as $conditionsKey => $conditionsValue)
                              @if(!empty($conditionsValue['conditionId']))
                                 <a type="button" href="{{route('condition',$conditionsValue['canonicalName'])}}" class="btn btn-outline-dark btn-sm mb-2">{{$conditionsValue['conditionName']}}</a>   
                              @else
                                 <a type="button" title="other text" class="btn btn-outline-dark btn-sm mb-2">{{ $conditionsValue['otherText'] }}</a> 
                              @endif  
                           @endforeach
                        @endif 
                     </div>
                  </div>
                     
                  
                  @if(!empty($value['note']))
                     @foreach($value['note'] as $noteKey => $noteValue)
                        @if(strlen($noteValue['notes']) > 130)
                           <p class="pb-2">
                           <span class="text-info">{{$noteValue['date']}}</span> - {{substr($noteValue['notes'],0,130)}}<span class="read-more-show hide_content"> More<i class="fa fa-angle-down"></i></span><span class="read-more-content">{{substr($noteValue['notes'],130,strlen($noteValue['notes']))}}<span class="read-more-hide hide_content"> Less <i class="fa fa-angle-up"></i></span> </span></p>
                        @else
                           <p class="pb-2"><span class="text-info">{{$noteValue['date']}}</span> - {{$noteValue['notes']}}</p>
                        @endif
                     @endforeach
                  @endif

               </div>
            @endforeach
         @endif
         <!-- If no records found then execute this code start -->
         @if(sizeof($my_therapy)==0)
            <div class="rating-star-block">
               <div class="h5 blue-color">​Build and track your integrative medicines here:</div>
               <span>
               1. Start by searching for natural medicines above. <br>
               2. Once you find your therapy, click “Save to myWellkasa”.<br>
               3. Come back to myWellkasa and click on the edit button to journal or take notes.

               </span>
               <div class="h5 pt-4 blue-color">Coming soon: Automated interaction checker – Free!</div>
               <p>Checks if your natural medicines interact with your prescription drugs</p>
            </div>
         @endif
         <!-- If no records found then execute this code end -->

      <!-- <a data-toggle="tooltip" data-placement="left" title="Add Integrative Therapy" class="add-btn" href="#"><img  src="{{asset('images/add.svg')}}" alt="add" title="add"></a> -->
      
      </div>
   </div>
</div>

<div class="modal fade" id="deleteTherapyConfirmation">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="therapy-modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="therapy-modal-body">
       
      </div>
      <input type="hidden" name="therapyModalId" value="" id="therapyModalId">
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button"  id="modalYes" class="btn btn-green modalYes">Yes</button>
      </div>
    </div>
  </div>
</div>
@endsection
@push('styles')
<style type="text/css">
    .read-more-show{
      cursor:pointer;
      color: #35C0ED;
      font-weight: 700;
    }
    .read-more-hide{
      cursor:pointer;
      color: #35C0ED;
      font-weight: 700;
    }

    .hide_content{
      display: none;
    }
</style>
@endpush
@push('scripts')
<script type="text/javascript">

// Local acordionToOpenSiteUrl url in localstorage then redirect therapy details page
if(localStorage.getItem("acordionToOpen") != null && localStorage.getItem("acordionToOpenSiteUrl") != null){ // check if accordionToOpen localStorage has value
   var acordionToOpenSiteUrl = localStorage.getItem("acordionToOpenSiteUrl");
   window.location.href = acordionToOpenSiteUrl;
}
   

   $(function () {
   $('[data-toggle="tooltip"]').tooltip()
   })

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

   // Hide the extra content initially, using JS so that if JS is disabled, no problemo:
    $('.read-more-content').addClass('hide_content')
            $('.read-more-show, .read-more-hide').removeClass('hide_content')

            // Set up the toggle effect:
            $('.read-more-show').on('click', function(e) {
              $(this).next('.read-more-content').removeClass('hide_content');
              $(this).addClass('hide_content');
              e.preventDefault();
            });

            // Changes contributed by @diego-rzg
            $('.read-more-hide').on('click', function(e) {
              var p = $(this).parent('.read-more-content');
              p.addClass('hide_content');
              p.prev('.read-more-show').removeClass('hide_content'); // Hide only the preceding "Read More"
              e.preventDefault();
            });

//----------------------------------  Delete Therapy Pop up --------------------------- //

function deleteTherapy(id){
    // Set modal title
    $('#therapy-modal-title').html('Delete therapy confirmation');
   
    // Set body
    $('#therapy-modal-body').html('Are you sure you want to delete this therapy details?');

    // Show Modal
    $('#deleteTherapyConfirmation').modal('show');
    
    // Set hiddan input type value 
    $('#therapyModalId').val(id);
}

//----------------------------------  Delete Therapy API  --------------------------- //

// Call Ajax for Delete Therapy
$('.modalYes').on('click',function()
{   
    
    var modalId = $('#therapyModalId').val();
    var csrf_token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: "delete-therapy",
        type : 'delete',
        dataType: "json",
        "data":{ _token: csrf_token,"usertherapyId":modalId},
        success: function(res){
            if(res.status == 0){
               $('#deleteTherapyConfirmation').modal('hide');
               window.location.href = "{{ route('my-wellkasa') }}"
            }
            else
            {
               window.location.href = "{{ route('my-wellkasa') }}"                      
            }
        }
    });    
    
});

</script>
@endpush
@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp
                
@section('title', __('Wellkasa - Edit Therapy'))
@section('meta-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-news-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-description', __('Wellkasa - Understanding the use prudent use of antimicrobials and their Health Canada categories'))

@section('content')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous"> 
<div class="container">
   <ul class="nav nav-tabs getcaretab d-none" id="myTab" role="tablist">
      <li class="nav-item">
         <a class="nav-link text-center" href="{{route('my-profile')}}" aria-controls="diagnosis" aria-selected="true"> <img src="{{asset('images/myprofile.svg')}}" alt="myprofile" title="myprofile"> <span class="tab-name">My <br>Profile </span> </a>
      </li>
      <li class="nav-item">
         <a class="nav-link text-center" id="therapy-tab" data-toggle="tab" href="#therapy" role="tab" aria-controls="therapy" aria-selected="false"><img src="{{asset('images/mycareteam.svg')}}" alt="mycareteam" title="mycareteam"> <span class="tab-name">My <br>Care Team </span></a>
      </li>
      <li class="nav-item">
         <a class="nav-link text-center" id="getcare-tab" data-toggle="tab" href="#getcare" role="tab" aria-controls="getcare" aria-selected="false"><img src="{{asset('images/myappointments.svg')}}" alt="myappointments" title="myappointments"> <span class="tab-name">My <br>Appointments </span></a>
      </li>
      <li class="nav-item">
         <a class="nav-link text-center active" href="{{route('my-wellkasa')}}" aria-controls="mytherapies" aria-selected="false"><img src="{{asset('images/mytherapies.svg')}}" alt="mytherapies" title="mytherapies"> <span class="tab-name">My <br>Therapies </span></a>
      </li>
   </ul>
   <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade " id="diagnosis" role="tabpanel" aria-labelledby="diagnosis-tab">...</div>
      <div class="tab-pane fade" id="therapy" role="tabpanel" aria-labelledby="therapy-tab">...</div>
      <div class="tab-pane fade" id="getcare" role="tabpanel" aria-labelledby="getcare-tab">...</div>
      <div class="tab-pane fade show active" id="mytherapies" role="tabpanel" aria-labelledby="mytherapies-tab">
        
      <div class="media d-none">
        <img  class="mr-4 rounded-circle" src="{{asset('uploads/avatar/'.Auth::user()->avatar)}}" onerror="this.onerror=null;this.src='{{ asset("images/user.jpg") }}';" style="width: 120px; height: 120px;" alt="user" title="user">
        <div class="media-body">
        <h2 class="profile-title">{{ Auth::user()->name." ".Auth::user()->last_name }}</h2>
        </div>
      </div>
      <h1 class="h3 mt-2 text-center">{{$therapyName}}</h1>
      <div class="rating-title text-secondary d-none">
            My Effectiveness
          </div>
        <div class="rating-star-block border-0 d-none">
          
          <div class="rating-title">
          <label>  Rate The Therapy<span class="error">*</span></label>
          </div>   
          <div>
            <i class="fa fa-star fa-2x" data-index="0"></i>
            <i class="fa fa-star fa-2x" data-index="1"></i>
            <i class="fa fa-star fa-2x" data-index="2"></i>
            <i class="fa fa-star fa-2x" data-index="3"></i>
            <i class="fa fa-star fa-2x" data-index="4"></i>
          </div>
          <span id="ratingvalue-error" class="error d-none" for="ratingvalue"></span>
        </div>
        <div class="tab-title mt-3 mb-3 d-none">
          Tell your provider 
          </div>
          <form name="update-therapy-form" id="update-therapy-form" method="post" action="{{url('update-therapy')}}">
            @csrf
            <div class="form-group d-none">
                <label class="d-none" for="provider">Select Provider</label>
                <select name="provider" class="col-lg-12 form-control" id="provider">
                  <option value="">Select Provider</option>
                  @foreach($providerDetails as $value)
                    <option value="{{$value['id']}}" <?php if($userTherapy->provider == $value['id']) {echo 'selected'; } ?> >{{$value['name']}}</option>
                  @endforeach
                </select>
                @error('provider')
                  <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                  @enderror
            </div>           
            

            <!-- Select conditions dropdown start -->
            <div class="floating multi">
              <label class="mb-3">Select Condition<span class="error">*</span></label>
              <div class="form-group select-condition-form-group">
                <label id="select-condition-placeholder" for="condition">Start typing your medical condition(s) and select</label>
                <select placeholder=" " class="form-control js-select mt-4" name="condition[]" id="condition" multiple="multiple">
                  @foreach($conditions as $value)
                    @if(!in_array($value['id'],$selectConditionArray))
                      <option value="{{$value['id']}}" {{ (collect(old('condition'))->contains($value['id'])) ? 'selected':'' }}>{{$value['name']}}</option>
                    @endif
                  @endforeach
                </select>
                <span id="select-conditions-error" class="error d-none" for="condition"></span>
              </div>
            </div>
            <!-- Select conditions dropdown end  -->
 
            <!-- Selected conditions tag code start -->
            <div class="form-group">
              @if(!empty($userTherapyConditionsTags))
                @foreach($userTherapyConditionsTags as $conditionsKey => $conditionsValue)
                <span class="btn btn-outline-dark btn-sm mb-2 conditionTags">
                  @if($conditionsValue['conditionId']!=0)
                    <a type="button" title="{{$conditionsValue['conditionName']}}" href="{{route('condition',$conditionsValue['canonicalName'])}}" class="">
                      {{$conditionsValue['conditionName']}}
                    </a>
                  @else
                    <a type="button" title="other text" class="">
                      {{$conditionsValue['otherText']}}
                    </a>  
                  @endif  
                    <a type="button" onClick="deleteTherapy({{$conditionsValue['id']}})" title="Click here to delete {{ $conditionsValue['conditionName'] ? $conditionsValue['conditionName'] : $conditionsValue['otherText'] }} condition" class="close-btn" aria-label="Close" >
                      <span aria-hidden="true">&times;</span>
                    </a>
                  </span>
                @endforeach
              @endif 
            </div>
            <!-- Selected conditions tag code end -->

            <div class="form-group">
              <label for="description">My Personal Journal </label>
              <textarea name="note" placeholder="Journal your experience with this therapy here" class="form-control" id="description" rows="3">{{old('note')}}</textarea>
              @error('note')
                <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
              @enderror
            </div>

            @if(sizeof($userTherapyHistory)!=0)
              <div class="history">
                @foreach ($userTherapyHistory as $key => $value)
                    @if(!empty($value['note']))
                      @if(strlen($value['note']) > 100)
                        <p ><i class="fa fa-calendar" aria-hidden="true"></i>
                        <span class="text-info">{{$value['date']}}</span> - {{substr($value['note'],0,100)}}<span class="read-more-show hide_content">More<i class="fa fa-angle-down"></i></span><span class="read-more-content">{{substr($value['note'],100,strlen($value['note']))}} <span class="read-more-hide hide_content">Less <i class="fa fa-angle-up"></i></span> </span></p>
                      @else
                        <p ><i class="fa fa-calendar" aria-hidden="true"></i> <span class="text-info">{{$value['date']}}</span> - {{$value['note']}}</p>
                      @endif
                    @endif
                @endforeach
              </div>
            @endif

            <div class="form-group form-check">
              <label class="form-check-label">
                @if($userTherapy->updated_at != '')
                  <input class="form-check-input" type="checkbox" name="shareWithOthers" {{ ($userTherapy->shareWithOthers == '1') ? 'checked' : '' }} > Make My Journal Public 
                @else
                  <input class="form-check-input" type="checkbox" name="shareWithOthers" checked> Make My Journal Public 
                @endif
              </label> 
              <a tabindex="0" class="dd" data-placement="top" role="button" data-toggle="popover" data-trigger="focus" data-content="By checking this box you agree to share your review with other Wellkasa users who may benefit from your experience with this therapy. Uncheck this box to keep your review private."><img class="mb-1" width="18" height="18" src="{{asset('images/info.svg')}}" alt="Info"></a>
            </div>

            <div class="row">
              <div class="col-6 col-md-6">
                <input type="hidden" name="ratings" id="ratingvalue" value="{{$userTherapy->ratings}}">
                <input type="hidden" name="userTherapyId" id="userTherapyId" value="{{$userTherapy->id}}"> 
                <input type="hidden" name="therapyID" id="therapyID" value="{{$userTherapy->therapyID}}">     
                <input type="hidden" name="oldNote" id="oldNote" value="{{$userTherapy->note}}">         
                <input type="hidden" name="provider" id="provider" value="1">    
                <input type="hidden" name="checkCondition" id="checkCondition" value="{{ isset($userTherapyConditionsTags[0]['conditionName']) ? '1' : '0' }}">       
                <a class="btn btn-green pl-5 pr-5" href="{{route('my-wellkasa')}}">Back</a>
              </div>
              <div class="col-6 col-md-6 text-right">
                <button class="update btn btn-green pl-5 pr-5">Update</button>
              </div>
            </div>  
          </form>      
      </div>
   </div>
</div>
<!-- Condition tags delete popup modal code start -->
<div class="modal fade" id="deleteConditionConfirmation">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="condition-modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="condition-modal-body">
       
      </div>
      <input type="hidden" name="usertherapyConditionId" value="" id="usertherapyConditionId">
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button"  id="modalYes" class="btn btn-green modalYes">Yes</button>
      </div>
    </div>
  </div>
</div>
<!-- Condition tags delete popup modal code end -->
@endsection

@push('styles')
<!-- Added select 2 css for condition dropdown -->
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
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
<!-- Added select 2 js for condition dropdown -->
<script src="{{ asset('js/select2.min.js') }}" defer></script>

<script type="text/javascript">

$( document ).ready(function() {

  //Select2  option for condition dropdown  
  $('.js-select').select2({
    closeOnSelect: true,
    tags: true,
    // tokenSeparators: [',', ' ']
    maximumSelectionLength: 10,
    language: {
      maximumSelected: function (e) {
        var t = "You can only select " + e.maximum + " conditions at a time";
        return t;
      }
    }
  }).on('select2:open', (elm) => {
    const targetLabel = $(elm.target).prev('#select-condition-placeholder');
    targetLabel.addClass('selected');
  }).on('select2:close', (elm) => {
    const target = $(elm.target);
    const targetLabel = target.prev('#select-condition-placeholder');
    const targetOptions = $(elm.target.selectedOptions);
    if (targetOptions.length === 0) {
      targetLabel.removeAttr('class');
    }
  });
  // for click on label of select2 dropdown then show label above dropdown
  $('#select-condition-placeholder').on('click', function(){      
    $(this).addClass('selected');
    $('#condition').select2('open');
  });

  resetStarColors();

  setStars(parseInt(<?=$userTherapy->ratings-1?>));

  $('.fa-star').on('click', function(){      
    ratedIndex = parseInt($(this).data('index'));

    $("#ratingvalue").val(ratedIndex+1);
    
    $("#ratingvalue-error").text('');
    $("#ratingvalue-error").hide();
  });

  $('.fa-star').mouseover(function(){
    resetStarColors();

    var currentIndex = parseInt($(this).data('index'));
    setStars(currentIndex);
  });

  $('.fa-star').mouseleave(function(){
    resetStarColors();
    if(ratedIndex != -1){
      setStars(ratedIndex);
    }else{
      setStars(<?=$userTherapy->ratings-1?>);
    }
  });
});

function setStars(max) {
  for (var i=0; i<=max; i++)
      $('.fa-star:eq('+i+')').css('color', '#35C0ED');
}

function resetStarColors() {
  $('.fa-star').css('color', '#dcdcdc');
}

$('#update-therapy-form').validate({ 
  errorElement: 'span', 
  errorClass: 'description-error error',     
  rules:{
    provider : { 
      required : false
    },
    note : { 
      required : false,
    },
  },
  messages:{
    provider : {
      required : "Please select provider name."
    },
    note : {
      required : "Please enter my personal journal.",
    },
  }     
});

//Check rating values not selected
$('.update').on('click',function(event){
  
  /*** Hide rating required code
  
    if(jQuery("#ratingvalue").val() == ''){
      $("#ratingvalue-error").removeClass('d-none');
      $("#ratingvalue-error").text('Please rate the therapy.');
      $("#ratingvalue-error").show();
      $("#update-therapy-form").valid();
      event.preventDefault();
    }else{
      $("#ratingvalue-error").text('');
      $("#ratingvalue-error").hide();
    }

   ***/
  
  
  //Check if conditions selected or not
  checkConditionsValue();

  //Check if other text is entered
  if($("#other_text_div").is(":visible") == true){
    if(jQuery("#otherText").val() == ''){
      $("#otherText-error").removeClass('d-none');
      $("#otherText-error").text('Please enter other medical condition for which you use this therapy.');
      $("#otherText-error").show();
      event.preventDefault();
    }else{
      $("#otherText-error").text('');
      $("#otherText-error").hide();
    } 
  }
});

//Check if conditions selected or not
function checkConditionsValue(){
  
  // Check if conditions tag does not exist
  if($(".conditionTags").length == 0){
    // Check if condition is empty
    if($("#condition").val() == ''){
      $("#select-conditions-error").text('');
      $("#select-conditions-error").text('Please select condition.');
      $("#select-conditions-error").removeClass('d-none');
      event.preventDefault();
    }else{
      // Condition is selected then remove validation message
      $("#select-conditions-error").text('');
      $("#select-conditions-error").addClass('d-none');
    }
  }
  // Remove validation if option selected
  $('#condition').on('select2:closing', function (e){
    if($("#condition").val() != ''){
      $("#select-conditions-error").text('');
      $("#select-conditions-error").addClass('d-none');
    }
  });


  /*** Old Multi-Select dropdown validation
    if(jQuery(".dropdown-label").text() == "Select Conditions"){
      $("#conditions-error").removeClass('d-none');
      $("#conditions-error").text('Please select conditions.');
      $("#conditions-error").show();
      event.preventDefault();
    }else{
      $("#conditions-error").text('');
      $("#conditions-error").show();
    }
  */
  
}

//If selected any conditions from dropdown then remove error field
$( '.dropdown-option' ).click(function() {

  // if unchecked all hide other text field
  if($(this).text() == 'Uncheck All'){
    $('#other_text_div').hide();
    $('#otherText').val('');
  }
  // if checked all show other text field
  else if($(this).text() == 'Check All That Apply'){
    $('#other_text_div').show();
    $('#otherText').val('{{$userTherapy->otherText}}');
  }

  $("#conditions-error").text('');
  $("#conditions-error").show();
  
});

// dropdwon
(function($) {
  var CheckboxDropdown = function(el) {
    var _this = this;
    this.isOpen = false;
    this.areAllChecked = false;
    this.$el = $(el);
    this.$label = this.$el.find('.dropdown-label');
    this.$checkAll = this.$el.find('[data-toggle="check-all"]').first();
    this.$inputs = this.$el.find('[type="checkbox"]');
    
    this.onCheckBox();
    
    this.$label.on('click', function(e) {
      e.preventDefault();
      _this.toggleOpen();
    });
    
    this.$checkAll.on('click', function(e) {
      e.preventDefault();
      _this.onCheckAll();
    });
    
    this.$inputs.on('change', function(e) {
      _this.onCheckBox();
    });
  };
  
  CheckboxDropdown.prototype.onCheckBox = function() {
    this.updateStatus();
  };
  
  CheckboxDropdown.prototype.updateStatus = function() {
    var checked = this.$el.find(':checked');

    this.areAllChecked = false;
    this.$checkAll.html('Check All That Apply');
    
    if(checked.length <= 0) {
      this.$label.html('Select Conditions');
    }
    else if(checked.length <= 9) {
      var selMulti = $.map(checked.parent(), function (el, i) {
          return $(el).text();
      });
      this.$label.html(selMulti.join(", "));

      if(checked.length === this.$inputs.length) {
        var selMulti = $.map(checked.parent(), function (el, i) {
            return $(el).text();
        });
        this.$label.html(selMulti.join(", "));
        this.areAllChecked = true;
        this.$checkAll.html('Uncheck All');
      }
    }
    else {
      this.$label.html(checked.length + ' Selected');
    }
  };
  
  CheckboxDropdown.prototype.onCheckAll = function(checkAll) {
    if(!this.areAllChecked || checkAll) {
      this.areAllChecked = true;
      this.$checkAll.html('Uncheck All');
      this.$inputs.prop('checked', true);
    }
    else {
      this.areAllChecked = false;
      this.$checkAll.html('Check All That Apply');
      this.$inputs.prop('checked', false);
    }
    
    this.updateStatus();
  };
  
  CheckboxDropdown.prototype.toggleOpen = function(forceOpen) {
    var _this = this;
    
    if(!this.isOpen || forceOpen) {
       this.isOpen = true;
       this.$el.addClass('on');
      $(document).on('click', function(e) {
        if(!$(e.target).closest('[data-control]').length) {
         _this.toggleOpen();
        }
      });
    }
    else {
      this.isOpen = false;
      this.$el.removeClass('on');
      $(document).off('click');
    }
  };
  
  var checkboxesDropdowns = document.querySelectorAll('[data-control="checkbox-dropdown"]');
  for(var i = 0, length = checkboxesDropdowns.length; i < length; i++) {
    new CheckboxDropdown(checkboxesDropdowns[i]);
  }
})(jQuery);


$('#other_option').bind('change', function () {

if ($(this).is(':checked'))
  $('#other_text_div').show();
else
  $('#other_text_div').hide();
  $('#otherText').val('');
});
</script>
<script type="text/javascript">
  $(function () {
    $('[data-toggle="popover"]').popover()
  })
</script>
<script>
//----------------------------------  Delete Condition Pop up --------------------------- //

function deleteTherapy(id){
    // Set modal title
    $('#condition-modal-title').html('Delete condition confirmation');
   
    // Set body
    $('#condition-modal-body').html('Are you sure you want to delete this condition?');

    // Show Modal
    $('#deleteConditionConfirmation').modal('show');
    
    // Set hiddan input type value 
    $('#usertherapyConditionId').val(id);
}

//----------------------------------  Delete Condition API  --------------------------- //

// Call Ajax for Delete Condition
$('.modalYes').on('click',function()
{   
    
  var modalId = $('#usertherapyConditionId').val();
  var csrf_token = $('meta[name="csrf-token"]').attr('content');
  $.ajax({
      url: "{{route('delete-condition')}}",
      type : 'delete',
      dataType: "json",
      "data":{ _token: csrf_token,"usertherapyConditionId":modalId},
      success: function(res){
          if(res.status == 0){
            $('#deleteConditionConfirmation').modal('hide');
            location.reload();
          }
          else
          {
            location.reload();
          }
      }
  });        
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

</script>

@endpush
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
<div class="container rx">
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
   <div class="row">
     <div class="col-12 col-lg-8 mx-auto">
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
      <h1 class="h3 mt-2">
        <a href="#">
          <svg width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18.9999 6.99997H4.41394L9.70694 1.70697L8.29294 0.292969L0.585938 7.99997L8.29294 15.707L9.70694 14.293L4.41394 8.99997H18.9999V6.99997Z" fill="#44546A"/>
          </svg>
        </a>
        Update Condition: {{$finalArray['therapyName']}}
      </h1>
      <div class="rating-title text-secondary d-none">My Effectiveness</div>
        <div class="rating-star-block border-0 d-none">
          <div class="rating-title"> <label>Rate The Therapy<span class="error">*</span></label></div>   
          <div>
            <i class="fa fa-star fa-2x" data-index="0"></i>
            <i class="fa fa-star fa-2x" data-index="1"></i>
            <i class="fa fa-star fa-2x" data-index="2"></i>
            <i class="fa fa-star fa-2x" data-index="3"></i>
            <i class="fa fa-star fa-2x" data-index="4"></i>
          </div>
          <span id="ratingvalue-error" class="error d-none" for="ratingvalue"></span>
        </div>
        <div class="tab-title mt-3 mb-3 d-none">Tell your provider</div>
          <form name="updateIntegrativeProtocolCondition" id="updateIntegrativeProtocolCondition" method="post" action="{{url('update-integrative-protocol')}}">
            @csrf
            <!-- Select conditions dropdown start -->
            <div class="floating multi">
              <label class="mb-2">Select Condition<span class="error">*</span></label>
              <div class="form-group select-condition-form-group">
                <!-- <label id="select-condition-placeholder" for="condition">Start typing your medical condition(s) and select</label> -->
                <select placeholder="Breast cancer" class="form-control js-select" name="condition" id="condition">
                  <option value="">-- Select Condition --</option>
                  @foreach($finalArray['conditions'] as $value)
                    @if($value['id'] == $finalArray['conditionId'])
                      <option value="{{$value['id']}}" selected>
                    @else
                      <option value="{{$value['id']}}">
                    @endif
                    {{$value['name']}}</option>
                  @endforeach
                </select>
                <label id="condition-error" class="error" for="condition" style="display: none;"></label>
              </div>
            </div>
            <!-- Select conditions dropdown end  -->
 


            <!--  Progressbar start-->
            <div class="bg-dark-blue rounded mt-2 mt-lg-5 mb-3 mt-lg-5 shadow">
              <div class="row">
                  <div class="col-12 col-md-3 col-lg-3">
                    <div class="d-flex justify-content-center ml-0 align-items-center">
                      <a href="javascript:void(0)" onclick="showAddconditionInfo()" class="rx-popup" style="text-decoration: underline;">Known Efficacy</a>
                      <div class="addcondition-info" id="addconditionId" style="display:none;">
                        <div id="condition-description-content"></div>
                        
                          <div class="info-link-popup">
                            @if(\Auth::user()->getSubscriptionStatus() == '1')
                              <a href="javascript:void(0)" class="dd" id="redirectToConditionSection"  style="display: none;">View therapy monograph</a> 
                            @else
                              <a class="dd" id="renewSubscriptionMsg" href='https://wellkasa.com/products/wellkasa-rx' target="_blank" style="display: none;">Renew subscription to view detail</a>
                            @endif
                          </div>
                       
                      </div> 
                    </div>
                  </div>
                <div class="col-12 col-md-9 col-lg-9 text-center">
                  <ul id="progressbar">
                        <li class="default-dot"></li>
                        <li class="INEFFECTIVE {{ (str_replace(' ', '_', $finalArray['effectiveness']) == 'INEFFECTIVE') ? 'active' : '' }}" id="confirm"><strong>Ineffective</strong></li>
                        <li class="LIKELY_INEFFECTIVE {{ (str_replace(' ', '_', $finalArray['effectiveness']) == 'LIKELY_INEFFECTIVE') ? 'active' : '' }}" id="confirm"><strong>Likely Ineffective</strong></li>
                        <li class="POSSIBLY_INEFFECTIVE {{ (str_replace(' ', '_', $finalArray['effectiveness']) == 'POSSIBLY_INEFFECTIVE') ? 'active' : '' }}" id="confirm"><strong>Possibly Ineffective</strong></li>
                        <li class="INSUFFICIENT_RELIABLE_EVIDENCE_to_RATE {{ (str_replace(' ', '_', $finalArray['effectiveness']) == 'INSUFFICIENT_RELIABLE_EVIDENCE_to_RATE') ? 'active' : '' }}" id="confirm"><strong>Inconclusive</strong></li>
                        <li class="POSSIBLY_EFFECTIVE {{ (str_replace(' ', '_', $finalArray['effectiveness']) == 'POSSIBLY_EFFECTIVE') ? 'active' : '' }}" id="confirm"><strong>Possibly Effective</strong></li>
                        <li class="LIKELY_EFFECTIVE {{ (str_replace(' ', '_', $finalArray['effectiveness']) == 'LIKELY_EFFECTIVE') ? 'active' : '' }}" id="confirm"><strong>Likely Effective</strong></li>
                        <li class="EFFECTIVE {{ (str_replace(' ', '_', $finalArray['effectiveness']) == 'EFFECTIVE') ? 'active' : '' }}" id="confirm"><strong>Effective</strong></li>
                      </ul>
                </div>
              </div>
            </div>
          <!-- Progressbar end -->

          <input type="hidden" name="updateId" value="{{$finalArray['updateId']}}"/>
          <input type="hidden" name="therapyId" value="{{$therapyId}}"/>
          <input type="hidden" id="redirectToConditionId" value="{{$redirectToConditionSection}}" data-redirect-url="{{$therapyRoute}}"/>
            
            <div class="form-group">
              <label for="note">Notes<span class="error">*</span></label>
              <textarea name="note" placeholder="Clinical observation or therapy notes" class="form-control" id="note" rows="3">{{$finalArray['notes']}}</textarea>
              @error('note')
                <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
              @enderror
            </div> 
            <div class="form-group">
                <button type="submit" name="submit" value="submit" id="saveAndExit" class="btn btn-blue">Save & Exit</button>
                <a href="{{route('my-wellkasa-rx')}}" type="button" class="ml-5 color-black">Cancel</a>
            </div> 
          </form>      
      </div>
   </div>
     </div>
   </div>
   
</div>

<!---Modal pop up for previous page redirection confirmation code start---->
<div class="modal fade" id="previousPageConfirmation">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="interaction-modal-title">Warning</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="interaction-modal-body">
        You pressed a back button. Are you sure you want to go back?
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button"  id="modalYes" class="btn btn-green modalYes">Yes</button>
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for previous page redirection confirmation code end---->

@endsection

@push('styles')
<!-- Added select 2 css for condition dropdown -->
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
<style type="text/css">
  #redirectToConditionSection{
    cursor: pointer;
  } 
</style>
@endpush

@push('scripts')
<!-- Added select 2 js for condition dropdown -->
<script src="{{ asset('js/select2.min.js') }}" defer></script>

<script type="text/javascript">

function showAddconditionInfo(){
  var selectConditionVal = $('#condition').val();
  if(selectConditionVal == ''){
    $("#condition-description-content").html("");
    $("#condition-description-content").html("<p>Select condition to view efficacy details</p>");
  }
  $('#addconditionId').toggle();
}

// on back button event
$(function() {
  window.history.pushState({page: 1}, "","");
  window.onpopstate = function(event) {
    if (window.performance && window.performance.navigation.type == window.performance.navigation.TYPE_BACK_FORWARD || event.currentTarget.performance.navigation.type == 2) {
        $('#previousPageConfirmation').modal('show');
        
        $('.modalYes').on('click',function()
        {
          $('#previousPageConfirmation').modal('hide');
          window.location.href = "{{ route('my-wellkasa-rx') }}"
        });
        history.pushState(null,  document.title, location.href); 

    }else{
        $('#previousPageConfirmation').modal('show');
        
        $('.modalYes').on('click',function()
        {
          $('#previousPageConfirmation').modal('hide');
          window.location.href = "{{ route('my-wellkasa-rx') }}"
        });
        history.pushState(null,  document.title, location.href); 

    }
  }
});

$( document ).ready(function() {

  //Select2  option for condition dropdown  
  $('.js-select').select2({
    closeOnSelect: true,
    tags: false,
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


  // hide condition error on selected option
  $('#condition').on('change', function (e) {
    var optionSelected = $("option:selected", this);
    var valueSelected = this.value;
    if(valueSelected != ""){
      $("#condition-error").hide()
    }
  });

  //Check rating values not selected
  $('#saveAndExit').on('click',function(event){
    
    $('#updateIntegrativeProtocolCondition').validate({ 
      rules:{
        condition : { 
          required : true
        },
        note : { 
          required : true,
        },
      },
      messages:{
        condition : {
          required : "Please select condition name."
        },
        note : {
          required : "Please enter your notes.",
        },
      },
      submitHandler: function(form) {
        form.submit();
        return false;

      }    
    });

  });

  // close popover on anywhere else in the body except on the popover content
  $('body').on('click', function (e) {
    $('[data-toggle=popover]').each(function () {
        // hide any open popovers when the anywhere else in the body is clicked
        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
          $(this).popover('hide');
        }
    });
  });



});
</script>
<script type="text/javascript">
  $(function () {
    $('[data-toggle="popover"]').popover()
  })

  // jump link to the condition description of the therapy page
  $(function(){
    $(document).on('click',"#redirectToConditionSection",function () {
      var selectedConditionId = $("#redirectToConditionId").val();
      var acordionToOpenSiteUrl = $("#redirectToConditionId").attr("data-redirect-url");
      localStorage.setItem("acordionToOpen", selectedConditionId); // add selected condition id to local storage
      localStorage.setItem("acordionToOpenSiteUrl", acordionToOpenSiteUrl); // add current url to local storage
      localStorage.setItem("acordionToOpenInMobile", selectedConditionId); // add selected condition id for mobileview to local storage
      localStorage.setItem("acordionToOpenSiteUrlInMobile", acordionToOpenSiteUrl); // add current url for mobileview to local storage
      window.location.href = acordionToOpenSiteUrl; // redirect to therapy page with condition description to open
    });
    
  });

</script>
<script>

$(document).ready(function(){

  var defaultDot = "{{$finalArray['effectiveness']}}"

  if(defaultDot!=''){
    $('.default-dot').hide();
  }else{
    $('.default-dot').show();
  }

  var current_fs, next_fs, previous_fs; //fieldsets
  var opacity;
  var current = 1;
  var steps = $("fieldset").length;

  setProgressBar(current);

  $(".next").click(function(){
      current_fs = $(this).parent();
      next_fs = $(this).parent().next();

      //Add Class Active
      $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

      //show the next fieldset
      next_fs.show();
      //hide the current fieldset with style
      current_fs.animate({opacity: 0}, {
      step: function(now) {
      // for making fielset appear animation
      opacity = 1 - now;

      current_fs.css({
      'display': 'none',
      'position': 'relative'
      });
      next_fs.css({'opacity': opacity});
      },
      duration: 500
      });
      setProgressBar(++current);
  });

  $(".previous").click(function(){
      current_fs = $(this).parent();
      previous_fs = $(this).parent().prev();

      //Remove class active
      $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

      //show the previous fieldset
      previous_fs.show();

      //hide the current fieldset with style
      current_fs.animate({opacity: 0}, {
      step: function(now) {
      // for making fielset appear animation
      opacity = 1 - now;

      current_fs.css({
      'display': 'none',
      'position': 'relative'
      });
      previous_fs.css({'opacity': opacity});
      },
      duration: 500
      });
      setProgressBar(--current);
  });

  function setProgressBar(curStep){
    var percent = parseFloat(100 / steps) * curStep;
    percent = percent.toFixed();
    $(".progress-bar")
    .css("width",percent+"%")
  }

  $(".submit").click(function(){
    return false;
  })

});

// display known efficacy condition description if condition is already selected
setTimeout(() => {
  if($('#condition').val() !=''){
    var alreadySelectedConditionVal = $('#condition').val();
    $('#condition').val(alreadySelectedConditionVal).trigger('change');
  }
}, 200);

$("#condition").on('change', function(){
    var conditionId = $(this).val();
    var therapyId = $("input[name='therapyId']").val();
    var csrf_token = $('meta[name="csrf-token"]').attr('content');
    if(conditionId!=''){

      $.ajax({
          url: "{{route('get-therapy-condition')}}",
          type : 'post',
          dataType: "json",
          "data":{ _token: csrf_token,"conditionId":conditionId,"therapyId":therapyId},
          success: function(res){
            var redirectToTherapyPage = res.therapyRoute;
            var redirectToConditionSection = res.redirectToConditionSection;
            var effectiveness = res.effectiveness;
            if(res.therapyDetails!=''){
              $("#condition-description-content").html(res.therapyDetails);
              $("#redirectToConditionSection").show();
            }else{
              $("#condition-description-content").html("<p>Description not found</p>");
              $("#redirectToConditionSection").hide();
            }
            $("#progressbar li").removeClass("active");
            $('.default-dot').hide();
            $("."+effectiveness).addClass("active");

            // Show renew subscription link if there
            if($("#renewSubscriptionMsg")){
              $("#condition-description-content").hide();
              return $("#renewSubscriptionMsg").show();
            } 

            $("#redirectToConditionId").val(redirectToConditionSection); // stores conditionId in hidden type redirectToConditionId input
            $("#redirectToConditionId").attr("data-redirect-url",redirectToTherapyPage); // stores therapy page url where condition description exists
          }
      });
      
    }else{
      // $("#knownEfficacy").attr("data-content",'');
      $("#condition-description-content").html("<p>Select condition to view efficacy details</p>");
      $("#redirectToConditionSection").hide();
      $("#progressbar li").addClass("active");
      $('.default-dot').show();
      $(".INEFFECTIVE").removeClass("active");
      $(".LIKELY_INEFFECTIVE").removeClass("active");
      $(".POSSIBLY_INEFFECTIVE").removeClass("active");
      $(".INSUFFICIENT_RELIABLE_EVIDENCE_to_RATE").removeClass("active");
      $(".POSSIBLY_EFFECTIVE").removeClass("active");
      $(".LIKELY_EFFECTIVE").removeClass("active");
      $(".EFFECTIVE").removeClass("active");
      $("#redirectToConditionId").val(''); // removes value of conditionId in hidden type redirectToConditionId input
      $("#redirectToConditionId").attr("data-redirect-url",''); // removes value of therapy page url where condition description exists
    }  

});
</script>

@endpush
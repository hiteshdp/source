
@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp

@section('title', __('Effectiveness and safety of '.$therapy_detail['name'].' for different medical conditions.'))
@section('meta-keywords', __($therapy_detail['name'].', effective, evidence, safety concerns, dosage, interactions'))
@section('meta-news-keywords', __($therapy_detail['name'].', effective, evidence, safety concerns, dosage, interactions'))
@section('meta-description', __('Evidence informed answers to questions on effectivessness and safety of '.$therapy_detail['name'].' for specific medical conditions.'))

@section('og-url', Request::url())
@section('og-title', __('Effectiveness and safety of '.$therapy_detail['name'].' for different medical conditions.'))
@section('og-description'){!!'Evidence informed answers to questions on effectivessness and safety of '.$therapy_detail['name'].' for specific medical conditions.'!!}@stop

@if ($agent->isMobile())
<!-- Mobile View Start -->

@section('content')

@if(!empty($therapy_detail))
<div class="container mobile floating pt-3">
    <!-- Single search bar code - start -->
    <div class="right-search round-search mb-0 text-left">
      <div class="input-group rounded mt-1 ">
      <span class="input-group-text border-0" id="search-addon">
            <img width="18"  src="{{asset('images/search-blue.svg')}}" alt="search" title="search">
          </span>
          <input type="search" class="form-control  therapy " placeholder=" " aria-label="Search"
          aria-describedby="search-addon" />
          <label for="email" class="float-label mob-font">Search natural medicines by name or medical condition</label>
          
      </div>
      <div class="small-text pl-3  pt-2">Didn't find what you were looking? Submit your suggestion by clicking the chat icon bottom right.  </div>
    </div>
    <!-- Single search bar code - end -->
    <div class="info-details">
      <h1 class="h3 mt-4 text-center mb-3">{{$therapy_detail['name']}}</h1>
        <a href ="JavaScript:void​(0);" id="infomobile" class="info-mobile" > <img class="mt-0"  src="{{asset('images/infoblue.svg')}}" alt="Info"> </a>
        <div id="infomobilediv" class="consumer-info consumer-info-mobile" style="display:none;">
        <div class="text-center infomobileText"><p>Consumer Information and Education based on Natural Medicines<sup>TM</sup></p></div> 
        This monograph was last reviewed on {{$therapyReviewedAt}} and last updated on {{$therapyUpdatedAt}}. 
        Monographs are reviewed and/or updated multiple times per month and at least once per year. 
        If you have comments or suggestions on something that should be reviewed or included, please <a href="javascript:void(Tawk_API.toggle())">tell us.</a> 
      </div>  
    </div>
    <div class="row">
    <div class="col-12 ">
        @if(!empty($therapyConditionsFinalArray))
          <select class="form-control categoryOption conditionsSelectOption">
            @if(!empty($sessionConditionDetails))
              @foreach ($sessionConditionDetails as $sessionConditionDetailsVal)
                <optgroup label=""> 
                  <option value='#{{$sessionConditionDetailsVal["conditionsId"]}}'>{{$sessionConditionDetailsVal["conditionsText"]}}</option>
                <optgroup>  
              @endforeach 
            @else
              @foreach ($therapyConditionsFinalArray as $conditionsVal)
                  <optgroup label=""> 
                    <option value='#{{$conditionsVal["conditionsId"]}}'>{{$conditionsVal["conditionsText"]}}</option>
                  <optgroup>  
              @endforeach 
            @endif   
          </select>
        @else
          <span class="no-condition">No conditions available for this therapy.</span>
        @endif  


    </div>
</div>  
<select class="form-control categoryOption categoryOptionPanel">
  <?php $optionNo = 0;?>
  @foreach ($therapy_detail as $key => $value)
    <?php
      if($key == '' || $key == 'id' || $key == 'name' || $key == 'reviewed-at' || $key == 'updated-at' || $key == 'type'){
        continue;
      }
      // Replace effective title name
      // if( $key == "effective" ){
      //   $effectiveTitle = array("effective");
      //   $effectiveReplaceTitle = array("effective for");
      //   $key = str_replace($effectiveTitle,$effectiveReplaceTitle,$key); 
      // }
      $firstLetterCapital = ucfirst($key);
      $spaceInHeader = str_replace("-", " ", $firstLetterCapital);
      
      

      // Replace title name
      $title = array("Description","Effectiveness header","Likely effective","Possibly effective","Possibly ineffective","Likely ineffective","Ineffective","Insufficient evidence","Action","Safety","Drug interactions","Herb interactions","Food interactions","Dosage","Other names");
      $replaceTitle = array("What is it?","Is it Effective?","Likely effective","Possibly effective","Possibly ineffective","Likely ineffective","Ineffective","Inconclusive evidence","How does it work?","Are there safety concerns?","Are there any interactions with medications?","Are there any interactions with Herbs and Supplements?","Are there interactions with Foods?","What dose is used?","What Are The Other Names For This Therapy?");
      $spaceInHeader = str_replace($title, $replaceTitle, $spaceInHeader);
      
      $blueColor = array("Name", "Is it Effective?", "Effective", "Likely effective", "Possibly effective", "Possibly ineffective", "Likely ineffective", "Ineffective", "Inconclusive evidence");
      $isItEffectiveAccordions = array("Effective","Likely effective", "Possibly effective", "Possibly ineffective", "Likely ineffective", "Ineffective", "Inconclusive evidence");
      $areThereAnyInteractionAccordions = array("Are there any interactions with medications?","Are there any interactions with Herbs and Supplements?","Are there interactions with Foods?");
      $areThereAnyInteractionAccordions1 = array("Are there any interactions with Herbs and Supplements?","Are there interactions with Foods?");
    ?>
    @if(!in_array($spaceInHeader,$isItEffectiveAccordions) && !in_array($spaceInHeader,$areThereAnyInteractionAccordions1))
      @if($spaceInHeader == "Are there any interactions with medications?")
        <?php $spaceInHeader = 'Are there any interactions?'; ?>
      @else
        <?php $spaceInHeader; ?>
      @endif
      <option value="{{str_replace(array( '(', ')', '-', ' ','/','?','.'),'',$spaceInHeader)}}" id="option{{$optionNo}}">{{$spaceInHeader}}</option>
    @endif
    <?php $optionNo++; ?>
  @endforeach
</select>

  <!--Accordion wrapper-->
  <div class="accordion md-accordion main-accordion mt-1" id="accordionEx" role="tablist" aria-multiselectable="true" style="display: none;">
  @php $n = 0 @endphp
  @foreach ($therapy_detail as $key => $value)

    <?php
      if($key == 'id' || $key == 'type')
      {
          continue;
      }

      if($key == 'name')
      { ?>
      <?php 
      continue;
      }
      // Remove reviewed At & updated At display
      if($key == 'reviewed-at' || $key == 'updated-at'){
        continue;
      }
      
      // Replace effective title name
      if( $key == "effective" ){
        $effectiveTitle = array("effective");
        $effectiveReplaceTitle = array("Effective");
        $key = str_replace($effectiveTitle,$effectiveReplaceTitle,$key); 
      }
      
      $firstLetterCapital = ucfirst($key);
      $spaceInHeader = str_replace("-", " ", $firstLetterCapital);

      // Replace title name
      $title = array("Description","Effectiveness header","Likely effective","Possibly effective","Possibly ineffective","Likely ineffective","Ineffective","Insufficient evidence","Action","Safety","Drug interactions","Herb interactions","Food interactions","Dosage","Other names");
      $replaceTitle = array("What is it?","Is it Effective?","Likely effective","Possibly effective","Possibly ineffective for","Likely ineffective","Ineffective","Inconclusive evidence","How does it work?","Are there safety concerns?","Are there any interactions with medications?","Are there any interactions with Herbs and Supplements?","Are there interactions with Foods?","What dose is used?","What Are The Other Names For This Therapy?");
      $spaceInHeader = str_replace($title, $replaceTitle, $spaceInHeader);
      
      $blueColor = array("Name", "Is it Effective?", "Effective", "Likely effective", "Possibly effective", "Possibly ineffective", "Likely ineffective", "Ineffective", "Inconclusive evidence");
      $isItEffectiveAccordions = array("Effective","Likely effective", "Possibly effective", "Possibly ineffective", "Likely ineffective", "Ineffective", "Inconclusive evidence");
      $areThereAnyInteractionAccordions = array("Are there any interactions with medications?","Are there any interactions with Herbs and Supplements?","Are there interactions with Foods?");
      $areThereAnyInteractionAccordions1 = array("Are there any interactions with Herbs and Supplements?","Are there interactions with Foods?");
    ?>
    @if(!in_array($spaceInHeader,$isItEffectiveAccordions) && !in_array($spaceInHeader,$areThereAnyInteractionAccordions1))
      <?php if($spaceInHeader == "Are there any interactions with medications?"){ $spaceInHeader = 'Are there any interactions?';} else{ $spaceInHeader;}?>
      <!-- Accordion card -->
      <div class="card mb-2 section-accordion border-0" id="{{str_replace(array( '(', ')', '-', ' ','/','?','.'),'',$spaceInHeader)}}" style="display: none;">
          <!-- Card header -->
          <div class="card-header d-none" role="tab" id="headingTwo2">
            <a class="collapsed" data-toggle="collapse" data-parent="#accordionEx" href="#collapseTwo{{$n = $n+1}}"
                aria-expanded="false" aria-controls="collapseTwo{{$n}}">
                <span class="blue-line"></span>
                <h3 class="mb-0 ml-2">
                <span class="acc-title"> <?php echo $spaceInHeader; ?> </span> 
                </h3>
            </a>
          </div>
          
          <!-- Card body -->
        
          <div id="collapseTwo{{$n}}" class="collapse" role="tabpanel" aria-labelledby="headingTwo2" data-parent="#accordionEx">
            <div class="card-body border-0 pt-1 pl-0 pr-0">
              <!-- Avoids interactions content to show code start -->
              @if(!in_array($spaceInHeader,$areThereAnyInteractionAccordions) && $spaceInHeader != 'Are there any interactions?')
                @if(!empty($value))
                  <?php if (in_array($spaceInHeader, $blueColor)) { ?>
                    <div class="black-color">
                      <?php 
                        $word = "scientific evidence";
                        $wordLink = 'https://naturalmedicines.therapeuticresearch.com/about-us/editorial-principles-and-process.aspx';
                        if(strpos($value,$word)){
                          echo str_replace($word,'<a href="'.$wordLink.'" target="_blank" style="text-decoration: underline;">'.$word.'</a>',$value);
                        }else{
                          echo $value;
                        }
                      ?>
                    </div>
                    <?php
                    } else { ?>
                    <p><?php echo $value; ?></p><?php
                    }   
                  ?>
                @else
                  <?php echo "No data available."?>
                @endif
                <?php $var = 211; if($spaceInHeader == 'Is it Effective?'){?>
                    <!-- <div class="info-list">
                      <ul >
                        <li>
                          <span class="circle  green-new"></span>EFFECTIVE
                        </li>
                        <li>
                          <span class="circle light-green-new"></span>LIKELY EFFECTIVE
                        </li>
                        <li>
                          <span class="circle light-orange-new"></span>POSSIBLY EFFECTIVE
                        </li>
                        <li>
                          <span class="circle red-new"></span>LIKELY INEFFECTIVE
                        </li>
                        <li>
                          <span class="circle gray-new"></span>INSUFFICIENT RELIABLE EVIDENCE TO RATE
                        </li>
                      </ul> 
                  </div> -->
                  @if(!empty($therapyFinalArr))
                    <!-- Accordion card -->
                    <div class="accordion md-accordion mt-3" id="accordionEx500" role="tablist" aria-multiselectable="true">
                      @foreach($therapyFinalArr as $therapyFinalArrKey => $therapyFinalArrData)
                        @if(!empty($therapyFinalArrData['data']))
                        <div class="info-evi">
                          @if($therapyFinalArrKey == 'Inconclusive evidence')
                            <a tabindex="0" class="evidence-info" data-placement="top" role="button" data-toggle="popover" data-html="true" data-content="Inconclusive Evidence category is associated with therapeutic uses where there is not enough evidence, positive or negative, for Natural Medicines<sup>TM</sup> to assign a rating. In such cases, either the strength of the evidence is not strong enough to rate or there are conflicting research studies with different findings. Wellkasa recommends users to review the research evidence provided and discuss with a qualified medical provider before using any therapies with Inconclusive Evidence."><img class="mb-1 info-icon" width="18" height="18" src="{{asset('images/info.svg')}}" alt="Info"></a>
                          @endif
                        </div>
                          <!-- card start -->
                          <div class="card mb-2">
                            <!-- Card header start -->
                            <div class="card-header" role="tab" id="headingTwo{{$var}}">
                              <a class="collapsed" data-toggle="collapse" href="#collapseTwo{{$var}}"
                                  aria-expanded="false" aria-controls="collapseTwo{{$var}}">
                                  <span class="circle accordion-circle  {{$therapyFinalArrData['color']}}"></span>
                                  <h3 class="mb-0 ml-4">
                                  <span class="acc-title"> <?php echo $therapyFinalArrKey; ?> </span> 
                                  </h3>
                              </a>
                              
                            </div>
                            <!-- Card header end -->

                            <!-- Card body start -->
                            <div id="collapseTwo{{$var}}" class="collapse" role="tabpanel" aria-labelledby="headingTwo{{$var}}" data-parent="#accordionEx500">
                              <div class="card-body">
                              
                              <?php 
                                // if user is logged in then show details in accordion else hide the details and show login button
                                $isLoggedInUser = \Auth::check();
                                if($isLoggedInUser == '1'){
                                  if (in_array($therapyFinalArrKey, $blueColor)) { ?>
                                  <div class="black-color">
                                    <?php 
                                      echo "<ul>";
                                        foreach ($therapyFinalArrData['data'] as $dataKey => $dataValue) {
                                          echo "<li>".$dataValue."</li>"; 
                                        }
                                      echo "</ul>";
                                    ?>
                                  </div>
                                  <?php
                                  } else { ?>
                                  <p>
                                    <?php
                                      echo "<ul>";
                                        foreach ($therapyFinalArrData['data'] as $dataKey => $dataValue) {
                                          echo "<li>".$dataValue."</li>"; 
                                        }
                                      echo "</ul>";
                                    ?>
                                  </p><?php
                                  } 
                                  $var = $var+1;   
                                } else{

                                  if (in_array($therapyFinalArrKey, $blueColor)) { ?>
                                  <div class="black-color">
                                    <?php 
                                      echo "<ul>";
                                        // $variablesMobile = $therapyFinalArrData['data'][0];
                                        // if( strpos( $variablesMobile, 'Details' ) !== false) {
                                        //   $variablesMobile = substr($variablesMobile, 0, strpos($variablesMobile, '<b>Details</b>:'));
                                        // }
                                        // echo "<li>".$variablesMobile."</li>"; 
                                        // // echo "<span> <a href=".route('login').">Login for details</a></span>"; 
                                        // echo "<span> <a href='javascript:void(0)' class='seeDetailsInMobile' id='seeDetailsInMobile".$var."' data-accord-mobile='collapseTwo".$var."'>Login for details</a></span>";
                                        
                                        foreach ($therapyFinalArrData['data'] as $dataKey => $dataValue) {
                                          // check if conditions has details data then hide details data and show login for details button else show data without details
                                          $checkDetailsValueInMobile = $dataValue;
                                          if( strpos( $checkDetailsValueInMobile, 'Details' ) !== false) {
                                            $showDataWithoutDetailsInMobile = substr($checkDetailsValueInMobile, 0, strpos($checkDetailsValueInMobile, '<b>Details</b>:'));
                                            echo "<li>".$showDataWithoutDetailsInMobile."</li>"; 
                                            echo "<span> <a href='javascript:void(0)' class='seeDetailsInMobile' id='seeDetailsInMobile".$var."' data-accord-mobile='collapseTwo".$var."'>Login for details</a></span><br><br>";
                                          }else{
                                            echo "<li>".$dataValue."</li>"; 
                                          }
                                        }

                                      echo "</ul>";
                                    ?>
                                  </div>
                                  <?php
                                  } 
                                  else { ?>
                                  <p>
                                    <?php
                                    
                                      echo "<ul>";
                                        foreach ($therapyFinalArrData['data'] as $dataKey => $dataValue) {
                                          // check if conditions has details data then hide details data and show login for details button else show data without details
                                          $checkDetailsValueInMobile = $dataValue;
                                          if( strpos( $checkDetailsValueInMobile, 'Details' ) !== false) {
                                            $showDataWithoutDetailsInMobile = substr($checkDetailsValueInMobile, 0, strpos($checkDetailsValueInMobile, '<b>Details</b>:'));
                                            echo "<li>".$showDataWithoutDetailsInMobile."</li>"; 
                                            echo "<span> <a href='javascript:void(0)' class='seeDetailsInMobile' id='seeDetailsInMobile".$var."' data-accord-mobile='collapseTwo".$var."'>Login for details</a></span><br><br>";
                                          }else{
                                            echo "<li>".$dataValue."</li>"; 
                                          }
                                        }
                                      echo "</ul>";
                                    ?>
                                  </p><?php
                                  } 
                                  $var = $var+1;
                                }

                              ?>
                              </div>
                            </div>
                            <!-- Card body end -->
                          </div>
                          <!-- card end -->
                        @else
                        <div class="info-evi">
                        @if($therapyFinalArrKey == 'Inconclusive evidence')
                          <a tabindex="0" class="evidence-info" data-placement="top" role="button" data-toggle="popover" data-html="true" data-content="Inconclusive Evidence category is associated with therapeutic uses where there is not enough evidence, positive or negative, for Natural Medicines<sup>TM</sup> to assign a rating. In such cases, either the strength of the evidence is not strong enough to rate or there are conflicting research studies with different findings. Wellkasa recommends users to review the research evidence provided and discuss with a qualified medical provider before using any therapies with Inconclusive Evidence."><img class="mb-1 info-icon" width="18" height="18" src="{{asset('images/info.svg')}}" alt="Info"></a>
                        @endif
                        </div>
                        <!-- card start -->
                        <div class="card mb-2">
                          <!-- Card header start -->
                          <div class="card-header disabled" role="tab" id="headingTwo{{$var}}">
                            <a class="collapsed" data-toggle="collapse" href="#"
                                aria-expanded="false" aria-controls="collapseTwo{{$var}}">
                                <span class="circle accordion-circle {{$therapyFinalArrData['color']}}"></span>
                                <h3 class="mb-0 ml-4">
                                <span class="acc-title"> <?php echo $therapyFinalArrKey; ?> </span> 
                                </h3>
                            </a>
                          </div>
                          
                          <!-- Card header end -->

                          <!-- Card body starts-->
                          <div id="collapseTwo{{$var}}" class="collapse" role="tabpanel" aria-labelledby="headingTwo{{$var}}" data-parent="#accordionEx500">
                            <div class="card-body">
                              <?php 
                                echo "No data available.";
                                $var = $var+1;
                              ?>
                            </div>
                          </div>
                          <!-- Card body ends -->
                        </div>
                        <!-- card end -->
                        @endif
                      @endforeach
                    </div>     
                  @else
                  <div class="text-center mt-2 h4">
                    <p>Effective details does not exist</p>
                  </div>
                  @endif
                <?php } ?>
              @endif
              <!-- Avoids interactions content to show code end -->

              <!-- Displays interactions content on sub-accordion to show code start -->
              @if($spaceInHeader == 'Are there any interactions?')
                
                  <?php $variable = 511;?>
                  @if(!empty($therapyInteractiveArr))
                    <!-- Accordion cards -->
                    <div class="accordion md-accordion" id="accordionEx1" role="tablist" aria-multiselectable="true">
                      @foreach($therapyInteractiveArr as $therapyInteractiveArrKey => $therapyInteractiveFinalArr)
                        @foreach($therapyInteractiveFinalArr as $therapyInteractiveFinalArrKey => $therapyInteractiveFinalArrData)
                          @if(!empty($therapyInteractiveFinalArrData['data']))
                            <!-- card start -->
                            <div class="card mb-2">
                              <!-- Card header start -->
                              <div class="card-header" role="tab" id="headingTwo{{$variable}}">
                                <a class="collapsed" data-toggle="collapse" href="#collapseTwo{{$variable}}"
                                    aria-expanded="false" aria-controls="collapseTwo{{$variable}}">
                                    <h3 class="mb-0 ml-2">
                                    <span class="acc-title"> <?php echo $therapyInteractiveFinalArrKey; ?> </span> 
                                    </h3>
                                </a>
                              </div>
                              <!-- Card header end -->

                              <!-- Card body start -->
                              <div id="collapseTwo{{$variable}}" class="collapse" role="tabpanel" aria-labelledby="headingTwo{{$variable}}" data-parent="#accordionEx1">
                                <div class="card-body">
                                
                                  <?php if (in_array($therapyInteractiveFinalArrKey, $blueColor)) { ?>
                                    <div class="black-color"><?php echo $therapyInteractiveFinalArrData['data']; ?></div>
                                    <?php
                                    } else { ?>
                                    <p><?php echo $therapyInteractiveFinalArrData['data']; ?></p><?php
                                    } $variable = $variable+1;   
                                  ?>
                                </div>
                              </div>
                              <!-- Card body end -->
                            </div>
                            <!-- card end -->
                          @else
                            <!-- card start -->
                            <div class="card mb-2">
                              <!-- Card header start -->
                              <div class="card-header" role="tab" id="headingTwo{{$variable}}">
                                <a class="collapsed" data-toggle="collapse" href="#collapseTwo{{$variable}}"
                                    aria-expanded="false" aria-controls="collapseTwo{{$variable}}">
                                    <h3 class="mb-0 ml-4">
                                    <span class="acc-title"> <?php echo $therapyInteractiveFinalArrKey; ?> </span> 
                                    </h3>
                                </a>
                              </div>
                              <!-- Card header end -->

                              <!-- Card body starts-->
                              <div id="collapseTwo{{$variable}}" class="collapse" role="tabpanel" aria-labelledby="headingTwo{{$variable}}" data-parent="#accordionEx">
                                <div class="card-body">
                                  <?php 
                                    echo "No data available.";
                                    $variable = $variable+1;
                                  ?>
                                </div>
                              </div>
                              <!-- Card body ends -->
                            </div>
                            <!-- card end -->
                          @endif
                        @endforeach
                      @endforeach
                    </div>     
                  @endif                  
                
              @endif
              <!-- Displays interactions content on sub-accordion to show code end -->
            </div>
          </div>
      </div>
      <!-- Accordion card -->
    @endif
  @endforeach
    @else
      <div class="alert alert-warning">
          <strong>Sorry!</strong> No Therapy Found.
      </div>
    @endif
  </div>
  <!-- Accordion wrapper -->
  <div class="row">
   <div class="col-6">
    <a id="prev" style="display: none;"><img class="align-baseline"  src="{{asset('images/previous.svg')}}" alt="previous" title="previous"> Previous Category</a>                            
   </div>
   <div class="col-6 text-right">
    <a id="next" style="display: none;">Next Category <img class="align-baseline"  src="{{asset('images/next.svg')}}" alt="next" title="next"></a>
    </div>
  </div>
  <div class="text-center mt-4 mb-4">
    <img   src="{{asset('images/trclogo.png')}}" alt="trclogo" title="trclogo">
  </div>
</div>
@if($therapyCount > 0)
  <a class="mobile-add disabled" tabindex="0" class="dd" data-placement="top" role="button" data-toggle="popover" data-trigger="focus" data-content="This therapy is already added to myWellkasa"><img src="{{asset('images/add-new.svg')}}" alt="add" title="add"></a>
@else
  <a class="mobile-add addToMyWellkasa" href="#"><img src="{{asset('images/add-new.svg')}}" alt="add" title="add"></a>
@endif
@endsection

@push('scripts')
<script type="text/javascript">

$( document ).ready(function() {
  
  var acordionToOpenSiteUrlInMobile = "<?php echo URL::current();?>"; // get current site url

  /**
   *  Add accordion id to open when clicked on Login for details in local storage and redirect to login page
   */
  $('.seeDetailsInMobile').click(function(event) {
    let loginForDetailsButton = $(event.target).parent().prev();
    var selectedConditionId = loginForDetailsButton.find('b').attr('id');
    localStorage.setItem("acordionToOpenInMobile", selectedConditionId); // add selected condition id to local storage
    localStorage.setItem("acordionToOpenSiteUrlInMobile", acordionToOpenSiteUrlInMobile); // add current url to local storage
    window.location.href = "{{route('login')}}"; // redirect to login page
  });

  /**
   *  Opens the accordion from condition id in local storage named acordionToOpenInMobile and deletes localStorage after its opened
   */
  setTimeout(() => {
    
    let isSameMobileSiteUrl = false;
    if(localStorage.getItem("acordionToOpenSiteUrlInMobile") != null){
      if(localStorage.getItem("acordionToOpenSiteUrlInMobile") == acordionToOpenSiteUrlInMobile){
        isSameMobileSiteUrl = true;
      }
    }

    if(localStorage.getItem("acordionToOpenInMobile") != null){ // check if acordionToOpenInMobile localStorage has value

      var acordionToOpen = localStorage.getItem("acordionToOpenInMobile"); // get selected condition id from localStorage
      if($(".select-options li[rel='#"+acordionToOpen+"']").length!='0' && isSameMobileSiteUrl){
        $(".select-options li[rel='isiteffective']").trigger('click');
        $(".select-options li[rel='#"+acordionToOpen+"']").trigger('click');
      }
      localStorage.removeItem("acordionToOpenInMobile"); // deletes the condition id from the local storage key acordionToOpenInMobile
      localStorage.removeItem("acordionToOpenSiteUrlInMobile"); // deletes the site url from the local storage key acordionToOpenSiteUrlInMobile
    }
  }, 800);


  // Hide popup details from info icon next to therapy name when clicked outside anywhere in the document
  $('html').click(function(event) {
    if(event.target != ""){
      setTimeout(() => { // Checks if infomobilediv is visible after 1 second
        // if visible and class name is not empty wherever clicked the execute this code
        if($("#infomobilediv").is(":visible") === true && event.target.innerText || event.target.className != "" || event.target.id != ""){
          if(event.target.id == "infomobilediv" || event.target.id == "infomobile" || $(event.target).parent().hasClass('infomobileText') == true || $(event.target).parent().attr('class') == "info-mobile"){
            // if id name is any above class or id then stop behaviour of hide/show
            event.stopPropagation();
          }else{
            // else hide the info details
            jQuery("#infomobilediv").slideUp();
          }
        }
      }, 100);
    }    
  });
  // Hide popup details from info icon next to therapy name
  $( ".therapy" ).focus(function() {
    jQuery("#infomobilediv").slideUp();
  });

  $('[data-toggle="popover"]').popover()

  // Hide/Show on popover next to "inconclusive evidence for" label
  $('.evidence-info').click(function (e) {
    $('.evidence-info').not(this).popover('hide');
    e.stopPropagation();
  });

  // Hide popover by clicking outside the content or on the content box next to "inconclusive evidence for" label
  $(document).click(function (e) {
    if (($('.popover').has(e.target).length == 0) || $(e.target).is('.close') || $(".popover-body").is(":visible") === true) {
      $('.evidence-info').popover('hide');
    }
  });

  // Default display "What is it?" option selected
  function firstOptionSelected(){
    // $(".categoryOption").val($(".categoryOption option:nth-child(2)").val()).trigger('change');
    $(".select-options li:nth-child(1)").trigger('click');
  }setTimeout(firstOptionSelected, 100);

  var data = { therapyID: "{{$therapyId}}" };
  var jsonString = JSON.stringify(data);

  $('.addToMyWellkasa').on('click',function(){   
    $.ajax({
      url: "<?=route('save-user-integrative-protocol')?>",
      type: "POST",
      data: jsonString,
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      contentType: 'application/json; charset=utf-8',
      success: function (response)
      {
        window.location.href = "{{ route('integrative-list') }}"
      },
      error: function (jqXHR,error) {
        if(jqXHR.status == 401){
          window.location.href = "{{ route('login') }}";
        }
        if(jqXHR.responseJSON.message == "Your email address is not verified."){
          window.location.href = "{{ url('/email/verify') }}";  
        }
      }
    });
  });

  // Opens accordion in which condition details exists
  $(".conditionName").on("click", function() {
    
    // Removes active class from the condition names
    $(".conditionName").removeClass("active");
    // Gets id from the condition name
    var ref = $(this).attr("href");
    // check if condition id exist in sub accordion
    if($("b").is(ref) == true){
      // Adds active class to visible condition name selected
      $(this).addClass("active");
      // Closes all accordion
      $(".collapse").collapse("hide");
      $(".section-accordion").hide();

      // Loads selected condition contents after few seconds
      function displayConditionContents(){
        if($("a[href='#collapseTwo2']").attr('class') == "collapsed" || $("a[href='#collapseTwo2']").hasClass('collapsed') == false){
          $(".main-accordion").show();
          var sectionId = $("b"+ref).parents().find('div.section-accordion').eq(1).attr('id');
          $("div#"+sectionId).removeAttr("style");
          
          // Selects the option from the dropdown
          $(".select-options li[rel='"+sectionId+"']").trigger('click');
          
          // Opens main accordion having conditions accordion
          $("a[href='#collapseTwo2']").removeClass("collapsed").attr("aria-expanded",true);
          $("#collapseTwo2").addClass("collapse show").slideDown(1);

          // Gets the id of the accordion where condition content exists
          var id = $("b"+ref).parents().closest(".collapse").eq(1).attr('id');

          // Opens the accordion where condition content exists
          $("a[href='#"+id+"']").removeClass("collapsed").attr("aria-expanded",true);
          $("#"+id).addClass("collapse show").slideDown(1);

          // Jumps to the content of the condition description
          $('html, body').animate({
            scrollTop: $("b"+ref).offset().top-85
          }, 900);
          
          // Higlights the selected condition name for few seconds
          $('b'+ref).css('backgroundColor', 'yellow');
          $('b'+ref).animate({
              'opacity': '0.5'
          }, 2000, function () {
              $('b'+ref).css({
                  'backgroundColor': '#fff',
                  'opacity': '1'
              });
          });

        }
      }setTimeout(displayConditionContents, 390);
    }else{
      $(".collapse").collapse("hide");
    }
  });

  // Remove active class in card click if any active
  $(".card").on("click", function() {
    if($(".conditionName").hasClass("active") == true){
      $(".conditionName").removeClass("active");
    }
  });

  jQuery("#infomobile").on("click", function() {
    jQuery("#infomobilediv").slideToggle("slow");
  });

  $(".main-accordion .section-accordion").each(function(e) {
    if (e != 0){
      $(this).hide();
    }
  });
    
  $("#next").click(function(){
    if ($(".main-accordion .section-accordion:visible").next().length != 0){
      $(".main-accordion .section-accordion:visible").next().show().prev().hide();
      // Gets the accordion id to open
      var nextAccordion = $(".main-accordion .section-accordion:visible").find('a').attr('href')
      // Opens the accordion from the given link
      $("a[href='" + nextAccordion + "']").trigger("click");
      // Gets the option id to select from the dropdown
      var nextOptionValue = $(".main-accordion .section-accordion:visible").attr('id');
      // Selects the option from given id
      $(".select-options li[rel='"+nextOptionValue+"']").trigger('click');

    }else {
      $(".main-accordion .section-accordion:visible").hide();
      $(".main-accordion .section-accordion:first").show();
      // Gets the accordion id to open
      var nextAccordion = $(".main-accordion .section-accordion:first").find('a').attr('href');
      // Opens the accordion from the given link
      $("a[href='" + nextAccordion + "']").trigger("click");
      // Gets the option id to select from the dropdown
      var nextOptionValue = $(".main-accordion .section-accordion:visible").attr('id');
      // Selects the option from given id
      $(".select-options li[rel='"+nextOptionValue+"']").trigger('click');
    }
    return false;
  });

  $("#prev").click(function(){
    if ($(".main-accordion .section-accordion:visible").prev().length != 0){
      $(".main-accordion .section-accordion:visible").prev().show().next().hide();
      // Gets the previous accordion id to open
      var prevAccordion = $(".main-accordion .section-accordion:visible").find('a').attr('href');
      // Opens the accordion from the given link
      $("a[href='" + prevAccordion + "']").trigger("click");
      // Gets the option id to select from the dropdown
      var prevOptionValue = $(".main-accordion .section-accordion:visible").attr('id');
      // Selects the option from given id
      $(".select-options li[rel='"+prevOptionValue+"']").trigger('click');

    }else {
      $(".main-accordion .section-accordion:visible").hide();
      $(".main-accordion .section-accordion:last").show();
      // Gets the previous accordion id to open
      var prevAccordion = $(".main-accordion .section-accordion:last").find('a').attr('href');
      // Opens the accordion from the given link
      $("a[href='" + prevAccordion + "']").trigger("click");
      // Gets the option id to select from the dropdown
      var prevOptionValue = $(".main-accordion .section-accordion:visible").attr('id');
      // Selects the option from given id
      $(".select-options li[rel='"+prevOptionValue+"']").trigger('click');
    }
    return false;
  });
  

 

  // Changes dropdown design
  $('.categoryOptionPanel').each(function(){
    var $this = $(this), numberOfOptions = $(this).children('option').length;
    $this.addClass('select-hidden'); 
    $this.wrap('<div class="select"></div>');
    $this.after('<div class="select-styled JumptoconditionPanel"></div>');

    var $styledSelect = $this.next('div.select-styled');
    $styledSelect.text($this.children('option').eq(0).text());

    var $list = $('<ul />', {
        'class': 'select-options'
    }).insertAfter($styledSelect);

    for (var i = 0; i < numberOfOptions; i++) {
      $('<li />', {
        text: $this.children('option').eq(i).text(),
        rel: $this.children('option').eq(i).val()
      }).appendTo($list);
    }

    var $listItems = $list.children('li');

    $styledSelect.click(function(e) {
      e.stopPropagation();
      $('div.select-styled.active').not(this).each(function(){
        $(this).removeClass('active').next('ul.select-options').hide();
      });
      $(this).toggleClass('active').next('ul.select-options').toggle();
    });

    $listItems.click(function(e) {
      e.stopPropagation();
      $styledSelect.text($(this).text()).removeClass('active');
      $this.val($(this).attr('rel'));
      $list.hide();
      // Opens the accordion from the selected option
      var optionValue = $this.val();
      if(optionValue != "Select Category"){
        $("#next").show();
        $("#prev").show();
        $(".section-accordion").hide();
        $(".main-accordion").show();
        $("#" + optionValue).show();
        // Gets the id having the same option selected
        var accordion = $("#" + optionValue).find('a').attr('href');
        // Opens the accordion
        if($("a[href='" + accordion + "']") != true){
          $("a[href='"+accordion+"']").removeClass("collapsed").attr("aria-expanded",true);
          $(accordion).addClass("collapse show").slideDown(100);
        }
        
        // Display info icon details on "IS IT EFFECTIVE?" accordion open
        if(optionValue == "IsitEffective"){
          if($(accordion).hasClass('show')){
            $('.evidence-info').popover('show');
            $('.evidence-info').trigger('click');
          }else{
            $('.evidence-info').popover('hide');
          }
        }

      } else{
        $("#next").hide();
        $("#prev").hide();
        $(".main-accordion").hide();
      }
    });

    $(document).click(function() {
      $styledSelect.removeClass('active');
      $list.hide();
    });
      
  });


  // Remove active class in card click if any active
  $(".JumptoconditionPanel").on("click", function() {
    // Reset options
    $(".JumptoconditionSelectOption").empty();
    $('.JumptoconditionSelectOption').append('SELECT CONDITION');
  });



  //<----------------  Start Condition tag mobile dropdown ---------------------------> 
 $('.conditionsSelectOption').each(function(){
    var $this = $(this), numberOfOptions = $(this).children().children('option').length;
    $this.addClass('select-hidden'); 
    $this.wrap('<div class="select"></div>');
    $this.after('<div class="select-styled JumptoconditionSelectOption"></div>');
    var $styledSelect = $this.next('div.select-styled');
    $styledSelect.text($this.children('option').eq(0).text());
    var $list = $('<ul />', {
        'class': 'select-options'
    }).insertAfter($styledSelect);
    
    $('<li />', {
        'id': 'Jumptocondition',
        text: 'SELECT CONDITION',
        rel: ''
      }).appendTo($list);
    for (var i = 0; i < numberOfOptions; i++) {
      $('<li />', {
        text: $this.children().children('option').eq(i).text(),
        rel: $this.children().children('option').eq(i).val()
      }).appendTo($list);
    }    
    
    var $listItems = $list.children('li');
    $styledSelect.click(function(e) {
      e.stopPropagation();
      $('div.select-styled.active').not(this).each(function(){
        $(this).removeClass('active').next('ul.select-options').hide();
      });
      $(this).toggleClass('active').next('ul.select-options').toggle();
    });

    
    //<---------------- Option Click logic ---------------------------> 
    $listItems.click(function(e) {
      $('#Jumptocondition').remove();

      e.stopPropagation();
      $styledSelect.text($(this).text()).removeClass('active');
      $this.val($(this).attr('rel'));
      $list.hide();
      // Opens the accordion from the selected option
      var optionValue = $this.val();
      if(optionValue != "SELECT CONDITION"){
        // Removes active class from the condition names
        $(".conditionName").removeClass("active");

        // Gets id from the condition name
        var ref = optionValue;

        // check if condition id exist in sub accordion
        if($("b").is(ref) == true){
          
          // Closes all accordion
          $(".collapse").collapse("hide");
          // Loads selected condition contents after few seconds
          function displayConditionContents(){
            if($("a[href='#collapseTwo2']").attr('class') == "collapsed" || $("a[href='#collapseTwo2']").hasClass('collapsed') == false){
              
              // Opens main accordion having conditions accordion
              var sectionId = $("b"+ref).parents().find('div.section-accordion').eq(1).attr('id');
              $("div#"+sectionId).removeAttr("style");
              $(".select-options li[rel='"+sectionId+"']").trigger('click');              

              // Gets the id of the accordion where condition content exists
              var id = $("b"+ref).parents().closest(".collapse").eq(1).attr('id');

              // Opens the accordion where condition content exists
              $("a[href='#"+id+"']").removeClass("collapsed").attr("aria-expanded",true);
              $("#"+id).addClass("collapse show").slideDown(1);

              // Jumps to the content of the condition description
              $('html, body').animate({
                scrollTop: $("b"+ref).offset().top-90
              }, 900);

              // Higlights the selected condition name for few seconds
              $('b'+ref).css('backgroundColor', 'yellow');
              $('b'+ref).animate({
                  'opacity': '0.5'
              }, 2000, function () {
                  $('b'+ref).css({
                      'backgroundColor': '#fff',
                      'opacity': '1'
                  });
              });
            }
          }setTimeout(displayConditionContents, 500);

        }else{
          $(".collapse").collapse("hide");
        }
      } 
    });

    $(document).click(function() {
      $styledSelect.removeClass('active');
      $list.hide();
    });
      
  });
 //<----------------  End Condition tag mobile dropdown ---------------------------> 

});

</script>

@endpush


<!-- Mobile View End -->
@else


<!-- Desktop View Start -->
@section('content')
<div class="container floating pt-3">
@if(!empty($therapy_detail))
 <!--Accordion wrapper-->
 <div class="accordion md-accordion" id="accordionEx" role="tablist" aria-multiselectable="true">
        @php $n = 0 @endphp
        @foreach ($therapy_detail as $key => $value)

        <?php
            if($key == 'id' || $key == 'type')
            {
                continue;
            }

            if($key == 'name')
            { ?>
            <!-- Single search bar code - start -->
            <div class="right-search round-search mb-0 text-left">
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
              <div class="info-details">
                <h1 class="text-center mt-4 mb-3"><?php echo $value; ?><a href ="JavaScript:void​(0);" id="infomobile" class="position-relative top-minus-two"> <img src="{{asset('images/infoblue.svg')}}" alt="Info"> </a></h1>
                <div id="infomobilediv" class="consumer-info consumer-info-mobile" style="display:none;">
                  <div class="text-center infomobileText">
                    <p>Consumer Information and Education based on Natural Medicines<sup>TM</sup></p>
                  </div> 
                  This monograph was last reviewed on {{$therapyReviewedAt}} and last updated on {{$therapyUpdatedAt}}. 
                  Monographs are reviewed and/or updated multiple times per month and at least once per year. 
                  If you have comments or suggestions on something that should be reviewed or included, please <a href="javascript:void(Tawk_API.toggle())">tell us.</a> 
                </div>
              </div>
              <div class="col-12 dark-blue-bg" >
              <div class="row">
                <div class="col-12 col-md-8">
                 
                <div class="btn-group-view">
                      <a class="btn btn-green mr-3 d-none" href="#"> <span class="plus-icon"> + </span> Add to myCart</a>
                      @if($therapyCount > 0)
                      <button href="#" class="btn btn-add disabled d-none" aria-disabled="true">Added to my Wellkasa</button>
                      @else
                      <button class="btn btn-add addToMyWellkasa d-none">Add to my Wellkasa</button>
                      @endif
              </div>
              
              @if(!empty($therapyConditionsFinalArray))
                <select class="form-control categoryOption" style="width:100% !important;">
                  <option value="">Filter by condition</option>
                  @if(!empty($sessionConditionDetails))
                    @foreach ($sessionConditionDetails as $sessionConditionDetailsVal)
                      <optgroup label=""> 
                        <option value='#{{$sessionConditionDetailsVal["conditionsId"]}}'>{{$sessionConditionDetailsVal["conditionsText"]}}</option>
                      <optgroup>  
                    @endforeach 
                  @else
                    @foreach ($therapyConditionsFinalArray as $conditionsVal)
                        <optgroup label=""> 
                          <option value='#{{$conditionsVal["conditionsId"]}}'>{{$conditionsVal["conditionsText"]}}</option>
                        </optgroup> 
                    @endforeach
                  @endif    
                </select>
                
              @else
                <span class="no-condition">No conditions available for this therapy.</span>
              @endif  
              
                </div>
                <div class="col-md-4 text-right">
                <div class="btn-group-view">
                @if($therapyCount > 0)
                  <a class="btn btn-green  disabled" href="#">Saved to my Wellkasa</a>
                @else
                  <a class="btn btn-green  addToMyWellkasa" href="#">Save to my Wellkasa</a>
                @endif
              </div> 
                </div>
            </div>
              
            </div> 
            <?php 
            continue;
            }
            // Remove reviewed At & updated At display
            if($key == 'reviewed-at' || $key == 'updated-at'){
              continue;
            }
            
            // Replace effective title name
            if( $key == "effective" ){
              $effectiveTitle = array("effective");
              $effectiveReplaceTitle = array("Effective");
              $key = str_replace($effectiveTitle,$effectiveReplaceTitle,$key); 
            }
            
            $firstLetterCapital = ucfirst($key);
            $spaceInHeader = str_replace("-", " ", $firstLetterCapital);

            // Replace title name
            $title = array("Description","Effectiveness header","Likely effective","Possibly effective","Possibly ineffective","Likely ineffective","Ineffective","Insufficient evidence","Action","Safety","Drug interactions","Herb interactions","Food interactions","Dosage","Other names");
            $replaceTitle = array("What is it?","Is it Effective?","Likely effective","Possibly effective","Possibly ineffective","Likely ineffective","Ineffective","Inconclusive evidence","How does it work?","Are there safety concerns?","Are there any interactions with medications?","Are there any interactions with Herbs and Supplements?","Are there interactions with Foods?","What dose is used?","What Are The Other Names For This Therapy?");
            $spaceInHeader = str_replace($title, $replaceTitle, $spaceInHeader);
            
            $blueColor = array("Name", "Is it Effective?", "Effective", "Likely effective", "Possibly effective", "Possibly ineffective", "Likely ineffective", "Ineffective", "Inconclusive evidence");
            $isItEffectiveAccordions = array("Effective","Likely effective", "Possibly effective", "Possibly ineffective", "Likely ineffective", "Ineffective", "Inconclusive evidence");
            $areThereAnyInteractionAccordions = array("Are there any interactions with medications?","Are there any interactions with Herbs and Supplements?","Are there interactions with Foods?");
            $areThereAnyInteractionAccordions1 = array("Are there any interactions with Herbs and Supplements?","Are there interactions with Foods?");
        ?>
        @if(!in_array($spaceInHeader,$isItEffectiveAccordions) && !in_array($spaceInHeader,$areThereAnyInteractionAccordions1))
        <!-- Accordion card -->
        <div class="card mb-2">
            <!-- Card header -->
            <div class="card-header" role="tab" id="headingTwo2">
              <a class="collapsed" data-toggle="collapse" data-parent="#accordionEx" href="#collapseTwo{{$n = $n+1}}"
                  aria-expanded="false" aria-controls="collapseTwo{{$n}}">
                  <span class="blue-line"></span>
                  <h3 class="mb-0 ml-2">
                  <span class="acc-title"> 
                    <?php
                      if($spaceInHeader == "Are there any interactions with medications?"){
                        echo "Are there any Interactions?"; 
                      }else{
                        echo $spaceInHeader; 
                      }
                      
                    ?> 
                  </span> 
                  </h3>
              </a>
            </div>

            <!-- Card body -->
           
            <div id="collapseTwo{{$n}}" class="collapse" role="tabpanel" aria-labelledby="headingTwo2" data-parent="#accordionEx">
              <div class="card-body">
                <!-- Avoids interactions content to show code start -->
                @if(!in_array($spaceInHeader,$areThereAnyInteractionAccordions))
                  @if(!empty($value))
                    <?php if (in_array($spaceInHeader, $blueColor)) { ?>
                      <div class="black-color">
                        <?php 
                          $word = "scientific evidence";
                          $wordLink = 'https://naturalmedicines.therapeuticresearch.com/about-us/editorial-principles-and-process.aspx';
                          if(strpos($value,$word)){
                            echo str_replace($word,'<a href="'.$wordLink.'" target="_blank" style="text-decoration: underline;">'.$word.'</a>',$value);
                          }else{
                            echo $value;
                          }
                        ?>
                      </div>
                      <?php
                      } else { ?>
                      <p><?php echo $value; ?></p><?php
                      }   
                    ?>
                  @else
                    <?php echo "No data available."?>
                  @endif
                  <?php $var = 211; if($spaceInHeader == 'Is it Effective?'){?>
                      <!-- <div class="info-list">
                        <ul >
                          <li>
                            <span class="circle  green-new"></span>EFFECTIVE
                          </li>
                          <li>
                            <span class="circle light-green-new"></span>LIKELY EFFECTIVE
                          </li>
                          <li>
                            <span class="circle light-orange-new"></span>POSSIBLY EFFECTIVE
                          </li>
                          <li>
                            <span class="circle red-new"></span>LIKELY INEFFECTIVE
                          </li>
                          <li>
                            <span class="circle gray-new"></span>INSUFFICIENT RELIABLE EVIDENCE TO RATE
                          </li>
                        </ul> 
                    </div> -->
                    @if(!empty($therapyFinalArr))
                      <!-- Accordion card -->
                      <div class="accordion md-accordion mt-3" id="accordionEx500" role="tablist" aria-multiselectable="true">
                        @foreach($therapyFinalArr as $therapyFinalArrKey => $therapyFinalArrData)
                          @if(!empty($therapyFinalArrData['data']))
                            <!-- card start -->
                            <div class="info-evi">
                                @if($therapyFinalArrKey == 'Inconclusive evidence')
                                <div id="wrap">
                                  <a data-container="#wrap" tabindex="0" id="evidence-info-id" class="evidence-info" data-placement="top" role="button" data-toggle="popover" data-html="true"  data-content="Inconclusive Evidence category is associated with therapeutic uses where there is not enough evidence, positive or negative, for Natural Medicines<sup>TM</sup> to assign a rating. In such cases, either the strength of the evidence is not strong enough to rate or there are conflicting research studies with different findings. Wellkasa recommends users to review the research evidence provided and discuss with a qualified medical provider before using any therapies with Inconclusive Evidence."><img class="mb-1 info-icon ml-2" width="18" height="18" src="{{asset('images/info.svg')}}" alt="Info"></a>
                                  </div>
                                @endif
                            </div>  
                            <div class="card mb-2">
                              <!-- Card header start -->
                              <div class="card-header" role="tab" id="headingTwo{{$var}}">
                                <a class="collapsed" data-toggle="collapse" href="#collapseTwo{{$var}}"
                                    aria-expanded="false" aria-controls="collapseTwo{{$var}}">
                                    <span class="circle accordion-circle  {{$therapyFinalArrData['color']}}"></span>
                                    <h3 class="mb-0 ml-4">
                                    <span class="acc-title"> <?php echo $therapyFinalArrKey; ?> </span> 
                                    </h3>
                                </a>
                               
                              </div>
                              <!-- Card header end -->

                              <!-- Card body start -->
                              <div id="collapseTwo{{$var}}" class="collapse" role="tabpanel" aria-labelledby="headingTwo{{$var}}" data-parent="#accordionEx500">
                                <div class="card-body">
                                
                                  <?php 
                                    // if user is logged in then show details in accordion else hide the details and show login button
                                    $isLoggedInUser = \Auth::check();
                                    if($isLoggedInUser == '1'){
                                      if (in_array($therapyFinalArrKey, $blueColor)) { ?>
                                      <div class="black-color">
                                        <?php 
                                          echo "<ul>";
                                            foreach ($therapyFinalArrData['data'] as $dataKey => $dataValue) {
                                              echo "<li>".$dataValue."</li>"; 
                                            }
                                          echo "</ul>";
                                        ?>
                                      </div>
                                      <?php
                                      } else { ?>
                                      <p>
                                        <?php
                                          echo "<ul>";
                                            foreach ($therapyFinalArrData['data'] as $dataKey => $dataValue) {
                                              echo "<li>".$dataValue."</li>"; 
                                            }
                                          echo "</ul>";
                                        ?>
                                      </p><?php
                                      } 
                                      $var = $var+1;   
                                    } else{

                                      if (in_array($therapyFinalArrKey, $blueColor)) { ?>
                                      <div class="black-color">
                                        <?php 
                                          echo "<ul>";
                                            foreach ($therapyFinalArrData['data'] as $dataKey => $dataValue) {
                                              // check if conditions has details data then hide details data and show login for details button else show data without details
                                              $checkDetailsValueInDesktop = $dataValue;
                                              if( strpos( $checkDetailsValueInDesktop, 'Details' ) !== false) {
                                                $showDataWithoutDetailsInDesktop = substr($checkDetailsValueInDesktop, 0, strpos($checkDetailsValueInDesktop, '<b>Details</b>:'));
                                                echo "<li>".$showDataWithoutDetailsInDesktop."</li>"; 
                                                echo "<span> <a href='javascript:void(0)' class='seeDetails' data-accord='collapseTwo".$var."' id='seeDetails".$var."' >Login for details</a></span><br><br>";
                                              }else{
                                                echo "<li>".$dataValue."</li>"; 
                                              }
                                            }
                                          echo "</ul>";
                                        ?>
                                      </div>
                                      <?php
                                      } 
                                      else { ?>
                                      <p>
                                        <?php
                                          echo "<ul>";
                                            foreach ($therapyFinalArrData['data'] as $dataKey => $dataValue) {
                                              // check if conditions has details data then hide details data and show login for details button else show data without details
                                              $checkDetailsValueInDesktop = $dataValue;
                                              if( strpos( $checkDetailsValueInDesktop, 'Details' ) !== false) {
                                                $showDataWithoutDetailsInDesktop = substr($checkDetailsValueInDesktop, 0, strpos($checkDetailsValueInDesktop, '<b>Details</b>:'));
                                                echo "<li>".$showDataWithoutDetailsInDesktop."</li>"; 
                                                echo "<span> <a href='javascript:void(0)' class='seeDetails' data-accord='collapseTwo".$var."' id='seeDetails".$var."'>Login for details</a></span><br><br>";
                                              }else{
                                                echo "<li>".$dataValue."</li>"; 
                                              }
                                            }
                                          echo "</ul>";
                                        ?>
                                      </p><?php
                                      } 
                                      $var = $var+1;
                                    }

                                  ?>
                                </div>
                              </div>
                              <!-- Card body end -->
                            </div>
                            <!-- card end -->
                          @else
                          <div class="info-evi">
                              @if($therapyFinalArrKey == 'Inconclusive evidence')
                                <a tabindex="0" class="evidence-info" id="evidence-info-id" data-placement="top" role="button" data-toggle="popover" data-html="true" data-content="Inconclusive Evidence category is associated with therapeutic uses where there is not enough evidence, positive or negative, for Natural Medicines<sup>TM</sup> to assign a rating. In such cases, either the strength of the evidence is not strong enough to rate or there are conflicting research studies with different findings. Wellkasa recommends users to review the research evidence provided and discuss with a qualified medical provider before using any therapies with Inconclusive Evidence."><img class="mb-1 info-icon" width="18" height="18" src="{{asset('images/info.svg')}}" alt="Info"></a>
                              @endif
                            </div>
                          <!-- card start -->
                          <div class="card mb-2">
                            <!-- Card header start -->
                            <div class="card-header disabled" role="tab" id="headingTwo{{$var}}">
                              <a class="collapsed" data-toggle="collapse" href="#"
                                  aria-expanded="false" aria-controls="collapseTwo{{$var}}">
                                  <span class="circle accordion-circle {{$therapyFinalArrData['color']}}"></span>
                                  <h3 class="mb-0 ml-4">
                                  <span class="acc-title"> <?php echo $therapyFinalArrKey; ?> </span> 
                                  </h3>
                              </a>
                            </div>
                            <!-- Card header end -->

                            <!-- Card body starts-->
                            <div id="collapseTwo{{$var}}" class="collapse" role="tabpanel" aria-labelledby="headingTwo{{$var}}" data-parent="#accordionEx500">
                              <div class="card-body">
                                <?php 
                                  echo "No data available.";
                                  $var = $var+1;
                                ?>
                              </div>
                            </div>
                            <!-- Card body ends -->
                          </div>
                          <!-- card end -->
                          @endif
                        @endforeach
                      </div>     
                    @else
                    <div class="text-center mt-2 h4">
                      <p>Effective details does not exist</p>
                    </div>
                    @endif
                  <?php } ?>
                @endif
                <!-- Avoids interactions content to show code end -->

                <!-- Displays interactions content on sub-accordion to show code start -->
                @if(in_array($spaceInHeader,$areThereAnyInteractionAccordions))
                  <?php $variable = 511;?>
                  @if(!empty($therapyInteractiveArr))
                    <!-- Accordion card -->
                    <div class="accordion md-accordion" id="accordionEx1" role="tablist" aria-multiselectable="true">
                      @foreach($therapyInteractiveArr as $therapyInteractiveArrKey => $therapyInteractiveFinalArr)
                        @foreach($therapyInteractiveFinalArr as $therapyInteractiveFinalArrKey => $therapyInteractiveFinalArrData)
                          @if(!empty($therapyInteractiveFinalArrData['data']))
                            <!-- card start -->
                            <div class="card mb-2">
                              <!-- Card header start -->
                              <div class="card-header" role="tab" id="headingTwo{{$variable}}">
                                <a class="collapsed" data-toggle="collapse" href="#collapseTwo{{$variable}}"
                                    aria-expanded="false" aria-controls="collapseTwo{{$variable}}">
                                    <h3 class="mb-0 ml-2">
                                    <span class="acc-title"> <?php echo $therapyInteractiveFinalArrKey; ?> </span> 
                                    </h3>
                                </a>
                              </div>
                              <!-- Card header end -->

                              <!-- Card body start -->
                              <div id="collapseTwo{{$variable}}" class="collapse" role="tabpanel" aria-labelledby="headingTwo{{$variable}}" data-parent="#accordionEx1">
                                <div class="card-body">
                                
                                  <?php if (in_array($therapyInteractiveFinalArrKey, $blueColor)) { ?>
                                    <div class="black-color"><?php echo $therapyInteractiveFinalArrData['data']; ?></div>
                                    <?php
                                    } else { ?>
                                    <p><?php echo $therapyInteractiveFinalArrData['data']; ?></p><?php
                                    } $variable = $variable+1;   
                                  ?>
                                </div>
                              </div>
                              <!-- Card body end -->
                            </div>
                            <!-- card end -->
                          @else
                            <!-- card start -->
                            <div class="card mb-2">
                              <!-- Card header start -->
                              <div class="card-header" role="tab" id="headingTwo{{$variable}}">
                                <a class="collapsed" data-toggle="collapse" href="#collapseTwo{{$variable}}"
                                    aria-expanded="false" aria-controls="collapseTwo{{$variable}}">
                                    <h3 class="mb-0 ml-4">
                                    <span class="acc-title"> <?php echo $therapyInteractiveFinalArrKey; ?> </span> 
                                    </h3>
                                </a>
                              </div>
                              <!-- Card header end -->

                              <!-- Card body starts-->
                              <div id="collapseTwo{{$variable}}" class="collapse" role="tabpanel" aria-labelledby="headingTwo{{$variable}}" data-parent="#accordionEx">
                                <div class="card-body">
                                  <?php 
                                    echo "No data available.";
                                    $variable = $variable+1;
                                  ?>
                                </div>
                              </div>
                              <!-- Card body ends -->
                            </div>
                            <!-- card end -->
                          @endif
                        @endforeach
                      @endforeach
                    </div>     
                  @endif                  
                @endif
                <!-- Displays interactions content on sub-accordion to show code end -->
              </div>
            </div>
        </div>
        <!-- Accordion card -->
        @endif
        @endforeach
    @else
        <div class="alert alert-warning">
            <strong>Sorry!</strong> No Therapy Found.
        </div>
    @endif
  </div>
<!-- Accordion wrapper -->

  <div class="text-center mt-4 mb-4">
    <img   src="{{asset('images/trclogo.png')}}" alt="trclogo" title="trclogo">
  </div>
</div>
@endsection

@push('styles')
<style>
#wrap .popover {
    /* margin-left:270px; */
    min-width: 92%;
    left:0 !important;
    transform: translate3d(4px, -106px, 0px) !important;
    font-size:16px;
    
}
#wrap .arrow { left:260px !important; }

</style>
@endpush

@push('scripts')

<script type="text/javascript">
$( document ).ready(function() {
  
  var acordionToOpenSiteUrl = "<?php echo URL::current();?>"; // get current site url

  // open what is it accordion default 
  function openDefaultFirstAccordion(){
    setTimeout(() => {
      $("a[href='#collapseTwo1']").trigger('click');
    }, 100);
  }
  if(localStorage.getItem("acordionToOpen") == null){
    openDefaultFirstAccordion();
  }

  /**
   *  Add accordion id to open when clicked on Login for details in local storage and redirect to login page
   */
  $('.seeDetails').click(function(event) {
    let loginForDetailsButton = $(event.target).parent().prev();
    var selectedConditionId = loginForDetailsButton.find('b').attr('id');
    localStorage.setItem("acordionToOpen", selectedConditionId); // add selected condition id to local storage
    localStorage.setItem("acordionToOpenSiteUrl", acordionToOpenSiteUrl); // add current url to local storage
    window.location.href = "{{route('login')}}"; // redirect to login page
  });


  /**
   *  Opens the accordion from condition id in local storage named accordionToOpen and deletes localStorage after its opened
   */
  setTimeout(() => {
    let isSameSiteUrl = false;
    if(localStorage.getItem("acordionToOpenSiteUrl") != null){
      if(localStorage.getItem("acordionToOpenSiteUrl") == acordionToOpenSiteUrl){
        isSameSiteUrl = true;
      }
    }
    if(localStorage.getItem("acordionToOpen") != null){ // check if accordionToOpen localStorage has value

      var acordionToOpen = localStorage.getItem("acordionToOpen"); // get selected condition id from localStorage
      if($(".select-options li[rel='#"+acordionToOpen+"']").length !='0'  && isSameSiteUrl){
        $(".select-options li[rel='#"+acordionToOpen+"']").trigger('click');
      }else{
        openDefaultFirstAccordion();
      }
      localStorage.removeItem("acordionToOpen"); // deletes the condition id from the local storage key accordionToOpen
      localStorage.removeItem("acordionToOpenSiteUrl"); // deletes the site url from the local storage key acordionToOpenSiteUrl
    }
  }, 500);
  
  $('[data-toggle="popover"]').popover()
  
  // Hide popup details from info icon next to therapy name when clicked outside anywhere in the document
  $('html').click(function(event) {
    if(event.target.innerText != ""){
      setTimeout(() => { // Checks if infomobilediv is visible after 1 second
        // if visible and class name is not empty wherever clicked the execute this code
        if($("#infomobilediv").is(":visible") === true && event.target.className!="" || event.target!=""){
          if(event.target!="" && event.target.id == "infomobilediv" || event.target.id == "infomobile"){
            // if id name is infomobilediv then stop behaviour of hide/show
            event.stopPropagation();
          }else{
            // else hide the info details
            jQuery("#infomobilediv").slideUp();
          }
        }
      }, 100);
    }    
  });

  // Hide/Show on popover next to "inconclusive evidence for" label
  $('#evidence-info-id').click(function (e) {
    $('#evidence-info-id').not(this).popover('hide');
    e.stopPropagation();
  });

  // Hide popover by clicking outside the content or on the content box next to "inconclusive evidence for" label
  $(document).click(function (e) {
    if (($('.popover').has(e.target).length == 0) || $(e.target).is('.close') || $(".popover-body").is(":visible") === true) {
      $('#evidence-info-id').popover('hide');
    }
  });
  
  // Display info icon details on "IS IT EFFECTIVE?" accordion open
  $("a[href='#collapseTwo2']").on("click", function() {
    function displayPopOver(){
      if($("#collapseTwo2").hasClass('show')){
        $('.evidence-info').popover('show')  
      }else{
        $('.evidence-info').popover('hide');
      }
      $('#evidence-info-id').trigger('click');
    }
    setTimeout(displayPopOver, 500)
  });
  
   var data = { therapyID: "{{$therapyId}}" };
   var jsonString = JSON.stringify(data);

  $('.addToMyWellkasa').on('click',function(){   
    $.ajax({
      url: "<?=route('save-user-integrative-protocol')?>",
      type: "POST",
      data: jsonString,
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      contentType: 'application/json; charset=utf-8',
      success: function (response)
      {
        window.location.href = "{{ route('integrative-list') }}"
      },
      error: function (jqXHR,error) {
        if(jqXHR.status == 401){
          window.location.href = "{{ route('login') }}";
        }
        if(jqXHR.responseJSON.message == "Your email address is not verified."){
          window.location.href = "{{ url('/email/verify') }}";  
        }
        
      }
    });
  });
  
  // Opens information next to therapy title icon click
  jQuery("#infomobile").on("click", function() {
    jQuery("#infomobilediv").slideToggle("slow");
  });

  // Remove active class in card click if any active
  $(".card").on("click", function() {
    // Reset options
    $(".jumpToConditionDiv").empty();
    $('.jumpToConditionDiv').append('SELECT CONDITION');

    if($(".conditionName").hasClass("active") == true){
      $(".conditionName").removeClass("active");
    }
  });

});

 //<----------------  Start Condition tag desktop dropdown ---------------------------> 
 $('select').each(function(){
    var $this = $(this), numberOfOptions = $(this).children().children('option').length;
    $this.addClass('select-hidden'); 
    $this.wrap('<div class="select"></div>');
    $this.after('<div class="select-styled jumpToConditionDiv"></div>');

    var $styledSelect = $this.next('div.select-styled');
    $styledSelect.text($this.children('option').eq(0).text());
    var $list = $('<ul />', {
        'class': 'select-options'
    }).insertAfter($styledSelect);
    
    /*var numberOfOptionsGroup = $this.children().length;
    for (var i = 0; i < numberOfOptionsGroup; i++) {
      if($this.children().eq(i).attr('label') != undefined){
        $('<div />', {
          'class': 'tag-title',
          text: $this.children().eq(i).attr('label'),
        }).appendTo($list);

        var numberOfOptions = $this.children().eq(i).children('option').length;
        for (var j = 0; j < numberOfOptions; j++) {
          $('<li />', {
            text: $this.children().children('option').eq(j).text(),
            rel: $this.children().children('option').eq(j).val()
          }).appendTo($list);
        }
      }
    }*/
    for (var i = 0; i < numberOfOptions; i++) {
      $('<li />', {
        text: $this.children().children('option').eq(i).text(),
        rel: $this.children().children('option').eq(i).val()
      }).appendTo($list);
    }    
    
    
    var $listItems = $list.children('li');
    $styledSelect.click(function(e) {
      e.stopPropagation();
      $('div.select-styled.active').not(this).each(function(){
        $(this).removeClass('active').next('ul.select-options').hide();
      });
      $(this).toggleClass('active').next('ul.select-options').toggle();
    });

    //<---------------- Option Click logic ---------------------------> 
    $listItems.click(function(e) {
      e.stopPropagation();
      $styledSelect.text($(this).text()).removeClass('active');
      $this.val($(this).attr('rel'));
      $list.hide();
      // Opens the accordion from the selected option
      var optionValue = $this.val();
      if(optionValue != "SELECT CONDITION"){
        // Removes active class from the condition names
        $(".conditionName").removeClass("active");
        // Adds active class to visible condition name selected
        $(this).addClass("active");
        // Gets id from the condition name
        var ref = $(this).attr("rel");

        // check if condition id exist in sub accordion
        if($("b").is(ref) == true){
          
          // Closes all accordion
          $(".collapse").collapse("hide");
          // Loads selected condition contents after few seconds
          function displayConditionContents(){
            if($("a[href='#collapseTwo2']").attr('class') == "collapsed" || $("a[href='#collapseTwo2']").hasClass('collapsed') == false){
              
              // Opens main accordion having conditions accordion
              $("a[href='#collapseTwo2']").removeClass("collapsed").attr("aria-expanded",true);
              $("#collapseTwo2").addClass("collapse show").slideDown(1);

              // Gets the id of the accordion where condition content exists
              var id = $("b"+ref).parents().closest(".collapse").eq(1).attr('id');

              // Opens the accordion where condition content exists
              $("a[href='#"+id+"']").removeClass("collapsed").attr("aria-expanded",true);
              $("#"+id).addClass("collapse show").slideDown(1);

              // Jumps to the content of the condition description
              $('html, body').animate({
                scrollTop: $("b"+ref).offset().top-90
              }, 900);

              // Higlights the selected condition name for few seconds
              $('b'+ref).css('backgroundColor', 'yellow');
              $('b'+ref).animate({
                  'opacity': '0.5'
              }, 2000, function () {
                  $('b'+ref).css({
                      'backgroundColor': '#fff',
                      'opacity': '1'
                  });
              });
            }
          }setTimeout(displayConditionContents, 500);

        }else{
          $(".collapse").collapse("hide");
        }
      } 
    });

    $(document).click(function() {
      $styledSelect.removeClass('active');
      $list.hide();
    });
      
  });
 //<----------------  End Condition tag desktop dropdown ---------------------------> 
  
</script>
<script type="text/javascript">
  
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })
  
</script>
@endpush
<!-- Desktop View End -->
@endif

<!-- Single search bar logic code for mobile & desktop - start -->
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
<!-- Single search bar logic code for mobile & desktop - end -->
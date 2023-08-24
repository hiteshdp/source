
@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp

@section('title', __(!empty($metaTitle) ? $metaTitle : 'Effectiveness and safety of '.$therapy_detail['name'].', backed by evidence.'))
@section('meta-keywords', __(!empty($metaKeywords) ? $metaKeywords : $therapy_detail['name'].', is '.$therapy_detail['name'].' effective, does '.$therapy_detail['name'].' work, evidence for '.$therapy_detail['name'].', safety concerns for '.$therapy_detail['name'].', dosage for '.$therapy_detail['name'].', interactions for '.$therapy_detail['name'].', side effects of '.$therapy_detail['name'].', '.$therapy_detail['name'].' effectiveness'))
@section('meta-news-keywords', __(!empty($metaNewsKeywords) ? $metaNewsKeywords : $therapy_detail['name'].', is '.$therapy_detail['name'].' effective, does '.$therapy_detail['name'].' work, evidence for '.$therapy_detail['name'].', safety concerns for '.$therapy_detail['name'].', dosage for '.$therapy_detail['name'].', interactions for '.$therapy_detail['name'].', side effects of '.$therapy_detail['name'].', '.$therapy_detail['name'].' effectiveness'))
@section('meta-description', __(!empty($metaDescription) ? $metaDescription : 'Safety, effectiveness, and dosage of '.$therapy_detail['name'].' for different conditions backed by evidence'))

@section('og-url', Request::url())
@section('og-title', __(!empty($metaOgTitle) ? $metaOgTitle : 'Effectiveness and safety of '.$therapy_detail['name'].' backed by evidence.'))
@section('og-description',__(!empty($metaOgDescription) ? $metaOgDescription : 'Safety, effectiveness, and dosage of '.$therapy_detail['name'].' for different conditions backed by evidence'))

@if ($agent->isMobile())
<!-- Mobile View Start -->

@section('content')

@if(!empty($therapy_detail))
<div class="container mobile floating pt-3">
    <!-- Single search bar code - start -->
    <div class="right-search round-search mb-0 text-left">
      <div class="input-group rounded mt-1 ">
      <span class="input-group-text border-0" id="search-addon">
            <img width="18" height="18" src="{{asset('images/search-blue.svg')}}" alt="search" title="search">
          </span>
          <input type="search" class="form-control  therapy " placeholder=" " aria-label="Search"
          aria-describedby="search-addon" />
          <label for="email" class="float-label mob-font">Search natural medicines by name or medical condition</label>
          
      </div>
      <div class="small-text pl-3  pt-2 d-none">Didn't find what you were looking? Submit your suggestion by clicking the chat icon bottom right.  </div>
    </div>
    <!-- Single search bar code - end -->
    <div class="info-details">
      <h1 class="text-center mt-4 mb-3 bg-blue" id="therapyNameContext"><?php echo $therapyName; ?><a href ="javascript:void(0);" id="infomobile" class=" top-minus-two"> <img Width="26" Height="26" w src="{{asset('images/infowhite.svg')}}" alt="Info"> </a></h1>
        <div id="infomobilediv" class="consumer-info consumer-info-mobile black-popup" style="display:none;">
        <div class="text-center infomobileText "> <a class="popupx" id="popupx" href="#"> <img  Width="20" Height="20" w src="{{asset('images/x.svg')}}" alt="setting"></a>
          <p>Consumer Information and Education based on Natural Medicines<sup>TM</sup></p></div> 
        This monograph was last reviewed on {{$therapyReviewedAt}} and last updated on {{$therapyUpdatedAt}}. 
        Monographs are reviewed and/or updated multiple times per month and at least once per year. 
        If you have comments or suggestions on something that should be reviewed or included, please <a href="javascript:void(Tawk_API.toggle())">tell us.</a> 
      </div>  
    

   

    <div class="row">
      <div class="col-12 top-dropdown">
        @if(!empty($therapyConditionsFinalArray))
          <select name="selectConditionDropDown" class="form-control categoryOption conditionsSelectOption" id="selectConditionDropDown">
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

        <div class="terapy-btn">

            <!----- if user is logged in and user type is paitent caregiver then show buy button of fullscript code - start ------>
            @if(\Auth::check() && !\Auth::user()->isUserHealthCareProvider())
              <!-- <a class="btn gradient-border buy-button" href="https://wellkasa.com/collections/all" target="_blank">
                <img class="buy-button-img" width="24" height="16" src="{{asset('images/mobilelogo.png')}}"  alt="Buy"> 
                  Buy
              </a>  -->
            @endif
            <!----- if user is logged in and user type is paitent caregiver then show buy button of fullscript code - end ------>

            @if($therapyCount > 0)
              @if(\Auth::check() && \Auth::user()->isUserHealthCareProvider())
                <a class="btn btn-gradient" href="{{route('my-wellkasa-rx')}}">
                  Saved 
                </a>
              @else
                <a class="btn btn-gradient" href="{{route('medicine-cabinet')}}">  
                  <img Width="28" Height="32" src="{{asset('images/tour-logo.svg')}}" alt="tour">Saved
                </a>
              @endif
            @else
              <a class="btn btn-gradient  addToMyWellkasa" href="#">
                @if(\Auth::check() && \Auth::user()->isUserHealthCareProvider())
                  Save
                @else
                  <img Width="28" Height="32" class="mr-1" src="{{asset('images/tour-logo.svg')}}" alt="tour">Save
                @endif
              </a>  
            @endif

            </div>
      </div>
      
    
    </div>

</div>  


<!-- What is it section display for mobile - start -->
@foreach ($therapy_detail as $key => $value)
  <?php 
    if($key == '' || $key == 'id' || $key == 'name' || $key == 'reviewed-at' || $key == 'updated-at' || $key == 'type'){
      continue;
    }
    $firstLetterCapital = ucfirst($key);
    $spaceInHeaderForMobile = str_replace("-", " ", $firstLetterCapital);

    // Replace title name
    $firstTitle = array("Description");
    $replaceFirstTitle = array("What is it?");
    $spaceInHeaderForMobile = str_replace($firstTitle, $replaceFirstTitle, $spaceInHeaderForMobile);
  ?>

  @php $n = 0 @endphp
  @if($spaceInHeaderForMobile == "What is it?")
    <div class="card mt-3 mb-2 border-0" id="{{str_replace(array( '(', ')', '-', ' ','/','?','.'),'',$spaceInHeaderForMobile)}}">
      <!-- Card header -->
        <h3 class="mb-0 ml-2 what-is-it-title"><?php echo $spaceInHeaderForMobile; ?> </h3>        
      <!-- Card body -->
      <div class="card-body border-0 p-2">
        <!-- Avoids interactions content to show code start -->
          @if(!empty($value))
            <p><?php echo $value; ?></p>
          @else
            <?php echo "No data available."?>
          @endif
        <!-- Avoids interactions content to show code end -->
      </div>
    </div>
  @endif
@endforeach
<!-- What is it section display for mobile - end -->

<!-- Products recommendation display for mobile code - start -->
@if(!empty($productRecommendationsData))
    <div class="swiper-container swiper-desktop py-3">
      <h2>Available on Wellkasa </h2>
      <!-- swiper slides -->
      <div class="swiper-wrapper pb-4">
        @foreach($productRecommendationsData as $productRecommendationsDataKey => $productRecommendationsDataVal)
          <div class="swiper-slide">
            <div class="product-img">
              <img src="{{$productRecommendationsDataVal['productImageLink']}}">
            </div>
            <div class="product-name p-2 text-overflow">
                {{$productRecommendationsDataVal['productName']}}
            </div>
            <div class="product-ratings">
              <div class="stars-box">
                <span class="fill-stars" style="width: {{$productRecommendationsDataVal['productStarRating']}}%;"> <img class="rating-fill" src="{{asset('images/ratings-fill.svg')}}" alt="Ratings {{$productRecommendationsDataVal['productRatings']}}"></span>
                <span class="empty-stars"><img  src="{{asset('images/ratings-empty.svg')}}" alt="Ratings Empty"> </span>
              </div>
              <span class="ml-1"> ({{$productRecommendationsDataVal['productReviewCount']}}) </span>
            </div>
            <div class="product-price">
              from {!! $productRecommendationsDataVal['productPriceSuperScript'] !!}
            </div>
            <a class="product-url" href="{{$productRecommendationsDataVal['productUrl']}}" target="_blank"></a>
          </div>
        @endforeach          
      </div>
      <div class="swiper-pagination"></div>
    </div>
    @endif
    <!-- Products recommendation display for mobile code - end -->


<select class="form-control categoryOption categoryOptionPanel">
  <?php $optionNo = 0;?>
  @foreach ($therapy_detail as $key => $value)
    <?php
      $keysOfMenuToSkip = array('','id','name','type','synonyms','categories','reviewed-at','updated-at');
      if(in_array($key,$keysOfMenuToSkip)){
        continue;
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
      @if($spaceInHeader == "Are there any interactions with medications?")
        <?php $spaceInHeader = 'Are there any interactions?'; ?>
      @else
        <?php $spaceInHeader; ?>
      @endif

      <!-- Skip what is it option from the dropdown - start -->
      <?php 
        if($spaceInHeader == 'What is it?'){
          continue;
        }
      ?>
      <!-- Skip what is it option from the dropdown - end -->

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
     
      // Remove synonyms, categories, reviewed At & updated At display
      $keysToSkip = array('','id', 'type', 'name','synonyms','categories','reviewed-at','updated-at');
      if(in_array($key,$keysToSkip)){
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
    @if(!in_array($spaceInHeader,["What is it?"]) && !in_array($spaceInHeader,$isItEffectiveAccordions) && !in_array($spaceInHeader,$areThereAnyInteractionAccordions1))
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
            <div class="card-body border-0 p-2">
              <!-- Avoids interactions content to show code start -->
              @if(!in_array($spaceInHeader,$areThereAnyInteractionAccordions) && $spaceInHeader != 'Are there any interactions?')
                @if(!empty($value))
                  <?php if (in_array($spaceInHeader, $blueColor)) { ?>
                    <div class="black-color p-2">
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
                  @if(!empty($therapyFinalArr))
                    <!-- Accordion card -->
                    <div class="accordion md-accordion mt-3 p-2" id="accordionEx500" role="tablist" aria-multiselectable="true">
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
                                  <div class="black-color p-2">
                                    <?php 
                                      echo "<ul>";
                                        foreach ($therapyFinalArrData['data'] as $dataKey => $dataValue) {
                                          /**
                                           * When Subscription is activated then show description data of each 
                                           * conditions else, prompt subscription to view details
                                           */
                                          if($subscriptionStatus){
                                            
                                            $checkDetailsColorInMobile = $dataValue;
                                            if( strpos( $checkDetailsColorInMobile, 'Details' ) !== false) {
                                              $showDetailsColorInMobile = str_replace( '<b>Details</b>:', "<b style='color: #44546a;'>Details</b>:", $checkDetailsColorInMobile);
                                              echo "<li>".$showDetailsColorInMobile."</li>"; 
                                            }else{
                                              echo "<li>".$dataValue."</li>"; 
                                            }

                                          }else{

                                            $subscriptionMsg = "Renew subscription to view details";

                                            // check if conditions has details data then hide details data and show login for details button else show data without details
                                            $checkDetailsValueInMobile = $dataValue;
                                            if( strpos( $checkDetailsValueInMobile, '<b>Details</b>' ) !== false) {
                                              $showDataWithoutDetailsInMobile = substr($checkDetailsValueInMobile, 0, strpos($checkDetailsValueInMobile, '<b>Details</b>'));
                                              echo "<li>".$showDataWithoutDetailsInMobile."</li>"; 
                                              echo "<span> <a href='".$subscriptionRenewLink."' target='_blank' class='renewToViewDetails' style='text-decoration: underline;'>".$subscriptionMsg."</a></span><br><br>";
                                            }else{
                                              // Add login for details button under condition name when there is no one liner with details description available
                                              if( strpos( $dataValue, '.&nbsp' ) !== false) {
                                                $showDataWithoutDetailsInMobile = substr($dataValue, 0, strpos($dataValue, '&nbsp'));
                                                echo "<li>".$showDataWithoutDetailsInMobile."</li>";
                                              }
                                              echo "<span> <a href='".$subscriptionRenewLink."' class='renewToViewDetails' target='_blank' style='text-decoration: underline;'>".$subscriptionMsg."</a></span><br><br>";
                                            }

                                          }                                   
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
                                          $checkDetailsColorInMobile = $dataValue;
                                          if( strpos( $checkDetailsColorInMobile, 'Details' ) !== false) {
                                            $showDetailsColorInMobile = str_replace( '<b>Details</b>:', "<b style='color: #44546a;'>Details</b>:", $checkDetailsColorInMobile);
                                            echo "<li>".$showDetailsColorInMobile."</li>"; 
                                          }else{
                                            echo "<li>".$dataValue."</li>"; 
                                          }
                                        }
                                      echo "</ul>";
                                    ?>
                                  </p><?php
                                  } 
                                  $var = $var+1;   
                                } else{

                                  if (in_array($therapyFinalArrKey, $blueColor)) { ?>
                                  <div class="black-color p-2">
                                    <?php 
                                      echo "<ul>";
                                                                              
                                        foreach ($therapyFinalArrData['data'] as $dataKey => $dataValue) {
                                          // check if conditions has details data then hide details data and show login for details button else show data without details
                                          $checkDetailsValueInMobile = $dataValue;
                                          if( strpos( $checkDetailsValueInMobile, '<b>Details</b>' ) !== false) {
                                            $showDataWithoutDetailsInMobile = substr($checkDetailsValueInMobile, 0, strpos($checkDetailsValueInMobile, '<b>Details</b>'));
                                            echo "<li>".$showDataWithoutDetailsInMobile."</li>"; 
                                            echo "<span> <a href='javascript:void(0)' class='seeDetailsInMobile' id='seeDetailsInMobile".$var."' data-accord-mobile='collapseTwo".$var."'>Login for details</a></span><br><br>";
                                          }else{
                                            // Add login for details button under condition name when there is no one liner with details description available
                                            if( strpos( $dataValue, '.&nbsp' ) !== false) {
                                              $showDataWithoutDetailsInMobile = substr($dataValue, 0, strpos($dataValue, '&nbsp'));
                                              echo "<li>".$showDataWithoutDetailsInMobile."</li>";
                                            }
                                            echo "<span> <a href='javascript:void(0)' class='seeDetailsInMobile' id='seeDetailsInMobile".$var."' data-accord-mobile='collapseTwo".$var."'>Login for details</a></span><br><br>";
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
                                          if( strpos( $checkDetailsValueInMobile, '<b>Details</b>' ) !== false) {
                                            $showDataWithoutDetailsInMobile = substr($checkDetailsValueInMobile, 0, strpos($checkDetailsValueInMobile, '<b>Details</b>'));
                                            echo "<li>".$showDataWithoutDetailsInMobile."</li>"; 
                                            echo "<span> <a href='javascript:void(0)' class='seeDetailsInMobile' id='seeDetailsInMobile".$var."' data-accord-mobile='collapseTwo".$var."'>Login for details</a></span><br><br>";
                                          }else{
                                            // Add login for details button under condition name when there is no one liner with details description available
                                            if( strpos( $dataValue, '.&nbsp' ) !== false) {
                                              $showDataWithoutDetailsInMobile = substr($dataValue, 0, strpos($dataValue, '&nbsp'));
                                              echo "<li>".$showDataWithoutDetailsInMobile."</li>";
                                            } 
                                            echo "<span> <a href='javascript:void(0)' class='seeDetailsInMobile' id='seeDetailsInMobile".$var."' data-accord-mobile='collapseTwo".$var."'>Login for details</a></span><br><br>";
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
                    <div class="accordion md-accordion p-2" id="accordionEx1" role="tablist" aria-multiselectable="true">
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
                                    <div class="black-color p-2"><?php echo $therapyInteractiveFinalArrData['data']; ?></div>
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
    <a id="prev" style="display: none;"><img width="20" height="10" class="align-baseline"  src="{{asset('images/previous.svg')}}" alt="previous" title="previous"> Previous Category</a>                            
   </div>
   <div class="col-6 text-right">
    <a id="next" style="display: none;">Next Category <img width="20" height="10" class="align-baseline"  src="{{asset('images/next.svg')}}" alt="next" title="next"></a>
    </div>
  </div>
  <div class="text-center mt-4 mb-4 d-flex flex-column">
    <a href="{{config('constants.Footer_TRC_URL')}}" target="_blank">  
      <img width="290" height="60" src="{{asset('images/trclogo.png')}}" alt="trclogo" title="trclogo">
    </a>
    <span>Licensed from Therapeutic Research Center, LLC</span>
    <span>Copyright © 1995-{{date('Y')}} by Therapeutic Research Center, LLC. All Rights Reserved.</span>
  </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{url('public/css/swiper.css')}}" />


@endpush

@push('scripts')

<script src="{{url('public/js/swiper.js')}}"></script>

<script type="text/javascript">

$( document ).ready(function() {

  // Swiper js code for product recommendation data in mobile view - start
  var Swipes = new Swiper('.swiper-container', {
    loop: false,
    slidesPerView: 2,
    spaceBetween: 10, 

    pagination: {
      el: '.swiper-pagination',
      type: 'bullets',
    },
  });
  // Swiper js code for product recommendation data in mobile view - end 

  // Close the info data popup next to therapy name
  $("#popupx").on('click',function() {
    $("#infomobile").trigger("click");
  });

  // get current site url
  var acordionToOpenSiteUrlInMobile = "<?php echo URL::current();?>";

  /**
   *  Add accordion id to open when clicked on Login for details in local storage and redirect to login page
   */
  $('.seeDetailsInMobile').click(function(event) {
    let loginForDetailsButton = $(event.target).parent().prev();
    var selectedConditionId = loginForDetailsButton.find('b').attr('id');
    // add selected condition id to local storage
    localStorage.setItem("acordionToOpenInMobile", selectedConditionId); 
    // add current url to local storage
    localStorage.setItem("acordionToOpenSiteUrlInMobile", acordionToOpenSiteUrlInMobile); 
    // redirect to login page
    window.location.href = "{{route('login')}}"; 
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

    // check if acordionToOpenInMobile localStorage has value
    if(localStorage.getItem("acordionToOpenInMobile") != null){ 
      // get selected condition id from localStorage
      var acordionToOpen = localStorage.getItem("acordionToOpenInMobile"); 
      if($(".select-options li[rel='#"+acordionToOpen+"']").length!='0' && isSameMobileSiteUrl){
        $(".select-options li[rel='isiteffective']").trigger('click');
        $(".select-options li[rel='#"+acordionToOpen+"']").trigger('click');
      }
      // deletes the condition id from the local storage key acordionToOpenInMobile
      localStorage.removeItem("acordionToOpenInMobile"); 
      // deletes the site url from the local storage key acordionToOpenSiteUrlInMobile
      localStorage.removeItem("acordionToOpenSiteUrlInMobile"); 
      // deletes the condition id from the local storage key accordionToOpen
      localStorage.removeItem("acordionToOpen");
      // deletes the site url from the local storage key acordionToOpenSiteUrl
      localStorage.removeItem("acordionToOpenSiteUrl"); 
     
    }
  }, 800);


  // Hide popup details from info icon next to therapy name when clicked outside anywhere in the document
  $('html').click(function(event) {
    if(event.target != ""){
      // Checks if infomobilediv is visible after 1 second
      setTimeout(() => { 
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

  var data = { ratings: '', provider: '1', note: '', therapyID: "{{$therapyId}}", isMedicineTab : "1"};
  var jsonString = JSON.stringify(data);
  let saveRoute = "{{$saveRoute}}"
  var redirectRoute = "{{$redirectRoute}}"
  $('.addToMyWellkasa').on('click',function(){   
    $.ajax({
      url: saveRoute ? saveRoute : "{{route('store-therapy')}}",
      type: "POST",
      data: jsonString,
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      contentType: 'application/json; charset=utf-8',
      success: function (response)
      {
        window.location.href = redirectRoute
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
    $this.wrap('<div class="select mt-3"></div>');
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
    $('.JumptoconditionSelectOption').append('Pick Condition');
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
        text: 'Pick Condition',
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
      if(optionValue != "Pick Condition"){
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
              
              // Opens "is it effective" accordion having conditions accordion
              var sectionId = $("b"+ref).parents().find('div.section-accordion').eq(0).attr('id');

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
 <div class="accordion md-accordion new-accordion" id="accordionEx" role="tablist" aria-multiselectable="true">
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
               <!-- <div class="small-text pl-3 pt-2">Didn't find what you were looking? Submit your suggestion by clicking the chat icon bottom right.  </div> -->
            </div>
            <!-- Single search bar code - end -->
              <div class="info-details round-search row">
                <div class="col-md-12 px-0 mb-2">
                <h1 class="text-center  bg-blue" id="therapyNameContext"><?php echo $therapyName; ?><a href ="javascript:void(0);" id="infomobile" class=" top-minus-two"> <img Width="26" Height="26" w src="{{asset('images/infowhite.svg')}}" alt="Info"> </a></h1>
                <div id="infomobilediv" class="consumer-info consumer-info-mobile black-popup" style="display:none;">
                  <div class="text-center infomobileText"> <a class="popupx" id="popupx" href="#"> <img  Width="20" Height="20" w src="{{asset('images/x.svg')}}" alt="setting"></a>
                    <p>Consumer Information and Education based on Natural Medicines<sup>TM</sup></p>
                  </div> 
                  This monograph was last reviewed on {{$therapyReviewedAt}} and last updated on {{$therapyUpdatedAt}}. 
                  Monographs are reviewed and/or updated multiple times per month and at least once per year. 
                  If you have comments or suggestions on something that should be reviewed or included, please <a href="javascript:void(Tawk_API.toggle())">tell us.</a> 
                </div>
                </div>
                <div class="col-md-6 px-2">
                <div class="top-dropdown">
                  <select name="selectConditionDropDown" class="form-control categoryOption" id="selectConditionDropDown" style="width:100% !important;">
                    <option value="">Pick Condition</option>
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
                </div>
                </div>
                <div class="col-md-6 text-right px-0">
                <div class="terapy-btn text-right">
                  
                  <!----- if user is logged in and user type is paitent caregiver then show buy button of fullscript code - start ------>
                  <!-- @if(\Auth::check() && !\Auth::user()->isUserHealthCareProvider())
                    <a class="btn gradient-border buy-button" href="https://wellkasa.com/collections/all" target="_blank"><img class="buy-button-img" width="24" height="16" src="{{asset('images/mobilelogo.png')}}"  alt="Buy"> Buy</a> 
                  @endif -->
                  <!----- if user is logged in and user type is paitent caregiver then show buy button of fullscript code - end ------>
                  
                  @if($therapyCount > 0)
                    @if(\Auth::check() && \Auth::user()->isUserHealthCareProvider())
                      <a class="btn btn-gradient" href="{{route('my-wellkasa-rx')}}">
                        Saved 
                      </a>
                    @else
                      <a class="btn btn-gradient" href="{{route('medicine-cabinet')}}">  
                        <img Width="28" Height="32" class="mr-1"  src="{{asset('images/tour-logo.svg')}}" alt="tour">Saved
                      </a>
                    @endif
                  @else
                    <a class="btn btn-gradient  addToMyWellkasa" href="#">
                      @if(\Auth::check() && \Auth::user()->isUserHealthCareProvider())
                        Save
                      @else
                        <img Width="28" Height="32" class="mr-1"  src="{{asset('images/tour-logo.svg')}}" alt="tour">Save
                      @endif
                    </a>  
                  @endif

                </div>
                </div>
               
                
                
              </div>
                
              
              
              <!--- disabled old filter conditions with save button code - start ---->
              <div class="col-12 dark-blue-bg d-none" >
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
                      <option value="">Pick Condition</option>
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
                      <a class="btn btn-green  disabled" href="#"> 
                        <?php
                          if(\Auth::check() && \Auth::user()->isUserHealthCareProvider()){
                            echo "Saved to mywellkasaRx";
                          }else{ 
                            echo "Saved to my Wellkasa";
                          }
                        ?>
                      </a>
                    @else
                      <a class="btn btn-green  addToMyWellkasa" href="#"> 
                        <?php 
                          if(\Auth::check() && \Auth::user()->isUserHealthCareProvider()){
                            echo "Save to mywellkasaRx";
                          }else{
                            echo "Save to my Wellkasa";
                          }
                        ?>
                      </a>
                    @endif
                  </div> 
                </div>
              </div>
              <!--- disabled old filter conditions with save button code - end ---->
            </div> 

            <?php 
            continue;
            }

            // Remove synonyms, categories, reviewed At & updated At display
            $keysToSkip = array('synonyms','categories','reviewed-at','updated-at');
            if(in_array($key,$keysToSkip)){
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
        <div class="card mb-2 mt-3">
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
                                              
                                              /**
                                               * When Subscription is activated then show description data of each 
                                               * conditions else, prompt subscription to view details
                                               */
                                              if($subscriptionStatus){

                                                $checkDetailsColorInDesktop = $dataValue;
                                                if( strpos( $checkDetailsColorInDesktop, 'Details' ) !== false) {
                                                  $showDetailsColorInDesktop = str_replace( '<b>Details</b>:', "<b style='color: #44546a;'>Details</b>:", $checkDetailsColorInDesktop);
                                                  echo "<li>".$showDetailsColorInDesktop."</li>"; 
                                                }else{
                                                  echo "<li>".$dataValue."</li>"; 
                                                }

                                              }else{

                                                $subscriptionMsg = "Renew subscription to view details";

                                                /**
                                                 * Check if conditions has details data then hide details data and show
                                                 * login for details button else show data without details
                                                 *  */ 
                                                $checkDetailsValueInDesktop = $dataValue;
                                                if( strpos( $checkDetailsValueInDesktop, '<b>Details</b>' ) !== false) {
                                                  $showDataWithoutDetailsInDesktop = substr($checkDetailsValueInDesktop, 0, strpos($checkDetailsValueInDesktop, '<b>Details</b>'));
                                                  echo "<li>".$showDataWithoutDetailsInDesktop."</li>"; 
                                                  echo "<span> <a href='".$subscriptionRenewLink."' target='_blank' class='renewToViewDetails' style='text-decoration: underline;'>".$subscriptionMsg."</a></span><br><br>";
                                                }else{

                                                  /** Add login for details button under condition name when there 
                                                   * is no one liner with details description available 
                                                   * */
                                                  if( strpos( $dataValue, '.&nbsp' ) !== false) {
                                                    $showDataWithoutDetailsInDesktop = substr($dataValue, 0, strpos($dataValue, '&nbsp'));
                                                    echo "<li>".$showDataWithoutDetailsInDesktop."</li>"; 
                                                  } 
                                                  echo "<span> <a href='".$subscriptionRenewLink."' class='renewToViewDetails' target='_blank' style='text-decoration: underline;'>".$subscriptionMsg."</a></span><br><br>";
                                                }

                                              }
                                             
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
                                              $checkDetailsColorInDesktop = $dataValue;
                                              if( strpos( $checkDetailsColorInDesktop, 'Details' ) !== false) {
                                                $showDetailsColorInDesktop = str_replace( '<b>Details</b>:', "<b style='color: #44546a;'>Details</b>:", $checkDetailsColorInDesktop);
                                                echo "<li>".$showDetailsColorInDesktop."</li>"; 
                                              }else{
                                                echo "<li>".$dataValue."</li>"; 
                                              } 
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
                                              if( strpos( $checkDetailsValueInDesktop, '<b>Details</b>' ) !== false) {
                                                $showDataWithoutDetailsInDesktop = substr($checkDetailsValueInDesktop, 0, strpos($checkDetailsValueInDesktop, '<b>Details</b>'));
                                                echo "<li>".$showDataWithoutDetailsInDesktop."</li>"; 
                                                echo "<span> <a href='javascript:void(0)' class='seeDetails' data-accord='collapseTwo".$var."' id='seeDetails".$var."' >Login for details</a></span><br><br>";
                                              }else{
                                                // Add login for details button under condition name when there is no one liner with details description available
                                                if( strpos( $dataValue, '.&nbsp' ) !== false) {
                                                  $showDataWithoutDetailsInDesktop = substr($dataValue, 0, strpos($dataValue, '&nbsp'));
                                                  echo "<li>".$showDataWithoutDetailsInDesktop."</li>"; 
                                                }
                                                echo "<span> <a href='javascript:void(0)' class='seeDetails' data-accord='collapseTwo".$var."' id='seeDetails".$var."' >Login for details</a></span><br><br>";

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
                                              if( strpos( $checkDetailsValueInDesktop, '<b>Details</b>' ) !== false) {
                                                $showDataWithoutDetailsInDesktop = substr($checkDetailsValueInDesktop, 0, strpos($checkDetailsValueInDesktop, '<b>Details</b>'));
                                                echo "<li>".$showDataWithoutDetailsInDesktop."</li>"; 
                                                echo "<span> <a href='javascript:void(0)' class='seeDetails' data-accord='collapseTwo".$var."' id='seeDetails".$var."'>Login for details</a></span><br><br>";
                                              }else{
                                                // Add login for details button under condition name when there is no one liner with details description available
                                                if( strpos( $dataValue, '.&nbsp' ) !== false) {
                                                  $showDataWithoutDetailsInDesktop = substr($dataValue, 0, strpos($dataValue, '&nbsp'));
                                                  echo "<li>".$showDataWithoutDetailsInDesktop."</li>"; 
                                                } 
                                                echo "<span> <a href='javascript:void(0)' class='seeDetails' data-accord='collapseTwo".$var."' id='seeDetails".$var."' >Login for details</a></span><br><br>";
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

        <!-- Display products recommendation after "what is it?" section - start -->
        @if($spaceInHeader == 'What is it?')

          <!-- Products recommendation display for desktop code - start -->
          @if(!empty($productRecommendationsData))
            <div class="swiper-container swiper-desktop py-3">
              <h2>Available on Wellkasa </h2>
              <!-- swiper slides -->
              <div class="swiper-wrapper">
                @foreach($productRecommendationsData as $productRecommendationsDataKey => $productRecommendationsDataVal)
                  <div class="swiper-slide">
                    <div class="product-img">
                      <a class="" href="{{$productRecommendationsDataVal['productUrl']}}" target="_blank">
                        <img src="{{$productRecommendationsDataVal['productImageLink']}}">
                      </a>
                    </div>
                    <a href="{{$productRecommendationsDataVal['productUrl']}}" target="_blank">
                      <div class="product-name text-overflow">
                        {{$productRecommendationsDataVal['productName']}}
                      </div>
                      <div class="product-ratings">
                        <div class="stars-box">
                          <span class="fill-stars" style="width: {{$productRecommendationsDataVal['productStarRating']}}%;"> <img class="rating-fill" src="{{asset('images/ratings-fill.svg')}}" alt="Ratings {{$productRecommendationsDataVal['productRatings']}}"></span>
                          <span class="empty-stars"><img  src="{{asset('images/ratings-empty.svg')}}" alt="Ratings Empty"> </span>
                        </div>
                        <span class="ml-1"> ({{$productRecommendationsDataVal['productReviewCount']}}) </span>
                      </div>
                      <div class="product-price">
                        from {!! $productRecommendationsDataVal['productPriceSuperScript'] !!}
                      </div>
                    </a>
                  </div>
                @endforeach          
              </div>
              <!-- !swiper slides -->
              
              <!-- next / prev arrows -->
              <div class="swiper-button-next"></div>
              <div class="swiper-button-prev"></div>
              <!-- !next / prev arrows -->
            </div>
          @endif
          <!-- Products recommendation display for desktop code - end -->

        @endif
        <!-- Display products recommendation after "what is it?" section - end -->

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

  <div class="text-center mt-4 mb-4 d-flex flex-column">
    <a href="{{config('constants.Footer_TRC_URL')}}" target="_blank">  
      <img width="290" height="60" src="{{asset('images/trclogo.png')}}" alt="trclogo" title="trclogo">
    </a>
    <span>Licensed from Therapeutic Research Center, LLC</span>
    <span>Copyright © 1995-{{date('Y')}} by Therapeutic Research Center, LLC. All Rights Reserved.</span>
  </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{url('public/css/swiper.css')}}" />


@endpush

@push('scripts')

<script src="{{url('public/js/swiper.js')}}"></script>

<script type="text/javascript">
// Swiper js code for product recommendation data in desktop view - start
  // get the products count  
  let productsCount = {!! count($productRecommendationsData) !!}
  var Swipes = new Swiper('.swiper-container', {
    loop: false,
    // If products count is greater than equals to 4 then show slide per 4 else show 3
    slidesPerView: productsCount >= 4 ? "4" : productsCount,
    // If products count is greater than equals to 4 then add increase space to 30 else use 20
    spaceBetween: productsCount < 4 ? 20 : 30, 
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    pagination: {
      el: '.swiper-pagination',
    },
    on: {
      init: function() {
        checkArrow();
      },
      resize: function () {
        checkArrow();
      }
    },
  });
  // Check the products list to hide/show arrows
  function checkArrow() {
    var swiperPrev = document.querySelector('.swiper-button-prev');
    var swiperNext = document.querySelector('.swiper-button-next');
    // If products less than 4 then hide arrows, else display the arrows
    if ( productsCount > 4  ) {
      swiperPrev.style.display = 'block';
      swiperNext.style.display = 'block';
    } else {
      swiperPrev.style.display = 'none';
      swiperNext.style.display = 'none';
    }
  }
// Swiper js code for product recommendation data in desktop view - end

$( document ).ready(function() {

  // get current site url
  var acordionToOpenSiteUrl = "<?php echo URL::current();?>"; 

  // open what is it accordion default 
  function openDefaultFirstAccordion(){
    setTimeout(() => {
      $("a[href='#collapseTwo1']").trigger('click');
    }, 100);
  }
  if(localStorage.getItem("acordionToOpen") == null){
    openDefaultFirstAccordion();
  }

  // Close the info data popup next to therapy name
  $("#popupx").on('click',function() {
    $("#infomobile").trigger("click");
  });


  /**
   *  Add accordion id to open when clicked on Login for details in local storage and redirect to login page
   */
  $('.seeDetails').click(function(event) {
    let loginForDetailsButton = $(event.target).parent().prev();
    var selectedConditionId = loginForDetailsButton.find('b').attr('id');
    // add selected condition id to local storage
    localStorage.setItem("acordionToOpen", selectedConditionId); 
    // add current url to local storage
    localStorage.setItem("acordionToOpenSiteUrl", acordionToOpenSiteUrl); 
    // redirect to login page
    window.location.href = "{{route('login')}}"; 
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
    // check if accordionToOpen localStorage has value
    if(localStorage.getItem("acordionToOpen") != null){ 

      // get selected condition id from localStorage
      var acordionToOpen = localStorage.getItem("acordionToOpen"); 
      if($(".select-options li[rel='#"+acordionToOpen+"']").length !='0'  && isSameSiteUrl){
        $(".select-options li[rel='#"+acordionToOpen+"']").trigger('click');
      }else{
        openDefaultFirstAccordion();
      }
      // deletes the condition id from the local storage key accordionToOpen
      localStorage.removeItem("acordionToOpen"); 
      // deletes the site url from the local storage key acordionToOpenSiteUrl
      localStorage.removeItem("acordionToOpenSiteUrl"); 
      // deletes the condition id from the local storage key acordionToOpenInMobile
      localStorage.removeItem("acordionToOpenInMobile"); 
      // deletes the site url from the local storage key acordionToOpenSiteUrlInMobile
      localStorage.removeItem("acordionToOpenSiteUrlInMobile"); 
    }
  }, 500);
  
  $('[data-toggle="popover"]').popover()
  
  // Hide popup details from info icon next to therapy name when clicked outside anywhere in the document
  $('html').click(function(event) {
    if(event.target.innerText != ""){
      // Checks if infomobilediv is visible after 1 second
      setTimeout(() => { 
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
  
   var data = { ratings: '', provider: '1', note: '', therapyID: "{{$therapyId}}" , isMedicineTab : "1"};
   var jsonString = JSON.stringify(data);
   let saveRoute = "{{$saveRoute}}"
   var redirectRoute = "{{$redirectRoute}}"
  $('.addToMyWellkasa').on('click',function(){   
    $.ajax({
      url: saveRoute ? saveRoute : "{{route('store-therapy')}}",
      type: "POST",
      data: jsonString,
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      contentType: 'application/json; charset=utf-8',
      success: function (response)
      {
        window.location.href = redirectRoute
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
    $('.jumpToConditionDiv').append('Pick Condition');

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
      if(optionValue != "Pick Condition"){
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
                scrollTop: $("b"+ref).offset().top-170
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
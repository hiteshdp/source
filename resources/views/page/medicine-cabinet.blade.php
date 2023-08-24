@extends('layout.default')

@php
   $locale = App::getLocale();
@endphp


@section('content')
<div class="container750">
   <div class="cabinet-accordion cabinet-header-new">
      <div class="cabinet-title-header">
         <h1>Design your wellness</h1>
         @if(!empty($userProfileMembersData))
            <span class="dropdown" title="Select dropdown to change profile member">
               <button class="btn btn-secondary wellkabinet-dropdown-toggle p-0" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span>{{$userName}}</span>
               </button>
               <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                  @foreach($userProfileMembersData as $userProfileMembersDataValue)
                     <a class="dropdown-item" href="{{$userProfileMembersDataValue['url']}}">{{$userProfileMembersDataValue['name']}}</a>
                  @endforeach
                  <!-------------- Check if user has subscription and has remaining profile member to add, then show add profile option - code start -------------------->   
                  @if(Auth::user()->planType == '2' && Auth::user()->remainingProfileMemberCount != 0)
                     <a class="dropdown-item" href="{{route('add-profile',['route' => \Crypt::encrypt('1')])}}" title="Add new profile member">Add Profile ({{Auth::user()->remainingProfileMemberCount}} Available)</a>
                  @endif
                  <!-------------- Check if user has subscription and has remaining profile member to add, then show add profile option - code end -------------------->   
               </div>
            </span>
         @else
         <span>{{$userName}}</span>
         @endif
      </div>  
      <!---------------- Tab Selection - Start ----------------->
      <div class="row mt-3 mb-4">
         <div class="col-md-6 col-6">
            <a class="btn-border btn-active" href="javascript:void(0);"><img width="70" height="47"  src="{{asset('images/WellkasaLogo.png')}}" alt="WellkasaLogo"> Wellkabinet</a>
         </div>
         <div class="col-md-6 col-6">
            <a class="btn-border" href="{{$profileMemberId ? route('event-selection',\Crypt::encrypt($profileMemberId)) : route('event-selection')}}"><img width="70" height="47"  src="{{asset('images/symptom.png')}}" alt="Symptom Tracker"> Symptom Tracker</a>
         </div>
      </div>
      <!---------------- Tab Selection - End ----------------->
      <div class="cabinet-add position-relative mb-3" >     
         <!--- Filters Header Section - Start --->
         @if(!empty($medicineCabinetData))
         <div class="filter-header-left">
            <span class="filter-title">Filter by</span>
            <div class="settin-icons position-relative">  
               <div class="tour-block tour-left-arrow left-0 d-none tour-popup-step" id="tourStep2">
                  <div class="tour-bubble-header">
                  <div id="step2">2/7:</div><a href="#" onClick="onClickCloseTour()"><img  src="{{asset('images/carbon_close.svg')}}" alt="carbon_close"> </a> 
                  </div>  
                  <div class="tour-bubble-body">
                  Filter medicines by condition, interaction or ones that you are taking
                  </div>
                  <div class="tour-bubble-footer">
                  <a class="mr-2" onClick="onClickQuickTourPrev(2)" href="#">PREV</a> <a  onClick="onClickQuickTourNext(2)" href="#">NEXT</a>
                  </div>
               </div>
            </div>
            <div class="cabinet-candition new-filter-btn <?php if(Auth::user()->planType == '1') { echo 'mr-2'; } ?>"  >
               <a href="javascript:void(0);" id="selectConditionDropdown"> Condition <img  src="{{asset('images/fildown.svg')}}" alt="fildown" class="ml-1"></a>
            </div>
            @if(\Auth::user()->planType == '2')
               <div class="cabinet-intercation new-filter-btn">
                  <a href="javascript:void(0);" id="selectInteractionDropdown">Interaction <img  src="{{asset('images/fildown.svg')}}" alt="fildown" class="ml-1"></a>
               </div>
            @endif
            <div class="cabinet-traking new-filter-btn">
               <a href="javascript:void(0);" id="selectTakingMedicineData"> <img class="mr-2"  src="{{asset('images/bluepill.svg')}}" alt="bluepill" class="ml-1"> Taking</a>
            </div>
         </div>  
         @endif
         <!--- Filters Header Section - End ---> 

         <div class="filter-add-mobile">
            <!-- Show "filter by" title for mobile view if medicine cabinet data exist - div start --->
            @if(!empty($medicineCabinetData))
               <span class="filter-title-mob">Filter by</span>
            @endif
            <!-- Show "filter by" title for mobile view if medicine cabinet data exist - div end --->
            <a id="open-main-popup" href="javascript:void(0);">
               <img width="35" height="35"  src="{{asset('images/add-filled.svg')}}" alt="add-filled"> 
               <span class="d-block wellkabinet-add-text">Add Medicine</span>
            </a>
         </div> 
      </div> 
   </div>

   <!--- Selected Filters Section - Start ---> 
   <div class="filter-list" style="display:none;">
      <span>Filters</span> 
      <ul id="filtersSection">
      </ul> 
   </div>
   <!--- Selected Filters Section - End ---> 

   <!--- Added Rx Drugs & Natural Medicine Data in accordion Section - Start ---> 
   <div class="cabinet-accordion min-h-500" id="wellkabinet-body-section">
      @if(!empty($medicineCabinetData))
         @foreach($medicineCabinetData as $dataArrKey => $medicineCabinetDataVal)
            @if($dataArrKey == 0)
               @php $isDrug = 0; @endphp
               @if(!empty($medicineCabinetDataVal['drugId']))
                  @php $isDrug = 1; @endphp
               @endif
               <input type="hidden" name="isFirstDrug" id="isFirstDrug" value="{{$isDrug}}"/>
            @endif
            <input type="hidden" name="planType" id="planType" value="{{Auth::user()->planType}}"/>

            <div class="card {{$medicineCabinetDataVal['interactionLabel']}} medicineCabinetData {{$medicineCabinetDataVal['isTaking'] == '1' ? 'isTakingMedicineData' : ''}}" data-interaction-filter="0" data-condition-filter="0" data-taking-filter="0" id="cardNumber{{$dataArrKey}}">
               <div class="card-head" id="headingOne">
                  <h2 class="mb-0 {{$dataArrKey == '0' ? '' : 'collapsed'}}" data-toggle="collapse" data-target="#collapseOne{{$dataArrKey}}" aria-expanded="fauls" aria-controls="collapseOne{{$dataArrKey}}" style="cursor: auto;">
                     <div class="cabinet-acco-img ">
                        <?php $redirectImageLink = "style=cursor:auto;" ; ?>
                        @if(!empty($medicineCabinetDataVal['imageRedirectLink']))
                          <?php $redirectImageLink = "href=".$medicineCabinetDataVal['imageRedirectLink']."  target=_blank" ; ?>
                        @endif
                        <a {{$redirectImageLink}}> 
                           <img width="40" height="40" src="{{$medicineCabinetDataVal['imageName']}}" 
                              @if(!empty($medicineCabinetDataVal['productId']))
                                 id='product-image-{{$medicineCabinetDataVal["productId"]}}'
                              @endif
                              alt="{{$medicineCabinetDataVal['name']}}"
                           >
                        </a>
                     </div>
                     <div class="cabinet-acco-title position-relative {{empty($medicineCabinetDataVal['canonicalName']) ? 'wellkabinet-rx-drug-title' : ''}}">
                        <!--- If this is natural medicine then give therapy route within the name, else display only name section - code start --->
                           <label class="mb-0 medicineName{{$medicineCabinetDataVal['medicineCabinetId']}}" data-name="{{$medicineCabinetDataVal['name']}}">
                           
                              <div class="main-list position-relative">
                              <div class="list-title">
                              {{$medicineCabinetDataVal['name']}}

                              <!---- If the data is product or medicine then show the info icon - code start ----->
                              
                                 @if(!empty($medicineCabinetDataVal['productId']) || !empty($medicineCabinetDataVal['naturalMedicineId']))
                                    <!-- info - popup start -->
                                       <!----- show info icon if natural medicine data - start  ----->
                                          @if(!empty($medicineCabinetDataVal['naturalMedicineId']))
                                             <a href="{{route('therapy',$medicineCabinetDataVal['canonicalName'])}}" title="Click here to learn more" target="_blank"> 
                                                <img class="info-icons cabinet-info-icon" data-id="{{$medicineCabinetDataVal['medicineCabinetId']}}" src="{{asset('images/info-blue.png')}}" alt="tab-icon">
                                             </a>
                                             
                                          @endif
                                       <!----- show info icon if natural medicine data - end  ----->
                                       
                                       <!----- show info icon if product data has ingredients - start  ----->
                                       
                                          @if(!empty($medicineCabinetDataVal['productDescription']))
                                             <a href="javascript:void(0);" title="Click here to view product details"> 
                                                <img class="info-icons cabinet-info-icon" data-id="{{$medicineCabinetDataVal['medicineCabinetId']}}" src="{{asset('images/info-blue.png')}}" alt="tab-icon">
                                             </a>
                                             </div>
                                             <div class="info-popup" id="info-popup-{{$medicineCabinetDataVal['medicineCabinetId']}}" style="display:none;">
                                                <?php echo nl2br($medicineCabinetDataVal['productDescription']); ?>
                                                @if(!empty($medicineCabinetDataVal['productDates']))
                                                   <div class="popup-info-date">
                                                      <div class="date-detail">
                                                         Last Purchased: {{$medicineCabinetDataVal['productDates']['lastPurchasedDate']}}
                                                      </div>
                                                      <div class="date-detail">
                                                         Next Refill: {{$medicineCabinetDataVal['productDates']['nextRefillDate']}}
                                                      </div>
                                                   </div>
                                                   
                                                @endif
                                                <div class="mt-2"><small><b>Note</b>: Ingredients listed using common research names. Interaction details based on research available for ingredients.</small></div>
                                                </div>
                                             </div>
                                             
                                          @endif
                                       <!----- show info icon if product data has ingredients - end  ----->
                                    <!-- info - popup end -->
                                 @endif
                              <!---- If the data is product or medicine then show the info icon - code end ----->

                           </label>
                        <!--- If this is natural medicine then give therapy route within the name, else display only name section - code end --->
                        <br>
                        
                        <!---- Display conditions added by the logged in user - code start ----->
                        @if(!empty($medicineCabinetDataVal['conditionIds']))
                           @php $lastArrayKey = count($medicineCabinetDataVal['conditionIds']) @endphp
                           <div>
                           @foreach($medicineCabinetDataVal['conditionIds'] as $conditionNameKey => $conditionName)
                              @php
                                    $medicineCabinetDataCount = count($medicineCabinetDataVal['conditionIds']);
                              @endphp
                              <span class="conditionListName{{$conditionName['id']}}" data-card-no="{{$dataArrKey}}" data-card-totalCount="{{$medicineCabinetDataCount}}"> 
                                 
                                 <!----- Check if data is not rx drug then implement below code, else show condition without redirect link ------->
                                 @if(empty($medicineCabinetDataVal['drugId']))
                                    
                                    <!----- Natural Medicine section condition tags - start ------->
                                    @if(empty($conditionName['canonicalName']))
                                       <!-- If canonical name is not available then show condition name in unlinkable form - start -->
                                       <a style="color: #828282 !important;cursor: default;"> 
                                          {{$conditionName['name']}} 
                                          <!-- add comma if more than one condition in list -->
                                          @if($lastArrayKey != $conditionNameKey + 1) , @endif
                                       </a>
                                       <!-- If canonical name is not available then show condition name in unlinkable form - end -->
                                    @else
                                       <!-- Display conditions with redirect link if its under therapy section - start -->
                                       <a href="{{route('condition',$conditionName['canonicalName'])}}" title="Click here to view {{$conditionName['name']}} condition efficacy chart"> 
                                          {{$conditionName['name']}} 
                                          <!-- add comma if more than one condition in list -->
                                          @if($lastArrayKey != $conditionNameKey + 1) , @endif 
                                       </a>
                                       <!-- Display conditions with redirect link if its under therapy section - end -->
                                    @endif
                                    <!----- Natural Medicine section condition tags - end ------->

                                 @else
                                    <!----- Rx Drugs section condition tags - start ------->
                                    <!-- Display conditions without redirect link if its under Rx Drugs section - start -->
                                    <a style="color: #828282 !important;cursor: default;"> 
                                       {{$conditionName['name']}} 
                                       <!-- add comma if more than one condition in list -->
                                       @if($lastArrayKey != $conditionNameKey + 1) , @endif
                                    </a>
                                    <!-- Display conditions without redirect link if its under Rx Drugs section - end -->
                                    <!----- Rx Drugs section condition tags - end ------->
                                 @endif
                                 

                              </span>
                           @endforeach
                           </div>
                        @endif
                        <!---- Display conditions added by the logged in user - code end ----->

                        <div class="tour-block tour-left-arrow left-0 text-left d-none tour-popup-step" style="left: 43px;" id="tourStep6">
                           <div class="tour-bubble-header">
                           <div id="step6" data-type="{{!empty($medicineCabinetDataVal['productId']) ? '1' : '0'}}">6/7:</div><a href="#" onClick="onClickCloseTour()"><img  src="{{asset('images/carbon_close.svg')}}" alt="carbon_close"> </a> 
                           </div>
                           <div class="tour-bubble-body" id="step6-body">
                              Click the info icon to view therapy details. Click condition name to view condition details 
                           </div>
                           <div class="tour-bubble-footer">
                              <a class="mr-2" onClick="onClickQuickTourPrev(6)" href="#">PREV</a> <a onClick="onClickQuickTourNext(6)" href="#">NEXT</a>
                           </div>
                        </div>
                     </div>
                  </h2>
                  <div class="cabinet-action-list">
                     <!----------------- Dosage data - code start --------------------->
                     @if(!empty($medicineCabinetDataVal['dosage']))
                     <div class="cab-act-list position-relative" style="width: 70px;">
                        <span class="mt-2"><?php echo html_entity_decode($medicineCabinetDataVal['dosage']);?></span>                        
                     </div>
                     @else
                     <div class="cab-act-list position-relative" style="width: 70px;">
                        <span class="mt-2">No dosage added</span>                        
                     </div>
                     @endif
                     <!----------------- Dosage data - code end --------------------->

                     @if(Auth::user()->planType == '2')
                        <!----- Display interaction option if user has subscribed to wellkasa plus - code start ------>
                        @if(str_contains($medicineCabinetDataVal['interactionIcon'], 'gray'))
                        <div class="cab-act-list position-relative mt-10">
                           <a href="javascript:void(0)" title="No interactions" style="cursor: auto;">
                              <img width="26" height="26"  src="{{$medicineCabinetDataVal['interactionIcon']}}" alt="gray-info">
                              <span>Interactions
                                 <div class="tour-block tour-right-arrow text-left mt-2 d-none tour-popup-step" id="tourStep4">
                                    <div class="tour-bubble-header">
                                    <div id="step4" style="color: #fff;">3/7:</div><a href="#" onClick="onClickCloseTour()"><img  src="{{asset('images/carbon_close.svg')}}" alt="carbon_close"> </a> 
                                    </div>
                                    <div class="tour-bubble-body">
                                       Interaction button  - Shows interaction between supplements and Rx for your safety
                                    </div>
                                    <div class="tour-bubble-footer">
                                       <a class="mr-2" onClick="onClickQuickTourPrev(3)" href="#">PREV</a> <a onClick="onClickQuickTourNext(3)" href="#">NEXT</a>
                                    </div>
                                 </div>
                              </span>
                           </a>
                        </div>
                        @else
                        <div class="cab-act-list position-relative">
                           <a href="javascript:void(0)" title="Click to see interactions" class="showInteractionsFromIcon" onClick="showInteractionsFromIcon(this)" data-drug-id="{{array_key_exists('drugId',$medicineCabinetDataVal) ? $medicineCabinetDataVal['drugId'] : ''}}" data-natural-medicine-id="{{ array_key_exists('naturalMedicineId',$medicineCabinetDataVal) ? $medicineCabinetDataVal['naturalMedicineId'] : ''}}" 
                           data-product-id="{{ array_key_exists('productId',$medicineCabinetDataVal) ? $medicineCabinetDataVal['productId'] : ''}}" data-header-name="{{$medicineCabinetDataVal['name']}}">
                              <img width="26" height="26"  src="{{$medicineCabinetDataVal['interactionIcon']}}" alt="gray-info">
                              <span>Interactions
                                 <div class="tour-block tour-right-arrow text-left mt-2 d-none tour-popup-step" id="tourStep3">
                                    <div class="tour-bubble-header">
                                    <div id="step3"  style="color: #fff;">3/7:</div><a href="#" onClick="onClickCloseTour()"><img  src="{{asset('images/carbon_close.svg')}}" alt="carbon_close"> </a> 
                                    </div>
                                    <div class="tour-bubble-body">
                                       Interaction button  - Shows interaction between supplements and Rx for your safety
                                    </div>
                                    <div class="tour-bubble-footer">
                                       <a class="mr-2" onClick="onClickQuickTourPrev(3)" href="#">PREV</a> <a onClick="onClickQuickTourNext(3)" href="#">NEXT</a>
                                    </div>
                                 </div>
                              </span>
                           </a>
                        </div>
                        @endif
                        <!----- Display interaction option if user has subscribed to wellkasa plus - code end ------>
                        
                     @endif

                     <!----------------- Taking data - code start --------------------->
                     <div class="cab-act-list position-relative">
                        <div class="custom-control custom-switch">
                           <input type="checkbox" onclick="updateTakingMedicineStatus(this,'{{$dataArrKey}}');" data-taking-status="{{$medicineCabinetDataVal['isTaking']}}" data-medicine-id="{{$medicineCabinetDataVal['medicineCabinetId']}}" class="custom-control-input" id="customSwitch{{$dataArrKey}}" {{$medicineCabinetDataVal['isTaking'] == '1' ? 'checked' :''}}>
                           <label class="custom-control-label" for="customSwitch{{$dataArrKey}}">Not Taking</label>
                        </div>
                        <span>Taking
                           <div class="tour-block tour-right-arrow right-0 text-left d-none tour-popup-step mt-2" id="tourStep4">
                              <div class="tour-bubble-header">
                              <div id="step4">4/7:</div><a href="#" onClick="onClickCloseTour()"><img  src="{{asset('images/carbon_close.svg')}}" alt="carbon_close"> </a> 
                              </div>
                              <div class="tour-bubble-body">
                                 If you are taking a medicine toggle this switch
                              </div>
                              <div class="tour-bubble-footer">
                                 <a class="mr-2" onClick="onClickQuickTourPrev(4)" href="#">PREV</a> <a onClick="onClickQuickTourNext(4)" href="#">NEXT</a>
                              </div>
                           </div>
                        </span>
                        
                     </div>
                     <!----------------- Taking data - code end --------------------->

                        <div class="cab-act-list position-relative">
                           <?php
                              $data = array('type'=> $medicineCabinetDataVal['type'],'id' => $medicineCabinetDataVal['medicineCabinetId']);                                  
                           ?>
                           <a href="{{route('cabinet-edit',Crypt::encrypt($data))}}" title="Click to tag condition, add dosage, and notes">
                           <img  src="{{asset('images/highlight.svg')}}" alt="highlight">
                           <span>Edit</span>
                           </a> 
                           <div class="tour-block tour-right-arrow right-0 text-left d-none tour-popup-step" style="right: -16px; top: 60px;" id="tourStep5">
                              <div class="tour-bubble-header">
                              <div id="step5">5/7:</div> <a href="#" onClick="onClickCloseTour()"><img  src="{{asset('images/carbon_close.svg')}}" alt="carbon_close"> </a> 
                              </div>
                              <div class="tour-bubble-body">
                                 Edit details of your medicine and journal your experience 
                              </div>
                              <div class="tour-bubble-footer">
                                 <a class="mr-2" onClick="onClickQuickTourPrev(5)" href="#">PREV</a> <a  onClick="onClickQuickTourNext(5)" href="#">NEXT</a>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
            </div>
         @endforeach

         <!---------------- Display message if no data found for selected filters - Start -------------------->
         <div class="text-center min-h-500 align-items-center justify-content-center" id="noRecordFoundMsg" style="display:none;">
            No records found
         </div>
         <!---------------- Display message if no data found for selected filters - End -------------------->
      @else
         <!---- If medicine data not added by current logged in user then show below message - Start ----->
         <div class="rating-star-block">
            <div class="h5 blue-color">Build and track your integrative medicines here:</div>
            <span>
            1. Click the + button to start searching<br>
            2. Choose the drug or Supplement from the search bar <br>
            3. Once selected click on “Save to cabinet” <br>
            4. Return to WellKabinet&#8482; and press the edit button to journal or take notes
            </span>
         </div>
         <!---- If medicine data not added by current logged in user then show below message - End ----->
      @endif
      <!-- Add profile member id - code start -->
      <input type="hidden" name="profileMemberId" id="profileMemberId" value="{{$profileMemberId ? $profileMemberId : ''}}">
      <!-- Add profile member id - code end -->
   </div>
   <!--- Added Rx Drugs & Natural Medicine Data in accordion Section - End ---> 

   @if(!empty($medicineCabinetData))
      <!---- Checker Detail & Disclaimer Options Div - Start --->
      <div class="wellkabinet-info-option">
         <button class="btn-report download-report mt-3 mb-4 position-static">
            <svg class="m-0 mb-1" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
               <path d="M6 10.5C6 10.3674 6.05268 10.2402 6.14645 10.1464C6.24021 10.0527 6.36739 10 6.5 10C6.63261 10 6.75979 10.0527 6.85355 10.1464C6.94732 10.2402 7 10.3674 7 10.5C7 10.6326 6.94732 10.7598 6.85355 10.8536C6.75979 10.9473 6.63261 11 6.5 11C6.36739 11 6.24021 10.9473 6.14645 10.8536C6.05268 10.7598 6 10.6326 6 10.5ZM6.5 12C6.36739 12 6.24021 12.0527 6.14645 12.1464C6.05268 12.2402 6 12.3674 6 12.5C6 12.6326 6.05268 12.7598 6.14645 12.8536C6.24021 12.9473 6.36739 13 6.5 13C6.63261 13 6.75979 12.9473 6.85355 12.8536C6.94732 12.7598 7 12.6326 7 12.5C7 12.3674 6.94732 12.2402 6.85355 12.1464C6.75979 12.0527 6.63261 12 6.5 12ZM6 14.5C6 14.3674 6.05268 14.2402 6.14645 14.1464C6.24021 14.0527 6.36739 14 6.5 14C6.63261 14 6.75979 14.0527 6.85355 14.1464C6.94732 14.2402 7 14.3674 7 14.5C7 14.6326 6.94732 14.7598 6.85355 14.8536C6.75979 14.9473 6.63261 15 6.5 15C6.36739 15 6.24021 14.9473 6.14645 14.8536C6.05268 14.7598 6 14.6326 6 14.5ZM8.5 10C8.36739 10 8.24021 10.0527 8.14645 10.1464C8.05268 10.2402 8 10.3674 8 10.5C8 10.6326 8.05268 10.7598 8.14645 10.8536C8.24021 10.9473 8.36739 11 8.5 11H13.5C13.6326 11 13.7598 10.9473 13.8536 10.8536C13.9473 10.7598 14 10.6326 14 10.5C14 10.3674 13.9473 10.2402 13.8536 10.1464C13.7598 10.0527 13.6326 10 13.5 10H8.5ZM8 12.5C8 12.3674 8.05268 12.2402 8.14645 12.1464C8.24021 12.0527 8.36739 12 8.5 12H13.5C13.6326 12 13.7598 12.0527 13.8536 12.1464C13.9473 12.2402 14 12.3674 14 12.5C14 12.6326 13.9473 12.7598 13.8536 12.8536C13.7598 12.9473 13.6326 13 13.5 13H8.5C8.36739 13 8.24021 12.9473 8.14645 12.8536C8.05268 12.7598 8 12.6326 8 12.5ZM8.5 14C8.36739 14 8.24021 14.0527 8.14645 14.1464C8.05268 14.2402 8 14.3674 8 14.5C8 14.6326 8.05268 14.7598 8.14645 14.8536C8.24021 14.9473 8.36739 15 8.5 15H13.5C13.6326 15 13.7598 14.9473 13.8536 14.8536C13.9473 14.7598 14 14.6326 14 14.5C14 14.3674 13.9473 14.2402 13.8536 14.1464C13.7598 14.0527 13.6326 14 13.5 14H8.5ZM6 2C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V16C4 16.5304 4.21071 17.0391 4.58579 17.4142C4.96086 17.7893 5.46957 18 6 18H14C14.5304 18 15.0391 17.7893 15.4142 17.4142C15.7893 17.0391 16 16.5304 16 16V7.414C15.9997 7.01631 15.8414 6.63503 15.56 6.354L11.646 2.439C11.3648 2.15798 10.9835 2.00008 10.586 2H6ZM5 4C5 3.73478 5.10536 3.48043 5.29289 3.29289C5.48043 3.10536 5.73478 3 6 3H10V6.5C10 6.89782 10.158 7.27936 10.4393 7.56066C10.7206 7.84196 11.1022 8 11.5 8H15V16C15 16.2652 14.8946 16.5196 14.7071 16.7071C14.5196 16.8946 14.2652 17 14 17H6C5.73478 17 5.48043 16.8946 5.29289 16.7071C5.10536 16.5196 5 16.2652 5 16V4ZM14.793 7H11.5C11.3674 7 11.2402 6.94732 11.1464 6.85355C11.0527 6.75979 11 6.63261 11 6.5V3.207L14.793 7Z" fill="white"/>
            </svg>
            Download Report
         </button>
         <div class="text-center">
            <a class="d-inline btn-report" href="javascript:void(0);" onClick="sendMailPopup(this)" data-send-mail="{{route('wellkabinet-pdf-generation',$profileMemberId ? $profileMemberId:'')}}"><img src="{{asset('images/email-icon.svg')}}" width="15" height="18" class="vertical-align-bottom">Email Report</a>
         </div>
         <ul class="mt-4">
            <li>
               <a href="javascript:void(0)" onclick="showCheckerDetailsPopUp();">
                  <span class="mr-1">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g transform="translate(-608 -1091)"><rect width="24" height="24" transform="translate(608 1091)" fill="none"/><path d="M4.444,18.315A10,10,0,1,1,10,20,10,10,0,0,1,4.444,18.315Zm.695-15.59A8.751,8.751,0,1,0,10,1.25,8.75,8.75,0,0,0,5.139,2.725Zm2.985,12.9a.625.625,0,0,1,0-1.25h1.25V8.813H8.749a.625.625,0,0,1,0-1.25h1.875v6.813h1.25a.625.625,0,0,1,0,1.25ZM9.105,5.282a.875.875,0,1,1,.874.874A.875.875,0,0,1,9.105,5.282Z" transform="translate(610 1093)" fill="#35c0ed"/></g></svg>
                  </span>
                  Checker details
               </a>
            </li>
            <li>
               <a href="javascript:void(0)" onclick="showDisclaimerPopUp();">
                  <span class="mr-1">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g transform="translate(-640 -1091)"><rect width="24" height="24" transform="translate(640 1091)" fill="none"/><path d="M8.11,19.608A1.643,1.643,0,0,1,7.549,18.6h3.236A1.642,1.642,0,0,1,9.2,20H9.173A1.64,1.64,0,0,1,8.11,19.608ZM.616,17.945A.615.615,0,0,1,0,17.33v-.837a.612.612,0,0,1,.055-.253.621.621,0,0,1,.154-.209,8.834,8.834,0,0,0,1.52-1.8A7.772,7.772,0,0,0,2.56,11.27V8.23A6.653,6.653,0,0,1,8.3,1.627V.822a.821.821,0,1,1,1.642,0v.794A6.65,6.65,0,0,1,15.752,8.23v3.04a7.788,7.788,0,0,0,.831,2.959,8.878,8.878,0,0,0,1.544,1.8.616.616,0,0,1,.21.462v.837a.616.616,0,0,1-.616.615ZM7.071,3.1A5.377,5.377,0,0,0,3.8,8.23v3.04a8.757,8.757,0,0,1-1.015,3.6,9.936,9.936,0,0,1-1.5,1.846H17.062a9.936,9.936,0,0,1-1.5-1.846,8.771,8.771,0,0,1-1.015-3.6V8.23A5.378,5.378,0,0,0,7.071,3.1Z" transform="translate(643 1093)" fill="#35c0ed"/></g></svg>
                  </span>
                  Disclaimer
               </a>
            </li>
         </ul>
      </div>
      <!---- Checker Detail & Disclaimer Options Div - End ----->
   @endif


<!-- pop up tour start -->
    <div class="tour-popup mobile-popup d-none" id="tourPopupConfirmation">
        <div class="tourlogo">
            <img  src="{{asset('images/tour-logo.svg')}}" alt="tour-logo"> 
        </div>
        <div class="tour-text text-center">
          <span class="tour-black-text"> Take a quick tour of the </span>
          <span class="tour-blue-text">Wellkasa WellKabinet&#8482; </span>
        </div>
        <div class="bottom-popup-btn">
          <a class="btn-gradient mr-2 ml-2" onClick="tourPopupYes()" href="#">Yes</a> <a class="gradient-border mr-2 ml-2" href="#" onClick="tourPopupLater()">Later</a>
        </div>
    </div>  
<!-- pop up tour start -->

<!-- saved medicine message div start -->
<div class="black-add-popup" id="savedMedicineDiv" style="display:none;">
   <span id="savedMedicineMessage"> </span>
</div>
<!-- saved medicine message div end -->

<!--- Remove medicine pop up window - code start --->
   <div class="tour-popup mobile-popup" style="display:none;" id="removeMedicineDrugPopUp">
      <div class="small-popup-header">
         <span id="removeMedicineDrugPopUpText"></span>
         <input type="hidden" name="removeMedicineId" id="removeMedicineId" value="">
      </div>
      <div class="bottom-popup-btn">
         <a class="btn-gradient mr-2 ml-2" href="javascript:void(0);" id="removeMedicineDrugButton">Remove</a> 
         <a class="gradient-border mr-2 ml-2" href="javascript:void(0);" id="cancelMedicineDrugButton">Cancel</a>
      </div>
   </div>  
<!--- Remove medicine pop up window - code end --->

<!-- Select Conditions popup start -->
  <div class="bottom-popup-small mobile-popup" id="displaySelectConditionOptions" style="display:none;">
     <div class="small-popup-header">
        Select Conditions <a href="javascript:void(0);" id="closeDisplaySelectConditionOptions"><img  src="{{asset('images/closex.svg')}}" alt="closex"></a>
      </div>
      @if(!empty($allConditions))
      <div class="small-popup-body">
         <ul class="popup-condition-list mb-4">
         @foreach($allConditions as $allConditionsKey => $allConditionsValue)
            <li>
               <a class="btn-conditoin conditionName{{$allConditionsValue['id']}}" href="javascript:void(0);" onclick="selectConditionOption('conditionName{{$allConditionsValue['id']}}');" data-condition-id="conditionName{{$allConditionsValue['id']}}" data-condition-list="conditionListName{{$allConditionsValue['id']}}">{{$allConditionsValue['name']}}</a>
            </li>
         @endforeach
         </ul>
         <a class="btn-gradient btn-filter mb-3"  href="javacript:void(0);" id="applySelectedConditionFilter">Apply Filter</a>
      </div>
      @else
      <div class="small-popup-body">
         <h5> No conditions found.</h5>
      </div>
      @endif
   </div> 
<!-- Select Conditions popup end -->
<!-- Select Interactions popup start -->
<div class="bottom-popup-small mobile-popup" id="displayInteractionsOptions" style="display: none;">
     <div class="small-popup-header">
      Select interactions <a href="javascript:void(0);" id="closeDisplayInteractionsOptions"><img  src="{{asset('images/closex.svg')}}" alt="closex"></a>
      </div>
      <div class="small-popup-body p-0">
         @if(!empty($medicineCabinetData))
            <ul class="popup-interactions-list mb-4" id="selected-interaction-option">
               <li>
                  <a class="red-color " href="javascript:void(0);" id="majorSelectionOption"><img src="{{asset('images/major.svg')}}" height="16" width="16" alt="major"> Major</a>
               </li>
               <li>
                  <a class="yellow-color" href="javascript:void(0);" id="moderateSelectionOption"><img src="{{asset('images/moderate.svg')}}" height="16" width="16" alt="Moderate"> Moderate</a>
               </li>
               <li>
                  <a class="green-color" href="javascript:void(0);" id="minorSelectionOption"><img src="{{asset('images/minor.svg')}}" height="16" width="16" alt="Minor"> Minor</a>
               </li>
               <li>
                  <a class="gray-color" href="javascript:void(0);" id="noneSelectionOption"><img src="{{asset('images/none.svg')}}" height="16" width="16" alt="None"> None</a>
               </li>
            </ul>
         @else
            <ul class="mb-4">
               <span>Found No Interactions. Please select any other Rx / Supplements.</span>
            </ul>
         @endif
      </div>
  </div> 
<!-- Select Interactions popup end -->
<!-- Display Interactions popup start -->
<div class="bottom-popup-small mobile-popup" id="displayInteractionsDetails" style="display:none;">
  <div class="small-popup-header">
      <span>
         <label id="interactionHeaderName"> </label>
         <img src="{{asset('images/beta-carotene.svg')}}" id="interactionHeaderImage" alt="">Interactions
      </span> 
      <a href="javascript:void(0);" id="closeDisplayInteractionsDetails">
         <img src="{{asset('images/closex.svg')}}" alt="closex">
      </a>
  </div>
   
   <!----- Interactions Accordion div start ------>
   <div class="small-popup-body scroll-popup">
      <div class="cabinet-accordion interactions-accordion" id="accordionInteractions"></div>
   </div>
   <!----- Interactions Accordion div end ------>

   <!----------------Footer div start-------------------------->
   <div class="text-center mt-2 mb-2 footer-trc">
    <a href="{{config('constants.Footer_TRC_URL')}}" target="_blank">  
      <img width="150" height="35" src="{{asset('images/trclogo.png')}}" alt="trclogo" title="trclogo">
    </a>
    <span class="small">Licensed from Therapeutic Research Center, LLC</span>
    <span class="small">Copyright © 1995-{{date('Y')}} by Therapeutic Research Center, LLC. All Rights Reserved.</span>
  </div>
   <!----------------Footer div end-------------------------->
</div> 
<!-- Display Interactions popup end -->

<!-- Main popup start -->
<div class="modal fade" id="main-popup">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow bg-white rounded">
         <div class="modal-header py-2 px-3">
            <button type="button" id="main-popup-close" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">
                  <svg width="16" height="16" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M12.7439 11.1157L21.955 1.90455C22.1371 1.69199 22.2322 1.41857 22.2214 1.13893C22.2106 0.859291 22.0947 0.594026 21.8968 0.396143C21.6989 0.19826 21.4336 0.0823348 21.154 0.0715335C20.8744 0.0607322 20.6009 0.155851 20.3884 0.33788L11.1773 9.54899L1.96616 0.326769C1.75693 0.117542 1.47316 0 1.17727 0C0.88138 0 0.597608 0.117542 0.388382 0.326769C0.179155 0.535995 0.0616131 0.819767 0.0616131 1.11566C0.0616131 1.41155 0.179155 1.69532 0.388382 1.90455L9.6106 11.1157L0.388382 20.3268C0.272069 20.4264 0.177602 20.549 0.110909 20.6868C0.0442165 20.8247 0.00673854 20.9748 0.00082799 21.1278C-0.00508256 21.2808 0.0207013 21.4334 0.0765619 21.576C0.132423 21.7186 0.217154 21.8481 0.325437 21.9564C0.43372 22.0647 0.563217 22.1494 0.7058 22.2053C0.848384 22.2611 1.00098 22.2869 1.154 22.281C1.30702 22.2751 1.45717 22.2376 1.59501 22.1709C1.73286 22.1042 1.85544 22.0098 1.95505 21.8934L11.1773 12.6823L20.3884 21.8934C20.6009 22.0755 20.8744 22.1706 21.154 22.1598C21.4336 22.149 21.6989 22.0331 21.8968 21.8352C22.0947 21.6373 22.2106 21.372 22.2214 21.0924C22.2322 20.8127 22.1371 20.5393 21.955 20.3268L12.7439 11.1157Z" fill="black"/>
                  </svg>
               </span>
            </button>
         </div>
         <div class="modal-body d-flex justify-content-around ">
         
           <!-- Scan Barcode option button code - start  -->
           <div class="gradient-bg" id="openBarcodeScanPopUp" onclick="showBarcodeScannerPopUp();" >
               <img width="45" height="45"  src="{{asset('images/barcode-reader.svg')}}" alt="Barcode Reader">
               <div class="main-popup-textinfo">
                  SCAN BARCODE <br> Supplements
               </div>
           </div>
           <!-- Scan Barcode option button code - end  -->

           <!-- Quick Add Wellkabinet option button code - start  -->
           <div class="gradient-bg" id="openAddWellkabinetPopUp">
               <img width="45" height="45" src="{{asset('images/quick-add.svg')}}" alt="Quick Add">
               <div class="main-popup-textinfo">
               QUICK SEARCH<br>Medicines/Therapies
               </div>
               
           </div>
           <!-- Quick Add Wellkabinet option button code - start  -->

         </div>
      </div>
   </div>
</div>
<!-- Main popup end -->

<!-- Add medicine popup start -->
<div class="add-drugs-popup add-drug-tab mobile-popup d-none select-dropdown"> 
   <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item">
         
         <a title="Herbs, Foods, Mind-Body Therapies" class="nav-link pt-3 pl-4" id="naturalmedicine-tab"  href="#" role="tab" aria-controls="naturalmedicine" aria-selected="true"> 
            <div><img  src="{{asset('images/tab-icon1.svg')}}" alt="tab-icon"><span>Supplements</span></div>
         </a> 
         
      </li>
      <li class="nav-item">
         
         <a title="" class="nav-link pt-3 pl-lg-5 add-medicine-title" id="naturalmedicine-tab"  href="#" role="tab" aria-controls="naturalmedicine" aria-selected="true"><div> Add Medicine</div></a> 
      
      </li>
      <li class="nav-item">
         
         <a title="Rx or OTC" class="nav-link pr-4 pt-3 justify-content-end" id="rxdrug-tab"  href="#" role="tab" aria-controls="rxdrug" aria-selected="false">
         <div><img  src="{{asset('images/tab-icon2.svg')}}" alt="tab-icon"> <span>Rx</span> </div></a> 
         
      </li>
   </ul>

   <!------- Suggest Supplement Header - Start --------->
   <ul class="nav nav-tabs" id="custom-suggest-supplement-tab" role="tablist" style="display:none;">
      <li class="nav-item">
         <a title="" class="nav-link pt-3 pl-1" id="naturalmedicine-tab"  href="#" role="tab" aria-controls="naturalmedicine" aria-selected="true"></a> 
      </li>
      <li class="nav-item">
         <a title="" class="nav-link pt-3 pl-1 add-supplement-title justify-content-center" id="naturalmedicine-tab"  href="#" role="tab" aria-controls="naturalmedicine" aria-selected="true"><span> Suggest Supplement</span></a> 
      </li>
      <li class="nav-item">
         <a title="" class="nav-link  pt-3" id="rxdrug-tab"  href="#" role="tab" aria-controls="rxdrug" aria-selected="false"></a> 
      </li>
   </ul>
   <!------- Suggest Supplement Header - End --------->

   <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade show active" id="naturalmedicine" role="tabpanel" aria-labelledby="naturalmedicine-tab">
         <form id="add-natural-medicine-form" method="post">
            <div class="form-group">
               <input type="text" autocomplete="off" class="form-control" aria-describedby="" id="medicine" name="medicine" value="" placeholder="Type Supplement or Prescription" >
            </div>
            <div class="form-group">
               <small class="form-text text-muted pb-1">Tag a condition (optional)</small>
               <input type="text" autocomplete="off" class="form-control" aria-describedby="" name="medicine-condition" id="medicine-condition" value="" placeholder="Type condition name">
               <input type="hidden" name="custom-medicine-condition" id="custom-medicine-condition" value="">
            </div>

           
           
            <div class="form-row tagged-condition mb-2">
                  <div class="form-group col-12 col-md-4 mb-0">
                     <div class="form-group mb-2 tagged-condition gradient-dropdown">
                        <label  for="frequency">Frequency</label> 
                        <select class="form-control select2" name="frequency" id="frequency">
                        {{old('frequency')}}
                           @if(!empty($frequency))
                              <option value="" disabled selected>Please select</option>
                              @foreach($frequency as $frequencyValue)
                                 <option value="{{$frequencyValue['id']}}" id="{{$frequencyValue['id']}}" 
                                 >
                        
                                    {{$frequencyValue['name']}}
                        
                                 </option>
                              @endforeach
                           @else
                              <option value="">Found no records for frequency</option>
                           @endif
                        </select>
                        <label id="frequency-error" class="error mb-0" for="frequency" style="display: none;"></label>
                     </div>
                  </div>
                  <div class="form-group col-6 col-md-4 mb-3 dosage-popup">
                     <label for="dosage">Dosage</label>
                     <input type="text" class="form-control" placeholder="Enter your dosage" name="dosage" id="dosage" value="">
                  </div>
                  <div class="form-group col-6 col-md-4 gradient-dropdown mb-3">
                     <label class="d-inherit" for="dosageType">Dosage Type</label>
                     <select id="dosageType" name="dosageType" class="form-control select2 ">
                     @if(!empty($dosageType))
                        <option value="" disabled selected>Please select</option>
                        @foreach($dosageType as $dosageTypeValue)
                           <option value="{{$dosageTypeValue['id']}}"
                             >
                        
                              {{$dosageTypeValue['name']}}
                        
                           </option>
                        @endforeach
                     @else
                        <option value="">Found no records for dosage type</option>
                     @endif
                     </select>
                     <label id="dosageType-error" class="error" for="dosageType" style="display: none;"></label>
                  </div>
                  
               </div>

            <button type="submit" id="save-medicine" class="btn-gradient w-100 border-0">Save to cabinet</button>
            <a href="javascript:void(0);" id="suggest-supplement" class="mt-2 btn-gradient w-100 border-0 d-block">Suggest Missing Supplement</a>
            <div class="text-center w-100">
               <a class="cancel-link pt-3 close-add-drugs-medicine-popup" href="javascript:void(0);">Cancel</a>
            </div>
         </form>
      </div>
      <div class="tab-pane fade" id="suggest-supplement-div" role="tabpanel" aria-labelledby="suggest-supplement-tab">
         <!-- <form id="add-rx-drug-form" method="post"> -->
         <form id="suggest-supplement-form" method="post">
            <div class="form-group">
               <small class="form-text text-muted pb-1">Supplement Name (i.e. : Melatonin 5 mg)</small>
               <input type="text" autocomplete="off" class="form-control" id="supplement-name" name="supplement-name" aria-describedby="" value="" placeholder="Type supplement name">
            </div>
            <div class="form-group">
               <small class="form-text text-muted pb-1">Brand Name (i.e. : Now Foods)</small>
               <input type="text" autocomplete="off" class="form-control" id="supplement-brand-name" name="supplement-brand-name" aria-describedby="" value="" placeholder="Type brand name">
            </div>
            <div class="form-group">
               <small class="form-text text-muted pb-1">Size (i.e. : 60 capsules, 3 Oz)</small>
               <input type="text" autocomplete="off" class="form-control" id="supplement-size" name="supplement-size" aria-describedby="" value="" placeholder="Type size">
            </div>
            
            <button type="submit" id="send-suggestion" class="btn-gradient w-100 border-0">Submit Your Suggestion</button>
            <div class="text-center w-100">
               <a class="cancel-link pt-3 close-add-drugs-medicine-popup" href="javascript:void(0);">Cancel</a>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- Add medicine popup end -->
</div>

<!-- Barcode scanner popup - start -->
<div class="modal fade" id="barcode-scanner">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow bg-white rounded text-center">
         <div class="modal-body p-0" id="barcode-scanner-modal-body">
            <div id="qr-reader" style="width:auto;"></div>
            <div class="p-1" id="qr-reader-results"></div>
            <span id="barcode-popup-message"></span>
            <a href="javascript:void(0);" id="barcode-popup-close" class="cancel-link pb-4" data-dismiss="modal" aria-label="Close">Cancel</a>
         </div>
      </div>
   </div>
</div>
<!-- Barcode scanner popup - end -->


<!-- Barcode scanner fail confirmation popup - start -->
<div class="modal fade" id="barcode-confirmation">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow bg-white rounded">
         <div class="modal-header">
            <h4 class="modal-title col-11 text-center" id="barcode-confirmation-popup-title"></h4>
            <button type="button" id="barcode-confirmation-popup-close" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">
                  <svg width="22" height="22" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M12.7439 11.1157L21.955 1.90455C22.1371 1.69199 22.2322 1.41857 22.2214 1.13893C22.2106 0.859291 22.0947 0.594026 21.8968 0.396143C21.6989 0.19826 21.4336 0.0823348 21.154 0.0715335C20.8744 0.0607322 20.6009 0.155851 20.3884 0.33788L11.1773 9.54899L1.96616 0.326769C1.75693 0.117542 1.47316 0 1.17727 0C0.88138 0 0.597608 0.117542 0.388382 0.326769C0.179155 0.535995 0.0616131 0.819767 0.0616131 1.11566C0.0616131 1.41155 0.179155 1.69532 0.388382 1.90455L9.6106 11.1157L0.388382 20.3268C0.272069 20.4264 0.177602 20.549 0.110909 20.6868C0.0442165 20.8247 0.00673854 20.9748 0.00082799 21.1278C-0.00508256 21.2808 0.0207013 21.4334 0.0765619 21.576C0.132423 21.7186 0.217154 21.8481 0.325437 21.9564C0.43372 22.0647 0.563217 22.1494 0.7058 22.2053C0.848384 22.2611 1.00098 22.2869 1.154 22.281C1.30702 22.2751 1.45717 22.2376 1.59501 22.1709C1.73286 22.1042 1.85544 22.0098 1.95505 21.8934L11.1773 12.6823L20.3884 21.8934C20.6009 22.0755 20.8744 22.1706 21.154 22.1598C21.4336 22.149 21.6989 22.0331 21.8968 21.8352C22.0947 21.6373 22.2106 21.372 22.2214 21.0924C22.2322 20.8127 22.1371 20.5393 21.955 20.3268L12.7439 11.1157Z" fill="black"/>
                  </svg>
               </span>
            </button>
         </div>
         <div class="modal-body" id="barcode-confirmation-scanner-modal-body">
            <input type="hidden" name="barcode-mail-url" id="barcode-mail-url" value="">
            <div class="row">
               <div class="col-6">
                  <div class="form-btn mt-4 mb-4">
                     <button type="submit" class="btn btn-green w-100" id="sendBarcodeEmail">Submit request to add to database</a>
                  </div>
               </div>
               <div class="col-6">
                  <div class="form-btn mt-4 mb-4">
                        <button type="reset" class="btn btn-green w-100" id="scanBarcodeAgain">Doesn't look right. Scan Again</a>
                  </div>
               </div>
            </div>
         </div>        
      </div>
   </div>
</div>
<!-- Barcode scanner fail confirmation popup - end -->

<!---Modal pop up for Level Of Evidence code start--->
<div class="modal fade" id="showLevelOfEvidence">
   <div class="modal-dialog">
      <div class="modal-content border-0 shadow bg-white rounded">
         <div class="modal-header">
            <h4 class="modal-title" id="showLevelOfEvidence-modal-title">Level of Evidence</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">
                  <svg width="22" height="22" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M12.7439 11.1157L21.955 1.90455C22.1371 1.69199 22.2322 1.41857 22.2214 1.13893C22.2106 0.859291 22.0947 0.594026 21.8968 0.396143C21.6989 0.19826 21.4336 0.0823348 21.154 0.0715335C20.8744 0.0607322 20.6009 0.155851 20.3884 0.33788L11.1773 9.54899L1.96616 0.326769C1.75693 0.117542 1.47316 0 1.17727 0C0.88138 0 0.597608 0.117542 0.388382 0.326769C0.179155 0.535995 0.0616131 0.819767 0.0616131 1.11566C0.0616131 1.41155 0.179155 1.69532 0.388382 1.90455L9.6106 11.1157L0.388382 20.3268C0.272069 20.4264 0.177602 20.549 0.110909 20.6868C0.0442165 20.8247 0.00673854 20.9748 0.00082799 21.1278C-0.00508256 21.2808 0.0207013 21.4334 0.0765619 21.576C0.132423 21.7186 0.217154 21.8481 0.325437 21.9564C0.43372 22.0647 0.563217 22.1494 0.7058 22.2053C0.848384 22.2611 1.00098 22.2869 1.154 22.281C1.30702 22.2751 1.45717 22.2376 1.59501 22.1709C1.73286 22.1042 1.85544 22.0098 1.95505 21.8934L11.1773 12.6823L20.3884 21.8934C20.6009 22.0755 20.8744 22.1706 21.154 22.1598C21.4336 22.149 21.6989 22.0331 21.8968 21.8352C22.0947 21.6373 22.2106 21.372 22.2214 21.0924C22.2322 20.8127 22.1371 20.5393 21.955 20.3268L12.7439 11.1157Z" fill="black"/>
                  </svg>
               </span>
            </button>
         </div>
         <div class="modal-body" id="showLevelOfEvidence-modal-body">
               
            <div align="center">
               <table border="0" cellspacing="0" cellpadding="0">
                  <tbody>
                     <tr>
                        <td valign="top" style="padding:0;">
                           <table border="0" cellspacing="6" cellpadding="0" style="width:100%;">
                              <tbody>
                                 <tr>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;"><b>Level</b></p>
                                    </td>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;"><b> Definition</b></p>
                                    </td>
                                 </tr>
                                 <tr id="a1" class="levelOfEvidenceDefinitionContent">
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">A</p>
                                    </td>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">High-quality randomized controlled trial (RCT)</p>
                                    </td>
                                 </tr>
                                 <tr id="a2" class="levelOfEvidenceDefinitionContent">
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">A</p>
                                    </td>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">High-quality meta-analysis (quantitative systematic review)</p>
                                    </td>
                                 </tr>
                                 <tr id="b1" class="levelOfEvidenceDefinitionContent">
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">B</p>
                                    </td>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">Nonrandomized clinical trial</p>
                                    </td>
                                 </tr>
                                 <tr id="b2" class="levelOfEvidenceDefinitionContent">
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">B</p>
                                    </td>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">Nonquantitative systematic review</p>
                                    </td>
                                 </tr>
                                 <tr id="b3" class="levelOfEvidenceDefinitionContent">
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">B</p>
                                    </td>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">Lower quality RCT</p>
                                    </td>
                                 </tr>
                                 <tr id="b4" class="levelOfEvidenceDefinitionContent">
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">B</p>
                                    </td>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">Clinical cohort study</p>
                                    </td>
                                 </tr>
                                 <tr id="b5" class="levelOfEvidenceDefinitionContent">
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">B</p>
                                    </td>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">Case-control study</p>
                                    </td>
                                 </tr>
                                 <tr id="b6" class="levelOfEvidenceDefinitionContent">
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">B</p>
                                    </td>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">Historical control</p>
                                    </td>
                                 </tr>
                                 <tr id="b7" class="levelOfEvidenceDefinitionContent">
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">B</p>
                                    </td>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">Epidemiologic study</p>
                                    </td>
                                 </tr>
                                 <tr id="c1" class="levelOfEvidenceDefinitionContent">
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">C</p>
                                    </td>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">Consensus</p>
                                    </td>
                                 </tr>
                                 <tr id="c2" class="levelOfEvidenceDefinitionContent">
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">C</p>
                                    </td>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">Expert opinion</p>
                                    </td>
                                 </tr>
                                 <tr id="d1" class="levelOfEvidenceDefinitionContent">
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">D</p>
                                    </td>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">Anecdotal evidence</p>
                                    </td>
                                 </tr>
                                 <tr id="d2" class="levelOfEvidenceDefinitionContent">
                                    <td style="padding: 0.75pt;">
                                          <p style="font-size:11pt;margin:0;">D</p>
                                    </td>
                                    <td style="padding: 0.75pt;">
                                          <p style="font-size:11pt;margin:0;">In vitro or animal study</p>
                                    </td>
                                 </tr>
                                 <tr id="d3" class="levelOfEvidenceDefinitionContent">
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">D</p>
                                    </td>
                                    <td style="padding:0.75pt;">
                                          <p style="font-size:11pt;margin:0;">Theoretical based on pharmacology</p>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
                  
         </div>
      </div>
   </div>
</div>
<!---Modal pop up for Level Of Evidence code end--->

<!------ Alert to Select atleast one condition pop up window - code start ------->
<div class="modal fade" id="atleastSelectConditionConfirmation">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="atleast-select-modal-title"> Alert </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="atleast-select-modal-body">
       <span class="small-popup-header" style="font-size: 20px; padding: 0;">Please select atleast one condition for filter.</span>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-green btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>
<!------ Alert to Select atleast one condition pop up window - code end ------->

<!------ Alert when no "taking" data is available pop up window - code start ------->
<div class="modal fade" id="noTakingDataAvailablePopUp">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="taking-modal-title"> Alert </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="taking-modal-body">
       <span class="small-popup-header" style="font-size: 20px; padding: 0;">No "Taking" data available.</span>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-green btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>
<!------ Alert when no "taking" data is available pop up window - code end ------->

<!---Modal pop up for checker details code start--->
<div class="modal fade" id="checkerDetail">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow bg-white rounded">
      <div class="modal-header">
        <h4 class="modal-title" id="checkerdetail-modal-title">Checker details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><svg width="22" height="22" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M12.7439 11.1157L21.955 1.90455C22.1371 1.69199 22.2322 1.41857 22.2214 1.13893C22.2106 0.859291 22.0947 0.594026 21.8968 0.396143C21.6989 0.19826 21.4336 0.0823348 21.154 0.0715335C20.8744 0.0607322 20.6009 0.155851 20.3884 0.33788L11.1773 9.54899L1.96616 0.326769C1.75693 0.117542 1.47316 0 1.17727 0C0.88138 0 0.597608 0.117542 0.388382 0.326769C0.179155 0.535995 0.0616131 0.819767 0.0616131 1.11566C0.0616131 1.41155 0.179155 1.69532 0.388382 1.90455L9.6106 11.1157L0.388382 20.3268C0.272069 20.4264 0.177602 20.549 0.110909 20.6868C0.0442165 20.8247 0.00673854 20.9748 0.00082799 21.1278C-0.00508256 21.2808 0.0207013 21.4334 0.0765619 21.576C0.132423 21.7186 0.217154 21.8481 0.325437 21.9564C0.43372 22.0647 0.563217 22.1494 0.7058 22.2053C0.848384 22.2611 1.00098 22.2869 1.154 22.281C1.30702 22.2751 1.45717 22.2376 1.59501 22.1709C1.73286 22.1042 1.85544 22.0098 1.95505 21.8934L11.1773 12.6823L20.3884 21.8934C20.6009 22.0755 20.8744 22.1706 21.154 22.1598C21.4336 22.149 21.6989 22.0331 21.8968 21.8352C22.0947 21.6373 22.2106 21.372 22.2214 21.0924C22.2322 20.8127 22.1371 20.5393 21.955 20.3268L12.7439 11.1157Z" fill="black"/></svg></span>
        </button>
      </div>
      <div class="modal-body" id="checkerdetail-modal-body">
         <h2>Interaction details</h2>
         <h3 class="ic-info">
            <img class="mr-1" width="30" height="30" src="{{asset('images/red-info.png')}}" alt="major">
           Major
         </h3>
         <p>Do not take this combination</p>
         <h3 class="ic-info">
            <img class="mr-1" width="30" height="30" src="{{asset('images/orange-info.png')}}" alt="moderate">
            Moderate
         </h3>
         <p>Be cautious with this combination</p>
         <h3 class="ic-info">
            <img class="mr-1" width="30" height="30" src="{{asset('images/green-info.png')}}" alt="minor">
           Minor
         </h3>
         <p>Be watchful with this combination</p>
         <h3 class="ic-info">
            <img class="mr-1" width="30" height="30" src="{{asset('images/gray-info.png')}}" alt="minor">
            None
         </h3>
         <p>No interactions</p>

        <div class="medicine-types">
            <h2>Medicine Types</h2>
            <div class="row pt-3">
               <div class="col-12 col-lg-6">
                  <p>
                     <img width="40" height="40" src="{{asset('images/beta-carotene.svg')}}" alt="Supplements">
                     Supplements
                  </p>
               </div>
               <div class="col-12 col-lg-6">
                  <p>
                     <img width="40" height="40" src="{{asset('images/rx-drug.svg')}}" alt="Rx">
                     Rx
                  </p>
               </div>
            </div> 
        </div>
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for checker details code end--->

<!---Modal pop up for disclaimer code start--->
<div class="modal fade" id="showDisclaimer">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow bg-white rounded">
      <div class="modal-header">
        <h4 class="modal-title" id="showDisclaimer-modal-title">Disclaimer</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><svg width="22" height="22" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M12.7439 11.1157L21.955 1.90455C22.1371 1.69199 22.2322 1.41857 22.2214 1.13893C22.2106 0.859291 22.0947 0.594026 21.8968 0.396143C21.6989 0.19826 21.4336 0.0823348 21.154 0.0715335C20.8744 0.0607322 20.6009 0.155851 20.3884 0.33788L11.1773 9.54899L1.96616 0.326769C1.75693 0.117542 1.47316 0 1.17727 0C0.88138 0 0.597608 0.117542 0.388382 0.326769C0.179155 0.535995 0.0616131 0.819767 0.0616131 1.11566C0.0616131 1.41155 0.179155 1.69532 0.388382 1.90455L9.6106 11.1157L0.388382 20.3268C0.272069 20.4264 0.177602 20.549 0.110909 20.6868C0.0442165 20.8247 0.00673854 20.9748 0.00082799 21.1278C-0.00508256 21.2808 0.0207013 21.4334 0.0765619 21.576C0.132423 21.7186 0.217154 21.8481 0.325437 21.9564C0.43372 22.0647 0.563217 22.1494 0.7058 22.2053C0.848384 22.2611 1.00098 22.2869 1.154 22.281C1.30702 22.2751 1.45717 22.2376 1.59501 22.1709C1.73286 22.1042 1.85544 22.0098 1.95505 21.8934L11.1773 12.6823L20.3884 21.8934C20.6009 22.0755 20.8744 22.1706 21.154 22.1598C21.4336 22.149 21.6989 22.0331 21.8968 21.8352C22.0947 21.6373 22.2106 21.372 22.2214 21.0924C22.2322 20.8127 22.1371 20.5393 21.955 20.3268L12.7439 11.1157Z" fill="black"/></svg></span>
        </button>
      </div>
      <div class="modal-body" id="showDisclaimer-modal-body">
          <p>The interaction checker does not check for drug-drug or supplement-supplement interactions. This is not an all-inclusive comprehensive list of potential interactions and is for informational purposes only. Not all interactions are known or well reported in the scientific literature, and new interactions are continually being reported. Input is needed from a qualified healthcare provider including a pharmacist before starting any therapy. Application of clinical judgement is necessary.</p>  
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for disclaimer code end--->

<!---Modal pop up for wellkabinet report html code - start --->
<div class="modal fade" id="messageWellkabinetReportPopUp">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="wellkabinet-report-select-modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="wellkabinet-report-select-modal-body">
       
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for wellkabinet report html code - end --->

<!---Modal pop up for email report with attachment code start---->
<div class="modal fade sendReportMailModalPopup" id="sendReportMailModalPopup"  data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="sendmail-modal-title">Email Report</h4>
        <button type="button" class="close" id="sendMailModalClose" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="sendmail-modal-body">
        
        <form action="" id="sendMailForm" class="floating">
          
          <div class="form-group ">    
              <input id="toMail" type="email" placeholder=" " class="form-control" name="toMail" value="" required>
              <label for="toMail" class="float-label">{{ __("Recepient's Email") }}</label>
          </div>
          
          <div class="form-group mb-2">    
              <label for="pdfAttached"><i class="fa fa-file-pdf-o mr-2" aria-hidden="true"></i><em>{{ __('PDF report will be attached with this email') }}</em></label>
          </div>

          <div class="row">
              <div class="col-6">
                  <div class="form-btn mt-4 mb-4">
                      <button type="submit" class="btn btn-green w-100" id="sendMailButton">Send Mail</a>
                  </div>
              </div>
              <div class="col-6">
                  <div class="form-btn mt-4 mb-4">
                      <button type="reset" class="btn btn-green w-100" id="resetButton">Reset</a>
                  </div>
              </div>
          </div>
          <div class="form-group mb-2" style="text-align: center;">
            <label id="loadingMsg"></label>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for email report with attachment code end---->

<!-- Show Background black for add medicine popup - start -->
<div class="modal-backdrop fade show d-none" id="background-modal-effect"></div>
<!-- Show Background black for add medicine popup - end -->

@endsection

@push('styles')
<!-- Added select 2 css dropdown -->
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
<style>
   /* update icon for interaction icon in checker detail popup */
   .ic-info::before {
    content: none !important;
   } 
   #html5-qrcode-anchor-scan-type-change{
      display:none !important;
   }
   div#qr-reader img{
      display:none !important;
   }
   #qr-reader{
      width: 100%;
      height: 100%;
   }

   #html5-qrcode-button-camera-permission, #html5-qrcode-button-camera-start, #html5-qrcode-button-camera-stop{
      background: linear-gradient(266.86deg, #35C0ED -10.35%, #7380B4 75.85%);
      box-shadow: 0px 2px 48px rgb(0 0 0 / 8%);
      border-radius: 4px;
      border: none;
      font-weight: bold;
      font-size: 12px;
      line-height: 14px;
      text-align: center;
      letter-spacing: 1px;
      color: #FFFFFF;
      padding: 16px 28px;
      width: 100%!important;
      border: 0!important;
   }
</style>
@endpush

@push('scripts')

<!-- barcodejs library -->
<script src="{{ asset('js/html5-qrcode.js') }}"></script>

<!-- Added select 2 js dropdown -->
<script src="{{ asset('js/select2.min.js') }}" defer></script>

@if ($agent->isMobile())
  <!--- If screen is mobile view then check the localstorage of therapy link jump for mobile view - code start ---->
    <script type="text/javascript">
      // Local acordionToOpenSiteUrlInMobile url in localstorage then redirect therapy details page
      if(localStorage.getItem("acordionToOpenInMobile") != null && localStorage.getItem("acordionToOpenSiteUrlInMobile") != null){ // check if acordionToOpenInMobile localStorage has value
        var acordionToOpenSiteUrl = localStorage.getItem("acordionToOpenSiteUrlInMobile");
        window.location.href = acordionToOpenSiteUrl;
      }
    </script> 
  <!--- If screen is mobile view then check the localstorage of therapy link jump for mobile view - code end ----> 
@else
  <!--- If screen is desktop view then check the localstorage of therapy link jump for mobile view - code start ---->
    <script type="text/javascript">
      // Local acordionToOpenSiteUrl url in localstorage then redirect therapy details page
      if(localStorage.getItem("acordionToOpen") != null && localStorage.getItem("acordionToOpenSiteUrl") != null){ // check if accordionToOpen localStorage has value
        var acordionToOpenSiteUrl = localStorage.getItem("acordionToOpenSiteUrl");
        window.location.href = acordionToOpenSiteUrl;
      }
    </script>  
  <!--- If screen is desktop view then check the localstorage of therapy link jump for mobile view - code end ---->
@endif

<script type="text/javascript">

$(document).ready(function(){
  
   //Select2 option for frequency & dosageType dropdown
   $('.select2').select2({
      closeOnSelect: true,
        "language": {
            "noResults": function(searchedTerm) {
                // If the there no records then add "no-data-select2" class to adjust the search dropdown
                $(".select2-results").addClass('no-data-select2');
                return "No Result Found";
            }
        },
   });

   // On search data from dropdown check if there is results then remove "no-data-select2" class
   $('body').on('keyup', '.select2-search__field', function() {
      if($(".select2-results__options li").length > 1){
         $(".select2-results").removeClass('no-data-select2');
      }else{
         $(".select2-results").addClass('no-data-select2');
      }
   });

   // Suggest supplement screen
   $('#suggest-supplement').on('click', function() {
      $("#naturalmedicine").removeClass('show active');
      $("#suggest-supplement-div").addClass('show active');
      $("#myTab").hide();
      $("#custom-suggest-supplement-tab").show();
   });


   // open main popup with the barcode scan 
   $('#open-main-popup').on('click',function(){
      closeAllPopUps(); // Close all pop ups
      // $("#main-popup").modal("show"); // Commented popup for barcode search + manual search option
      // Open the manual search and add wellkabinet popup
      $("#openAddWellkabinetPopUp").trigger("click");
   });
   
   // On click of add button show pop up to add drugs & add natural medicine tab. - code start
   $('#openAddWellkabinetPopUp').on('click',function(){
      closeAllPopUps(); // Close all pop ups
      $('.add-drugs-popup').removeClass('d-none');
      $('#background-modal-effect').removeClass('d-none');
   });
   // On click of add button show pop up to add drugs & add natural medicine tab. - code end

   //when there is an AJAX request and the user is not authenticated then reload the page - code start
   $(document).ajaxError(function (event, xhr, settings, error) {
      if(xhr.status == 401) {
         alert("Your session has timed out. Please login.")
         window.location.reload();
      }
   });
   //when there is an AJAX request and the user is not authenticated then reload the page - code end
 
   // Auto complete ajax call for medicine dropdown - code start
   // var medicine_list_url_old = "{{ route('get-natural-medicine-list') }}";  
   var medicine_list_url = "{{ route('autocomplete-wellkabinet') }}";    
  
   $('input#medicine').typeahead({
      items:'all',
      source: function (query, process) {
         return $.ajax({
            url: medicine_list_url,
            type: 'get',
            data: { query: query, profileMemberId : currentProfileMemberId() },
            dataType: 'json',
            success: function (result) {
               var resultList = result.map(function (item) {
                     var aItem = { id: item.id, name: item.name };
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
         $('input#medicine').attr('value', item.id);
         return item.name;
      }
   });
   // Auto complete ajax call for medicine dropdown - code end

   // Auto complete ajax call for drugs dropdown - code start
   var drug_list_url = "{{ route('get-drugs-list') }}";
   $('input#drug').typeahead({
      items:'all',
      source: function (query, process) {
         return $.ajax({
            url: drug_list_url,
            type: 'get',
            data: { query: query, profileMemberId : currentProfileMemberId() },
            dataType: 'json',
            success: function (result) {
               var resultList = result.map(function (item) {
                     var aItem = { id: item.id, name: item.name };
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
         $('input#drug').attr('value', item.id);
         return item.name;
      }
   });
   // Auto complete ajax call for drugs dropdown - code end


   // Auto complete ajax call for conditions dropdown - code start
   var condition_url = "{{ route('get-conditions-list') }}"
   $('input#drug-condition').typeahead({
      items:'all',
      source: function (query, process) {
         return $.ajax({
            url: condition_url,
            type: 'get',
            data: { query: query },
            dataType: 'json',
            success: function (result) {
               // if condition is not selected from the dropdown, then clear previous id of condition from "drug-condition" input.
               if(result.length === 0) {
                  $('input#drug-condition').attr('value', '');
                  $('#custom-drug-condition').val($('input#drug-condition').val());
               }else{
                  $('#custom-drug-condition').val('');
               }
               var resultList = result.map(function (item) {
                     var aItem = { id: item.id, name: item.name };
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
         $('input#drug-condition').attr('value', item.id);
         return item.name;
      }
   });

   $('input#medicine-condition').typeahead({
      items:'all',
      source: function (query, process) {
         return $.ajax({
            url: condition_url,
            type: 'get',
            data: { query: query },
            dataType: 'json',
            success: function (result) {
               // if condition is not selected from the dropdown, then clear previous id of condition from "medicine-condition" input.
               if(result.length === 0) {
                  $('input#medicine-condition').attr('value', '');
                  $('#custom-medicine-condition').val($('input#medicine-condition').val());
               }else{
                  $('#custom-medicine-condition').val('');
               }
               var resultList = result.map(function (item) {
                     var aItem = { id: item.id, name: item.name };
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
         $('input#medicine-condition').attr('value', item.id);
         return item.name;
      }
   });
   // Auto complete ajax call for conditions dropdown - code end


   // function to clear input values from add natural medicine tab & add rx drug tab
   function clearInputFields(){
      $('#medicine').val('').attr('value', '');
      $('#medicine-condition').val('').attr('value', '');
      $('#drug').val('').attr('value', '');
      $('#drug-condition').val('').attr('value', '');
      // Clear frequency data value
      $('#frequency').val('');
      // Clear dosage data value
      $('#dosage').val('');
      // Clear dosage type data value
      $('#dosageType').val('');

      // Suggest supplement screen data fields
      $('#supplement-name').val('').attr('value', '');
      $('#supplement-brand-name').val('').attr('value', '');
      $('#supplement-size').val('').attr('value', '');
   }

   // On click of cancel button from pop up to close add drugs & add natural medicine tab. - code start
   $('.close-add-drugs-medicine-popup').on('click',function(){
      $('.add-drugs-popup').addClass('d-none');
      $('#background-modal-effect').addClass('d-none');
      $('#naturalmedicine').addClass('show active');
      $('#suggest-supplement-div').removeClass('show active');
      $("#custom-suggest-supplement-tab").hide();
      $("#myTab").show();
      clearInputFields(); // clear input values from the form
      
   });
   // On click of cancel button from pop up to close add drugs & add natural medicine tab. - code end

   // validate add natural medicine tab form - code start
   $('#add-natural-medicine-form').validate({
      rules:{
         medicine : {
            required : true,
            normalizer: function(value) {
               // Trim the value of the input
               return $.trim(value);
            }
         }
      },
      messages:{
         medicine : {
            required : "Please enter supplement or prescription drug.",
         }
      }
   });
   // validate add natural medicine tab form - code end


   $("#medicine , #medicine-condition").on("input", function() {
      if($(this).val() == ''){
         $(this).val('').attr('value', '');
      }
   });

   $("#drug , #drug-condition").on("input", function() {
      if($(this).val() == ''){
         $(this).val('').attr('value', '');
      }
   });

   // validate add rx drug tab form - code start
   $('#add-rx-drug-form').validate({
      rules:{
         drug : {
            required : true,
            normalizer: function(value) {
               // Trim the value of the input
               return $.trim(value);
            }
         }
      },
      messages:{
         drug : {
            required : "Please enter drug name.",
         }
      }
   });
   // validate add rx drug tab form - code end

   // get profile member id from the hidden input
   var profileMemberId = $("#profileMemberId").val();
   
   // Save natural medicine in medicine cabinet - code start
   $('#add-natural-medicine-form').on('submit', function (e) {

      e.preventDefault();
      
      var formValidated = $('#add-natural-medicine-form').valid();
      
      // check if form is validated then send data
      if(formValidated === true){
         var customAddedConditionText = '';
         var selectedNaturalMedicineId = $("#medicine").attr('value');         
         // check if the condition is selected from the dropdown then process that data else check if custom condition is added and use that as condition.
         let selectedNaturalMedicineConditionId = $("#medicine-condition").attr('value');
         if(selectedNaturalMedicineConditionId!=''){
            selectedNaturalMedicineConditionId = selectedNaturalMedicineConditionId;
         }else{
            if($("#medicine-condition").val() !=''){
               let customConditionText = $('#custom-medicine-condition').val();
               if(customConditionText != ''){
                  customAddedConditionText = customConditionText;
                  selectedNaturalMedicineConditionId = '00';
               }
            }
            
         }

         // Store the frequency value if exists in the frequencyValue variable
         var frequencyValue = $('#frequency').val() ? $('#frequency').val() : '';
         // Store the dosage value if exists in the dosageValue variable
         var dosageValue = $('#dosage').val() ? $('#dosage').val() : '';
         // Store the dosage type value if exists in the dosageTypeValue variable
         var dosageTypeValue = $('#dosageType').val() ? $('#dosageType').val() : '';
         
         // check what type of search is (i.e, Product, Therapy, RxDrug)
         if(selectedNaturalMedicineId.includes('therapy')){        
            let id = selectedNaturalMedicineId.replace('-therapy','');
            var data = { naturalMedicineId: id, conditionId : selectedNaturalMedicineConditionId, 
            customConditionText:  customAddedConditionText, frequency : frequencyValue, dosage : dosageValue,
            dosageType : dosageTypeValue, isMedicineTab: '1', profileMemberId : profileMemberId };
         }
         if(selectedNaturalMedicineId.includes('rxdrug')){
            let id = selectedNaturalMedicineId.replace('-rxdrug','');
            var data = { drugId: id, conditionId : selectedNaturalMedicineConditionId, 
            customConditionText:  customAddedConditionText, frequency : frequencyValue, dosage : dosageValue,
            dosageType : dosageTypeValue, isMedicineTab: '2', profileMemberId : profileMemberId };
         }
         if(selectedNaturalMedicineId.includes('product')){
            let id = selectedNaturalMedicineId.replace('-product','');
            var data = { productId: id, conditionId : selectedNaturalMedicineConditionId, 
            customConditionText:  customAddedConditionText, frequency : frequencyValue, dosage : dosageValue,
            dosageType : dosageTypeValue, isMedicineTab: '3', profileMemberId : profileMemberId };
         }
         
         // Convert the data response object into json string 
         var jsonString = JSON.stringify(data);
         $.ajax({
            url: "{{route('save-medicine-cabinet')}}",
            type: "POST",
            data: jsonString,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            contentType: 'application/json; charset=utf-8',
            success: function (response)
            {
               window.location.reload();
            }
         });
      }
   });
   // Save natural medicine in medicine cabinet - code end

   // Save rx drug in medicine cabinet - code start
   $('#add-rx-drug-form').on('submit', function (e) {

      e.preventDefault();
      
      var rxFormValidated = $('#add-rx-drug-form').valid();
      
      // check if form is validated then send data
      if(rxFormValidated === true){
         var customAddedConditionText = '';
         var selectedDrugId = $("#drug").attr('value');
         // check if the condition is selected from the dropdown then process that data else check if custom condition is added and use that as condition.
         let selectedDrugConditionId = $("#drug-condition").attr('value');
         if(selectedDrugConditionId!=''){
            selectedDrugConditionId = selectedDrugConditionId;
         }else{
            if($("#drug-condition").val() !=''){
               let customConditionText = $('#custom-drug-condition').val();
               if(customConditionText != ''){
                  customAddedConditionText = customConditionText;
                  selectedDrugConditionId = '00';
               }
            }
         }

         var data = { drugId: selectedDrugId, conditionId : selectedDrugConditionId, customConditionText:  customAddedConditionText, isMedicineTab: '2', profileMemberId : profileMemberId };
         var jsonString = JSON.stringify(data);
         $.ajax({
            url: "{{route('save-medicine-cabinet')}}",
            type: "POST",
            data: jsonString,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            contentType: 'application/json; charset=utf-8',
            success: function (response)
            {
               window.location.reload();
            }
         });
      }
   });
   // Save rx drug in medicine cabinet - code end


   // validate supplement form - code start
   $('#suggest-supplement-form').validate({
      rules:{
         "supplement-name" : {
            required : true,
            normalizer: function(value) {
               // Trim the value of the input
               return $.trim(value);
            },
            maxlength : 100
         },
         "supplement-brand-name" : {
            required : true,
            normalizer: function(value) {
               // Trim the value of the input
               return $.trim(value);
            },
            maxlength : 100
         },
         "supplement-size" : {
            required : true,
            normalizer: function(value) {
               // Trim the value of the input
               return $.trim(value);
            },
            maxlength : 20
         }
      },
      messages:{
         "supplement-name" : {
            required : "Please enter supplement name.",
            maxlength : "Maximum only 100 characters allowed.",
         },
         "supplement-brand-name" : {
            required : "Please enter supplement brand name.",
            maxlength : "Maximum only 100 characters allowed.",
         },
         "supplement-size" : {
            required : "Please enter size.",
            maxlength : "Maximum only 20 characters allowed.",
         }
      }
   });
   // validate supplement form - code end

   // Send supplement data form - code start
   $('#suggest-supplement-form').on('submit', function (e) {

      e.preventDefault();

      var supplementFormValidated = $('#suggest-supplement-form').valid();

      // check if form is validated then send data
      if(supplementFormValidated === true){
        
         var supplementName = $('#supplement-name').val();
         var supplementBrandName = $('#supplement-brand-name').val();
         var supplementSize = $('#supplement-size').val();
         
         var data = { supplementName: supplementName, supplementBrandName : supplementBrandName, supplementSize:  supplementSize};
         var jsonString = JSON.stringify(data);
         $.ajax({
            url: "{{route('send-suggested-supplement')}}",
            type: "POST",
            data: jsonString,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            contentType: 'application/json; charset=utf-8',
            beforeSend: function(){
               $("#send-suggestion").text('Sending email.. Please Wait..').attr('disabled', 'disabled');
            },
            complete: function(){
               $("#send-suggestion").text('Email your input request').attr("disabled", false);
            },
            success: function (response)
            {

               $('#responseMessage').html('<div class="alert alert-info mb-0"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span>'+response.message+'</span></div>');
               $('#responseMessage').show();

               setTimeout(function() { $('#responseMessage').hide();  }, 5000);                     

               $('.close-add-drugs-medicine-popup').trigger('click');
            }
         });
      }
   });
   // Send supplement data form - code end


   // ---------------- Info icon - code start ------------------- //
   
   // function to show the info data popup
   function infoPopUpShow(id){
      $('.info-popup').hide(); // close all info popup
      $('#info-popup-'+id).show(); // open specific popup
   }
   
   // function to hide the info data popup
   function infoPopUpHide(){
      $('.info-popup').hide();
   }

   // on click of info icon hide/show pop up
   $('.cabinet-info-icon').on('click',function()
   {  
      var info_id = $(this).attr('data-id');
      // if info popup is open then close the popup else open the popup
      if($('#info-popup-'+info_id).is(':visible') == true){
         infoPopUpHide();
      }else{
         infoPopUpShow(info_id);
      }

   });

   // Hide popup details from info icon next to product name when clicked outside anywhere in the html document
   $('html').click(function(event) {
      if(event.target != ""){
         setTimeout(() => { // Checks if info-popup is visible after 1 second
            // if visible and class name is not empty wherever clicked the execute this code
            if($(".info-popup").is(":visible") === true && event.target.innerText || event.target.className != "" || event.target.id != ""){
                  var id = event.target.getAttribute("data-id");
                  if($(event.target).hasClass("info-icons") || $(event.target).hasClass("info-popup") || $(event.target).hasClass("popup-info-date") || $(event.target).hasClass("date-detail") || $(event.target).parent().hasClass('medicineName'+id) == true){
                     // if id name is any above class or id then stop behaviour of hide/show
                     event.stopPropagation();
                  }else{
                     // else hide the info details
                     infoPopUpHide();
                  }
            }
         }, 100);
      }    
   });

   // ---------------- Info icon - code end ------------------- //
});


// Display checker details pop up
function showCheckerDetailsPopUp(){
   $('#checkerDetail').modal('show');
   $('.modal-backdrop').attr('style','opacity:0 !important;');
}

// Display disclaimer pop up
function showDisclaimerPopUp(){
   $('#showDisclaimer').modal('show');
   $('.modal-backdrop').attr('style','opacity:0 !important;');
}

// Close all pop ups
function closeAllPopUps(){
   // Close add drugs/natural medicine pop up
   $('.add-drugs-popup').addClass('d-none'); 
   // Close add black background for medicine pop up
   $('#background-modal-effect').addClass('d-none'); 
   // Close display interactions pop up
   $("#displayInteractionsDetails").hide(); 
   // Close select interactions options pop up
   $("#displayInteractionsOptions").hide();
   // Close remove drugs/natural medicine pop up
   $('#removeMedicineDrugPopUp').hide(); 
   // Close select conditions options pop up
   $('#displaySelectConditionOptions').hide(); 
   // Close the main 2 selection option popup
   $("#main-popup").modal('hide');
   // Close the barcode scan popup
   $("#barcode-scanner").modal('hide');  
}

// Display saved medicine message popup - code start
const savedMsgFromSession = "{{Session::get('savedMedicineData')}}"
if(savedMsgFromSession !=''){
   // display popup
   $("#savedMedicineDiv").fadeIn();
   // display message from the session in popup
   $("#savedMedicineMessage").text(savedMsgFromSession); 
   // terminate the code inside this function after 3 seconds
   setTimeout(function() { 
      // hide popup 
      $("#savedMedicineDiv").fadeOut(); 
      // clear message in popup
      $("#savedMedicineMessage").text('');
      // delete session based message from savedMedicineData value
      "{{Session::forget('savedMedicineData')}}"
   }, 3000);
}
// Display saved medicine message popup - code end


//----------------------------------  Delete Medicine Cabinet Pop up --------------------------- //

function deleteMedicineCabinet(id){
   // Close all pop ups
   closeAllPopUps(); 
   
   // Open pop up
   $('#removeMedicineDrugPopUp').show();

   // Get medicine name
   var medicineName = $('.medicineName'+id).attr('data-name');

   // Set message
   $('#removeMedicineDrugPopUpText').html('Remove '+medicineName+' ?');
   
   // Set hidden input type value 
   $('#removeMedicineId').val(id);
}

// Close Remove Drugs / natural medicine confirmation popup on close / cancel buton
$('#closeRemoveMedicineDrugPopUp, #cancelMedicineDrugButton').on('click',function(){
   
   // Close pop up
   $('#removeMedicineDrugPopUp').hide();

   //Remove set hidden input value
   $('#removeMedicineId').val('');

});


//----------------------------------  Delete Medicine API  --------------------------- //

// Call Ajax for Delete Medicine
$('#removeMedicineDrugButton').on('click',function()
{   
   
   var modalId = $('#removeMedicineId').val();
   var csrf_token = $('meta[name="csrf-token"]').attr('content');
   var redirect_route = "{{ route('medicine-cabinet') }}";
   let profileMemberId = "{{$profileMemberId}}";
   if(profileMemberId!=''){
      redirect_route = "{{ route('medicine-cabinet',Crypt::encrypt($profileMemberId)) }}";
   }
   $.ajax({
      url: "{{route('delete-medicine')}}",
      type : 'delete',
      dataType: "json",
      "data":{ _token: csrf_token,"medicineCabinetId":modalId},
      success: function(res){
         if(res.status == 0){
            $('#removeMedicineDrugPopUp').hide();
            window.location.href = redirect_route;
         }
         else
         {
            window.location.href = redirect_route;
         }
      }
   });    
   
});

// //----------------------------------  Update Taking Medicine Status API  --------------------------- //

function updateTakingMedicineStatus(id,dataArrKey){

   var cardId = 'cardNumber'+dataArrKey;
   let checkboxId = $(id).attr('id');
   let medicineTakingStatusValue = $(id).attr('data-taking-status');
   let medicineCabinetIdValue = $(id).attr('data-medicine-id');

   // revoke check/uncheck the checkbox till the status is changed
   if(medicineTakingStatusValue == '0'){
      $("#"+checkboxId).prop('checked',false);
   }else{
      $("#"+checkboxId).prop('checked',true);
   }

   var status = medicineTakingStatusValue;
   // if value is 0 (not taking) then consider to 1 (taking) for taking medicine value
   status = status == '0' ? '1' : '0'; 
   var medicineCabinetId = medicineCabinetIdValue;
   var csrf_token = $('meta[name="csrf-token"]').attr('content');
   $.ajax({
      url: "{{route('update-taking-medicine-status')}}",
      type : 'put',
      dataType: "json",
      "data":{ _token: csrf_token,"takingStatus":status,"medicineCabinetId":medicineCabinetId},
      success: function(res){
         if(res.status == 0){
            $('#responseMessage').show();
            $('#responseMessage').html('<div class="alert alert-info mb-0"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span>'+res.message+'</span></div>');
            
            // check if taking status is updated then get return value to update in the checkbox
            if(res.isTakingStatus!=''){
               // update the status in the checkbox
               $("#"+checkboxId).attr('data-taking-status',res.isTakingStatus);

               // get the status and convert to boolean value to check/uncheck checkbox
               var isTakingStatus = res.isTakingStatus == '1' ? true : false;
               $("#"+checkboxId).prop('checked',isTakingStatus);
               if(isTakingStatus){ // If status is 1 then add taking data filter class
                  $("#"+cardId).addClass("isTakingMedicineData");
                  $("#"+cardId).attr('data-taking-filter','1');
               }else{ // If status is 1 then removed taking data filter class
                  $("#"+cardId).removeClass("isTakingMedicineData");
                  $("#"+cardId).attr('data-taking-filter','0');
               }
               
            }
            setTimeout(function() { $('#responseMessage').hide();  }, 5000);
         }
         else
         {
            $('#responseMessage').show();
            $('#responseMessage').html('<div class="alert alert-danger mb-0"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span>'+res.message+'</span></div>');
            setTimeout(function() { $('#responseMessage').hide();  }, 5000);
         }
      }
   }); 

}

//---------------------------------- Interactions Details Popup - Code Start ------------------------------//

// Open Interactions Details Popup
function showInteractionsFromIcon(val){
   
   closeAllPopUps(); // Close all pop ups

   var naturalMedicineId = $(val).attr('data-natural-medicine-id');
   var drugId = $(val).attr('data-drug-id');
   var productId = $(val).attr('data-product-id');
   var headername = $(val).attr('data-header-name');

   
   var imageurl = "";
   var altvalue = "";
   if(drugId!=''){
      imageurl = "{{asset('images/beta-carotene.svg')}}";
      altvalue = "natural-medicine-interaction"
   }else{
      imageurl = "{{asset('images/rx-drug.svg')}}";
      altvalue = "rx-drug-interaction"
   }

   // Show the product image if product interaction is clicked
   if(productId!=''){
      // get the image from the listed product image
      let productImage = $("#product-image-"+productId).attr("src"); 
      let interactionHeaderProductImage = '<img src="'+productImage+'" height="37" width="36" id="interactionHeaderProductImage" alt="" style="display:none;"> - ';
      // add the image instead of displaying the name, show image 
      $("#interactionHeaderName").html(interactionHeaderProductImage); 
      $("#interactionHeaderProductImage").show();
   }else{
      $("#interactionHeaderProductImage").attr("src", '');
      $("#interactionHeaderProductImage").hide();
      // Set image in header text for popup
      $("#interactionHeaderName").text(headername+' - '); 
   }
   // Set image in header for popup
   $("#interactionHeaderImage").attr('src',imageurl); 
   // Set image alt value in header for popup
   $("#interactionHeaderImage").attr('alt',altvalue); 


   var csrf_token = $('meta[name="csrf-token"]').attr('content');

   $.ajax({
      url: "{{route('get-interactions-data')}}",
      type : 'get',
      dataType: "json",
      "data":{ _token: csrf_token,"drugId":drugId,"naturalMedicineId":naturalMedicineId,"productId":productId,"profileMemberId":$("#profileMemberId").val()},
      success: function(res){
         $(".interactions-accordion").html('');
         if(res.status == '1'){
            $(".interactions-accordion").html(res.interactionsDataHtml);
            $('[data-toggle="popover"]').popover();
            
            // open the interactions details popup
            $("#displayInteractionsDetails").show();
         }
      }
   });
}

// Display Level Of Evidence pop up
function showLevelOfEvidencePopUp(id){
   // Removes previous highlighted level of evidence definition
   $(".levelOfEvidenceDefinitionContent").removeAttr('style');

   // gets the tr id of the level of evidence definition
   var trId = $(id).attr('id'); 
   // applies highlight of the selected level of evidence definition in popup
   $("#"+trId).css('background-color','yellow'); 

   $('#showLevelOfEvidence').modal('show');
} 

// Close Interactions Details Popup
$('#closeDisplayInteractionsDetails').on('click', function(e) {
   $("#displayInteractionsDetails").hide();
   $(".interactions-accordion").html('');

});



//---------------------------------- Interactions Details Popup - Code End  ------------------------------//


//----------------------------------  Filters Section Start  --------------------------- //

// Interactions Filter - Code start

   // Open interactions options dropdown
   $('#selectInteractionDropdown').on('click', function(e) {
      hideShowDisplayInteractionsOptions();
      showInteractionsSelectOptions();
   });

   // Close Interactions Options Popup
   $('#closeDisplayInteractionsOptions').on('click', function(e) {
      $("#displayInteractionsOptions").hide();
   });

   // Hide / Show Select Interactions Options Popup
   function hideShowDisplayInteractionsOptions(){
      // Close all pop ups
      closeAllPopUps();
      if($('#displayInteractionsOptions').is(':visible')){
         $("#displayInteractionsOptions").hide();
      }else{
         $("#displayInteractionsOptions").show();
      }
   }

   /**
    * 
    * Hide / show select interactions options filter based on interactions data availability
    *  */ 
   function showInteractionsSelectOptions(){
      var majorInterctionsDiv = $('.majorInteraction').length;
      var moderateInteractionsDiv = $('.moderateInteraction').length;
      var minorInteractionsDiv = $('.minorInteraction').length;
      var noneInteractionsDiv = $('.noneInteraction').length;

      if(majorInterctionsDiv == '0'){
      $('#majorSelectionOption').hide();
      }else{
         $('#majorSelectionOption').show();
      }

      if(moderateInteractionsDiv == '0'){
      $('#moderateSelectionOption').hide();
      }else{
         $('#moderateSelectionOption').show();
      }

      if(minorInteractionsDiv == '0'){
      $('#minorSelectionOption').hide();
      }else{
         $('#minorSelectionOption').show();
      }

      if(noneInteractionsDiv == '0'){
      $('#noneSelectionOption').hide();
      }else{
         $('#noneSelectionOption').show();
      }

   }

   // Select Interactions - code start


      //Major option selected - code start
      $('#majorSelectionOption').on('click', function(e) {
         // close select interaction option pop up
         hideShowDisplayInteractionsOptions(); 
         // display the major selected interaction in the filters section and show the major interactions in the screen
         showOnlyMajorInteractions();
      });
      function showOnlyMajorInteractions(){
         
         // display only major interaction data and selected interaction filters section - code start
         $('.majorInteraction').attr('data-interaction-filter','1');
         $('.filter-list').show();
         // display only major interaction data and selected interaction filters section - code end


         // add html of major filter tag in the filters section - code start
         let majorInteractionHtml = '';
         majorInteractionHtml += '<li id="filteredMajor" class="interactionsFilter">';
         majorInteractionHtml += '<img width="12" height="15" class="pb-1" src="{{asset('images/major.svg')}}" alt="major">';
         majorInteractionHtml += '<span>Major</span>';
            majorInteractionHtml += '<a href="javascript:void(0);" id="closeFilteredMajor">';
               majorInteractionHtml += '<img width="12" src="{{asset('images/closex.svg')}}" alt="closex">';
            majorInteractionHtml += '</a>';
         majorInteractionHtml += '</li>';
         // append the html tag of major interaction
         $('#filtersSection').append(majorInteractionHtml); 
         // add html of major filter tag in the filters section - code end

         // Disable major selection option from the select interaction pop up.
         $('#majorSelectionOption').attr('title','Click on close button of major to select again')
         .attr('disabled','disabled').css('pointer-events','none')
         .css('cursor','not-allowed').addClass('disabled pointer');

         // Hide the select interactions pop up.
         $("#displayInteractionsOptions").hide();
         
         
         // Close Major selected filter
         $('#closeFilteredMajor').on('click', function() {
            // remove selected major filter option
            $("#filteredMajor").remove(); 
            $('#majorSelectionOption').removeAttr('title','')
            .removeAttr('disabled').css('pointer-events','')
            .css('cursor','').removeClass('disabled pointer');
            $('.majorInteraction').attr('data-interaction-filter','0');
            // hide / show all selected filter data
            hideShowAllFilterData(); 
            // hide / show the filters section if there are other selected filters 
            hideShowFiltersSection(); 
            // Close all pop ups    
            closeAllPopUps(); 
         });
         // hide / show all selected filter data
         hideShowAllFilterData(); 
      }
      //Major option selected - code end


      //Moderate option selected - code start
      $('#moderateSelectionOption').on('click', function(e) {
         // close select interaction option pop up
         hideShowDisplayInteractionsOptions(); 
         // display the moderate selected interaction in the filters section and show the moderate interactions in the screen
         showOnlyModerateInteractions(); 
      });
      function showOnlyModerateInteractions(){
         
         // display only moderate interaction data and selected interaction filters section - code start
         $('.moderateInteraction').attr('data-interaction-filter','1');
         $('.filter-list').show();
         // display only moderate interaction data and selected interaction filters section - code end


         // add html of moderate filter tag in the filters section - code start
         let moderateInteractionHtml = '';
         moderateInteractionHtml += '<li id="filteredModerate" class="interactionsFilter">';
            moderateInteractionHtml += '<img width="12" height="15" class="pb-1" src="{{asset('images/moderate.svg')}}" alt="moderate">';
            moderateInteractionHtml += '<span>Moderate</span>';
            moderateInteractionHtml += '<a href="javascript:void(0);" id="closeFilteredModerate">';
               moderateInteractionHtml += '<img width="12" src="{{asset('images/closex.svg')}}" alt="closex">';
            moderateInteractionHtml += '</a>';
         moderateInteractionHtml += '</li>';
         // append the html tag of moderate interaction
         $('#filtersSection').append(moderateInteractionHtml); 
         // add html of moderate filter tag in the filters section - code end

         // Disable moderate selection option from the select interaction pop up.
         $('#moderateSelectionOption').attr('title','Click on close button of moderate to select again')
         .attr('disabled','disabled').css('pointer-events','none')
         .css('cursor','not-allowed').addClass('disabled pointer');

         // Hide the select interactions pop up.
         $("#displayInteractionsOptions").hide(); 
         
         // Close Moderate selected filter
         $('#closeFilteredModerate').on('click', function() {
            // remove selected Moderate filter option
            $("#filteredModerate").remove(); 
            $('#moderateSelectionOption').removeAttr('title','')
            .removeAttr('disabled').css('pointer-events','')
            .css('cursor','').removeClass('disabled pointer');
            $('.moderateInteraction').attr('data-interaction-filter','0');
            // hide / show all selected filter data
            hideShowAllFilterData(); 
            // hide / show the filters section if there are other selected filters 
            hideShowFiltersSection();  
            // Close all pop ups
            closeAllPopUps();    
         });
         // hide / show all selected filter data
         hideShowAllFilterData(); 
      }
      //Moderate option selected - code end


      //Minor option selected - code start
      $('#minorSelectionOption').on('click', function(e) {
         // close select interaction option pop up
         hideShowDisplayInteractionsOptions(); 
         // display the Minor selected interaction in the filters section and show the Minor interactions in the screen
         showOnlyMinorInteractions(); 
      });
      function showOnlyMinorInteractions(){
         
         // display only Minor interaction data and selected interaction filters section - code start
         $('.minorInteraction').attr('data-interaction-filter','1');
         $('.filter-list').show();
         // display only Minor interaction data and selected interaction filters section - code end


         // add html of Minor filter tag in the filters section - code start
         let minorInteractionHtml = '';
         minorInteractionHtml += '<li id="filteredMinor" class="interactionsFilter">';
            minorInteractionHtml += '<img width="12" height="15" class="pb-1" src="{{asset('images/minor.svg')}}" alt="minor">';
            minorInteractionHtml += '<span>Minor</span>';
            minorInteractionHtml += '<a href="javascript:void(0);" id="closeFilteredMinor">';
               minorInteractionHtml += '<img width="12" src="{{asset('images/closex.svg')}}" alt="closex">';
            minorInteractionHtml += '</a>';
         minorInteractionHtml += '</li>';
         // append the html tag of Minor interaction
         $('#filtersSection').append(minorInteractionHtml);
         // add html of Minor filter tag in the filters section - code end

         // Disable Minor selection option from the select interaction pop up.
         $('#minorSelectionOption').attr('title','Click on close button of minor to select again')
         .attr('disabled','disabled').css('pointer-events','none')
         .css('cursor','not-allowed').addClass('disabled pointer');

         // Hide the select interactions pop up.
         $("#displayInteractionsOptions").hide(); 
         


         // Close Minor selected filter
         $('#closeFilteredMinor').on('click', function() {
            // remove selected Minor filter option
            $("#filteredMinor").remove(); 
            $('#minorSelectionOption').removeAttr('title','')
            .removeAttr('disabled').css('pointer-events','')
            .css('cursor','').removeClass('disabled pointer');
            $('.minorInteraction').attr('data-interaction-filter','0');
            // hide / show all selected filter data
            hideShowAllFilterData(); 
            // hide / show the filters section if there are other selected filters
            hideShowFiltersSection(); 
            // Close all pop ups
            closeAllPopUps(); 
         });
         // hide / show all selected filter data
         hideShowAllFilterData(); 
      }
      //Minor option selected - code end


      //None option selected - code start
      $('#noneSelectionOption').on('click', function(e) {
         // close select interaction option pop up
         hideShowDisplayInteractionsOptions(); 
         // display the None selected interaction in the filters section and show the None interactions in the screen
         showOnlyNoneInteractions(); 
      });
      function showOnlyNoneInteractions(){
         
         // display only None interaction data and selected interaction filters section - code start
         $('.filter-list').show();
         $('.noneInteraction').attr('data-interaction-filter','1');
         // display only None interaction data and selected interaction filters section - code end


         // add html of None filter tag in the filters section - code start
         let noneInteractionHtml = '';
         noneInteractionHtml += '<li id="filteredNone" class="interactionsFilter">';
         noneInteractionHtml += '<img width="12" height="15" class="pb-1" src="{{asset('images/none.svg')}}" alt="none">';
            noneInteractionHtml += '<span>None</span>';
            noneInteractionHtml += '<a href="javascript:void(0);" id="closeFilteredNone">';
               noneInteractionHtml += '<img width="12" src="{{asset('images/closex.svg')}}" alt="closex">';
            noneInteractionHtml += '</a>';
         noneInteractionHtml += '</li>';
         // append the html tag of None interaction
         $('#filtersSection').append(noneInteractionHtml); 
         // add html of None filter tag in the filters section - code end

         // Disable None selection option from the select interaction pop up.
         $('#noneSelectionOption').attr('title','Click on close button of none to select again')
         .attr('disabled','disabled').css('pointer-events','none')
         .css('cursor','not-allowed').addClass('disabled pointer');

         // Hide the select interactions pop up.
         $("#displayInteractionsOptions").hide(); 
         

         
         // Close None selected filter
         $('#closeFilteredNone').on('click', function() {
            // remove selected None filter option
            $("#filteredNone").remove(); 
            $('#noneSelectionOption').removeAttr('title','')
            .removeAttr('disabled').css('pointer-events','')
            .css('cursor','').removeClass('disabled pointer');
            $('.noneInteraction').attr('data-interaction-filter','0');
            // hide / show all selected filter data
            hideShowAllFilterData(); 
            // hide / show the filters section if there are other selected filters
            hideShowFiltersSection(); 
            // Close all pop ups
            closeAllPopUps(); 
         });
         // hide / show all selected filter data
         hideShowAllFilterData(); 
      }
      //None option selected - code end


   // Select Interactions - code end

// Interactions Filter - Code end


// Condition Filter - Code Start
   
   $('#selectConditionDropdown').on('click', function() {
      // Close all pop ups
      closeAllPopUps(); 
      // display the select condition popup
      $("#displaySelectConditionOptions").show(); 
   });

   // Select conditions from the select condition pop up
   function selectConditionOption(id){
     var card = $("."+id).attr('data-condition-list');

      if($("."+id).hasClass('act')){
         // remove active class from the condition name of the select condition popup
         $("."+id).removeClass('act'); 

         // Change the data-condition-filter value to 0 when that condition is not selected
         var getAllCardDiv = $("."+card);
         $.each(getAllCardDiv, function(key,val) {             
            var cardNo = $(val).attr('data-card-no');
            $("#cardNumber"+cardNo).attr('data-condition-filter','0')
         });
         // remove selected condition filter from the filter section
         $(".selectedFilter"+id).remove();
      
      }else{

         //add active class from the condition name of the select condition popup
         $("."+id).addClass('act'); 

         // Change the data-condition-filter value to 1 when that condition is selected
         var getAllCardDiv = $("."+card);
         $.each(getAllCardDiv, function(key,val) {             
            var cardNo = $(val).attr('data-card-no');
            $("#cardNumber"+cardNo).attr('data-condition-filter','1');
         });

         // Add the condition name tag in the filters section 
         let selectedConditionTagHtml = '';
         selectedConditionTagHtml += '<li class="conditionFilter selectedFilter'+id+'" data-selected-condition="'+id+'" data-selected-condition-list="'+card+'" style="display:none;">';
            selectedConditionTagHtml += '<span class="mr-2">'+ $('.'+id).text() +'</span>';
            selectedConditionTagHtml += '<a href="javascript:void(0);" onClick="closeSelectedConditionFilter('+"'"+id+"'"+');">';
               selectedConditionTagHtml += '<img width="12" src="{{asset('images/closex.svg')}}" alt="closex">';
            selectedConditionTagHtml += '</a>';
         selectedConditionTagHtml += '</li>';
         // append the html tag of selected conditions
         $('#filtersSection').append(selectedConditionTagHtml); 
      }
      hideShowFiltersSection();
   }

   // close select condition from the filters section
   function closeSelectedConditionFilter(condition){
      // remove selected condition name from the filters section
      $(".selectedFilter"+condition).remove(); 
      // remove selected condition name from the select option popup
      $("."+condition).removeClass('act'); 
      
      // hide the selected filtered condition data
      var conditionListName = $("."+condition).attr('data-condition-list');
      let getAllCardDiv = $("."+conditionListName);
      $.each(getAllCardDiv, function(key,val) {   
         var cardNo = $(val).attr('data-card-no');
         $("#cardNumber"+cardNo).attr('data-condition-filter','0');
      });

      // recheck remaining conditions medicine cabinet data to be shown
      let remainingConditionsTag = $('.conditionFilter');
      $.each(remainingConditionsTag, function(key,val) {   
         var conditionListName = $(val).attr('data-selected-condition-list');
         var cardNo = $("."+conditionListName).attr('data-card-no');
         $("#cardNumber"+cardNo).attr('data-condition-filter','1');
      });

      $("."+condition).attr('onclick','selectConditionOption("'+condition+'")').unbind('click');

      // hide / show all selected filter data
      hideShowAllFilterData(); 
      // check if no other filter is selected then close the filter section
      hideShowFiltersSection(); 
   }


   // Apply filter for selected conditions from the select condition popup
   $('#applySelectedConditionFilter').on('click', function() {
      // show the alert box for if conditions is not selected from the select condition pop up
      if($(".btn-conditoin").hasClass('act') == false){
         $("#atleastSelectConditionConfirmation").modal('show');
         return false;
      }
      $(".conditionFilter").show();
      // close the select condition popup after selecting any conditions
      $("#displaySelectConditionOptions").hide(); 

      // disable un-select selected condition name from the select condition popup
      $.each($(".conditionFilter"), function(key,val) {             
         var conditionNameNo = $(val).attr('data-selected-condition');
         $("."+conditionNameNo).attr('onclick','').unbind('click');
      });

      // hide / show all selected filter data
      hideShowAllFilterData(); 
      // check if no other filter is selected then close the filter section
      hideShowFiltersSection();
      return false;
   });


   // Close the select condition pop up 
   $('#closeDisplaySelectConditionOptions').on('click', function() {
      $("#displaySelectConditionOptions").hide();
      if($(".conditionFilter:not([style*='display:none'])").length == '0'){
         // remove active class from the selected conditions in the select condition pop up
         $(".btn-conditoin").removeClass('act'); 
         // remove filter for condition selected  
         $(".medicineCabinetData").attr('data-condition-filter','0');        
      }else{
         var conditionListName = $(".conditionFilter:is([style*='display:none'])").attr('data-selected-condition-list');
         var conditionNameNo = $(".conditionFilter:is([style*='display:none'])").attr('data-selected-condition');
         $("."+conditionNameNo).removeClass('act');
         $("#cardNumber"+$("."+conditionListName).attr('data-card-no')).attr('data-condition-filter','0');
         $(".conditionFilter:is([style*='display:none'])").remove();
      }
      hideShowAllFilterData();
      hideShowFiltersSection();
   });

// Condition Filter - Code End


// Taking Filter - Code Start

   $('#selectTakingMedicineData').on('click', function() {
      closeAllPopUps(); // Close all pop ups

      // Display alert popup message when no "taking" data is available
      if( $(".isTakingMedicineData").length == '0' ){
         $("#noTakingDataAvailablePopUp").modal('show');
         return false
      }

      $(".medicineCabinetData").hide();

      let takingTagHtml = '';
      takingTagHtml += '<li class="isTakingFilter">';
         takingTagHtml += '<span class="mr-2">Taking</span>';
         takingTagHtml += '<a href="javascript:void(0);" id="closeTakingFilter">';
            takingTagHtml += '<img width="12" src="{{asset('images/closex.svg')}}" alt="closex">';
         takingTagHtml += '</a>';
      takingTagHtml += '</li>';

      // remove taking filter if already added in the filter section 
      $(".isTakingFilter").remove();
      // append the html tag of selected conditions 
      $('#filtersSection').append(takingTagHtml); 
      
      // check if no other filter is selected then close the filter section
      hideShowFiltersSection(); 

      // Add data-taking-filter = 1 where the medicine cabinet data has taking value
      var getAllCardDiv = $(".isTakingMedicineData");
      $.each(getAllCardDiv, function(key,val) {
         var cardNo = $(val).attr('id');
         $("#"+cardNo).attr('data-taking-filter','1');
      });
      // hide / show all selected filter data
      hideShowAllFilterData(); 

      // Close the taking filter from the filter section
      $('#closeTakingFilter').on('click', function() {
         $(".isTakingFilter").remove();
         var getAllCardDiv = $(".isTakingMedicineData");
         $.each(getAllCardDiv, function(key,val) {
            var cardNo = $(val).attr('id');
            $("#"+cardNo).attr('data-taking-filter','0');
         });
         // hide / show all selected filter data
         hideShowAllFilterData(); 
         // check if no other filter is selected then close the filter section
         hideShowFiltersSection(); 
      });
   });

  
// Taking Filter - Code End


   /***
    * Check status of the selected filters 
    */
   function checkIfSelectedFilters(){
      let hasConditionsFilter = $("#filtersSection li").hasClass('conditionFilter');
      let hasInteractionsFilter = $("#filtersSection li").hasClass('interactionsFilter');
      let hasTakingFilter = $("#filtersSection li").hasClass('isTakingFilter');

      // return values
      return {
         'hasConditionsFilter': hasConditionsFilter,
         'hasInteractionsFilter': hasInteractionsFilter,
         'hasTakingFilter': hasTakingFilter
      };
   }

   /*** 
    * Hide / Show the selected filtered data if any of the filter is selected or un-selected
   */
   function hideShowAllFilterData(){
      let data = checkIfSelectedFilters();

      let hasConditionsFilter = data.hasConditionsFilter;
      let hasInteractionsFilter = data.hasInteractionsFilter;
      let hasTakingFilter = data.hasTakingFilter;

      var getAllCardDiv = $(".medicineCabinetData");
      $.each(getAllCardDiv, function(key,val) {             
         var cardNo = $(val).attr('id');

         // default hide all cabinet data
         $("#"+cardNo).hide();
         // If only condition selected then display those data and hide other cabinet data
         if(hasConditionsFilter == true && hasInteractionsFilter == false && hasTakingFilter == false){
            if($("#"+cardNo).attr('data-condition-filter') == '1' && 
               $("#"+cardNo).attr('data-interaction-filter') == '0' && 
               $("#"+cardNo).attr('data-taking-filter') == '0')
            {
               $("#"+cardNo).show();
            }
         }
         // If only interaction filter is selected then display those data and hide other cabinet data
         if(hasConditionsFilter == false && hasInteractionsFilter == true && hasTakingFilter == false){
            if($("#"+cardNo).attr('data-condition-filter') == '0' && 
               $("#"+cardNo).attr('data-interaction-filter') == '1' && 
               $("#"+cardNo).attr('data-taking-filter') == '0')
            {
               $("#"+cardNo).show();
            }
         }
         // If only taking filter is selected then display those data and hide other cabinet data
         if(hasConditionsFilter == false && hasInteractionsFilter == false && hasTakingFilter == true){
            if($("#"+cardNo).attr('data-condition-filter') == '0' && 
               $("#"+cardNo).attr('data-interaction-filter') == '0' && 
               $("#"+cardNo).attr('data-taking-filter') == '1')
            {
               $("#"+cardNo).show();
            }
         }
         // If only condition & interaction filter selected then display those data and hide other cabinet data
         if(hasConditionsFilter == true && hasInteractionsFilter == true && hasTakingFilter == false){
            if($("#"+cardNo).attr('data-condition-filter') == '1' && 
               $("#"+cardNo).attr('data-interaction-filter') == '1' && 
               $("#"+cardNo).attr('data-taking-filter') == '0')
            {
               $("#"+cardNo).show();
            }
         }
         // If only condition, interaction & taking filter is selected then display those data and hide other cabinet data
         if(hasConditionsFilter == true && hasInteractionsFilter == true && hasTakingFilter == true){
            if($("#"+cardNo).attr('data-condition-filter') == '1' && 
               $("#"+cardNo).attr('data-interaction-filter') == '1' && 
               $("#"+cardNo).attr('data-taking-filter') == '1')
            {
               $("#"+cardNo).show();
            }
         }
         // If only taking & interaction filter is selected then display those data and hide other cabinet data
         if(hasConditionsFilter == false && hasInteractionsFilter == true && hasTakingFilter == true){
            if($("#"+cardNo).attr('data-condition-filter') == '0' && 
               $("#"+cardNo).attr('data-interaction-filter') == '1' && 
               $("#"+cardNo).attr('data-taking-filter') == '1')
            {
               $("#"+cardNo).show();
            }
         }
         // If only condition & taking filters are selected then display those data and hide other cabinet data
         if(hasConditionsFilter == true && hasInteractionsFilter == false && hasTakingFilter == true){
            if($("#"+cardNo).attr('data-condition-filter') == '1' && 
               $("#"+cardNo).attr('data-interaction-filter') == '0' && 
               $("#"+cardNo).attr('data-taking-filter') == '1')
            {
               $("#"+cardNo).show();
            }
         }

      });

      // Check if there are no data as per selected filter then display appropriate message
      let medicineCabinetDataDisplay = $(".medicineCabinetData").is(":visible");
      let selectedFiltersDisplay = $("#filtersSection li").is(":visible");
      if(selectedFiltersDisplay == true && medicineCabinetDataDisplay == false){
         $("#noRecordFoundMsg").addClass('d-flex').show();
      }else{
         $("#noRecordFoundMsg").removeClass('d-flex').hide();
      }
   }

   /** 
    * Hide filters section if there are no selected filters or else show the filters section with selected
    * interaction filter
    */
   function hideShowFiltersSection(){
      // Check if there are already filters added by count
      let addedFilters = $('#filtersSection li:not([style*="display:none"])').length;
      // if the count is not 0 then show the filter section, else hide that section 
      if(addedFilters != '0' && addedFilters != ''){
            $(".filter-list").show();
      }else{
         $(".filter-list").hide();
         $(".medicineCabinetData").show();
      }
   }
//----------------------------------  Filters Section End  --------------------------- //


//------------------------------------ Tour Popup Started ---------------------------- //
var planType = $('#planType').val();
var stepNo = 1;
var totalStep = 7;

function onClickTourPopup(){
   $('#tourPopupConfirmation').removeClass('d-none');
   // Hide tour steps popup and reset step to 1
   $('.tour-popup-step').addClass('d-none');
   stepNo = 1;
}

function tourPopupLater(){
   $('#tourPopupConfirmation').addClass('d-none');
}

function tourPopupYes(){
   var isFirstDrug = $('#isFirstDrug').val();
   if(isFirstDrug == 1){
      totalStep = 6;
   }

   // Checked plan type if plan type is 1 that meance removed third step
   if(planType == 1){
      totalStep--;
   }

   $('#tourPopupConfirmation').addClass('d-none');
   $('#tourStep1').removeClass('d-none');
   $('#step1').html('');
   $('#step1').html('1/'+totalStep);   
   $('.collapseClass').removeClass('show');
   $('#collapseOne0').addClass('show');
}

function onClickQuickTourNext(step){
   totalStep = 7;

   var isFirstDrug = $('#isFirstDrug').val();
    // Checked first data drug or therapy in listing if durg thet meance removed six step
   if(isFirstDrug == 1){
      totalStep = 6;
   }


   // Checked plan type if plan type is 1 that meance removed third step
   if(planType == 1){
      totalStep--;
   }
   // Hide old step
   $('#tourStep'+step).addClass('d-none');
   step++;
   stepNo++;

   if(planType == 1 && step == 4){
      step++;
   }

   // Jumps to the top content of the wellkabinet section when step is 3
   if(step == 3 || step == 4){
      $('#wellkabinet-body-section, html, body').animate({
         scrollTop: $("#step3").offset().top-90
      }, 900);
   }

   if(step == 6){
      if(isFirstDrug == 1){
         step++;
      }
   }

   if(step == 6){
      $('#step6-body').html('');
      msgType = $('#step6').attr('data-type');
      if(msgType == '1'){
         $('#step6-body').html('Click the info icon to view product details.');
      }else{
         $('#step6-body').html('Click the info icon to view therapy details. Click condition name to view condition details');
      }
   }

   $('#step'+stepNo).html('');
   $('#step'+step).html(stepNo+'/'+totalStep);
   $('#tourStep'+step).removeClass('d-none');
}

function onClickQuickTourPrev(step){
   totalStep = 7;

   var isFirstDrug = $('#isFirstDrug').val();
   if(isFirstDrug == 1){
      totalStep = 6;
   }

   // Checked plan type if plan type is 1 that means removed third step
   if(planType == 1){
      totalStep--;
   }

   $('#tourStep'+step).addClass('d-none');
   // Show old step
   step--;
   stepNo--;

   if(planType == 1 && step == 4){
      step--;
   }

   // Checked first drug or therapy
   if(step == 6){
      var isFirstDrug = $('#isFirstDrug').val();
      if(isFirstDrug == 1){
         step--;
      }
   }


   if(step == 6){
      $('#step6-body').html('');
      msgType = $('#step6').attr('data-type');
      if(msgType == '1'){
         $('#step6-body').html('Click the info icon to view product details.');
      }else{
         $('#step6-body').html('Click the info icon to view therapy details. Click condition name to view condition details');
      }
      
   }


   $('#step'+stepNo).html('');
   $('#step'+step).html(stepNo+'/'+totalStep);
   $('#tourStep'+step).removeClass('d-none');
}

function onClickCloseTour(){
   // Hide step
   $('.tour-popup-step').removeClass('d-none');
   $('.tour-popup-step').addClass('d-none');
   stepNo = 1;
   totalStep = 7;
}
//------------------------------------ Tour Popup End -------------------------------- //


//------------------------------------ Create Report functionality - Start -------------------------------- //
$(".download-report").click(function () {
    
   var subscriptionStatus = "{{\Auth::user()->getSubscriptionStatus()}}";
   if(!subscriptionStatus){

      // Set modal title
      $('#wellkabinet-report-select-modal-title').html('Permission denied!');
      
      // Set body
      $('#wellkabinet-report-select-modal-body').html('<p><a class="dd" href="https://wellkasa.com/products/wellkabinet" target="_blank">Renew subscription to generate PDF</a></p>');

      // Show Modal
      $('#messageWellkabinetReportPopUp').modal('show');

      return false;
   }
      
   var profileMemberId = $("#profileMemberId").val();
   // Check if profile member id exists, if exists then generate wellkabinet PDF structure accordingly
   if(profileMemberId!=''){
      window.location.href = "{{ route('wellkabinet-pdf-generation',$profileMemberId) }}"
   }else{
      window.location.href = "{{ route('wellkabinet-pdf-generation') }}"
   }



  });
//------------------------------------ Create Report functionality - End -------------------------------- //

//----------------- Email Report functionality - Start ---------------- //
   // Send mail popup
   function sendMailPopup(id){
      // get the url to send the mail with report id
      let route = $(id).attr("data-send-mail");

      // assign route in action attr of the form
      $("#sendMailForm").attr("action",route);

      // Show Modal
      $('#sendReportMailModalPopup').modal('show');

      // reset to email field value
      $('#toMail').val('');

   }

   // disable buttons and modal popup hide when email is sending
   $("#sendMailButton").on("click", function(){
      // check if form is validated, then execute below code
      if($('#sendMailForm')[0].checkValidity()){
         // disable modal popup
         $('#sendReportMailModalPopup').modal({backdrop: 'static', keyboard: false});
         // execute disable buttons after 1 sec
         setTimeout(function() { 
               // disable all input and buttons in modal popup
               $("#sendMailButton").attr("disabled",true);
               $("#resetButton").attr("disabled",true);
               $("#toMail").attr("disabled",true);
               $("#sendMailModalClose").attr("disabled",true);
               $("#sendMailModalCloseButton").attr("disabled",true);  
               $("#loadingMsg").text("Please wait... while your email is being sent!");  
         }, 100);
         $("#loadingMsg").text('');
      }
   });
//----------------- Email Report functionality - End ---------------- //


//Barcode js code - start

function docReady(fn) {
   // see if DOM is already available
   if (document.readyState === "complete"
      || document.readyState === "interactive") {
      // call on next available tick
      setTimeout(fn, 1);
   } else {
      document.addEventListener("DOMContentLoaded", fn);
   }
}

// Display scan barcode pop up - start
function showBarcodeScannerPopUp(){
   // Open the barcode scanner popup
   $('#barcode-scanner').modal('show');   
   $("#barcode-popup-close").show();

   // Close the main popup
   $('#main-popup').modal('hide');

   docReady(function () {

      var resultContainer = document.getElementById('qr-reader-results');
      var lastResult, countResults = 0;
      function onScanSuccess(decodedText, decodedResult) {
         if (decodedText !== lastResult) {
            ++countResults;
            lastResult = decodedText;


            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
               url: "{{route('get-product-by-barcode')}}",
               type : 'post',
               dataType: "json",
               "data":{ _token: csrf_token,"barcode":decodedText},
               beforeSend: function(){
                  // Add the message while barcode scan
                  $("#barcode-popup-message").text('Please wait..');
                  // hide the cancel button in barcode scanner popup
                  $("#barcode-popup-close").hide();
                  
               },
               success: function(res){
                  // Hide barcode scanner popup
                  $('#barcode-scanner').modal('hide');
                  // Hide the message popup
                  $("#barcode-popup-message").text('');

                  if(res.status == '1'){
                     // Set the search input value of the product name by the barcode value
                     $("#medicine").val(res.data.productName);
                     // Set the hidden input value of the product id by the barcode value
                     $("#medicine").attr("value", res.data.id+"-product");


                     // Success message show in popup
                     $('#responseMessage').html('<div class="alert alert-info mb-0"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span>'+res.message+'</span></div>');
                     $('#responseMessage').show();
                     setTimeout(function() { 
                        $('#responseMessage').hide(); 
                        $(".notification-block").removeClass('mobile-view-message'); 
                     }, 5000);     

                     // Open the add wellkabinet popup
                     $('#openAddWellkabinetPopUp').trigger('click');

                     // Adjust the message block with css
                     $(".notification-block").addClass('mobile-view-message');

                  }else if(res.status == '0'){

                     // Open barcode confirmation popup after 1200 second
                     setTimeout(() => {
                        // Open the confirmation popup
                        $('#barcode-confirmation').modal('show');
                        // Display the message for the confirmation message
                        $('#barcode-confirmation-popup-title').html(res.message);
                        // Set the barcode value in input hidden 
                        $("#barcode-mail-url").val(res.sendBarcodeMailUrl);
                     }, 1200);

                     // Reset the search input value of the product name
                     $("#medicine").val("");
                     // Reset the hidden input value of the product id
                     $("#medicine").attr("value","");
                  }
               }
            });

            // Stop scanning and clear barcode scan view
            html5QrcodeScanner.clear()
         }
      }

      // Set the barcode scan reader with qrcode box css 
      var html5QrcodeScanner = new Html5QrcodeScanner(
         "qr-reader", { fps: 10, qrbox: 250,  facingMode: { exact: "environment"} } );
      html5QrcodeScanner.render(onScanSuccess);
   });
}

// Send email for the barcode missing on click of send request button
$("#sendBarcodeEmail").on("click", function(){
   let sendBarcodeMailUrl = $("#barcode-mail-url").val();
   if(sendBarcodeMailUrl!=''){
      window.location.href = sendBarcodeMailUrl;
   }else{
      alert('Can\'t send mail, Please try again later.');
   }
});

// Open scan barcode popup again on click of scan again button
$("#scanBarcodeAgain").on("click", function(){
   // Open barcode scanner popup
   showBarcodeScannerPopUp();
   // Close barcode confirmation popup
   $('#barcode-confirmation').modal('hide');
});

// Display scan barcode pop up - end

// On close of the scan barcode popup, stop scanning for the barcode
$("#barcode-popup-close").on("click", function(){
   $("#html5-qrcode-button-camera-stop").trigger('click');
});

//Barcode js code - end
</script>

@endpush
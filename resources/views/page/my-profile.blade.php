@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp
                
@section('meta-keywords', __('wellkasa, profile, privacy, secure'))
@section('meta-news-keywords', __('wellkasa, profile, privacy, secure'))
@section('meta-description', __('Your personalized, private, and secure Wellkasa profile '))
@section('title', __('Your personalized, private, and secure Wellkasa profile.'))

@section('content')
<div class="container">
   <ul class="nav nav-tabs getcaretab d-none" id="myTab" role="tablist">
      <li class="nav-item">
         <a class="nav-link text-center active"  href="{{route('my-profile')}}" aria-controls="diagnosis" aria-selected="true"> <img src="{{asset('images/myprofile.svg')}}" alt="myprofile" title="myprofile"> <span class="tab-name">My <br>Profile </span> </a>
      </li>
      <li class="nav-item">
         <a class="nav-link text-center" id="therapy-tab" data-toggle="tab" href="#therapy" role="tab" aria-controls="therapy" aria-selected="false"><img src="{{asset('images/mycareteam.svg')}}" alt="mycareteam" title="mycareteam"> <span class="tab-name">My <br>Care Team </span></a>
      </li>
      <li class="nav-item">
         <a class="nav-link text-center" id="getcare-tab" data-toggle="tab" href="#getcare" role="tab" aria-controls="getcare" aria-selected="false"><img src="{{asset('images/myappointments.svg')}}" alt="myappointments" title="myappointments"> <span class="tab-name">My <br>Appointments </span></a>
      </li>
      <li class="nav-item">
         <a class="nav-link text-center" id="getcare-tab" href="{{route('my-wellkasa')}}" aria-selected="false"><img class="svg" src="{{asset('images/mytherapies.svg')}}" alt="mytherapies" title="mytherapies"> <span class="tab-name">My <br>Therapies </span></a>
      </li>
   </ul>
   <div class="container750 user-profile-wrapper mt-0 mt-md-5" id="myTabContent">
      <div class="user-profile-container" id="diagnosis" role="tabpanel" aria-labelledby="diagnosis-tab">
         <div class="small-container">
               <h1 class="h3 text-left text-md-center mt-4">My Profile</h1>
               <div class="card profile-card align-items-center mt-3 mt-md-5 mb-2">
                  <div class="profile-img position-relative">
                     <img id="preview" class="img-thumbnail rounded-circle p-0" src="{{asset('uploads/avatar/'.Auth::user()->avatar)}}" onerror="this.onerror=null;this.src='{{ asset("images/user.jpg") }}';" alt="user" title="user" style="width: 84px; height: 84px;">
                      <input type="file" class="myprofile-phpto" id="avatar" name="avatar" accept="image/*">
                  </div>
                  <div class="ml-4 card-body p-0 d-flex flex-column align-items-start">
                     <p class="d-inline-block mb-0">{{$userDetails['firstName']." ".$userDetails['lastName']}}</p>
                     <p class="d-inline-block mb-0">{{$userDetails['email']}}</p>
                     <p class="gender-age mb-0">{{!empty($userDetails['gender']) ? $userDetails['gender'] : ''}}{{!empty($userDetails['patientAge']) ? ", ".$userDetails['patientAge'] : ''}}</p>
                  </div>
                  <a href="{{route('profile')}}"><img class="p-3" src="{{asset('images/profile-edit.svg')}}" alt="Profile Edit"></a>
               </div>
               <a href="{{route('change.password')}}" class="font-weight-bold">Change password</a>
               <div class="subscription-links mt-1 mb-2">
                  <a href="javascript:void(0);" onClick="deleteTherapy({{\Auth::user()->id}})" class="color-red">Delete Account</a>                               
               </div>
               <div class="accordion mt-3" id="accordionExample">
                  
                  <!--  Member profile accordion start  -->
                  <!-- Show member profile information only if user is wellkasa plus member else do not display this accordion -->
                  @if(Auth::user()->planType == '2')
                     <div class="card border-top">
                        <div class="card-header pl-0 pr-0" id="headingOne">
                           <a href="" class="mb-0 text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                           <img class="p-0 mr-3" src="{{asset('images/icon_group.svg')}}" alt="Group"> 
                           <strong>Member Profiles</strong>
                           </a>
                        </div>

                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                           @if(!empty($profileMembers) && $showSelectPlan == '0')
                              @foreach($profileMembers as $profileMembersKey => $profileMembersData)
                                 <div class="card-body p-0">
                                    @if($profileMembersKey == '0')
                                       <!-- Show add profile button only if available count is not 0 - code start -->
                                       @if(!empty($availableCount) && $availableCount != '0')
                                          <a href="{{route('add-profile')}}" class="btn btn-gradient mb-3">                                  
                                             Add Profile ({{$availableCount}} Available)
                                          </a>
                                       @endif
                                       <!-- Show add profile button only if available count is not 0 - code end -->
                                    @endif
                                    <div class="member-profiles media align-items-center">
                                       <img class="img-thumbnail rounded-circle p-0" src="{{$profileMembersData['profile_picture']}}" onerror="this.onerror=null;this.src='{{ asset("images/user.jpg") }}';" alt="user" title="user" style="width: 40px; height: 40px;">
                                       <div class="media-body mb-0 ml-3">
                                          <strong class="font-weight-normal">{{$profileMembersData['name']}}</strong>
                                          <p class="m-0">{{$profileMembersData['genderAge']}}</p>
                                       </div>
                                       <div class=""> 
                                          <a href="{{route('edit-profile-member',Crypt::encrypt($profileMembersData['id']))}}"><img class="p-2" src="{{asset('images/profile-edit.svg')}}" alt="Profile Edit"></a>
                                          <a href="{{route('remove-profile',Crypt::encrypt($profileMembersData['id']))}}"><img class="p-2" src="{{asset('images/delet-filled.svg')}}" alt="Profile Delete"></a>
                                       </div>
                                    </div>
                                 </div>
                              @endforeach
                           @else
                              @if(Auth::user()->profileMemberCount != '0' && $availableCount != '0' && $showSelectPlan == '0')
                                 <a href="{{route('add-profile')}}" class="btn btn-gradient mb-3">                                  
                                    Add Profile ({{$availableCount}} Available)
                                 </a>    
                              @else
                                 <div class="card-body p-0">
                                    <!-- <a href="{{route('select-plan')}}" class="btn btn-gradient mb-3 d-none">Select Plan</a> -->
                                    <a href="https://wellkasa.com/products/wellkabinet" class="btn btn-gradient mb-3">Buy Wellkabinet&#8482;</a><br>
                                    <span class="">Note: you will be redirected to shopify website</span>
                                 </div>
                              @endif        
                           @endif
                        </div>
                     </div>
                  @endif
                  <!-- Member profile accordion end -->

                  <!-- NOT IN USE : Subscription details accordion start -->
                  <div class="card border-top d-none">
                     <div class="card-header pl-0 pr-0" id="headingTwo">
                        <a href="" class="mb-0 text-left" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <img class="p-0 mr-3" src="{{asset('images/icon_card-list.svg')}}" alt="Card List">
                        <strong>Subscription details & invoices</strong></a>
                     </div>

                     @if(Auth::user()->planType == '2' && Auth::user()->profileMemberCount != '0' && Auth::user()->getInvoiceStatus() != '0')
                        <!-- Show subscription information only if user is wellkasa plus member - code start -->
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                           <div class="card-body p-0">
                              @if(!empty($userDetails['subscriptionDetails']))
                                 <div class="subscription-info">
                                    <p>{{$userDetails['subscriptionDetails']['planType']}} <br> 
                                       <?php echo nl2br($userDetails['subscriptionDetails']['planDescription']) ?>  
                                       {{!empty($userDetails['subscriptionDetails']['planNextPayment']) ? $userDetails['subscriptionDetails']['planNextPayment'] : ''}}
                                    <p>
                                    @if(!empty($userDetails['subscriptionDetails']['planSubDescription']))
                                       <p class="small text-muted">{{$userDetails['subscriptionDetails']['planSubDescription']}}</p>
                                    @endif
                                 </div>
                              @endif
                              <div class="subscription-links mt-4 mb-4">
                                 <a href="{{route('invoices', Crypt::encrypt(\Auth::user()->id))}}" class="blue-color mr-4">Invoices</a>
                                 @if(Auth::user()->getSubscriptionStatus()=='1')
                                    <a href="{{route('remove-subscription', Crypt::encrypt(\Auth::user()->id))}}" class="color-red">Cancel subscription</a>
                                 @else
                                    <a href="javascript:void(0);" onClick="deleteTherapy({{\Auth::user()->id}})" class="color-red">Delete Account</a>                               
                                 @endif
                              </div>
                           </div>
                        </div>
                        <!-- Show subscription information only if user is wellkasa plus member - code end -->
                     @else
                        <!-- Upgrade wellkasa plus  - code start -->
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                           <div class="card-body p-0">                              
                              <div class="subscription-info">
                                 <p>Wellkasa Basic</p>
                                 <div class="subscription-links">
                                    <a href="{{route('select-plan')}}" class="blue-color mr-4">
                                       Upgrade to WellKabinet&#8482;
                                    </a>
                                 </div>
                              </div>
                              <div class="subscription-links mt-4 mb-4">
                                 <a href="javascript:void(0);" onClick="deleteTherapy({{\Auth::user()->id}})" class="color-red">Delete Account</a>                               
                              </div>

                           </div>
                        </div>
                        <!-- Upgrade wellkasa plus - code end -->
                     @endif

                  </div>
                  <!-- NOT IN USE : Subscription details accordion end -->

                  <!-- NOT IN USE : Payment method accordion start -->
                  <div class="card border-top d-none">
                     <div class="card-header pl-0 pr-0" id="headingThree">
                        <a href="" class="mb-0 text-left" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        <img class="p-0 mr-3" src="{{asset('images/icons_bank-card.svg')}}" alt="Bank Card">
                        <strong>Payment method</strong>
                        </a>
                     </div>
                     <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                        <div class="card-body p-0">
                           <div class="subscription-info">
                              <p>Credit card ending X323 <br>Expires 6/23<p>
                           </div>
                           <div class="subscription-links mt-4 mb-4">
                              <a href="" class="blue-color mr-4">Change</a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- NOT IN USE : Payment method accordion end -->

               </div>
         </div>
            
      </div>
      
   <!-- popup start -->
   <div class="add-drugs-popup successfully add-drug-tab mobile-popup" style="display: none;"> 
      <img class="pb-2" src="{{asset('images/check-circle-filled.png')}}" alt="check">
      <p>
         Greta Thunberg was added successfully to your account
      </p>
  </div>
   <!-- end -->

   <!-- profile pic update success popup - start -->
   <div class="add-drugs-popup successfully add-drug-tab mobile-popup" id="popUpMessageDisplay" style="display: none;"> 
      <img class="pb-2" src="{{asset('images/check-circle-filled.png')}}" alt="check">
      <p id="popUpMessageDisplayText"></p>
  </div>
   <!-- profile pic update success popup - end -->

   </div>
   
   <!---- Delete account pop up - start ----->
   <div class="modal fade" id="deleteAccountConfirmation">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
            <h4 class="modal-title" id="delete-modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body" id="delete-modal-body">
            
            </div>
            <input type="hidden" name="deleteModalId" value="" id="deleteModalId">
            <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button"  id="modalYes" class="btn btn-green modalYes">Yes</button>
            </div>
         </div>
      </div>
   </div>
   <!---- Delete account pop up - end ----->


</div>
@endsection

@push('scripts')
<script type="text/javascript">
   $(document).ready(function(){

      //when there is an AJAX request and the user is not authenticated then reload the page - code start
      $(document).ajaxError(function (event, xhr, settings, error) {
         if(xhr.status == 401) {
            alert("Your session has timed out. Please login.")
            window.location.reload();
         }
      });
      //when there is an AJAX request and the user is not authenticated then reload the page - code end
      
      // display profile deleted successfully popup - code start
      const deleteMsgFromSession = "{{Session::get('deletedProfile')}}"
      if(deleteMsgFromSession !=''){
         $("#popUpMessageDisplay").show(); // display popup
         $("#popUpMessageDisplayText").text(deleteMsgFromSession); // display message from the session in popup
         // terminate the code inside this function after 3 seconds
         setTimeout(function() { 
            $("#popUpMessageDisplay").hide(); // hide popup 
            $("#popUpMessageDisplayText").text(''); // clear message in popup
            "{{Session::forget('deletedProfile')}}" // delete session based message from deletedProfile value
         }, 3000);
      }
      // display profile deleted successfully popup - code end

      // display profile new saved successfully popup - code start
      const savedMsgFromSession = "{{Session::get('savedProfile')}}"
      if(savedMsgFromSession !=''){
         $("#popUpMessageDisplay").show(); // display popup
         $("#popUpMessageDisplayText").text(savedMsgFromSession); // display message from the session in popup
         // terminate the code inside this function after 3 seconds
         setTimeout(function() { 
            $("#popUpMessageDisplay").hide(); // hide popup 
            $("#popUpMessageDisplayText").text(''); // clear message in popup
            "{{Session::forget('savedProfile')}}" // delete session based message from deletedProfile value
         }, 3000);
      }
      // display profile new saved successfully popup - code end

   });

</script>

<!-- Code to show selected Files -->
<script>
   $(document).on("click", ".browse", function() {
      var file = $(this).parents().find(".file");
      file.trigger("click");
   });

   $('input[type="file"]').change(function(e) {
      var fileInput = 
         document.getElementById('avatar');
         
      var filePath = fileInput.value;
      
      // Allowing file type
      var allowedExtensions = 
               /(\.jpg|\.jpeg|\.png)$/i;
         
      if (!allowedExtensions.exec(filePath)) {
         alert('Only image type jpg/png/jpeg is allowed');
         fileInput.value = '';
         return false;
      }else{
         var fileName = e.target.files[0].name;
         $("#file").val(fileName);

         var reader = new FileReader();
         reader.onload = function(eve) {

       
            var formData = new FormData();
            formData.append("avatar", e.target.files[0]);
            
            $.ajaxSetup({
               headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
            });

            $.ajax({
               method: 'post',
               url: "{{route('update-profile-pic')}}",
               data: formData,
               cache:false,
               contentType: false,
               processData: false,
               success: function(data,xhr) {

                  if(data.status == '1'){
                     $('#responseMessage').show();
                     $('#responseMessage').html('<div class="alert alert-info mb-0"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span>'+data.message+'</span></div>');
                     setTimeout(function() { $('#responseMessage').hide();  }, 5000);
                     $(".mr-2.rounded-circle").removeAttr("src").attr("src", eve.target.result); //temporary show uploaded image 

                     // get loaded data and render thumbnail.
                     document.getElementById("preview").src = eve.target.result;

                  }else{
                     $('#responseMessage').show();
                     $('#responseMessage').html('<div class="alert alert-danger mb-0"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span>'+data.message+'</span></div>');
                     setTimeout(function() { $('#responseMessage').hide();  }, 5000);
                  }
               },
               error: function(jqXHR,data,xhr,error) {

                  var err = JSON.parse(jqXHR.responseText).message;

                  $('#responseMessage').show();
                  $('#responseMessage').html('<div class="alert alert-danger mb-0"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span>'+err+'</span></div>');
                  setTimeout(function() { $('#responseMessage').hide();  }, 5000);
               }
            });
            
         };
         // read the image file as a data URL.
         reader.readAsDataURL(this.files[0]);
      }  
   });

   //----------------------------------  Delete Account Pop up --------------------------- //

   function deleteTherapy(id){
      // Set modal title
      $('#delete-modal-title').html('Delete Account Confirmation');

      // Set body
      $('#delete-modal-body').html('Are you sure you want to delete your account?');

      // Show Modal
      $('#deleteAccountConfirmation').modal('show');
      
      // Set hiddan input type value 
      $('#deleteModalId').val(id);
   }

   //----------------------------------  Delete Account API  --------------------------- //

   // Call Ajax for Delete Therapy
   $('.modalYes').on('click',function()
   {   
      
      var modalId = $('#deleteModalId').val();
      var csrf_token = $('meta[name="csrf-token"]').attr('content');
      $.ajax({
         url: "delete-account",
         type : 'delete',
         dataType: "json",
         "data":{ _token: csrf_token,"userId":modalId},
         success: function(res){

            if(res.status == 1){
               $('#deleteAccountConfirmation').modal('hide');
               window.location.reload();
            }
            else
            {
               window.location.reload();
            }
         }
      });    
      
   });

</script>
@endpush
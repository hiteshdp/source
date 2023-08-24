@extends('layout.default')


@section('content')
<div class="container750 min-450">
    <div class="providers-user">

        <!---- Display add provider input box - Start ----->
        <div class="manage-symptom-footer mb-3 text-center">
            <form id="add-provider" method="POST" action="">
                <div class="top-add-form align-items-start mt-4">
                    @csrf
                    <div class="manage-symptom-add">
                        <div class=" gradient-dropdown text-left">
                            <input type="text" autocomplete="off" class="form-control" aria-describedby="" id="search_provider" name="search_provider" value="" placeholder="Search Provider by First and Last Name" >
                            <label id="search_provider-error" class="error" for="search_provider" style="display: none;"></label>
                        </div>
                    </div>
                    <div class="manage-symptom-save-btn ml-2 mt-0">
                        <button class=" btn-gradient w-100 border-0 d-block" type="submit" id="add-provider-name">Select</button>
                    </div>
                </div>
            </form>
        </div>
        <!---- Display add provider input box - End ----->
        
        @if(!empty($providerData))
            
            <div class="text-center justify-content-center mt-5 mb-4">
                <h2 class="patients-list-title m-0">Shared Access Provider List</h2>
            </div>

            <!---- Check if data is available then show the providers listing - Start ----->
            <div class="top-add-form ">
                <!---- Show the providers list with the action items - Start ----->
                <ul class="provider-list"> 
                    @foreach($providerData as $providerDataKey => $providerDataValue)
                        <li class="mb-2">
                            <div class="provider-list-photo">
                                <a href="javascript:void(0);" data-url="{{$providerDataValue->accessProviderDetailsURL}}" onclick="openDoctorDetails(this);"><img Width="40" height="40" class="rounded-circle mr-2" src="{{$providerDataValue->image}}" alt="{{$providerDataValue->firstName}}"> {{$providerDataValue->firstName. " " . $providerDataValue->lastName}}</a>
                            </div>
                            <div class="provider-access">
                                <a class="mr-lg-4" href="javascript:void(0);" data-url="{{$providerDataValue->accessDetailsURL}}" onclick="openAccessDetails(this);">Access Details</a>
                                <a href="{{$providerDataValue->removeAccessURL}}">Revoke Access</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <!---- Show the providers list with the action items - End ----->
            </div>
            <!---- Check if data is available then show the providers listing - End ----->
        @else
            <!---- When data is not available then message - Start ----->
            <div class="providers-info mt-5">
                <img class="mb-3" src="{{asset('images/share_user.svg')}}" alt="Share User">
                <p>You have not given any provider shared access to your reports yet. Start by searching for approved providers to give them secure shared access.</p>
            </div>
            <!---- When data is not available then message - Start ----->
        @endif

    </div>
</div>


<!---Modal pop up for access details html code - start --->
<div class="modal fade provider-popup access-details" id="accessDetailsPopUp">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="accessDetailsTitle">Access Details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="access-details-modal-body">
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for access details html code - end --->


<!---Modal pop up for doctor details html code - start --->
<div class="modal fade provider-popup" id="doctorDetailsPopUp">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="doctorDetailsTitle">Doctor Details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="doctor-details-modal-body">
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for doctor details html code - end --->

@endsection


@push('scripts')

<!-- Include auto search provider error functions -->
<script src="{{ asset('js/provider-search-error-libraries.js') }}"></script>
<!-- Include auto search of provider dropdown -->
<script src="{{ asset('js/provider-search.js') }}"></script>

<script>

    /**
     * Open popup for the access details of provider
     */
    function openAccessDetails(event){
       // Get the current selection of access details HTML DOM
       let dom = $(event);
       // Use the url from its attribute element
       let fetchProviderDataRoute = dom.attr('data-url');

       $.ajax({
            url: fetchProviderDataRoute,
            type : 'get',
            dataType: "json",
            success: function(res){
                $("#access-details-modal-body").html('');
                if(res.status == '1'){
                    // Display the html dom
                    $("#access-details-modal-body").html(doctorAccessHTML());
                    
                    /** Add the provider/doctor details - start */ 
                    $("#doctor-image").attr('src',res.data.image);
                    $("#doctor-name").text('Dr. '+res.data.providerName);
                    $("#start-date").text(res.data.start_date);
                    $("#end-date").text(res.data.end_date);
                    $("#data-shared").text(res.data.dataShared);
                    /** Add the provider/doctor details - end */ 
                
                }else{
                    // Show no data provider/doctor data
                    $("#access-details-modal-body").html('<p>'+res.message+'</p>');
                }

                // Show Popup
                $('#accessDetailsPopUp').modal('show');
            }
        });
    }

    // Render the html dom for access details popup
    function doctorAccessHTML(){
        let html = '';
        html += '<div class="doctor-detail">';
            html += '<div class="doctor-photo">';
                html += '<img width="139" height="139" src="" id="doctor-image" alt="doctor">';
            html += '</div>';
            html += '<div class="doctor-name">';
                html += '<h2 id="doctor-name"></h2>';
                html += '<p>Access Given: <span id="start-date"></span></p>';
                html += '<p>Access Ends: <span id="end-date"></span></p>';
                html += '<p>Data Shared: <span id="data-shared"></span></p>';
            html += '</div>';
        html += '</div>';
        return html;
    }

    // Open the doctor details popup
    function openDoctorDetails(event) {
        // Get the current selection of access details HTML DOM
       let dom = $(event);
       // Use the url from its attribute element
       let fetchDoctorDataRoute = dom.attr('data-url');

       $.ajax({
            url: fetchDoctorDataRoute,
            type : 'get',
            dataType: "json",
            success: function(res){
                $("#doctor-details-modal-body").html('');
                if(res.status == '1'){
                    // Display the html dom
                    $("#doctor-details-modal-body").html(doctorDetailsHTML());
                    
                    /** Add the provider/doctor details - start */ 
                    $("#doctor-access-image").attr('src',res.data.image);
                    $("#doctorname").text('Dr. '+res.data.firstName+" "+res.data.lastName);
                    $("#title").text(res.data.title);
                    $("#institute-name").text(res.data.institution);
                    /** Add the provider/doctor details - end */ 
                
                }else{
                    // Show no data provider/doctor data
                    $("#doctor-details-modal-body").html('<p>'+res.message+'</p>');
                }

                // Show Popup
                $('#doctorDetailsPopUp').modal('show');
            }
        });
    }

    // Render the html dom for doctor details popup
    function doctorDetailsHTML(){
        let html = '';
        html += '<div class="doctor-detail">';
            html += '<div class="doctor-photo">';
                html += '<img width="139" height="139" src="" id="doctor-access-image" alt="doctor">';
            html += '</div>';
            html += '<div class="doctor-name">';
                html += '<h2 id="doctorname"></h2>';
                html += '<p id="title"></p>';
                html += '<p id="institute-name"></p>';
            html += '</div>';
        html += '</div>';
        return html;
    }

</script>
@endpush
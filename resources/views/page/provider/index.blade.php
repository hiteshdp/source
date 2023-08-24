@extends('layout.default')


@section('content')
<div class="container750 min-450">
    <div class="providers-user patients-flow">

        
        <div class=" top-add-form flex-column mt-2">
            <div class="doctor-detail align-items-center justify-content-center">
                <div class="doctor-photo">
                    <img width="82" height="82" class="rounded-circle"  src="{{$providerData->image}}" id="doctor-access-image" alt="doctor">
                </div>
                <div class="doctor-name">
                    <h2 id="doctorname">Dr. {{$providerData->firstName." ".$providerData->lastName}}</h2>
                </div>
            </div>

            <h2 class="patients-list-title">Shared Access Patient List</h2>

            <!---- Check if data is available then show the patients listing - Start ----->
                @if(!empty($patientsList))
                    <!---- Show the patients list with the action items - Start ----->
                    @foreach($patientsList as $patientsKey => $patientsValue)
                        <ul class="provider-list mt-2"> 
                            <li class="mb-2">
                                <div class="provider-list-photo">
                                    <img width="40" height="40" class="rounded-circle mr-2" src="{{asset('images/profile-member.jpg')}}" alt="Louis">{{$patientsValue->firstName. " " . $patientsValue->lastName}}
                                </div>
                                <div class="provider-access">
                                    <a class="mr-lg-4" href="{{$patientsValue->reportAccessURL}}">Report</a>
                                    <a href="{{$patientsValue->removeAccessURL}}">Revoke Access</a>
                                </div>
                            </li>
                            
                        </ul>
                    @endforeach
                    <!---- Show the patients list with the action items - End ----->
                @else
                <div class="providers-info">
                   
                    <div class="mt-5">
                        <img class="mb-3" src="{{asset('images/provider-dashboard.svg')}}" alt="Share User">
                        <p>Once a patient approves shared access you<br> will see them listed here.</p>
                    </div>
                </div>
                @endif
            <!---- Check if data is available then show the patients listing - End ----->
        </div>


    </div>
</div>

@endsection

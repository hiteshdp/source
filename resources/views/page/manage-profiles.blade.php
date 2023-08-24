@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp


@section('content')
<div class="container750">
    
          
    <div class="cabinet-title mt-4 mb-4">
        <h1>Manage profiles</h1>
        
    </div>  
   
   <div class="container300">
        <div class="manage-profiles media align-items-center">
            <img class="img-thumbnail rounded-circle p-0" src="{{$userDetails['avatar']}}" alt="user" title="user" style="width: 48px; height: 48px;" onerror="this.onerror=null;this.src='{{ asset("images/user-img.svg") }}';">
            <div class="media-body mb-0 ml-3">
                <strong class="font-weight-normal">{{$userDetails['name']}}</strong>
                <p class="m-0">{{$userDetails['genderAge']}}</p>
            </div>
            <div class=""> 
                <a href=""><img class="p-2" src="{{asset('images/profile-edit.svg')}}" alt="Profile Edit"></a>
            </div>
        </div>
        @if(!empty($profileMembers))
            @foreach($profileMembers as $value)
                <div class="manage-profiles media align-items-center">
                    <img class="img-thumbnail rounded-circle p-0" src="{{$value['profile_picture']}}" alt="user" title="user" style="width: 48px; height: 48px;" onerror="this.onerror=null;this.src='{{ asset("images/user-img.svg") }}';">
                    <div class="media-body mb-0 ml-3">
                        <strong class="font-weight-normal">{{$value['name']}}</strong>
                        <p class="m-0">{{$value['genderAge']}}</p>
                    </div>
                    <div class=""> 
                        <a href="{{route('edit-profile-member',Crypt::encrypt($value['id']))}}"><img class="p-2" src="{{asset('images/profile-edit.svg')}}" alt="Profile Edit"></a>
                        <a href="{{route('remove-profile',Crypt::encrypt($value['id']))}}"><img class="p-2" src="{{asset('images/delet-filled.svg')}}" alt="Profile Delete"></a>
                    </div>
                </div>
            @endforeach
        @endif
        <a href="{{route('medicine-cabinet')}}" class="btn w-100 btn-gradient "> Continue to WellKabinet&#8482;</a>
   </div>
</div>
@endsection


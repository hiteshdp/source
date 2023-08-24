@extends('layout.default')

@section('title', __('Wellkasa - Update Profile'))
@section('meta-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-news-keywords', __('wellkasa prevention, wellkasa treatment, Wellkasa therapy'))
@section('meta-description', __('Wellkasa - Understanding the use prudent use of antimicrobials and their Health Canada categories'))

@section('content')
<div class="container">
    <div class="container750  mt-0 mt-md-5" id="myTabContent">
        <a class="arrow-back" href="{{route('my-profile')}}"><img class="back-icon" src="{{asset('images/arrow-back-fill.svg')}}" alt="back"></a>
        <div class="container300">
            <form id="deleteProfileForm" action="{{$profileMemberData['deleteUrl']}}" method="POST">
                @csrf
                @method('delete')
                <h2 class="heading2 mt-4 mb-4">Remove profile</h2>
                <div class="user-photo">
                    <img src="{{$profileMemberData['profile_picture']}}" onerror="this.onerror=null;this.src='{{ asset("images/profile-member.jpg") }}';" alt="unsplash"  width="144" height="144">
                </div>
                <div class="username">
                    {{$profileMemberData['name']}}
                    <span>{{$profileMemberData['genderAge']}}</span>
                </div>
                <div class="delete-details">
                    <p>Please confirm if you want to delete this profile from your account.</p> 
                </div>
                <input type="submit" class="btn-red mt-5 w-100 border-0" value="Remove">
            </form>
        </div>
   </div>
</div>
@endsection

@extends('layout.default')

@section('title', __('Wellkasa - Cancel Subscription'))
@section('meta-keywords', __('wellkasa cancel subscription'))
@section('meta-news-keywords', __('wellkasa cancel subscription'))
@section('meta-description', __('Wellkasa - wellkasa cancel subscription'))

@section('content')
<div class="container">
    <div class="container750  mt-0 mt-md-5" id="myTabContent">
        <a class="arrow-back" href="{{route('my-profile')}}"><img class="back-icon" src="{{asset('images/arrow-back-fill.svg')}}" alt="back"></a>
            <div class="container300">
                <h2 class="heading2 mt-4 mb-5">Cancel Subscription</h2>
                <div class="delete-details text-left">
                    <p>Please confirm cancellation. You can continue to use your current plan benefits until the next billing date {{$nextBillingCycleDate}} after which your plan will be changed to Wellkasa Basic</p>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">Delete account</label>
                </div>
                <a class="btn-red mt-2" href="{{route('cancel-subscription',$subscriptionId)}}">Cancel</a>
            </div>
   </div>
</div>
@endsection

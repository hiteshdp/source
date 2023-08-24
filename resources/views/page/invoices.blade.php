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
                <h2 class="heading2 mt-4 mb-5">Invoices</h2>
                @if(!empty($invoiceDetails))
                    @foreach($invoiceDetails as $invoice)
                        <div class="invoices-list">
                            <div class="invoice-date">
                                {{$invoice['createdAt']}}                              
                                <span>{{$invoice['status']}}</span>
                            </div>
                            <div class="invoice-price">
                                {{$invoice['amount']}} 
                            </div>
                            @if(!empty($invoice['invoice_url']))
                            <div class="invoice-pdf">
                                <a href="{{$invoice['invoice_url']}}"><img  src="{{asset('images/download.svg')}}" alt="download"></a>
                            </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
   </div>
</div>
@endsection

@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp


@section('content')
<div class="container750 ">
    <div class="container300">
    <div class="cabinet-title mt-4 mb-3">
            <h1>Payment Details</h1>
            <span>You will be charged <br><strong>60 days</strong>   from today!</span>
        </div>  
    <form action="/subscribe" method="POST" class="subscribe-form" id="subscribe-form">
    <input type="hidden" id="subscribe" name="selectedPlanId" value="{{ $selectedPlanId }}">
        <div class="form-group">
            <!-- <label for="card-holder-name">Card Holder Name</label> -->
            <input placeholder="Card Holder Name " class="form-control" id="card-holder-name" type="text">
            <div id="card-holder-errors" role="alert" style="display:none; color: #ff0000;">Card holder name is required.</div>
        </div>
        @csrf
        <div class="form-group">
            <!-- <label for="card-element">Credit or debit card</label> -->
            <div id="card-element" class="form-control">
            </div>
            <!-- Used to display form errors. -->
            <div id="card-errors" role="alert"></div>
        </div>
        <div class="stripe-errors"></div>
        @if (count($errors) > 0)
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
            {{ $error }}<br>
            @endforeach
        </div>
        @endif
        <div class="form-group text-center">
            <button type="button" id="card-button" data-secret="{{ $intent->client_secret }}" class="btn btn-gradient w-100 font-weight-bold">Verify & Proceed</button>
        </div>
    </div>
    </form>
</div>
</div>
@endsection
@push('styles')
<style>
    .StripeElement {
        background-color: white;
        padding: 8px 12px;
        border-radius: 4px;
        border: 1px solid transparent;
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }
    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }
    .StripeElement--invalid {
        border-color: #fa755a;
    }
    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
</style>
@endpush
@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('pk_test_51KYoTVHtbsz4q7WstlCP78fElHf6M6JiQpqcpjVtSZ5MkaiY9xXVSvQRobaU8yLeCudfduRVe1uYWTKGOuOmrcay00nAJrhzH3');
    var elements = stripe.elements();
    var style = {
        base: {
            color: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };
    var card = elements.create('card', {hidePostalCode: true,
        style: style});
    card.mount('#card-element');
    card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });
    const cardHolderName = document.getElementById('card-holder-name');
    const cardButton = document.getElementById('card-button');
    const clientSecret = cardButton.dataset.secret;

    // Checked card holder name validation
    cardHolderName.addEventListener('change', function(event) {
        if(cardHolderName.value === ""){
            $('#card-holder-errors').show();
            return true;
        }else{
            $('#card-holder-errors').hide();
        }
    });

    cardButton.addEventListener('click', async (e) => {
        if(cardHolderName.value === ""){
            $('#card-holder-errors').show();
            return true;
        }else{
            $('#card-holder-errors').hide();
        }
        const { setupIntent, error } = await stripe.confirmCardSetup(
            clientSecret, {
                payment_method: {
                    card: card,
                    billing_details: { name: cardHolderName.value }
                }
            }
            );
        if (error) {
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
        } else {
            paymentMethodHandler(setupIntent.payment_method);
        }
    });
    function paymentMethodHandler(payment_method) {
        var form = document.getElementById('subscribe-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'payment_method');
        hiddenInput.setAttribute('value', payment_method);
        form.appendChild(hiddenInput);
        form.submit();
    }
</script>
@endpush
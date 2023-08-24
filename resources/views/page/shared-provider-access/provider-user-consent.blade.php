@extends('layout.default')


@section('content')
<div class="container750 min-450 p-0 mb-3">
    <div class="providers-user">

        <div class="providers-header">
        <a href="{{$backRoute}}"> <img class="arrow-back-doc"  src="{{asset('images/arrow-back-doc.svg')}}" alt="Share User"></a>  <h2 class="mb-0"> Release Consent Form for Dr. {{ $providerData->firstName.' '.$providerData->lastName }}</h2> 
        </div>
        <div class="providers-body p-4">
            <div class="manage-symptom-tracker-list">
                <div class="providers-doc-detail">
                    <p>PLACEHOLDER</p>
                    <p>HIPPA compliance T&Cs</p>
                    <p>Access Duration:<strong> 1 yr from today</strong></p>
                    <p>Shared Data:<strong> First Name, Last Name, Symptoms, Meds, Notes.</strong></p>
                    <p>Data Not Shared:<strong> DOB, Address, Phone, Email</strong></p>
                </div>
                
            </div>
            <form method="POST" class="mt-4 providers-checkbox" id="consent-form" action="{{ $sendVerificationCodeRoute }}">
                @csrf
                <div class="form-check form-group mb-1">
                    <input type="checkbox" class="form-check-input" name="i_agree" id="i_agree" required>                                
                    <label class="form-check-label" for="i_agree">I agree to give access to Dr. {{ $providerData->firstName.' '.$providerData->lastName }} until {{$accessTillDate}}*</label>
                    <div class="form-group mb-1" style="min-height: 24px;">
                        <label id="i_agree-error" class="error" for="i_agree"></label>
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn btn-gradient font-weight-bold w-40" disabled="disabled" id="continue"> Continue </button>
                </div>
                
            </form>
        </div>
    </div>
</div>
<div class="revoke-access">*You can revoke this access anytime</div>
@endsection

@push('scripts')
<script>

    // Disable/Enable on the consent checkbox
    $('#i_agree').change(function() {
        $("#continue").attr("disabled","disabled");
        if($(this).is(":checked")) {
            $("#continue").removeAttr("disabled");
        }
    });

    // validate check box - code start
    $('#consent-form').validate({
        rules:{
            i_agree : {
                required : true
            }
        },
        messages:{
            i_agree : {
                required : "Please tick your consent to proceed further.",
            }
        }
    });
    // validate check box - code end
    
</script>
@endpush('scripts')
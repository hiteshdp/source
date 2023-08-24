@extends('layout.default')


@section('content')
<div class="container750 min-450">
    <div class="providers-user">

        <!---- Display add provider input box - Start ----->
        <div class="manage-symptom-footer mb-3 text-center">
            <form id="add-provider" method="POST" action="">
                <div class="top-add-form align-items-start">
                    @csrf
                    <div class="manage-symptom-add">
                        <div class=" gradient-dropdown text-left">
                            <input type="text" autocomplete="off" class="form-control" aria-describedby="" id="search_provider" name="search_provider" value="" placeholder="Search by First and Last Name" >
                            <label id="search_provider-error" class="error" for="search_provider" style="display: none;"></label>
                        </div>
                    </div>
                    <div class="manage-symptom-save-btn ml-2">
                        <button class=" btn-gradient w-100 border-0 d-block" type="submit" id="add-provider-name">Select</button>
                    </div>
                </div>
            </form>
        </div>
        <!---- Display add provider input box - End ----->

        
        <div class="providers-doctor-info mt-4">
            <div class="doctor-detail">
                <div class="doctor-photo">
                    <img width="75" height="75" src="{{$providerData->image}}" alt="{{$providerData->firstName}} {{$providerData->lastName}}"> 
                </div>
                <div class="doctor-name">
                    <h2>Dr. {{$providerData->firstName}} {{$providerData->lastName}}</h2>
                    <p>{{$providerData->title}}</p>
                    <p>{{$providerData->institution}}</p>
                </div>
            </div>
            <form method="POST" class="mt-4 providers-checkbox" id="details-form" action="{{ $providerData->consentUrl }}">
                @csrf
                <div class="form-check form-group mb-1">
                    <input type="checkbox" class="form-check-input" name="i_agree" id="i_agree" required>                                
                    <label class="form-check-label" for="i_agree">I have reviewed the provider info. Looks good!</label>
                    <div class="form-group mb-1">
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

@endsection

@push('scripts')

<!-- Include auto search provider error functions -->
<script src="{{ asset('js/provider-search-error-libraries.js') }}"></script>
<!-- Include auto search of provider dropdown -->
<script src="{{ asset('js/provider-search.js') }}"></script>

<script>

    // Disable/Enable on the consent checkbox
    $('#i_agree').change(function() {
        $("#continue").attr("disabled","disabled");
        if($(this).is(":checked")) {
            $("#continue").removeAttr("disabled");
        }
    });


</script>
@endpush
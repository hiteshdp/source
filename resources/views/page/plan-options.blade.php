@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp


@section('content')
    <section class="notification-block">
        <div class="alert alert-danger mb-0" style="display: none;" id="showSelectPlanError">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            <strong>Whoops!</strong> There were some problems occured.<br><br>
            <ul>
                <li>Please select plan.</li>
            </ul>
        </div>
    </section>
    <div class="container750">
        <a href="{{route('select-plan')}}"><img class="back-icon" src="{{asset('images/arrow-back-fill.svg')}}" alt="back"></a>
        <div class="container300">
            <h2 class="heading2 mt-4 mb-4">Plan options</h2>
                <form class="edit-meicine-field ">
                <div class="form-row tagged-condition">
                    <div class="form-group col-8 additional-info">
                        <strong> Additional Profiles</strong>
                        <span>5 add-on profiles already included in below packages.</span>
                    </div>
                </div>
                </form> 
                <form name="addPaymentMethod" id="addPaymentMethod" method="post" action="{{url('add-payment-method')}}">
                    @csrf
                    <input type="hidden" value="" name="selectedPlanId" id="selectedPlanId"/>
                    <h2 class="detail-title">Select billing cycle</h2>
                    @foreach($plans as $plan)
                        <div class="billing-list" onClick="selectbillingCycle('{{$plan->id}}')">
                                <span class="billing-title">{{$plan->product->name}}
                                    <p>{{$plan->product->description}}</p>
                                </span> 
                                <span class="billing-price" id="billing-price-annually">${{$plan->amount/100}} </span>
                            </div>
                    @endforeach   
                    <button type="button" onClick="submitAddPaymentForm()" class="btn-gradient w-100 mt-5 border-0">Add Payment Method</button>
                </form>
        </div>
    </div>
@endsection
@push('scripts')
<script type="text/javascript">
    $(function() {
        $('.billing-list').on('click', function() {
            $(".billing-list").removeClass("selected-plan");
            $(this).addClass('selected-plan');
            $('#showSelectPlanError').hide();
        });
    });

    $('#profileMemberCount').change(function () { 
        var profileMemberCount = $(this).val();
        // Get defualt value of plan
        var defaultMonthlyPaid = $('#defaultMonthlyPaid').val();
        var defaultAnnuallyPaid = $('#defaultAnnuallyPaid').val();

        var profileMonthlyPrice = 0.99;
        var profileAnnuallyPrice = 9;

        // Monthly Price calculations
        var monthlyProfilePrice = profileMemberCount*profileMonthlyPrice;
        var totalMonthlyPaidPrice = Number(monthlyProfilePrice) + Number(defaultMonthlyPaid);
        totalMonthlyPaidPrice = totalMonthlyPaidPrice.toFixed(2);


        // Annually price calculations
        var annuallyProfilePrice = profileMemberCount*profileAnnuallyPrice;
        var totalAnnuallyPaidPrice = Number(annuallyProfilePrice) + Number(defaultAnnuallyPaid);
        totalAnnuallyPaidPrice = totalAnnuallyPaidPrice.toFixed(2);


        // Set value 
        $('#monthlyPaid').val(totalMonthlyPaidPrice);
        $('#annuallyPaid').val(totalAnnuallyPaidPrice);

        $("#billing-price-monthly").html("$"+totalMonthlyPaidPrice);
        $("#billing-price-annually").html("$"+totalAnnuallyPaidPrice);
        $('#profileMemberCountVal').val(profileMemberCount);


    });

    function selectbillingCycle(plan_id){
        $('#selectedPlanId').val(plan_id);
    }

    function submitAddPaymentForm(){
        $('#showSelectPlanError').hide();
        var selectedPlanId = $('#selectedPlanId').val();
        $isValid = 1;
        if(selectedPlanId == ''){
            $isValid = 0;
        }

        if($isValid == 0){
            // alert('Please select plan');
            $('#showSelectPlanError').show();
            return false;
        }

        $('#addPaymentMethod').submit();

    }
</script>    
@endpush
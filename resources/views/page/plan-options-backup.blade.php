@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp


@section('content')
    
    <div class="container750">
        <div class="container300">
            <h2 class="heading2 mt-4 mb-4">Plan options</h2>
                <form class="edit-meicine-field ">
                <div class="form-row tagged-condition">
                    <div class="form-group col-8 additional-info">
                   <strong> Additional Profiles</strong>
                    <span>Each add-on profile $0.99/month or $9/year</span>
                       
                    </div>
                    <div class="form-group col-4 ">
                       
                        <select id="profileMemberCount" class="form-control" name="profileMemberCount">
                         @for($i = 0; $i <= 8; $i++)   
                            <option value="{{$i}}">{{$i}}</option>
                         @endfor   
                        </select>
                    </div>
                </div>
                    <!-- <div class="form-group">
                        <div class="input-group coupon-input"> 
                            <input type="text" class="form-control coupon" name="" placeholder="Promo Code (Optional)"> <span class="input-group-append"> <button class="btn-coupon">Apply</button> </span> 
                        </div>
                        <span class="code-green">Promo Code Applied!</span> 
                    </div> -->
                </form> 
                <form name="addPaymentMethod" id="addPaymentMethod" method="post" action="{{url('add-payment-method')}}">
                    @csrf
                    <input type="hidden" value="2" name="billingCycleType" id="billingCycleType"/>
                    <input type="hidden" value="0" name="profileMemberCountVal" id="profileMemberCountVal"/>
                    <input type="hidden" value="0" name="profileMemberCountVal111" />
                    <h2 class="detail-title">Select billing cycle</h2>
                    <div class="billing-list" id="monthlyBillingList">
                        <span class="billing-title">Paid monthly </span> <span class="billing-price" id="billing-price-monthly">$4.99 </span>
                        <input type="hidden" value="4.99" name="defaultMonthlyPaid" id="defaultMonthlyPaid"/>
                        <input type="hidden" value="4.99" name="monthlyPaid" id="monthlyPaid"/>
                    </div>
                    <div class="billing-list selected-plan" id="annuallyBillingList">
                        <span class="billing-title">Paid annually  
                            <p>$3.75/monthly, 25% Off</p>
                        </span> 
                        <span class="billing-price" id="billing-price-annually">$45 </span>
                        <input type="hidden" value="45" name="defaultAnnuallyPaid"  id="defaultAnnuallyPaid"/>
                        <input type="hidden" value="45" name="annuallyPaid" id="annuallyPaid"/>
                    </div>   
                    <button type="submit" class="btn-gradient w-100 mt-5 border-0">Add Payment Method</button>
                </form>
        </div>
    </div>
@endsection
@push('scripts')
<script type="text/javascript">
    $(function() {
        $('#monthlyBillingList').on('click', function() {
            $(".billing-list").removeClass("selected-plan");
            $(this).addClass('selected-plan');
            $('#billingCycleType').val(1); // 1 for monthaly and 2 for annullay
        });

        $('#annuallyBillingList').on('click', function() {
            $(".billing-list").removeClass("selected-plan");
            $(this).addClass('selected-plan');
            $('#billingCycleType').val(2); // 1 for monthaly and 2 for annullay
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
</script>    
@endpush
@extends('layout.default')
      
@section('meta-keywords', __('Migraine, Tracker, MIDAS, Hit-6, '))
@section('meta-news-keywords', __('Migraine, Tracker, MIDAS, Hit-6, '))
@section('meta-description', __('Migraine Might - Gaining Power over Migraines with Knowledge and Self Care'))
@section('title', __('Migraine Might - Gaining Power over Migraines with Knowledge and Self Care'))

@section('content')
<div class="pt-5 pb-0 quiz-wrapper" style="background: linear-gradient(180deg, #FFF 0%, rgba(231, 230, 230, 0) 100%);">
  <div class="container750 quiz-flow bg-transparent  mt-0">
    <div class="cabinet-accordion cabinet-header-new">
      <div class="quiz-title py-4">
        Take this simple test to find the severity of your migraines
      </div>
      @include('page.migraine-tracker.tabs')
      <div class="row mt-5" style="font-weight:bold;">
        <div class="col-md-12 mb-3 text-center">
          <!-- About Migraine Might click point - code start -->
          <a class="px-3 w-100" href="{{route('about-migraine-might')}}" target="_blank">About Migraine Might</a>
          <!-- About Migraine Might click point - code end -->
        </div>
        <div class="col-md-12 mb-3 text-center">
          <!-- Why Migraine Might is different modal popup click point - code start -->
          <a class="px-3 w-100" href="javascript:void(0)" onclick="showMigraineMightPopUp();">Why Migraine Might is Different?</a>
          <!-- Why Migraine Might is different modal popup click point - code end -->
        </div>
        <div class="col-md-12 mb-3 text-center">
            <!-- 10 benefits of Assessing & Tracking modal popup click point - code start -->
            <a class="px-3 w-100" href="javascript:void(0)" onclick="showAssessingTrackingPopUp();">10 Benefits of Assessing & Tracking</a>
            <!-- 10 benefits of Assessing & Tracking modal popup click point - code end -->
        </div>
        <div class="col-md-12 mb-3 text-center">
          <!-- Emerging Integrative Therapies modal popup click point - code start -->
          <a class="px-3 w-100" href="https://cdn.shopify.com/s/files/1/0620/4099/8073/files/UCI_WWD_Migraine_Bonakdar_45e21446-0dc5-4f85-b416-9fb9fd1053f4.pdf?v=1683254507" target="_blank">Emerging Integrative Therapies for Migraines</a>
          <!-- Emerging Integrative Therapies modal popup click point - code end -->
        </div>
      </div>
    </div>
  </div>
</div>


<!---Modal pop up for Why Migraine Might is different - code start --->
<div class="modal fade info-note-popup test-popup" id="migraineMightPopUp">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow bg-white rounded">
      <div class="modal-header">
        <h4 class="modal-title w-100 text-center" id="migraineMightPopUp-modal-title"><img src="{{asset('images/popup-logo.png')}}" width="300" height="63" alt="Migraine Might" title="Migraine Might"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><svg width="20" height="20" viewBox="0 0 23 23" fill="#fff" xmlns="http://www.w3.org/2000/svg"> <path d="M12.7439 11.1157L21.955 1.90455C22.1371 1.69199 22.2322 1.41857 22.2214 1.13893C22.2106 0.859291 22.0947 0.594026 21.8968 0.396143C21.6989 0.19826 21.4336 0.0823348 21.154 0.0715335C20.8744 0.0607322 20.6009 0.155851 20.3884 0.33788L11.1773 9.54899L1.96616 0.326769C1.75693 0.117542 1.47316 0 1.17727 0C0.88138 0 0.597608 0.117542 0.388382 0.326769C0.179155 0.535995 0.0616131 0.819767 0.0616131 1.11566C0.0616131 1.41155 0.179155 1.69532 0.388382 1.90455L9.6106 11.1157L0.388382 20.3268C0.272069 20.4264 0.177602 20.549 0.110909 20.6868C0.0442165 20.8247 0.00673854 20.9748 0.00082799 21.1278C-0.00508256 21.2808 0.0207013 21.4334 0.0765619 21.576C0.132423 21.7186 0.217154 21.8481 0.325437 21.9564C0.43372 22.0647 0.563217 22.1494 0.7058 22.2053C0.848384 22.2611 1.00098 22.2869 1.154 22.281C1.30702 22.2751 1.45717 22.2376 1.59501 22.1709C1.73286 22.1042 1.85544 22.0098 1.95505 21.8934L11.1773 12.6823L20.3884 21.8934C20.6009 22.0755 20.8744 22.1706 21.154 22.1598C21.4336 22.149 21.6989 22.0331 21.8968 21.8352C22.0947 21.6373 22.2106 21.372 22.2214 21.0924C22.2322 20.8127 22.1371 20.5393 21.955 20.3268L12.7439 11.1157Z" fill="#fff"/></svg></span>
        </button>
      </div>
      <div class="modal-body" id="migraineMightPopUp-modal-body">
      <div class="row">
          <div class="col-md-6 mb-3">
             <p><strong>Simple</strong> Tracking is important but so
                is life. Migraine Might makes tracking
                seamless so you can get back to
                living.
            </p> 
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Free</strong> Understanding your migraine is
                fundamental to optimal treatment and
                that's why Migraine Might is Free.
            </p>  
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Validated</strong> Score your migraines
                on 2 validated scales to find your
                baseline severity score & help
                your healthcare team better
                understand their impact.
            </p> 
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Individual</strong> Allows you to pick
                & track your most bothersome
                symptoms.
            </p>   
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Trendy</strong> You can track your migraine
                symptoms and migraine survey
                scores longterm to determine trends.
            </p>  
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Integrative</strong> Allows you to track the
                medications and treatments you use to
                manage migraine to evaluate benefit.
            </p>   
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Easy to View</strong> Migraine Might
                dashboard gives a quick snapshot
                of your migraine episodes &
                severity.
            </p>  
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Secure</strong> Your data is protected.</p>   
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Easy to share</strong> With export &
                email options, your reports
                are easy to share & discuss
                with your healthcare team.
            </p>   
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Easy access</strong> From any device
                small or large.
            </p>   
          </div> 
      </div> 
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for Why Migraine Might is different - code end--->


<!---Modal pop up for 10 benefits of Assessing & Tracking - code start --->
<div class="modal fade info-note-popup test-popup" id="assessingTrackingPopUp">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow bg-white rounded">
      <div class="modal-header">
        <h4 class="modal-title w-100 text-center" id="assessingTrackingPopUp-modal-title"><img src="{{asset('images/popup-logo.png')}}" width="300" height="63" alt="Migraine Might" title="Migraine Might"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><svg width="20" height="20" viewBox="0 0 23 23" fill="#fff" xmlns="http://www.w3.org/2000/svg"> <path d="M12.7439 11.1157L21.955 1.90455C22.1371 1.69199 22.2322 1.41857 22.2214 1.13893C22.2106 0.859291 22.0947 0.594026 21.8968 0.396143C21.6989 0.19826 21.4336 0.0823348 21.154 0.0715335C20.8744 0.0607322 20.6009 0.155851 20.3884 0.33788L11.1773 9.54899L1.96616 0.326769C1.75693 0.117542 1.47316 0 1.17727 0C0.88138 0 0.597608 0.117542 0.388382 0.326769C0.179155 0.535995 0.0616131 0.819767 0.0616131 1.11566C0.0616131 1.41155 0.179155 1.69532 0.388382 1.90455L9.6106 11.1157L0.388382 20.3268C0.272069 20.4264 0.177602 20.549 0.110909 20.6868C0.0442165 20.8247 0.00673854 20.9748 0.00082799 21.1278C-0.00508256 21.2808 0.0207013 21.4334 0.0765619 21.576C0.132423 21.7186 0.217154 21.8481 0.325437 21.9564C0.43372 22.0647 0.563217 22.1494 0.7058 22.2053C0.848384 22.2611 1.00098 22.2869 1.154 22.281C1.30702 22.2751 1.45717 22.2376 1.59501 22.1709C1.73286 22.1042 1.85544 22.0098 1.95505 21.8934L11.1773 12.6823L20.3884 21.8934C20.6009 22.0755 20.8744 22.1706 21.154 22.1598C21.4336 22.149 21.6989 22.0331 21.8968 21.8352C22.0947 21.6373 22.2106 21.372 22.2214 21.0924C22.2322 20.8127 22.1371 20.5393 21.955 20.3268L12.7439 11.1157Z" fill="#fff"/></svg></span>
        </button>
      </div>
      <div class="modal-body" id="assessingTrackingPopUp-modal-body">
      <div class="row">
          <div class="col-md-6 mb-3">
            <p><strong>More accurately captures the details</strong> of your migraine episodes than with
                recall.
            </p> 
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Identifies the true impact of your
                migraines</strong> on work, school and social
                activities which is often minimized with
                recall.
            </p>  
          </div>
          <div class="col-md-6 mb-3">
            <p>Tracking has been <strong>proven to
                improve</strong> migraine management.
            </p> 
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Determines tolerability & benefit of
                treatments</strong> once they have been
                initiated.
            </p>  
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Finds patterns that may have been
                missed</strong> to inform treatment planning.
            </p> 
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Creates a rapid, accurate & easy to
                share summary</strong> which focuses time
                on treatment planning.
            </p>  
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Creates a long-term record and
                summary</strong> of migraine trends and
                treatments.
            </p> 
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Motivates appropriate
                consultation & discussion </strong> of treatment options which are
                underutilized.
            </p>  
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Details & advocates for
                treatment of often missed
                bothersome symptoms
                beyond headaches</strong> such as
                fatigue, brain fog, sleep
                difficulties, GI symptoms and
                mood changes.
            </p>  
          </div>
          <div class="col-md-6 mb-3">
            <p><strong>Improves communication</strong> with the healthcare team.
            </p>  
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for 10 benefits of Assessing & Tracking - code end--->
@endsection

@push('scripts')
<script type="text/javascript">
   // Display Migraine Might pop up
   function showMigraineMightPopUp(){
       $('#migraineMightPopUp').modal('show');
   }

   // Display Assessing & Tracking pop up
   function showAssessingTrackingPopUp(){
       $('#assessingTrackingPopUp').modal('show');
   }
</script>
@endpush
@php
    $locale = App::getLocale();
    if ($locale == 'fr') {
        $url = 'fr/offer-promotion';
    } else {
        $url = 'offer-promotion';
    }

    $companies = json_encode(Cache::get("companies"));
@endphp


<!-- ======= promotions ======= -->


<!-- ======= footer ======= -->
<footer class="footer">
    <div class="container">
       <div class="row">
            <div class="col-12 col-md-6 col-lg-9">
                
            <ul class="footer-links">
                
                <li><a href="https://wellkasa.com/pages/privacy-policy" target="_blank">Privacy Policy</a></li>
                <li><a href="https://wellkasa.com/pages/terms-of-service" target="_blank">Terms of Service</a></li>
                
                <!-- Hide the below screen for user migraine role - start -->
                {{-- @if( !in_array(request()->segment(1),Config::get('constants.MigraineRoutes')) && !is_migraine_quiz())
                    @if(Auth::check() && Auth::user()->isMigraineOrProviderUser())
                       
                    @else --}}
                        <!-- Hide the return & shipping menus in footer - start -->
                        @if(1!=1)
                            <li><a href="https://wellkasa.com/policies/refund-policy" target="_blank">Returns & Refunds</a></li>
                            <li><a href="https://wellkasa.com/policies/shipping-policy" target="_blank">Shipping Policy</a></li>
                        @endif
                        <!-- Hide the return & shipping menus in footer - end -->
                    {{-- @endif
                @endif --}}
                <!-- Hide the below screen for user migraine role - start -->

                <li><a href="https://wellkasa.com/pages/contact" target="_blank">Contact Us</a></li>
                
            </ul>
                <p class="d-none d-lg-block">&copy; {{date('Y')}} Wellkasa. All Rights Reserved. </p>
                <p class="pt-3 d-none d-lg-block"><strong>Disclaimer:</strong> Wellkasa does not offer clinical advice. Nothing stated or posted on the Site is intended to be the practice of medicine. Consult your medical provider for any medical advice.</p>
                <p class="pt-3 d-none d-lg-block">By using this site you are agreeing to our Term of Service.</p>
            </div>    
            <div class="col-12 col-md-6 col-lg-3 footer-right">
            <ul class="social-links">
                <li>
                    <a href="https://www.facebook.com/wellkasa" rel="noreferrer" target="_blank">
                        <img width="32" height="32" src="{{asset('images/fb.svg')}}" alt="fb"> 
                    </a>
                </li>
                <li>
                    <a href="https://www.twitter.com/wellkasa" rel="noreferrer" target="_blank">
                        <img width="32" height="32" src="{{asset('images/twitter.svg')}}" alt="twitter"> 
                    </a>
                </li>
                <li>
                    <a href="https://www.instagram.com/wellkasa" rel="noreferrer" target="_blank">
                        <img width="32" height="32" src="{{asset('images/instagram.svg')}}" alt="instagram"> 
                    </a>
                </li>
                <li>
                    <a href="https://www.linkedin.com/company/wellkasa" rel="noreferrer" target="_blank">
                        <img width="32" height="32" src="{{asset('images/in.svg')}}" alt="in"> 
                    </a>
                </li>
                <li>
                    <a href="https://www.youtube.com/channel/UCQGfkRTafhEcsxoahJOTp7Q" rel="noreferrer" target="_blank">
                        <img width="32" height="32" src="{{asset('images/youtube.svg')}}" alt="youtube"> 
                    </a>
                </li>
            </ul>   
            <p class="d-block d-lg-none">&copy; {{date('Y')}} Wellkasa. All Rights Reserved. </p>
            <p class="pt-3 d-block d-lg-none"><strong>Disclaimer:</strong> Wellkasa does not offer clinical advice. Nothing stated or posted on the Site is intended to be the practice of medicine. Consult your medical provider for any medical advice.</p>
            <p class="pt-3 d-block d-lg-none">By using this site you are agreeing to our Term of Service.</p>
            </div>
        </div>    



</div> 
</footer>


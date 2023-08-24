<head>
    
    <link rel="manifest" href="{{ url('public/manifest.json') }}">

    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="application-name" content="PWA">
    <link rel="icon" sizes="144x144" href="/images/icons/wl-144x144.png">

    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-title" content="PWA">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Page titles -->
    <title>@yield('title')</title>

    @if(!Request::is('password/reset/*') && !Request::is('password/reset'))
    <!-- Dynamic Meta Content Start -->
    <meta name="keywords" content="@yield('meta-keywords')" />
    <meta name="news-keywords" content="@yield('meta-news-keywords')" />
    <meta name="description" content="@yield('meta-description')" />
    <!-- Dynamic Meta Content End -->  
    @endif

    @if(Request::is('/'))
    <!-- Dynamic twitter Start -->
    <meta name="twitter:url" content="@yield('twitter-url')">
    <meta name="twitter:title" content="@yield('twitter-title')">
    <meta name="twitter:description" content="@yield('twitter-description')">
    <!-- Dynamic twitter End -->

    <!-- Dynamic og tag Start -->
    <meta name="og:url" content="@yield('og-url')">
    <meta name="og:title" content="@yield('og-title')">
    <meta name="fb:title" content="@yield('fb-title')">
    <meta name="og:description" content="@yield('og-description')">
    <!-- Dynamic og tag End -->  
    @endif

    @if(Request::is('condition*') || Request::is('contact-us*') ||Request::is('therapy*'))
    <!-- Dynamic og tag Start -->
    <meta name="og:url" content="@yield('og-url')">
    <meta name="og:title" content="@yield('og-title')">
    <meta name="og:description" content="@yield('og-description')">
    <!-- Dynamic og tag End -->
    @endif
    
    <meta name="facebook-domain-verification" content="w85s4sp01us5t5s4rahj0i2wki6png" />

    <!-- Page canonical URLs -->
    <link rel="canonical" href="{{ Request::is('/') ? url()->full().'/' : url()->full() }}" />

    <!-- Favicons -->
    <link href="{{asset('images/favicon.png')}}" rel="icon">
    <link href="{{asset('images/apple-touch-icon.png')}}" rel="apple-touch-icon">
    
    <!-- Combined System CSS file -->
    <link rel="stylesheet" href="{{ mix('css/all.css') }}">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @stack('styles')


    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-L34SFCV253"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){window.dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-L34SFCV253');
    </script>
    

    <!-- Start Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){
            w[l]=w[l]||[];w[l].push({
                'gtm.start': new Date().getTime(),event:'gtm.js'
            });
            var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
            j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);

        })(window,document,'script','dataLayer','GTM-MKXH4C8');
    </script>
    <!-- End Google Tag Manager -->

    <!-- Google tag (gtag.js) - code start -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-T9EHHGPWKX"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-T9EHHGPWKX');
    </script>
    <!-- Google tag (gtag.js) - code end -->

    @if(Request::is('condition*') || Request::is('therapy*') || Request::is('research*') || Request::is('medicine-cabinet*'))
    <!-- Start Hotjar Tracking Code --> 
    <script> 
        (function(h, o, t, j, a, r) {
            h.hj = h.hj || function() {
                (h.hj.q = h.hj.q || []).push(arguments)
            };
            h._hjSettings = {
                hjid: 3146502,
                hjsv: 6
            };
            a = o.getElementsByTagName('head')[0];
            r = o.createElement('script');
            r.async = 1;
            r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
            a.appendChild(r);
        })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');    
    </script>
    <!-- Start Hotjar Tracking Code -->
    @endif
</head>
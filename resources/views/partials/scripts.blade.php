<!-- Load website combined js -->
<script src="{{mix('js/all.js')}}"></script>

<!-- Load page specific JS file or code here if any-->
@stack('scripts')

<!-- Cookies PopUP Start
<script>
    $.cookieMessage({
        'mainMessage': '<div class="alert-content">Cookies are required to understand how you and other visitors use our websites and applications and to improve your browsing experience. By using this website without changing your browser settings, you consent to the use of cookies and other device identifiers. For more information on our use of cookies, please visit <a href="https://www.jqueryscript.net/privacy/" target="_blank">Privacy Policy</a>.</div>',
        'acceptButton': 'Understood'
    });
</script>
Cookies PopUP End-->

<!-- Message display time out code start -->
<script>
  const alertMsg = setTimeout(function() { $(".alert").fadeOut(); }, 5000); //display the message for 3 seconds
</script>
<!-- Message display time out code end -->

<!--Reload the page if user comes with back button - Start-->
<script type="text/javascript">
if(performance.navigation.type == 2){
   location.reload(true);
}
</script>
<!--Reload the page if user comes with back button - End-->

<!--Start of Tawk.to Script-->
<script type="text/javascript">
// var Tawk_API=Tawk_API||{};
// Tawk_API.visitor = {
// name : '{{{ isset(Auth::user()->name) ? Auth::user()->name : "Guest User" }}}',
// email : '{{{ isset(Auth::user()->name) ? Auth::user()->email : "guest@email.com" }}}',
// };
// var Tawk_LoadStart=new Date();
// (function(){
// var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
// s1.async=true;
// s1.src='https://embed.tawk.to/60f85044649e0a0a5ccd4dfe/1fb4vj2q6';
// s1.charset='UTF-8';
// s1.setAttribute('crossorigin','*');
// s0.parentNode.insertBefore(s1,s0);
// })();
</script>
<!--End of Tawk.to Script-->
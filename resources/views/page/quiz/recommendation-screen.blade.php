@extends('layout.default')
      
@section('meta-keywords', __(html_entity_decode($attemptedQuizTitle)))
@section('meta-news-keywords', __(html_entity_decode($attemptedQuizTitle)))
@section('meta-description', __(html_entity_decode($attemptedQuizTitle)))
@section('title', __(html_entity_decode($attemptedQuizTitle)))

@section('content')

<div style="background: linear-gradient(180deg, {{$quizConfigurations->background_color_screen}} 0%, rgba(231, 230, 230, 0) 100%);">
<div class="container750 quiz-flow bg-transparent mt-0">
    <div class="cabinet-accordion cabinet-header-new">
        
        
        <div class="quiz-wrapper">

            <div class="quiz-step step-1">
                <div class="container text-center">
                    <div id="analyse-screen" style="display:block;">
                        <h2 class="ttl mb-5"  style="color:{{$quizConfigurations->question_text_color}}">{!! nl2br($thanksMessage) !!}</h2>
                        <div class="mb-3">
                            <img src="{{asset('images/loading.gif')}}" alt="Loading" width="100" height="100" class="img-fluid">
                        </div>
                    </div>

                    <!-- score start -->
                    @if($showScore == '1' && !empty($quizScore))
                        <div id="score-screen" style="display:none;">
                            <div class="score">
                                <b>Score</b>
                                <span>{{$quizScore}}</span>
                            </div>
                        </div>
                    @endif
                    <!-- score end -->



                    <!-- Quiz Score range design - start -->
                    <div class="score-borad my-5" style="display:none;">
                        @if(!empty($quizDataRes))
                            @foreach($quizDataRes as $qk=>$qv)
                                <div class="score-borad-list {{isset($qv['activeIconClass']) ? $qv['activeIconClass'] : '' }}">
                                    <div class="score-medicine-title mb-3">
                                        {!! nl2br($qv['title']) !!}<br>
                                        <span style="color:{{$qv['title_label_color']}}">
                                            <?php 
                                                $range = $qv['score_range_low']."-".$qv['score_range_high'];
                                                if($qv['score_range_low']=='21' && $qv['score_range_high']=='105'){
                                                    $range =$qv['score_range_low']."+";
                                                } 
                                            ?>
                                            {{$range}}
                                        </span>
                                    </div>
                                    <div class="disability">
                                        @php    
                                            $titleLbl = explode(' ',$qv['title_label']);
                                        @endphp
                                        {{$titleLbl[0]}}<br>
                                        {{$titleLbl[1]}}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <!-- Quiz Score range design - end -->

                    <!-- Show the message based on the quiz attempted - start -->
                    @if(!empty($quizScoreMsg))
                        <p id="quiz-message" class="quiz-text-alignment" style="display:none;">{!! nl2br($quizScoreMsg) !!}</p>
                    @endif
                    <!-- Show the message based on the quiz attempted - end -->

                    <!-- Show below message after test attempted for guest user - start -->
                    @guest
                    <div class="userinfo mt-2" id="below-title-screen" style="display:none;">                    

                        <div class="col-12 mt-4 mb-3">
                            <a href="{{route('signup')}}" class="btn btn-primary btn-blue btn-100 config-button-quiz" style="background-color: #7380B4; color: #FFF;">Save Results and Start Tracking Migraine Symptoms</a>
                        </div>

                    </div>                    
                    @endguest
                    <!-- Show below message after test attempted for guest user - end -->

                    <!-- Show below message after test attempted for logged in user - start -->
                    @if(Auth::check())
                    <div class="userinfo mt-2" id="below-title-screen" style="display:none;">
                        <div class="col-12 mt-4 mb-3">
                            <a href="{{route('event-selection')}}" class="btn btn-primary btn-blue" style="background-color: #7380B4; color: #FFF;">Back to Tracker</a>
                        </div>
                    </div>
                    @endif
                    <!-- Show below message after test attempted for logged in user - end -->


                    <!-- Don't hide below code if not attempted 2 quiz - start-->
                    <div id="next-quiz-div" style="display:none;">
                        @if(!$hideNextQuizButton)
                                
                            @if(!empty($quizRoute))
                                <div class="text-center">
                                    <span>Or Take <a href="{{$quizRoute}}">{!! nl2br($quizTitleName) !!}  </a></span>
                                </div>
                            @endif

                        @endif
                    </div>
                    <!-- Don't hide below code if not attempted 2 quiz - end -->

              

                    <div id="result-screen" style="display:none;">
                        <h2 class="ttl mb-5"> {!! $quizData->final_text !!}</h2>

                        <div class="mb-3">
                            <img src="{{$quizData->image}}" alt="Quick Name" width="477" height="325" class="img-fluid">
                        </div>
                        <h5 id="redirectingMsg" style="display:none;">Directing to recommendation page</h5>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>
</div>
@endsection

@push('styles')
<style>
    .config-button-quiz{
        height: auto !important;
        line-height: 25px !important;
        padding: 10px 40px !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function(){
        // Hide the analysing screen after 2 seconds
        setTimeout(() => {
            $("#analyse-screen").fadeOut();
        }, 2000);

        var isRedirect = {!! $isRedirect !!}

        var redirectUrl = "{!! $quizData->redirect_url !!}";

        var showScore = {!! $showScore !!}

        var isMigraineMightQuiz = {!! $migraineMightQuiz !!}

        // Check if the quiz is for the migraine might then execute if code, or execute else code
        if(isMigraineMightQuiz == '1'){

            // If recommendation is screen is off then execute below code, or execute "else" code
            if(isRedirect == 0 && showScore == 1){

               
                setTimeout(() => {                
                    // Show the result div 
                    $("#score-screen").fadeIn();
                    
                    // Show the result div 
                    $("#below-title-screen").fadeIn();

                    // Score board
                    $(".score-borad").fadeIn();
                    // Quiz message description
                    $("#quiz-message").fadeIn();

                    // Show next quiz title & route
                    $("#next-quiz-div").fadeIn();
                }, 2200);


            }else{

                /**
                 * If the recommendation screen check is on then show the message and redirect to given url
                 */
                if(isRedirect == 1 && showScore == 0){
                    // Hide the analysing screen after 3 seconds
                    setTimeout(() => {
                        // Show the result div 
                        $("#result-screen").fadeIn();
                        // Display the redirecting message after 1 second
                        setTimeout(() => {                
                            $("#redirectingMsg").fadeIn();
                        }, 1000);

                        // After 3 seconds redirect to the given url
                        setTimeout(() => {                
                            // Redirect the page to given url
                            // window.location.href = redirectUrl
                        }, 5000);
                    }, 2500);
                
                }else{
                    // After 3 seconds redirect to the given url
                    setTimeout(() => {                
                        // Redirect the page to given url
                        // window.location.href = redirectUrl
                    }, 2000);
                }
                
            }
        }else{

            /**
             *  If quiz is not migraine might then check if recommendation screen is off then 
             * redirect to the url, else show the message and redirect
             */
            if(isRedirect == 0){
                // After 3 seconds redirect to the given url
                setTimeout(() => {                
                    // Redirect the page to given url
                    window.location.href = redirectUrl
                }, 1500);
            }else{
                // Hide the analysing screen after 3 seconds
                setTimeout(() => {
                    // Show the result div 
                    $("#result-screen").fadeIn();
                    // Display the redirecting message after 1 second
                    setTimeout(() => {                
                        $("#redirectingMsg").fadeIn();
                    }, 1000);

                    // After 3 seconds redirect to the given url
                    setTimeout(() => {                
                        // Redirect the page to given url
                        window.location.href = redirectUrl
                    }, 5000);
                }, 2500);
            }

        }



        
        
    });
</script>
@endpush
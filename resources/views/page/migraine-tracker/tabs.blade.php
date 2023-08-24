<?php
    $data = App\Helpers\Helpers::getQuizData();
    $quizScoreMidasTestMsg = $data['quizScoreMidasTestMsg'];
    $quizScoreHitSixTestMsg = $data['quizScoreHitSixTestMsg'];
    $midasTestRoute = $data['midasTestRoute'];
    $hit6TestRoute = $data['hit6TestRoute'];
    $midasTestScore = $data['quizScoreMidasTest'];
    $hit6TestScore = $data['quizScoreHitTest'];
    
?>

<!-- Migraine tracker tabs - start -->
<div class="row mt-3 mb-4">
    <div class="col-12 col-6 mb-3">
        <a class="btn-border btn-migraine p-2 " href="{{$midasTestRoute}}">
            <span> <img width="34" height="37" src="{{asset('images/midastest.png')}}" alt="midastest"></span> 
            <div class="btn-text">Start the {!! nl2br($data['midasTestQuizName']) !!}</div>
        </a>
    </div>
</div>
<div class="text-center">
    <span>Or Take <a href="{{$hit6TestRoute}}">{!! nl2br($data['hit6TestQuizName']) !!} </a></span>
</div>
<!-- Migraine tracker tabs - end -->

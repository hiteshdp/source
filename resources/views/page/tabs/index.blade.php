<!-- Migraine tracker tabs - start -->

<!-- <div class="quiz-title">
    <img src="{{asset('images/WellkasaLogo.png')}}" width="70" height="47" alt="Wellkasa" title="Wellkasa"> Migraine Tracker
</div> -->

<?php
    $data = App\Helpers\Helpers::getQuizData();
    $quizScoreMidasTestMsg = $data['quizScoreMidasTestMsg'];
    $quizScoreHitSixTestMsg = $data['quizScoreHitSixTestMsg'];
    $midasTestRoute = $data['midasTestRoute'];
    $hit6TestRoute = $data['hit6TestRoute'];
    $midasTestScore = $data['quizScoreMidasTest'];
    $hit6TestScore = $data['quizScoreHitTest'];
    
?>

<div class="quiz-flow pt-2">
<div class="row mt-3 mb-4">
    <div class="col-md-6 col-6">
        <a class="btn-border p-2 " href="{{$midasTestRoute}}">
            <span> <img width="34" height="37" src="{{asset('images/midastest.png')}}" alt="midastest"></span> 
            <div class="btn-text">MIDAS  @if(isset($quizScoreMidasTestMsg)) <span class="btn-text-small">  {{$midasTestScore}} {{$quizScoreMidasTestMsg}}</span> @endif</div>
        </a>
    </div>
    <div class="col-md-6 col-6">
        <a class="btn-border p-2" href="{{$hit6TestRoute}}">
            <span> <img width="36" height="38" src="{{asset('images/hittest.png')}}" alt="Symptom Tracker"></span>
            <div class="btn-text">Hit-6™ @if(isset($quizScoreHitSixTestMsg)) <span class="btn-text-small"> {{$hit6TestScore}} {{$quizScoreHitSixTestMsg}}</span> @endif</div>
        </a>
    </div>
</div>
</div>
<!-- Migraine tracker tabs - end -->
@push('styles')
<style type="text/css">
     .quiz-flow {
        max-width: 590px;
    margin: auto;
     }
    .quiz-flow .btn-border{
        font-size: 17px;
      

    }
</style>
@endpush
@extends('layout.default')
      
@section('meta-keywords', __(html_entity_decode($introScreenData->title)))
@section('meta-news-keywords', __(html_entity_decode($introScreenData->title)))
@section('meta-description', __(html_entity_decode($introScreenData->title)))
@section('title', __(html_entity_decode($introScreenData->title)))

@section('content')
<div style="background: linear-gradient(180deg, {{$quizConfiguration->background_color_screen}} 0%, rgba(231, 230, 230, 0) 100%);">
<div class="container750 quiz-flow  bg-transparent mt-0">
    <div class="quiz-wrapper">

        <div class="cabinet-accordion cabinet-header-new">
            
            

            <!--Quiz intro-->
            <div class="quiz-intro">
                <div class="container text-center">
                    <h1 class="ttl mb-2" style="color:{{$quizConfiguration->question_text_color}}">{!! nl2br($introScreenData->title) !!}</h1>
                    
                    <div class="mb-3 position-relative">
                        <!-- Show the first image if there - start -->
                        @if(!empty($introScreenData->first_image))
                            <img src="{{$introScreenData->first_image}}" alt="Quick Name" width="310" class="img-fluid quiz-intro-first-image {{empty($introScreenData->second_image) ? 'position-static' : ''}}">
                        @endif                
                        <!-- Show the first image if there - end -->

                        <!-- Show the second image if there - start -->
                        @if(!empty($introScreenData->second_image))
                            <img src="{{$introScreenData->second_image}}" alt="Quick Name" width="280" class="img-fluid quiz-intro-second-image {{empty($introScreenData->first_image) ? 'position-static' : ''}}">
                        @endif
                        <!-- Show the second image if there - end -->
                    </div>

                    <p class="mb-4 mt-5 pt-2 quiz-text-alignment" style="color:{{$quizConfiguration->question_text_color}}">{!! nl2br($introScreenData->description) !!}</p>
                    <div class="mt-4 mb-3">
                        <a href="{{route('quiz',['condition_name'=>$introScreenData->condition_name,'quiz_name'=>$introScreenData->quiz_name,'id'=>$introScreenData->question_id])}}" class="btn btn-primary btn-blue" style="background-color: {{$quizConfiguration->button_color}}; color: {{$quizConfiguration->button_text_color}};">{!! nl2br($introScreenData->button_label) !!}</a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
</div>

@endsection
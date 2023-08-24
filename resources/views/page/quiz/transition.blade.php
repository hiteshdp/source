@extends('layout.default')

@section('content')

<div style="background: linear-gradient(180deg, {{$quizConfiguration->background_color_screen}} 0%, rgba(231, 230, 230, 0) 100%);">
    <div class="container750 quiz-flow  bg-transparent mt-0">
        <div class="quiz-wrapper">

            <div class="cabinet-accordion cabinet-header-new">

                <div class="quiz-intro">
                    <div class="container text-center">
                        
                        <h1 class="ttl mb-2" style="color:{{$quizConfiguration->question_text_color}}">{!! nl2br($quizTransitionData->title) !!}</h1>
                        
                        <div class="mb-3 position-relative">
                            <!-- Show the image if there - start -->
                            @if(!empty($quizTransitionData->image))
                                <img src="{{$quizTransitionData->image}}" alt="Quick Name" width="280" class="img-fluid">
                            @endif                
                            <!-- Show the image if there - end -->
                        </div>

                        <p class="mb-4 mt-5 pt-2 quiz-text-alignment" style="color:{{$quizConfiguration->question_text_color}}">{!! nl2br($quizTransitionData->description) !!}</p>

                        <div class="mt-4 mb-3">
                            <a href="{{route('quiz',['condition_name'=>$quizTransitionData->condition_name,'quiz_name'=>$quizTransitionData->quiz_name,'id'=>$quizTransitionData->next_question_id])}}" class="btn btn-primary btn-blue" id="skipButton" style="background-color: {{$quizConfiguration->button_color}}; color: {{$quizConfiguration->button_text_color}};">{!! nl2br($quizTransitionData->button_label) !!}</a>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>

    $(document).ready(function(){
        // Get the delay value
        var delay = {!! $quizTransitionData->delay !!}
        // Check if delay value is not empty then execute the timeout functionality
        if(delay != ''){
            // Redirect the page after defined delay variable value
            setTimeout(() => {
               window.location.href = $("#skipButton").attr('href');
            }, delay);
        }
    });

</script>
@endpush
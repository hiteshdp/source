@extends('layout.default')
      
@section('meta-keywords', __(html_entity_decode($introScreenData->title)))
@section('meta-news-keywords', __(html_entity_decode($introScreenData->title)))
@section('meta-description', __(html_entity_decode($introScreenData->title)))
@section('title', __(html_entity_decode($introScreenData->title)))
@section('content')

<div style="background: linear-gradient(180deg, {{$quizConfiguration->background_color_screen}} 0%, rgba(231, 230, 230, 0) 100%);">
<div class="container750 quiz-flow mt-0  bg-transparent">
    <div class="cabinet-accordion cabinet-header-new">

        

        <div class="quiz-wrapper" >
            <div class="quiz-step">
                <div class="container text-center">

                <div class="quiz-progress">
                    <div class="progress-wrapper">
                        <progress max="100" class="progress is-darkgrey progress-bar-striped" value="{{$currentQuestionNumber*10}}">{{$currentQuestionNumber*10}}</progress>
                        <div class="progress-value">
                            <div class="tip-wrapper" style="left: {{$currentQuestionNumber*10}}%;">
                                <div class="b-tooltip is-primary is-top is-medium is-always">
                                    <div class="tooltip-content">{{$currentQuestionNumber*10}}% Complete</div>
                                    <div class="tooltip-trigger"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                    <!-- Show steps for the questions - start -->
                    <!-- <div class="progress mb-3 mt-3">
                        <div class="quiz-progress">
                            <div class="progress-wrapper">
                                <progress max="100" class="progress is-darkgrey" value="{{$currentQuestionNumber*10}}">{{$currentQuestionNumber*10}}</progress>
                                <div class="progress-value">
                                    <div class="tip-wrapper" style="left: {{$currentQuestionNumber*10}}%;">
                                        <div class="b-tooltip is-primary is-top is-medium is-always">
                                            <div class="tooltip-content">{{$currentQuestionNumber*10}}% Complete</div>
                                            <div class="tooltip-trigger"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <!-- <div class="progress-bar progress-bar-striped progress-bar-animated progress-bar-stack-color" role="progressbar" style="width: {{$currentQuestionNumber*10}}%" aria-valuenow="{{$currentQuestionNumber*10}}" aria-valuemin="0" aria-valuemax="100" data-toggle="tooltip" title="<span>{{$currentQuestionNumber*10}}% complete</span>"></div> -->
                    </div>
                    <!-- Show steps for the questions - end -->

                    <!-- Show the question image if exist - start -->
                    <div class="mb-3 position-relative text-center">
                        @if(!empty($quizData->image))
                            <img src="{{$quizData->image}}" alt="Quick Name" width="280" class="img-fluid quiz-intro-second-image position-static">
                        @endif
                    </div>
                    <!-- Show the question image if exist - end -->

                    <!-- Show question title - start -->
                    <h2 class="ttl mb-4 quiz-text-alignment" style="color:{{$quizConfiguration->question_text_color}}">{!! nl2br($quizData->question) !!}</h2>
                    <!-- Show question title - end -->


                    {!! Form::open(['url' => 'selected-option-quiz', 'class'=>'login-signup-form', 'id'=>'quiz', 'method'=>'post']) !!}

                        <div class="row justify-content-center">
                            <!-- Check if the options are there for this question - start -->
                            @if(count($quizData->options) != 0)


                                <!-- Display option as radiobutton - start -->

                                @if($quizData->display_options_as == '1')

                                    <!-- When options exist then show with the radio buttons - start -->
                                    @foreach($quizData->options as $option)
                                        <div class="col-12 col-lg-6 mb-4">
                                            <label class="c-radio c-radio{{$option['id']}}" style="color:{{$quizConfiguration->answer_text_color}}">
                                                <input type="radio" id="option_id" name="option_id" value="{{$option['id']}}">
                                                <span class="radio-box"></span>
                                                {!! nl2br($option['option_text']) !!}
                                            </label>
                                        </div>
                                    @endforeach
                                    <!-- When options exist then show with the radio buttons - end -->
                                

                                <!-- Display option as radiobutton - end -->

                                <!-- Display option as dropdown - start -->
                                @elseif($quizData->display_options_as == '2')
                                    <div class="form-group col-12 col-md-6 gradient-dropdown mb-3">
                                        <select name="option_id" id="option_id" class="form-control select2">
                                            <option value="" disabled selected></option>
                                            @foreach($quizData->options as $option)
                                            <option value="{{$option['id']}}">
                                                {!! nl2br($option['option_text']) !!}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <!-- Display option as dropdown - end -->

                            @endif
                            <!-- Check if the options are there for this question - end -->
                        

                            <input type="hidden" name="question_id" value="{{$quizData->id}}">
                            <input type="hidden" name="intro_screen_id" value="{{$quizData->intro_screen_id}}">
                            <input type="hidden" name="transition_id" value="{{$quizData->transition_id}}">
                            

                            <!-- Display error when option not selected - start -->
                            <div class="col-12 mt-2 mb-2">
                                <label id="option_id-error" class="error" for="option_id" style="display:none;"></label>
                            </div>
                            <!-- Display error when option not selected - end -->


                            <!-- Hide the continue button for quiz - start -->
                            @if(1!=1)
                                <div class="col-12 mt-4 mb-3 d-none text-center"><button type="submit" class="btn btn-primary btn-blue" style="background-color: {{$quizConfiguration->button_color}}; color: {{$quizConfiguration->button_text_color}};">Continue</button></div>
                            @endif
                            <!-- Hide the continue button for quiz - end -->

                            <!-- Check if there is the previous question id value exist - start -->
                            <p>
                                @if($currentQuestionNumber == 0)
                                <div class="col-12 mt-4 mb-3 text-center"><a href="{{ route('quiz/intro',['condition_name'=>$quizData->condition_name,'quiz_name'=>$quizData->quiz_name]) }}" class="btn btn-primary btn-blue" style="background-color: {{$quizConfiguration->button_color}}; color: {{$quizConfiguration->button_text_color}};">Back</a></div>
                                @else
                                <div class="col-12 mt-4 mb-3 text-center"> <a href="{{route('quiz',['condition_name'=>$quizData->condition_name,'quiz_name'=>$quizData->quiz_name,'id'=>$quizData->options[0]['previous_question_screen_id']])}}" class="btn btn-primary btn-blue" style="background-color: {{$quizConfiguration->button_color}}; color: {{$quizConfiguration->button_text_color}};">Back</a> </div>
                                @endif
                            </p>
                            <!-- Check if there is the previous question id value exist - end -->
                        </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('styles')

<!-- Added select 2 css for dosage & dosageType dropdown -->
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">

<style>
.error {
    color: #ca0000;
}

.select2-selection__placeholder{
    font-size:16px;
}


/** Tooltip css changes - start */
.quiz-progress {
    margin-bottom: 30px;
    margin-right: auto;
    margin-left: auto;
    width: 66%;
}

.quiz-progress .test {
    background-color: red
}

.quiz-progress .progress-wrapper {
    overflow: visible;
    position: relative;
}

.quiz-progress .progress-wrapper .progress-value {
    width: 100%
}

.quiz-progress .progress-wrapper .progress-value .tip-wrapper {
    position: absolute;
    top: -.7142857143rem;
    transition: all .5s
}

.quiz-progress .progress-wrapper progress[value] {
    height:1rem;
    width:100%;
}

.quiz-progress .progress-wrapper progress[value]::-webkit-progress-value {
    background-color: #7380B4 !important;

    background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);
    background-size: 1rem 1rem;
}

.quiz-progress  .progress, .progress-wrapper.is-not-native {
    -moz-appearance: none;
    -webkit-appearance: none;
    border: 0;
    display: block;
    height: 1rem;
    overflow: hidden;
    padding: 0;
    width: 100%;
}

.quiz-progress .progress, .quiz-progress .progress-wrapper.is-not-native {
    background-color: #eaeaea;
    color: #b49b57
}

.quiz-progress .progress-wrapper.is-not-native::-webkit-progress-bar,.progress::-webkit-progress-bar {
    background-color: #eaeaea
}

.quiz-progress .progress-wrapper.is-not-native::-webkit-progress-value,.progress::-webkit-progress-value {
    background-color: #b49b57
}

.quiz-progress .progress-wrapper.is-not-native::-moz-progress-bar,.progress::-moz-progress-bar {
    background-color: #b49b57
}

.quiz-progress .progress-wrapper.is-not-native::-ms-fill,.progress::-ms-fill {
    border: 0
}

.quiz-progress .progress[value="100"]::-ms-fill,[value="100"].progress-wrapper.is-not-native::-ms-fill {
    background-color: #b49b57
}

.quiz-progress  .is-mini.progress-wrapper.is-not-native,.progress.is-mini {
    height: .3125rem
}

.quiz-progress .is-micro.progress-wrapper.is-not-native,.progress.is-micro {
    height: .125rem
}

.quiz-progress  .is-black.progress-wrapper.is-not-native,.progress.is-black {
    background-color: #555;
    color: #0a0a0a
}

.is-black.progress-wrapper.is-not-native::-webkit-progress-bar,.progress.is-black::-webkit-progress-bar {
    background-color: #555
}

.is-black.progress-wrapper.is-not-native::-webkit-progress-value,.progress.is-black::-webkit-progress-value {
    background-color: #0a0a0a
}

.is-black.progress-wrapper.is-not-native::-moz-progress-bar,.progress.is-black::-moz-progress-bar {
    background-color: #0a0a0a
}

.is-black.progress-wrapper.is-not-native::-ms-fill,.progress.is-black::-ms-fill {
    border: 0
}

.is-black[value="100"].progress-wrapper.is-not-native::-ms-fill,.progress.is-black[value="100"]::-ms-fill {
    background-color: #0a0a0a
}

.quiz-progress  .b-tooltip {
    position: relative;
    display: inline-flex;
}

.quiz-progress .b-tooltip .tooltip-content {
    width: auto;
    padding: .35rem .75rem;
    border-radius: 6px;
    font-size: .85rem;
    font-weight: 400;
    box-shadow: 0 1px 2px 1px rgba(0,1,0,.2);
    z-index: 38;
    white-space: nowrap;
    position: absolute;
}
.quiz-progress .b-tooltip.is-top .tooltip-content {
    top: auto;
    right: auto;
    bottom: calc(100% + 5px + 2px);
    left: 50%;
    transform: translateX(-50%);
}
.quiz-progress  .b-tooltip.is-link .tooltip-content, .quiz-progress .b-tooltip.is-primary .tooltip-content {
    background: #7b87b8;
    color: #fff;
}
.quiz-progress  .b-tooltip.is-always .tooltip-content, .quiz-progress  .b-tooltip.is-always .tooltip-content::before {
    opacity: 1;
    visibility: visible;
}
.quiz-progress  .b-tooltip .tooltip-content::before {
    position: absolute;
    content: "";
    pointer-events: none;
    z-index: 38;
}

.quiz-progress .b-tooltip.is-top .tooltip-content::before {
    top: 100%;
    right: auto;
    bottom: auto;
    left: 50%;
    transform: translateX(-50%);
    border-top: 5px solid #7b87b8;
    border-right: 5px solid transparent;
    border-left: 5px solid transparent;
}
.quiz-progress .b-tooltip.is-always .tooltip-content, .quiz-progress .b-tooltip.is-always .tooltip-content::before {
    opacity: 1;
    visibility: visible;
}
.quiz-progress .progress-wrapper .progress-value {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    font-size: calc(1rem/1.5);
    line-height: 1rem;
    font-weight: 500;
    color: rgba(0,0,0,.7);
    white-space: nowrap;
}
.quiz-progress .b-tooltip .tooltip-trigger, .quiz-progress .datepicker .dropdown, .quiz-progress .datepicker .dropdown-trigger {
    width: 100%;
}
/** Tooltip css changes - end */
</style>
@endpush

@push('scripts')

<!-- Added select 2 js for dosage & dosageType dropdown -->
<script src="{{ asset('js/select2.min.js') }}" defer></script>

<script>

    // Tooltip intialised with its properties
    $(document).ready(function(){


        //Select2 option for options dropdown
        $('.select2').select2({
            closeOnSelect: true,
            placeholder: "Please select an option",
            minimumResultsForSearch: Infinity,
        });

        // Remove extra whitespace below the dropdown options if the list is less than equals to 3 
        $('#option_id').on('select2:open', function (e) {
            setTimeout(() => {
                if($(".select2-results__options li").length <= 3){
                    $(".select2-results").addClass('no-data-select2');

                }else{
                    $(".select2-results").removeClass('no-data-select2');

                }
            }, 100);
        });

    
        // hide error when option is selected
        $('#option_id').on('change', function() {
            if ($('#option_id').val() != ""){
                $('#option_id-error').hide();
            }
        });

        // Add the active class on option selection
        $('input[type=radio][name=option_id]').change(function() {
            $(".c-radio").removeClass('c-radio-active');
            var selectedoptionid = $(this).val();
            $(".c-radio"+selectedoptionid).addClass('c-radio-active');
        });

        // Validate the form for option selection
        $('#quiz').validate({ 
            rules:{
                option_id : { 
                    required : true
                }
            },
            messages:{
                option_id : {
                    required : "Please select any one option."
                }
            },
            submitHandler: function(form) {
                form.submit();
                return false;
            }    
        });

        // On select option go to next quiz
        $('#option_id').on('change', function() {
            $('#quiz').submit();
        });
        // Check if radio button click of option
        $('input[name="option_id"]').on('change', function() {
            $('#quiz').submit();
        }); 
    });
</script>
@endpush
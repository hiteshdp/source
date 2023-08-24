<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helpers as Helper;
use Carbon\Carbon;
use DB;
use Auth;
use Validator;
use Crypt;
use Config;
use App\Models\QuizIntroScreen;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\QuizRecommendationScreen;
use App\Models\QuizConfiguration;
use App\Models\QuizTransition;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class QuizController extends Controller
{

    /*** 
     * Show the intro screen page
    */
    public function index(Request $request){
        try {
            Session::forget('isQuizStarted');
            Session::forget('queAnsData');
            Session::forget('intro_screen_id');
            Session::forget('is_migraine_quiz');

            $condition_name = '';
            $quiz_name = '';
            // Get the quiz questions by the intro id value
            if(!empty($request->condition_name) && !empty($request->quiz_name)){
                $condition_name = $request->condition_name;
                $quiz_name = $request->quiz_name;
            }
            // Get the data from the intro screen id
            $introScreenData = QuizIntroScreen::where('condition_name', $condition_name)->where('quiz_name', $quiz_name)->get()->first();
            // If the data is empty then show error message accordingly
            if (empty($introScreenData)) {
                
                return redirect()->back()->with('error', 'Quiz Not Found.');
            } else {
                $introScreenId = $introScreenData->id;
                // Store the session if current quiz is migraine might quiz
                if( in_array($introScreenId, Config::get('constants.MigraineQuizIds') )){
                    Session::put('is_migraine_quiz', '1');
                }
            }

            // Get the quiz configurations for current quiz
            $quizConfiguration = QuizConfiguration::introScreen($introScreenId);

            return view('page.quiz.quiz-intro',compact('introScreenData','quizConfiguration'));

        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
        
    }


    /*** 
     * Show the quiz page
    */
    public function quiz(Request $request){
        try{
            // Set the default current question number as 0
            $currentQuestionNumber = 0;
            // Set the default total number of question as 0
            $totalNumberOfQuestions = 0;
            // Get the question id from the request
            $questionId = $request->id;

            // Check if the question id exist in input, if not then show error page
            if(empty($questionId)){
                return redirect()->back()->with('error','Question id not found. Please try again');
            }

            // Get the question for the quiz by the question id value
            $quizData = QuizQuestion::select('quiz_question.id','question_text as question','image','option_ids','intro_screen_id','quiz_question.percent_progress','display_options_as','transition_id','condition_name','quiz_name')->join('quiz_intro_screen','quiz_question.intro_screen_id','=','quiz_intro_screen.id')->where('quiz_question.id', $questionId)->get()->first();


            if(!empty($quizData)){
                // Add the options value for current question in the quiz
                $options = QuizQuestionOption::select('id','option_text','previous_question_screen_id','score')->whereIn('id',explode(',',$quizData->option_ids))
                ->get()->toArray();
                $quizData['options'] = $options;

                // Get the current step of the question quiz
                $currentQuestionNumber = $quizData->percent_progress;

                // Get the quiz configurations for current quiz
                $quizConfiguration = QuizConfiguration::quizScreen($quizData->intro_screen_id);

                // Store the session if current quiz is migraine might quiz
                if( in_array($quizData->intro_screen_id, Config::get('constants.MigraineQuizIds') )){
                    Session::forget('is_migraine_quiz');
                    Session::put('is_migraine_quiz','1');
                }else{
                    Session::forget('is_migraine_quiz');
                    Session::put('is_migraine_quiz','0');

                }

                // Get the data from the intro screen id
                $introScreenData = QuizIntroScreen::where('id', $quizData->intro_screen_id)->get()->first();

                return view('page.quiz.quiz',compact('quizData','currentQuestionNumber','quizConfiguration','introScreenData'));
            
            }else{
                return redirect()->back()->with('error','Found no questions for this quiz.');
            }


        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }


    /*** 
     * Show the next question for quiz by selection of an option by user
    */
    public function selectedQuizOption(Request $request){
        try{
            
            /***
             * Validate the required field
             */
            $validator = Validator::make($request->all(), [
                'option_id' => 'required'
            ]);
            //validation failed
            if ($validator->fails()) 
            {
                return back()->with('error','Did not found any selection. Please any one option.');
            }
            
            // Get the selected option id value
            $optionId = $request->option_id;

            // Get the options for the question data
            $optionData = QuizQuestionOption::where('id',$optionId)->get()->first();
            // If the option data exist then display the data
            if(!empty($optionData)){

                // Add the input value from the attempted quiz data
                $finalQueAnsData['option_id'] = $request->option_id;
                $finalQueAnsData['question_id'] = $request->question_id;
                $finalQueAnsData['intro_screen_id'] = $request->intro_screen_id;
                $finalQueAnsData['score'] = $optionData->score;

                // Add the intro screen id in the session
                if(!Session::has('intro_screen_id'))
                {
                    Session::put('intro_screen_id',$request->intro_screen_id);
                }

                // Check if the quiz started session has value, then execute below code
                if(!Session::has('isQuizStarted'))
                {
                    // add the attempted quiz data in the queAnsData session
                    Session::put('queAnsData',$finalQueAnsData);
                    // Store the isQuizStarted session value to 1 to indicate quiz has been started
                    Session::put('isQuizStarted','1');
                }
                else
                {
                    // Get queAnsData session data
                    $temp = Session::get('queAnsData');
                    // Push the session data into the array
                    array_push($temp,$finalQueAnsData);
                    // Store the quiz data in the session value
                    Session::put('queAnsData',$temp);
                }

                // Store the session if current quiz is migraine might quiz
                if( in_array($request->intro_screen_id, Config::get('constants.MigraineQuizIds') )){
                    Session::forget('is_migraine_quiz');
                    Session::put('is_migraine_quiz','1');
                }else{
                    Session::forget('is_migraine_quiz');
                    Session::put('is_migraine_quiz','0');
                }

                // If the option selected is the last question then go to recommendation screen
                if($optionData['is_last_question'] == 1){
                    // Store the attempted quiz data in queAnsData session
                    $finalQueAnsRes = Session::get('queAnsData');

                    // Extract the quiz attempted data to particular variables
                    $firstOptId = $finalQueAnsRes['option_id'];
                    $firstQueId = $finalQueAnsRes['question_id'];
                    $firstIntoScreenId = $finalQueAnsRes['intro_screen_id'];
                    $firstScore = $finalQueAnsRes['score'];

                    // Delete the key value form the attempted quiz data array
                    unset($finalQueAnsRes['option_id']);
                    unset($finalQueAnsRes['question_id']);
                    unset($finalQueAnsRes['intro_screen_id']);
                    unset($finalQueAnsRes['score']);

                    // Store the data in the array format
                    $firstOptArr[] = array('option_id'=>$firstOptId,'question_id'=>$firstQueId,'intro_screen_id'=>$firstIntoScreenId,'score'=>$firstScore);

                    // Merge the array of the last stored quiz data and attempted quiz array data
                    $finalQueAnsRes = array_merge($firstOptArr,$finalQueAnsRes);
                    // Store the questions attempt in the array
                    $result = array();
                    foreach ($finalQueAnsRes as $key => $finalQueAnsResVal) {
                        $result[] = $finalQueAnsRes[$key]['question_id'];
                    }
                    // Reverse the array data
                    $result = array_reverse($result,true);
                    // Arrange the array in unique format by removing the first attempt and maintain the second attempt records
                    $uniqueArraykey = array_unique($result);

                    // Store the final result data
                    $finalResult = [];

                    // Set default score value to 0
                    $score=0;
                    // Loop through each attempted quiz to sum the scores
                    foreach($finalQueAnsRes as $fk=>$fv)
                    {
                        // Removed duplicate questions attempts from the array
                        if(in_array($fk,array_keys($uniqueArraykey))){
                            // Add the array data which has the unique attempt
                            $finalResult[] = $finalQueAnsRes[$fk];
                            // Add the score values
                            $score += $fv['score'];
                        }
                    } 

                    // Get the intro screen id value from the session data
                    $intoScreenId = $finalResult[0]['intro_screen_id'];
                    // Store the attempted quiz data in json format
                    $finalQueAnsRes = json_encode($finalResult);

                    $userId = null;
                    if(Auth::check() == '1'){
                        // Fetch the user id for the current logged in user
                        $userId = Auth::user()->id;
                    }

                    // Generate random string of 8 characters
                    $unique_id = Str::random(8);
                    // Store the data for the quiz in the quiz result table
                    DB::table('quiz_result')->insert([
                        'intro_screen_id' => $intoScreenId,
                        'unique_id' => $unique_id,
                        'user_id' => $userId,
                        'response_json' =>$finalQueAnsRes,
                        'score'=>$score,
                        'created_at' => Carbon::now()
                    ]);
                    // Destory the session for the quiz started check value
                    Session::forget('isQuizStarted');
                    // Destory the session for the attempted quiz data
                    Session::forget('queAnsData');
                    // Destory the session for the next question in transition screen
                    Session::forget('next_question_id');

                    // Get the condition name and quiz name by the current quiz intro screen id
                    $quizIntroScreenData = QuizIntroScreen::where('id',$intoScreenId)->get()->first();
                    if(!empty($quizIntroScreenData)){
                        $condition_name = $quizIntroScreenData->condition_name;
                        $quiz_name = $quizIntroScreenData->quiz_name;
                        // Redirect to the recommendation screen by the id and the unique id based on the current attempted quiz for the score display
                        return redirect()->route('recommendation-screen',['condition_name'=>$condition_name,'quiz_name'=>$quiz_name,'id'=>$optionData['next_question_screen_id']])->with('unique_id',$unique_id);
                    }else{
                        return redirect()->back()->with('error','Quiz name not found. Please try again.');
                    }

                    
                }else{

                    //Get attempted Quiz details
                    $intro_screen_quiz_data = QuizIntroScreen::where('id',$request->intro_screen_id)->get()->first();

                   
                    // Goes to next question route
                    $nextQuestionId = $optionData['next_question_screen_id'];

                    // Check if transition screen exist for current selected quiz
                    if(!empty($request->transition_id)){
                        
                        // Pass the current question id 
                        $question_id = $request->question_id;
                        // Pass the intro screen id 
                        $intro_screen_id = $request->intro_screen_id;
                        // Redirect to the transition screen with the ids
                        Session::put('next_question_id',$nextQuestionId);
                        return redirect()->route('quiz-information',['condition_name'=>$intro_screen_quiz_data->condition_name,'quiz_name'=>$intro_screen_quiz_data->quiz_name,'id'=>$request->transition_id]);

                    }else{
                        // Redirect to the quiz screen with next question id
                        return redirect()->route('quiz',['condition_name'=>$intro_screen_quiz_data->condition_name,'quiz_name'=>$intro_screen_quiz_data->quiz_name,'id'=>$nextQuestionId]);
                    }
                }
            }else{
                return redirect()->back()->with('error','Next question not found.');
            }

        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }

    /*** 
     * Show the transition screen based on the question selection
    */
    public function transitionScreen(Request $request){
        try {
            $question_id = $request->id;
            // Get the data from the transition table based on the question id
            $quizTransitionData = QuizTransition::getTransitionData($question_id);
            if(!empty($quizTransitionData)){
                // Check session for the next_question_id, if found then assign or else redirect back to previous attempted question
                if(Session::has('next_question_id')){
                    $quizTransitionData->next_question_id = Session::get('next_question_id');
                }else{
                    return redirect()->back()->with('Next question not found. Please attempt again.');
                }
            }
            // Get the intro screen id from the session to get the current quiz configuration
            $intro_screen_id = Session::get('intro_screen_id');
            // Pass the quiz configuration details
            $quizConfiguration = QuizConfiguration::introScreen($intro_screen_id);

            // Redirect to the quiz information screen
            return view('page.quiz.transition',compact('quizTransitionData','quizConfiguration'));

        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }

}

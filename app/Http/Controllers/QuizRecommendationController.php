<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helpers as Helper;
use Carbon\Carbon;
use DB;
use Auth;
use Validator;
use Crypt;
use App\Models\QuizIntroScreen;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\QuizRecommendationScreen;
use App\Models\QuizResult;
use App\Models\QuizScore;
use App\Models\QuizConfiguration;
use Illuminate\Support\Facades\Session;
use Config;

class QuizRecommendationController extends Controller
{
    /*** 
     * Show the recommendation screen with data
    */
    public function index(Request $request){
        try {

            // Get the recommendation screen id
            $recommendation_screen_id = $request->route('id');
            // Check if the id is not empty, then execute below code
            if(!empty($recommendation_screen_id)){
                // Get the data from the given decrypted id from quiz recommendation screen
                $quizData = QuizRecommendationScreen::where('quiz_recommendation_screen.id', $recommendation_screen_id)
                ->join('quiz_intro_screen','quiz_recommendation_screen.intro_screen_id','=','quiz_intro_screen.id')->get()->first();
                // If data is not empty then redirect to the recommendation screen with the data
                if(!empty($quizData)){

                    //Store attempted Quiz title name
                    $attemptedQuiz = QuizIntroScreen::where('id',Session::get('intro_screen_id'))->get()->first();
                    $attemptedQuizTitle = $attemptedQuiz->title;
                    // Get the quiz configurations for the current quiz
                    $quizConfigurations = QuizConfiguration::quizScreen(Session::get('intro_screen_id'));
                    if(!empty($quizConfigurations->thanks_message)){
                        $thanksMessage = $quizConfigurations->thanks_message;
                    }else{
                        $thanksMessage = "Thank you for taking the ".$attemptedQuizTitle;
                    }

                    $quizScoreMidasTestMsg = "";
                    $quizScoreHitSixTestMsg = "";

                    // Get the current attempted quiz score from the unique id
                    $getQuizAttemptUniqueId = Session::get('unique_id');
                    
                    // Store the attempted quiz in the array session
                    Session::push('unique_id_value',$getQuizAttemptUniqueId);
                    
                    // Check if the session value exist for score then set the message for the score, else show empty message                
                    if(!empty($getQuizAttemptUniqueId)){
                        // Get the score from the table by the unique id value
                        $quizResultData = QuizResult::select('score','created_at')->where('unique_id', $getQuizAttemptUniqueId)->first();
                        $quizScore = $quizResultData->score;
                    }else{
                        $quizScore = "";  
                    }

                    // Store default empty quiz title name of next quiz
                    $quizTitleText = '';
                    $quizTitleName = '';
                    // Store the route for the next quiz
                    $quizRoute = '';
                    // If the intro screen id is stored in session then execute below code
                    if(Session::has('intro_screen_id')){

                        //Get the midas & hit6 test name
                        $quizName = Helper::getMidasAndHitTestName();

                        // Get the intro screen id from the session
                        $intro_screen_id = Session::get('intro_screen_id');
                        
                        // If quiz test is of MidasTest then show Hit-6 Test title, else show MidasTest title
                        if($intro_screen_id == Config::get('constants.MidasTestId')){
                            $quizTitleText = 'Hit-6 Test';
                            $quizTitleName = $quizName['hitTestName'];
                            $quizRoute = QuizIntroScreen::getHitSixQuizURL();
                            
                        }else{
                            $quizTitleText = 'MIDAS Test';
                            $quizTitleName = $quizName['midasTestName'];
                            $quizRoute = QuizIntroScreen::getMidasTestQuizURL();
                        }
                    }else{
                        return redirect()->route('migrainemight');
                    }

                    // Check if current session has both attempted quiz then hide the next quiz button
                    $hideNextQuizButton = Helper::hideNextQuizButton();
                    
                    // Get the action check from current intro screen id
                    $actionData = QuizIntroScreen::getActionCheck($intro_screen_id);
                    // Store the check of to display recommendation screen or not
                    $isRedirect = !empty($actionData) ? $actionData['show_recco_screen'] : '0';
                    // Store the check of to display score or not
                    $showScore = !empty($actionData) ? $actionData['show_score'] : '0';

                    // Display the score based message for the quiz attempted
                    $quizScoreMsg =  QuizScore::showScoreMessage($intro_screen_id, $quizScore);

                    //Get the quiz score data based on quiz id
                    $quizDataRes = QuizScore::where('quiz_id',$intro_screen_id)->get()->toArray();

                    // Get the score range data
                    if(!empty($quizDataRes))
                    {
                        foreach($quizDataRes as $qk=>$qv)
                        {
                            if ( in_array($quizScore, range($qv['score_range_low'],$qv['score_range_high'])) ) {
                                $quizDataRes[$qk]['activeIconClass'] = 'score-active';
                                break;
                            }
                        }
                    }

                    $migraineMightQuiz = '0';
                    // Check if attempted quiz is migraine might then update the session value
                    if(in_array($intro_screen_id,Config::get('constants.MigraineQuizIds'))){
                        $migraineMightQuiz = '1';            
                        Session::forget('is_migraine_quiz');
                        Session::put('is_migraine_quiz','1');
                    }else{
                        Session::forget('is_migraine_quiz');
                        Session::put('is_migraine_quiz','0');
                    }

                    
                                        
                    // Redirect to quiz recommendation screen with the quiz data, quiz score message and show score
                    return view('page.quiz.recommendation-screen',compact('quizData','quizScore','quizTitleText','quizTitleName','showScore','quizScoreMsg','isRedirect','quizRoute','hideNextQuizButton','quizDataRes','attemptedQuizTitle','quizConfigurations','thanksMessage','migraineMightQuiz'));
                }else{
                    // Else redirect back to current screen
                    return redirect()->back()->with('error','Did not found the quiz data');
                }

            }else{
                // Else redirect back to current screen when id value is not found
                return redirect()->back()->with('error','Did not found the quiz data');
            }
            
        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
        
    }
}

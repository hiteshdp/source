<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helpers;
use Carbon\Carbon;
use DB;
use Auth;
use Validator;
use Crypt;
use App\Models\Provider;
use App\Models\User;
use App\Models\Event;
use PDF;
use Illuminate\Support\Facades\Session;

class ProviderController extends Controller
{

    /*** 
     * Show the paitents listing linked for current logged in doctor/provider
    */
    public function index(Request $request){
        try {
            
            // get logged in user id
            $userId = Auth::user()->id;

            // get logged in provider user details            
            $providerData = Provider::fetchById($userId);

            // get the associated users of logged in user's listing 
            $patientsList = Provider::getAssociatedPatientsList($userId);

            return view('page.provider.index',compact('patientsList','providerData'));

        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }


    /**
     * This function is to download the trend chart for given user id
     */
    public function downloadReport(Request $request,$userId){

        try {

            // decrypt the user id from the parameter request
            $userId = Crypt::decrypt($userId);

            // get the user details by the user id
            $userDetails = User::where('id',$userId)->get()->first();
            if(empty($userDetails)){
                return redirect()->back()->with('error','Patient details not found. Please try again.');
            }

            // Add created by provider name in report name
            $providerName = Auth::user()->getUserName();

            // get user name
            $userName = $userDetails->name." ".$userDetails->last_name;

            // Get First Name of given user id
            $userFirstName = $userDetails->name;

            //No.of days of difference to get the report
            $noOfDays = '30';

            // Get the previous logged event dates of current user
            $eventDates = Event::select(DB::raw('DATE_FORMAT(eventDate, "%m/%d/%Y") AS eventDate'),'id')->where('userId',$userId);
            $eventDates = $eventDates->whereNull('profileMemberId');
            $eventDates = $eventDates->whereNull('deleted_at')->orderBy('event.id','DESC')->get()->toArray();
            
            // Get the first 30 days previous from current date trend chart data for the existing logged in user or it's profile member & it's number of days
            $firstTrendChartData = Helpers::getTrendChartData($userId,null,$noOfDays,date('Y-m-d'));

            // Store the first date end from the trend chart to calculate next 30 days data
            $firstTrendChartDate = $firstTrendChartData['newPreviousDate'];
            // Store the severity names listing array from the get trend chart data
            $firstSeverityNames = $firstTrendChartData['severityNames'];
            // Store the symptom names listing array from the get trend chart data
            $firstSymptomNames = $firstTrendChartData['symptomNames'];
            // Store the last event dates array from the get trend chart data recorded by logged in user
            $firstLastDaysDateArray = $firstTrendChartData['lastDaysDateArray'];
            // Store the trend chart data array from the get trend chart data
            $firstTrendChartData = $firstTrendChartData['trendChartData'];

            // Get the second 30 days previous from first trend chart date to display trend chart data for the existing logged in user or it's profile member & it's number of days
            $secondTrendChartData = Helpers::getTrendChartData($userId,null,$noOfDays,$firstTrendChartDate);

            // Store the second date end from the trend chart to calculate next 30 days data
            $secondTrendChartDate = $secondTrendChartData['newPreviousDate'];
            // Store the severity names listing array from the get trend chart data
            $secondSeverityNames = $secondTrendChartData['severityNames'];
            // Store the symptom names listing array from the get trend chart data
            $secondSymptomNames = $secondTrendChartData['symptomNames'];
            // Store the last event dates array from the get trend chart data recorded by logged in user
            $secondLastDaysDateArray = $secondTrendChartData['lastDaysDateArray'];
            // Store the trend chart data array from the get trend chart data
            $secondTrendChartData = $secondTrendChartData['trendChartData'];

            // Get the third 30 days previous from second trend chart date to display trend chart data for the existing logged in user or it's profile member & it's number of days
            $thirdTrendChartData = Helpers::getTrendChartData($userId,null,$noOfDays,$secondTrendChartDate);

            // Store the severity names listing array from the get trend chart data
            $thirdSeverityNames = $thirdTrendChartData['severityNames'];
            // Store the symptom names listing array from the get trend chart data
            $thirdSymptomNames = $thirdTrendChartData['symptomNames'];
            // Store the last event dates array from the get trend chart data recorded by logged in user
            $thirdLastDaysDateArray = $thirdTrendChartData['lastDaysDateArray'];
            // Store the trend chart data array from the get trend chart data
            $thirdTrendChartData = $thirdTrendChartData['trendChartData'];

            // Get the color codes by the highest severity with the event date & profile member id in array
            $eventDates = Helpers::getHighlightColorEventDates($eventDates,null);

            // Get current date of file creation
            $createdOnDate = date("d M Y, H:i A");

            // Add title header name
            $titleHeader = 'Gaining Power over Migraines';

            // Update the title name
            $titleName = 'Migraine Tracker';

            $profileMemberId = null;

            view()->share(compact('userFirstName','providerName','titleHeader','titleName','userName','createdOnDate','profileMemberId','eventDates','firstSeverityNames','secondSeverityNames','thirdSeverityNames','firstLastDaysDateArray','secondLastDaysDateArray','thirdLastDaysDateArray','firstTrendChartData','secondTrendChartData','thirdTrendChartData','firstSymptomNames','secondSymptomNames','thirdSymptomNames')); 

            $pdf = PDF::loadView('page.reports.trend-chart-pdf.index');

            // If the request is from the "Download Report" button then download the report
            return $pdf->download(str_replace(" ","_",$userName.' Trend Chart').'.pdf');

        } catch (Exception $e) {
            /* Something went wrong while executing code */
            $error_message = $e->getMessage();
            return redirect()->back()->with('error',$error_message);
        }
    }

}
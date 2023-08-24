<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsletterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

use App\Http\Middleware\SetLocale;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// social media login callback routes - start
Route::get('auth/social', 'Auth\LoginController@show')->name('social.login');
Route::get('oauth/{driver}', 'Auth\LoginController@redirectToProvider')->name('social.oauth');
Route::get('oauth/{driver}/callback', 'Auth\LoginController@handleProviderCallback')->name('social.callback');
// social media login callback routes - end

// resend code verification email
Route::post('resend-verify-email-code', 'Auth\AuthController@resendVerifyEmailCode')->name('resend-verify-email-code')->middleware('auth');

Route::group(['middleware' => ['auth','verified']], function () {
   
    Route::get('change-password', 'Auth\AuthController@index')->name('change.password');
    Route::post('change-password', 'Auth\AuthController@changePassword');
    

    // Gets cities by state id in profile page
    Route::post('get-cities-by-state','CountryStateCityController@getCity');
   

    // user role paitent/caregiver routes
    Route::group(['middleware' => ['checkuserrole']], function(){
    
        // user profile routes
        Route::get('my-profile', 'MyProfileController@index')->name('my-profile');
        Route::get('/profile', 'Auth\AuthController@viewProfile')->name('profile');
        Route::post('/update-profile', 'Auth\AuthController@updateProfile')->name('update-profile');
        Route::post('/update-profile-pic', 'Auth\AuthController@updateProfilePic')->name('update-profile-pic');

        //complete profile routes
        Route::get('/complete-profile', 'CompleteProfileController@index')->name('complete-profile')->middleware(['auth']);
        Route::post('update-complete-profile', 'CompleteProfileController@updateCompleteProfile')->middleware(['auth']);

    
        //Delete Account Routes - start
        Route::delete('delete-account', 'MyProfileController@deleteAccount')->name('delete-account');
        //Delete Account Routes - end
        

        // Symptom tracker related routes - start
        Route::get('event-selection/{profileMemberId?}','SymptomTrackerController@eventSelection')->name('event-selection');
        Route::post('symptom-session','SymptomTrackerController@symptomSession')->name('symptom-session');
        Route::get('symptom-tracker/{profileMemberId?}','SymptomTrackerController@symptomTracker')->name('symptom-tracker');
        Route::post('check-time-window-event','SymptomTrackerController@checkTimeWindowEvent')->name('check-time-window-event');
        Route::post('save-symptom-tracker','SymptomTrackerController@saveTracker')->name('save-symptom-tracker');
        Route::get('add-event-notes/{date}','SymptomTrackerController@displayEventNotePage')->name('add-event-notes');
        Route::post('save-event-notes','SymptomTrackerController@saveEventNote')->name('save-event-notes');
        Route::get('edit-event-notes/{id}','SymptomTrackerController@editEventNote')->name('edit-event-notes');
        Route::put('update-event-notes','SymptomTrackerController@updateEventNote')->name('update-event-notes');
        Route::delete('delete-event-note','SymptomTrackerController@deleteEventNote')->name('delete-event-note');
        Route::get('trend-chart-pdf/{profileMemberId?}',array('as'=>'trend-chart-pdf','uses'=>'SymptomTrackerController@saveTrendChartPDF'));
        Route::get('manage-symptom-list/{profileMemberId?}','SymptomTrackerController@manageSymptomList')->name('manage-symptom-list');
        Route::post('save-symptom','SymptomTrackerController@saveSymptom')->name('save-symptom');
        Route::put('update-symptom-status', 'SymptomTrackerController@changeSymptomStatus')->name('update-symptom-status');
        Route::post('send-suggested-symptom', 'SymptomTrackerController@sendSuggestedSymptom')->name('send-suggested-symptom');
        // Symptom tracker related routes - end
        
        
    });
    

    Route::get('/logout','Auth\LoginController@logout');
});

// Research page route
Route::get('research', function () {
    return view('page.research');
})->name('research');

// Migraine user sign up page route
Route::get('signup', 'Auth\RegisterController@migraineSignUpPage')->name('signup');


// Migraine User Routes - Start
Route::get('migrainemight', function () {
    return view('page.migraine-tracker.index');
})->name('migrainemight');

Route::get('redirect-to-migraine-tracker/{unique_id_value}','MigraineController@redirectToIndex')->name('redirect-to-migraine-tracker');

Route::group(['middleware' => ['auth','checkWellkabinetMigraineProviderUserRole']], function(){
    Route::get('add-medicine-symptom/{eventId}', 'AddMedicineController@index')->name('add-medicine-symptom');
    Route::get('search-medicine', 'AddMedicineController@searchMedicine')->name('search-medicine');
    Route::post('save-medicine-symptom','AddMedicineController@saveMedicineSymptom')->name('save-medicine-symptom');
    Route::delete('delete-medicine-symptom','AddMedicineController@deleteMedicineSymptom')->name('delete-medicine-symptom');
});
// Migraine User Routes - End

//Auth::routes();
Auth::routes(['verify' => true]);

/*** //default email verify page
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');
***/

//Overrided email verify display page to accept 6 digit code for verification
Route::get('/email/verify', function () {
    return view('auth.verify-email-code');
})->name('verification.notice')->middleware(['auth']);

//verify email 6 digit code
Route::post('verify-email-code','Auth\AuthController@verifyEmailCode')->name('verify-email-code')->middleware(['auth']);

//overrided custom login & register page
Route::get('/login', function () {
    return view('auth.signup_login');
})->name('login');

// about migraine might page
Route::get('about-migraine-might', function () {
    return view('page.about-migraine-might');
})->name('about-migraine-might');


Route::get('/', 'HomController@index')->name('hom');


// Footer url routes - start
Route::get('contact-us', 'ContactUsController@index')->name('contact-us');

Route::get('/privacy-policy', function () {
    return view('page.privacy-policy');
})->name('privacy-policy');

Route::get('/terms-conditions', function () {
    return view('page.terms-conditions');
})->name('terms-conditions');
// Footer url routes - end
<?php

use Illuminate\Http\Request;
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Api for single search condition/therapy in wordpress
Route::group(['prefix' => 'v1', 'namespace' => 'Api\v1'], function () {
	Route::get('autocomplete-search', 'SingleSearchController@autocompleteSearch')->name('autocomplete-search');
	Route::get('get-symptom-list', 'SingleSearchController@getSymptomsList')->name('get-symptom-list');
});
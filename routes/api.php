<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\screenController;
use App\Http\Controllers\API\videoController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('admin-file-uploads','App\Http\Controllers\FileUploadController@adminfileuploads')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('admin-file-delete','App\Http\Controllers\FileUploadController@adminfiledelete')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::post('get-meeting-log', 'App\Http\Controllers\MeetingLogController@getMeetingLogforAPI');
Route::post('meetingfeedback', 'App\Http\Controllers\MeetingLogController@meetingfeedback');
Route::post('meetingfeedbackremark', 'App\Http\Controllers\MeetingLogController@meetingfeedbackremark');
Route::post('pan_validation', 'App\Http\Controllers\pan_validationController@index');
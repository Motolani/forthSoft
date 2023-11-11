<?php

use App\Http\Controllers\IPController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ServicesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [LoginController::class, 'login']);

Route::group(['middleware' => ['AuthCheckApi']], function () {

    Route::post('/changePassword', [LoginController::class, 'changePassword']);
    Route::post('/changeApiKey', [LoginController::class, 'changeApiKey']);
    Route::post('/userDetails', [LoginController::class, 'getUserDetails']);

    Route::post('/whitelistIP', [IPController::class, 'whiteListIP']);
    Route::post('/deleteIP', [IPController::class, 'deleteWhitelistedIP']);
    Route::get('/viewIpList', [IPController::class, 'viewWhitelistedIP']);
    Route::post('/updateIpList', [IPController::class, 'updateWhiteListedIP']);


    Route::post('/services', [ServicesController::class, 'getServices']);
    Route::post('/keywords', [ServicesController::class, 'getKeyword']);
    Route::post('/getPricePoint', [ServicesController::class, 'getPricePoint']);



});
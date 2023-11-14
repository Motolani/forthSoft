<?php

use App\Http\Controllers\IPController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\SubscribersController;
use App\Models\Subscriber;
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
    Route::post('/pricePoint', [ServicesController::class, 'getPricePoint']);
    Route::post('/serviceCount', [ServicesController::class, 'countServices']);
    Route::post('/serviceChannels', [ServicesController::class, 'serviceChannels']);

    Route::post('/subscribers', [SubscribersController::class, 'getSubscribers']);
    Route::post('/subscribersCount', [SubscribersController::class, 'subscribersCount']);
    Route::post('/unsubscribers', [SubscribersController::class, 'getUnsubscribers']);
    Route::post('/unsubscribersCount', [SubscribersController::class, 'unsubscribersCount']);




});
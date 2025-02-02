<?php

use App\Http\Controllers\AppVersionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ChartItemController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\TelegramMessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'prefix' => 'auth',
], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

});

Route::middleware('auth:sanctum')->get('user/me', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('user/logout', [AuthController::class, 'logout']);
    Route::apiResources([
        'charts' => ChartController::class,
        'chart-item' => ChartItemController::class,
        'app-version' => AppVersionController::class,
        'telegram-message' => TelegramMessageController::class,
        'stations' => StationController::class
    ]);
});

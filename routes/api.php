<?php

use App\Http\Controllers\AppVersionController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ChartItemController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResources([
    'charts'=> ChartController::class,
    'chart-item' => ChartItemController::class,
    'app-version'=>AppVersionController::class
]);
Route::apiResource('telegram-message',TelegramMessageController::class);

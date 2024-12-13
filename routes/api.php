<?php

use App\Http\Controllers\Api\v1\BotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\SmmController;
use App\Http\Controllers\Api\v1\ProxyController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\SupportController;
use App\Http\Controllers\Api\v1\OrderController;

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

/**
 * Получение данных (Товары, категории, соц. сети)
 */
Route::get('getSocial', [SmmController::class, 'getSocial']);
Route::get('getCategories', [SmmController::class, 'getCategories']);
Route::get('getTypes', [SmmController::class, 'getTypes']);


Route::get('createOrder', [OrderController::class, 'createOrder'])->middleware('throttle_user_secret_key');
Route::get('getOrder', [OrderController::class, 'getOrder']);
Route::get('orders', [OrderController::class, 'orders']);

/**
 * Роуты API (пользователи)
 */
Route::get('setLanguage', [UserController::class, 'setLanguage'])->middleware('throttle_user_secret_key');
Route::get('getUser', [UserController::class, 'getUser']);

/**
 * Роуты API (боты)
 */
Route::get('ping', [BotController::class, 'ping']);
Route::get('create', [BotController::class, 'create']);
Route::get('error', [BotController::class, 'error']);
Route::get('get', [BotController::class, 'get']);
Route::post('update', [BotController::class, 'update']);
Route::get('delete', [BotController::class, 'delete']);
Route::get('getSettings', [BotController::class, 'getSettings']);



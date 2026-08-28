<?php

use App\Http\Controllers\Api\v1\BotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\SmmController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Получение данных (Товары, категории, соц. сети)
 */
Route::get('getSocial', [SmmController::class, 'getSocial'])->withoutMiddleware('throttle:api');
Route::get('getCategories', [SmmController::class, 'getCategories'])->withoutMiddleware('throttle:api');
Route::get('getTypes', [SmmController::class, 'getTypes'])->withoutMiddleware('throttle:api');


Route::get('createOrder', [OrderController::class, 'createOrder'])->middleware('throttle_user_secret_key');
Route::get('getOrder', [OrderController::class, 'getOrder']);
Route::get('orders', [OrderController::class, 'orders']);

/**
 * Роуты API (пользователи)
 */
Route::get('setLanguage', [UserController::class, 'setLanguage'])->middleware('throttle_user_secret_key');
Route::get('getUser', [UserController::class, 'getUser'])->withoutMiddleware('throttle:api');

/**
 * Роуты API (боты)
 */
Route::get('ping', [BotController::class, 'ping'])->withoutMiddleware('throttle:api');
Route::get('create', [BotController::class, 'create']);
Route::get('error', [BotController::class, 'error']);
Route::get('get', [BotController::class, 'get']);
Route::post('update', [BotController::class, 'update']);
Route::match(['get', 'post'], 'rotatePrivateKey', [BotController::class, 'rotatePrivateKey']);
Route::get('delete', [BotController::class, 'delete']);
Route::get('getSettings', [BotController::class, 'getSettings'])->withoutMiddleware('throttle:api');


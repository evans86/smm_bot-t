<?php

use App\Http\Controllers\Api\v1\BotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\CountryController;
use App\Http\Controllers\Api\v1\ProxyController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\ProductController;
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

Route::get('pingProxy', [CountryController::class, 'pingProxy']);

Route::get('getProxy', [ProxyController::class, 'getProxy']);
Route::get('getCountry', [CountryController::class, 'getCountry']);
Route::get('getCount', [ProxyController::class, 'getCount']);
Route::get('getPrice', [ProxyController::class, 'getPrice']);





/**
 * Роуты API (страны, операторы, сервисы), ресурсный подход
 */
Route::resources([
    'countries' => CountryController::class,
    'services' => ProductController::class,
]);

/**
 * Роуты API (пользователи)
 */
Route::get('setCountry', [CountryController::class, 'setCountry']);
Route::get('setService', [ProductController::class, 'setService']);
Route::get('setLanguage', [UserController::class, 'setLanguage']);
Route::get('getUser', [UserController::class, 'getUser']);
Route::get('balance', [UserController::class, 'balance']);

/**
 * Роуты API (боты)
 */
//Route::get('ping', [BotController::class, 'ping']);
Route::get('create', [BotController::class, 'create']);
Route::get('error', [BotController::class, 'error']);
Route::get('get', [BotController::class, 'get']);
Route::post('update', [BotController::class, 'update']);
Route::get('delete', [BotController::class, 'delete']);

/**
 * Роуты API (заказы (создание, получение, все))
 */
Route::get('createOrder', [OrderController::class, 'createOrder']);
Route::get('getOrder', [OrderController::class, 'getOrder']);
Route::get('orders', [OrderController::class, 'orders']);

/**
 * Роуты API (заказы (изменение статусов))
 */
Route::get('closeOrder', [OrderController::class, 'closeOrder']);
Route::get('reportOrderSms', [OrderController::class, 'reportOrderSms']);
Route::get('secondSms', [OrderController::class, 'secondSms']);
Route::get('confirmOrder', [OrderController::class, 'confirmOrder']);

/**
 * Роуты API (заказы (рабочие))
 */
Route::get('getActive', [OrderController::class, 'getActive']);
Route::get('getStatus', [OrderController::class, 'getStatus']);



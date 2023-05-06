<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

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

Route::get('/', [Controller::class, 'home'])->name('home');
//Route::get('/test', [Controller::class, 'test'])->name('home');

/**
 * Роуты для админки (страны, операторы, сервисы)
 */
Route::group(['namespace' => 'Activate', 'prefix' => 'activate'], function () {
    Route::get('countries', 'CountryController@index')->name('activate.countries.index')->middleware('auth');
    Route::get('countries/update', 'CountryController@update')->name('activate.countries.update')->middleware('auth');
    Route::get('countries/delete', 'CountryController@delete')->name('activate.countries.delete')->middleware('auth');
    Route::get('product', 'ProductController@index')->name('activate.product.index')->middleware('auth');
    Route::get('order', 'OrderController@index')->name('activate.order.index')->middleware('auth');
    Route::get('bot', 'BotController@index')->name('activate.bot.index')->middleware('auth');
});

/**
 * Роуты для админки (пользователи)
 */
Route::group(['namespace' => 'User', 'prefix' => ''], function () {
    Route::get('users', 'UserController@index')->name('users.index')->middleware('auth');
});

Auth::routes();


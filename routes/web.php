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

//Route::get('/', [Controller::class, 'home'])->name('home');
//Route::get('/test', [Controller::class, 'test'])->name('home');

/**
 * Роуты для админки (страны, операторы, сервисы)
 */
Route::group(['namespace' => 'Activate', 'prefix' => 'activate'], function () {
    Route::get('countries', 'CountryController@index')->name('activate.countries.index')->middleware('auth');
    Route::get('social', 'SocialController@index')->name('activate.social.index')->middleware('auth');
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

Route::get('/', 'HomeController@index')->name('home')->middleware('auth');

Route::group(['middleware' => 'auth'], function () {
		Route::get('icons', ['as' => 'pages.icons', 'uses' => 'PageController@icons']);
		Route::get('maps', ['as' => 'pages.maps', 'uses' => 'PageController@maps']);
		Route::get('notifications', ['as' => 'pages.notifications', 'uses' => 'PageController@notifications']);
		Route::get('rtl', ['as' => 'pages.rtl', 'uses' => 'PageController@rtl']);
		Route::get('tables', ['as' => 'pages.tables', 'uses' => 'PageController@tables']);
		Route::get('typography', ['as' => 'pages.typography', 'uses' => 'PageController@typography']);
		Route::get('upgrade', ['as' => 'pages.upgrade', 'uses' => 'PageController@upgrade']);
});

//Route::group(['middleware' => 'auth'], function () {
//	Route::resource('user', 'App\Http\Controllers\UserController', ['except' => ['show']]);
//	Route::get('profile', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\ProfileController@edit']);
//	Route::put('profile', ['as' => 'profile.update', 'uses' => 'App\Http\Controllers\ProfileController@update']);
//	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);
//});


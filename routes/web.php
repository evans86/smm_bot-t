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
 * Роуты для админки (боты, заказы, соу. сети)
 */
Route::group(['namespace' => 'Activate', 'prefix' => 'activate'], function () {
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
Route::get('show', 'ShowController@index')->name('show.index');

Route::group(['middleware' => 'auth'], function () {
		Route::get('icons', ['as' => 'pages.icons', 'uses' => 'PageController@icons']);
		Route::get('maps', ['as' => 'pages.maps', 'uses' => 'PageController@maps']);
		Route::get('notifications', ['as' => 'pages.notifications', 'uses' => 'PageController@notifications']);
		Route::get('rtl', ['as' => 'pages.rtl', 'uses' => 'PageController@rtl']);
		Route::get('tables', ['as' => 'pages.tables', 'uses' => 'PageController@tables']);
		Route::get('typography', ['as' => 'pages.typography', 'uses' => 'PageController@typography']);
		Route::get('upgrade', ['as' => 'pages.upgrade', 'uses' => 'PageController@upgrade']);
});


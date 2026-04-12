<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Доступ в админку: HTTP Basic (.env) → вход по БД (/login) → панель.
|
*/

Auth::routes(['register' => false]);

/**
 * Роуты админки (боты, заказы, соц. сети)
 */
Route::group(['namespace' => 'Activate', 'prefix' => 'activate', 'middleware' => 'auth'], function () {
    Route::get('social', 'SocialController@index')->name('activate.social.index');
    Route::get('order', 'OrderController@index')->name('activate.order.index');
    Route::get('bot', 'BotController@index')->name('activate.bot.index');
    Route::get('show', 'ShowController@index')->name('activate.show.index');
});

/**
 * Роуты админки (пользователи модуля)
 */
Route::group(['namespace' => 'User', 'prefix' => '', 'middleware' => 'auth'], function () {
    Route::get('users', 'UserController@index')->name('users.index');
});

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

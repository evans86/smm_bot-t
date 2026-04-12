<?php

use App\Http\Controllers\Admin\AdminEnvAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Панель: сначала вход по пользователю из БД (login), затем второй шаг —
| логин/пароль из .env (admin/login). Оба middleware: auth + admin.env.
|
*/

Auth::routes(['register' => false]);

Route::get('admin/login', [AdminEnvAuthController::class, 'showLoginForm'])
    ->name('admin.login')
    ->middleware(['auth', 'admin.env.guest']);
Route::post('admin/login', [AdminEnvAuthController::class, 'login'])
    ->name('admin.login.submit')
    ->middleware(['auth', 'admin.env.guest', 'throttle:10,1']);
Route::post('admin/logout', [AdminEnvAuthController::class, 'logout'])
    ->name('admin.logout')
    ->middleware(['auth', 'admin.env']);

/**
 * Роуты админки (боты, заказы, соц. сети)
 */
Route::group(['namespace' => 'Activate', 'prefix' => 'activate', 'middleware' => ['auth', 'admin.env']], function () {
    Route::get('social', 'SocialController@index')->name('activate.social.index');
    Route::get('order', 'OrderController@index')->name('activate.order.index');
    Route::get('bot', 'BotController@index')->name('activate.bot.index');
    Route::get('show', 'ShowController@index')->name('activate.show.index');
});

/**
 * Роуты админки (пользователи модуля)
 */
Route::group(['namespace' => 'User', 'prefix' => '', 'middleware' => ['auth', 'admin.env']], function () {
    Route::get('users', 'UserController@index')->name('users.index');
});

Route::get('/', 'HomeController@index')->name('home')->middleware(['auth', 'admin.env']);

Route::group(['middleware' => ['auth', 'admin.env']], function () {
    Route::get('icons', ['as' => 'pages.icons', 'uses' => 'PageController@icons']);
    Route::get('maps', ['as' => 'pages.maps', 'uses' => 'PageController@maps']);
    Route::get('notifications', ['as' => 'pages.notifications', 'uses' => 'PageController@notifications']);
    Route::get('rtl', ['as' => 'pages.rtl', 'uses' => 'PageController@rtl']);
    Route::get('tables', ['as' => 'pages.tables', 'uses' => 'PageController@tables']);
    Route::get('typography', ['as' => 'pages.typography', 'uses' => 'PageController@typography']);
    Route::get('upgrade', ['as' => 'pages.upgrade', 'uses' => 'PageController@upgrade']);
});

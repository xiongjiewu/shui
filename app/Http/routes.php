<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ['uses' => 'HomeController@home']);
Route::any('/login', ['uses' => 'LoginController@login']);
Route::any('/register', ['uses' => 'RegisterController@register']);
Route::any('/activeDetail', ['uses' => 'ActivityController@activeDetail']);
Route::any('/activeList', ['uses' => 'ActivityController@activeList']);
Route::any('/newReport', ['uses' => 'UserController@report']);
Route::post('/newHead', ['uses' => 'UserController@newHead']);
Route::any('/newPassword', ['uses' => 'UserController@newPassword']);
Route::any('/bagSend', ['uses' => 'UserController@bagSend']);
Route::any('/bagSend', ['uses' => 'UserController@bagGet']);
Route::any('/bagList', ['uses' => 'UserController@bagList']);
Route::any('/search', ['uses' => 'UserController@search']);
Route::any('/bankOrder', ['uses' => 'UserController@bankOrder']);
Route::any('/bankSure', ['uses' => 'UserController@bankSure']);
Route::any('/mapDetail', ['uses' => 'WaterController@mapDetail']);
Route::any('/mapList', ['uses' => 'WaterController@mapList']);
Route::any('/activeFocusCancel', ['uses' => 'UserController@activeFocusCancel']);
Route::any('/activeFocus', ['uses' => 'UserController@activeFocus']);

//管理后台
Route::get('admin/login', ['as' => 'admin::login', 'uses' => 'Admin\LoginController@login']);
Route::post('admin/login', ['as' => 'admin::login::action', 'uses' => 'Admin\LoginController@loginAction']);
Route::group(['as' => 'admin::', 'prefix' => 'admin', 'middleware' => 'admin.check'], function () {
    Route::get('/', ['as' => 'home', 'uses' => 'Admin\AdminController@home']);
});
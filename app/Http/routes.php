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
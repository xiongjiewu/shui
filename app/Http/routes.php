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


//反馈
Route::any('/newHead', ['uses' => 'UserController@report']);
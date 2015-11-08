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

//用户
Route::get('/', ['uses' => 'HomeController@home']);
Route::any('/login', ['uses' => 'LoginController@login']);
Route::any('/verify', ['uses' => 'RegisterController@verify']);
Route::any('/register', ['uses' => 'RegisterController@userRegister']);
Route::any('/otherRegister', ['uses' => 'RegisterController@otherRegister']);
Route::any('/activeDetail', ['uses' => 'ActivityController@activeDetail']);
Route::any('/userDonations', ['uses' => 'UserController@userDonations']);
Route::any('/activeList', ['uses' => 'ActivityController@activeList']);
Route::any('/activeDonations', ['uses' => 'ActivityController@activeDonations']);
Route::any('/newReport', ['uses' => 'UserController@report']);
Route::post('/newHead', ['uses' => 'UserController@newHead']);
Route::any('/newPassword', ['uses' => 'UserController@newPassword']);
Route::any('/bagSend', ['uses' => 'UserController@bagSend']);
Route::any('/bagGet', ['uses' => 'UserController@bagGet']);
Route::any('/bagList', ['uses' => 'UserController@bagList']);
Route::any('/search', ['uses' => 'UserController@search']);
Route::any('/bankOrder', ['uses' => 'UserController@bankOrder']);
Route::any('/bankSure', ['uses' => 'UserController@bankSure']);
Route::any('/bankInfo', ['uses' => 'UserController@bankInfo']);
Route::any('/mapBag', ['uses' => 'WaterController@mapBag']);
Route::any('/mapDetail', ['uses' => 'WaterController@mapDetail']);
Route::any('/mapList', ['uses' => 'WaterController@mapList']);
Route::any('/activeFocusCancel', ['uses' => 'UserController@activeFocusCancel']);
Route::any('/activeFocus', ['uses' => 'UserController@activeFocus']);
Route::any('/activeSupport', ['uses' => 'UserController@activeSupport']);
//商户
Route::any('/businessLogin', ['uses' => 'LoginController@businessLogin']);
Route::any('/businessRegister', ['uses' => 'RegisterController@businessRegister']);
Route::any('/businessInfoFinish', ['uses' => 'BusinessController@businessInfoFinish']);
Route::any('/businessInfo', ['uses' => 'BusinessController@businessInfo']);
Route::any('/businessStart', ['uses' => 'BusinessController@businessStart']);
Route::any('/businessEnd', ['uses' => 'BusinessController@businessEnd']);
Route::any('/businessNewLogo', ['uses' => 'BusinessController@businessNewLogo']);
Route::any('/businessNewPassword', ['uses' => 'BusinessController@businessNewPassword']);
Route::any('/businessImage', ['uses' => 'BusinessController@businessImage']);
Route::any('/businessNewInfo', ['uses' => 'BusinessController@businessNewInfo']);
Route::any('/businessReport', ['uses' => 'BusinessController@businessReport']);
Route::any('/businessBankOrder', ['uses' => 'BusinessController@businessBankOrder']);
Route::any('/businessBankSure', ['uses' => 'BusinessController@businessBankSure']);
Route::any('/businessOutOrder', ['uses' => 'BusinessController@businessOutOrder']);
Route::any('/businessOutSure', ['uses' => 'BusinessController@businessOutSure']);
//分享
Route::any('/share', ['uses' => 'UserController@userShare']);
Route::get('/get/{code}.html', ['uses' => 'ShareController@shareCode']);
Route::any('/shareGet', ['uses' => 'ShareController@shareGet']);
//分享列表展示
Route::any('/share/show/{code}.html', ['uses' => 'ShareController@shareShow']);
//管理后台
Route::get('admin/login', ['as' => 'admin::login', 'uses' => 'Admin\LoginController@login']);
Route::post('admin/login', ['as' => 'admin::login::action', 'uses' => 'Admin\LoginController@loginAction']);
Route::group(['as' => 'admin::', 'prefix' => 'admin', 'middleware' => 'admin.check'], function () {
    Route::get('/', ['as' => 'home', 'uses' => 'Admin\AdminController@home']);
    Route::get('users', ['as' => 'users', 'uses' => 'Admin\UserController@index']);
    //用户编辑
    Route::get('users/update', ['as' => 'users', 'uses' => 'Admin\UserController@update']);
    //用户或者商户详情页展示
    Route::get('users/show/{id}', ['as' => 'users', 'uses' => 'Admin\UserController@show']);
    //商户编辑
    Route::get('business/update', ['as' => 'users', 'uses' => 'Admin\UserController@businessUpdate']);

    Route::get('logout', ['as' => 'logout', 'uses' => 'Admin\LoginController@logout']);
    Route::post('users/action/status/change', ['as' => 'user.status.change', 'uses' => 'Admin\UserController@statusChange']);
    Route::get('activity/add', ['as' => 'activity.add', 'uses' => 'Admin\ActivityController@add']);
    Route::post('activity/add', ['as' => 'activity.add.submit', 'uses' => 'Admin\ActivityController@addSubmit']);
    Route::get('activity/manage', ['as' => 'activity.manage', 'uses' => 'Admin\ActivityController@manage']);
    Route::post('activity/action/status/change', ['as' => 'activity.status.change', 'uses' => 'Admin\ActivityController@statusChange']);
});
//第三方类库
Route::any('/qiniu/token', ['uses' => 'QiniuController@getToken']);
//生成二维码并下载
Route::get('/download/qrcode/{string}', ['uses' => 'QiniuController@downloadQrcode']);
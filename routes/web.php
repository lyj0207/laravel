<?php

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

Route::get('/', function () {
    return view('welcome');
});

//注册   登录
Route::any('/test/register','test\TestController@register');//注册
Route::any('/test/do_register','test\TestController@do_register');//注册执行
Route::any('/test/login','test\LoginController@login');//登录
Route::any('/test/do_login','test\LoginController@do_login');//登录执行
Route::any('/test/login_time','test\LoginController@login_time');//登陆时间
Route::any('/test/update_time','test\LoginController@update_time');//延长过期时间
Route::any('/test/index','test\LoginController@index')->Middleware('login');//展示     防非法登录 middleware



//微信扫码登录
Route::any('/test/logo','test\DemoController@login');//登录
Route::any('/test/wechatlogin','test\DemoController@wechatlogin');//判断是否已经扫码
Route::any('/test/index','test\DemoController@index');

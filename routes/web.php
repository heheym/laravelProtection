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
    return redirect('admin');
//    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/promote', 'PromoteController@index');  //新歌推广页面
Route::get('/promote/song', 'PromoteController@song');//新歌推广ajax获取数据

Route::get('/qrCodeUrl', 'OrderController@qrCodeUrl');//二维码页面

Route::get('/notify_url', 'OrderController@notify_url');//乐刷通知地址





<?php

use Illuminate\Http\Request;
use App\Article;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function() {

    Route::post('/songs/addSongs', 'Api\SongController@addSongs');  //添加高危歌曲
    Route::post('/songs/modifySongs', 'Api\SongController@modifySongs');  //修改高危歌曲
    Route::post('/songs/addSongbanned', 'Api\SongController@addSongbanned');  //添加禁播歌曲
    Route::post('/songs/songsUpload', 'Api\SongController@songsUpload');  //客户点播歌曲上传接口
    Route::post('/songs/delSongs', 'Api\SongController@delSongs');  //删除歌曲
    Route::post('/songs/warningSong', 'Api\SongController@warningSong');  //场所预警接口
});

//url，需要加一个api，如：192.168.10.227:81/api/login
Route::get('register', 'Auth\RegisterController@register');  //api 注册 by ma
Route::post('/songs/login', 'Auth\LoginController@login');  //api 登录 by ma






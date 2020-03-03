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

    Route::post('/songs/service/addSongs', 'Api\SongController@serviceAddSongs');  //歌曲入库
    Route::post('/songs/service/modifySongs', 'Api\SongController@serviceModifySongs');  //修改歌曲
    Route::post('/songs/service/addSinger', 'Api\SongController@serviceAddSinger');  //新增歌星
    Route::post('/songs/service/modifySinger', 'Api\SongController@serviceModifySinger');  //修改歌星
    Route::post('/songs/service/changeSongsStatus', 'Api\SongController@serviceChangeSongsStatus');  //歌曲上下架

});

//url，需要加一个api，如：192.168.10.227:81/api/login
Route::get('register', 'Auth\RegisterController@register');  //api 注册 by ma
Route::post('/songs/service/login', 'Auth\LoginController@login');  //api 登录 by ma

Route::post('/songs/regsrv', 'Api\PlaceController@regsrv');  //服务端信息更新
Route::post('/songs/regbox', 'Api\PlaceController@regbox');  //机顶盒注册
Route::post('/songs/qrysrvmsg', 'Api\PlaceController@qrysrvmsg');  //服务端数据查询接口
Route::post('/songs/qryboxmsg', 'Api\PlaceController@qryboxmsg');  //机顶盒数据查询接口

Route::post('/songs/songWarning', 'Api\PlaceController@songWarning');  //机顶盒数据查询接口
Route::post('/songs/placeWarning', 'Api\PlaceController@placeWarning');  //场所预警接口

Route::post('/fees/setMealKtv', 'Api\FeeController@setMealKtv');  //场所获取套餐接口









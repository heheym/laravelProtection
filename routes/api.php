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

//需要Authorization，是users表，更新歌曲歌星等信息
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

    Route::post('/songs/service/delSinger', 'Api\SongController@serviceDelSinger');  //删除歌星接口

    Route::post('/songs/service/updateVer', 'Api\SongController@serviceUpdateVer');  //更新版本接口

    Route::post('/songs/addregbox', 'Api\SongController@addRegbox');  //机顶盒预登记接口

    Route::post('/songs/service/rcompany', 'Api\SongController@rcompany');  //机顶盒预登记接口

    Route::post('/songs/service/urgentDelsong', 'Api\SongController@urgentDelsong');  //歌曲紧急下架接口

    Route::post('/songs/busong/busonglist', 'Api\SongController@busonglist');  //补歌数据查询接口
    Route::post('/songs/busong/updateStatus', 'Api\SongController@updateStatus');  //补歌状态更新接口


    Route::post('/songs/busong/getplace', 'Api\SongController@getplace');  //获取场所信息接口

    Route::post('/songs/service/playRecord', 'Api\SongController@playRecord');  //获取点播记录接口

    Route::post('/songs/service/hotspotUpload', 'Api\SongController@hotspotUpload'); //热点歌曲可预先下载列表上传接口



});

//url，需要加一个api，如：192.168.10.227:81/api/login
//需要srvkey，场所使用，是place表用户，
Route::get('register', 'Auth\RegisterController@register');  //api 注册 by ma
Route::post('/songs/service/login', 'Auth\LoginController@login');  //api 登录 by ma

Route::post('/songs/regsrv', 'Api\PlaceController@regsrv');  //服务端信息更新
Route::post('/songs/regbox', 'Api\PlaceController@regbox');  //机顶盒注册
Route::post('/songs/qrysrvmsg', 'Api\PlaceController@qrysrvmsg');  //服务端数据查询接口
Route::post('/songs/qryboxmsg', 'Api\PlaceController@qryboxmsg');  //机顶盒数据查询接口

Route::post('/songs/songWarning', 'Api\PlaceController@songWarning');  //机顶盒点播歌曲上传接口
Route::post('/songs/placeWarning', 'Api\PlaceController@placeWarning');  //场所预警接口

Route::post('/fees/setMealKtv', 'Api\FeeController@setMealKtv');  //场所获取套餐接口

Route::post('/songs/parameter', 'Api\PlaceController@parameter');  //获取系统参数接口

Route::post('/songs/downsonghttp', 'Api\PlaceController@downsonghttp');  //获取歌曲下载地址接口

Route::post('/songs/downsongok', 'Api\PlaceController@downsongok');  //歌曲下载成功上传接口

Route::post('/songs/isboxreg', 'Api\PlaceController@isboxreg');  //机顶盒是否登记接口

Route::post('/songs/posterList', 'Api\PlaceController@posterList');  //机顶盒是否登记接口

Route::post('/songs/posterList', 'Api\PlaceController@posterList');  //机顶盒是否登记接口


Route::post('/fees/getQrCodeUrl', 'OrderController@getQrCodeUrl');  //生成接口，返回二维码链接


Route::post('/songs/companyWarning', 'Api\PlaceController@companyWarning');  //唱片公司异常接口

Route::post('/songs/urgentDelsong', 'Api\PlaceController@urgentDelsong');  //紧急下架歌曲

Route::post('/songs/urgentCompany', 'Api\PlaceController@urgentCompany');  //紧急预警唱片公司

Route::post('/songs/urgentCompanylist', 'Api\PlaceController@urgentCompanylist');  //获取紧急预警唱片公司列表

Route::post('/songs/busong/busongAdd', 'Api\PlaceController@busongAdd');  //场所新增提交补歌接口
Route::post('/songs/busong/busongplacelist', 'Api\PlaceController@busongplacelist');  //补歌数据查询接口

Route::post('/songs/urgentPaymentno', 'Api\PlaceController@urgentPaymentno');  //上传异常支付帐号接口
Route::post('/songs/urgentPaymentlist', 'Api\PlaceController@urgentPaymentlist');  //获取异常支付帐号列表


Route::post('/uploadfile', 'Api\PlaceController@uploadfile');  //上传文件

Route::post('/songs/ktvonoff', 'Api\PlaceController@ktvonoff');  //开关房记录上传接口

Route::post('/songs/hotspotsong', 'Api\PlaceController@hotspotsong');  //热点歌曲可预先下载列表

Route::post('/songs/exchangroom', 'Api\PlaceController@exchangroom');  //场所换房接口

//预付款
Route::post('/songs/openroom/openfeeroom', 'Api\PlaceController@openfeeroom');  //预付款开房扣费接口
Route::post('/songs/openroom/remainingsum', 'Api\PlaceController@remainingsum');  //查询场所预付款余额接口

Route::post('/songs/softversion', 'Api\PlaceController@softversion');  //版本管理







//接收产品管理后台数据
Route::post('/agent/service/login', 'Api\ProductAgentController@login');  //接口授权
Route::Post('/agent/service/agentupdate', 'Api\ProductAgentController@agentupdate');  //更新代理商接口
Route::Post('/agent/service/boxupdate', 'Api\ProductAgentController@boxupdate');  //更新机顶盒接口
Route::Post('/agent/service/agentdelete', 'Api\ProductAgentController@agentdelete');  //删除代理商接口
Route::Post('/agent/service/placeupdate', 'Api\ProductAgentController@placeupdate');  //更新场所资料接口
Route::post('/agent/service/billrecord', 'Api\ProductAgentController@billrecord');  //获取交易流水查询接口

//定时ws推送信息
Route::Get('/crontab/index', 'Api\CrontabController@index');  //定时访问ws





//malai
Route::post('/songs/service/softVer', 'Api\MalaiController@softVer');  //更新版本接口
Route::any('/getsoftver', 'Api\MalaiController@getsoftver');  //获取版本接口



//备份数据库
Route::Post('/backup/getlist', '\App\Admin\Api\BackUp\IndexApi@getlist');  //新增出仓单接口
Route::Post('/backup/isdownload', '\App\Admin\Api\BackUp\IndexApi@isdownload');  //是否下载









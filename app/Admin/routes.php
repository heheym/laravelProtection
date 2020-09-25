<?php

use Illuminate\Routing\Router;
//use App\Admin\Controllers\ReceiptController;

Admin::registerAuthRoutes();


//Route::get('/admin/itemId', 'App\Admin\Controllers\ReceivableController@itemId');  //表单联动
//Route::post('/admin/ireceipt/invalid', 'ReceiptController@Invalid'); //收款作废
//Route::get('/admin/ipostertab/address', 'PosterTabController@address'); //


Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
],function(Router $router){
    // Route::any('', 'HomeController@index');
    // $router->resource('collect', CollectController::class);  //用户歌曲
});

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {
    Route::any('itemId', 'App\Admin\Controllers\ReceivableController@itemId');  //表单联动
    Route::any('receipt/invalid', 'ReceiptController@Invalid'); //收款作废
    Route::any('postertab/address', 'PosterTabController@address'); //

    $router->get('/', 'HomeController@index')->name('admin.home');
//    $router->resource('uploads', UploadController::class);  //upload
//    $router->get('upload','UploadController@store1');  //获取七牛云上传token

    $router->resource('usersong', UserSongController::class);  //用户歌曲
    $router->get('song','SongController@getToken');  //获取七牛云上传token

    $router->resource('config',ConfigController::class);  //系统配置
    $router->resource('user',UserController::class);  //用户
//    $router->resource('bansong', BanSongController::class); //禁播管理
//    $router->resource('dangersong', DangerSongController::class); //高危管理

    $router->resource('place', PlaceController::class); //场所管理
    $router->resource('settopbox', SetTopBoxController::class); //机顶盒管理

    $router->resource('songs', SongController::class); //歌曲管理
    $router->resource('singer', SingerController::class); //歌曲管理

    $router->post('song/songonline', 'SongController@songonline');//后台批量上下架

    $router->resource('warningmode', WarningModeController::class);//预警模式

    $router->resource('setmeal', SetMealController::class);//套餐

    $router->resource('receivable', ReceivableController::class);//应收管理

    $router->resource('receipt', ReceiptController::class);//收费管理

    $router->resource('promote', PromoteController::class);//新歌推广

    $router->resource('songdownload', SongDownloadController::class);//歌曲下载查询

    $router->resource('boxregister', BoxRegisterController::class);//机顶盒预登记



    $router->resource('postertab', PosterTabController::class);  //广告推广


    $router->resource('ordersn', OrderController::class); //订单列表

    $router->resource('merchanttable', MerchanttableController::class); //商户号管理

    $router->resource('merchantset', MerchantSetController::class); //场所分成管理


    $router->resource('warningcompany', WarningCompanyController::class);//唱片公司异常

    $router->resource('busong', BuSongController::class);//补歌


    $router->resource('adminpermissions', AdminPermissonsController::class);//权限表


    $router->resource('urgentpaymentno', UrgentPaymentnoController::class);//异常用户


    $router->resource('ordercount', OrderCountController::class); //订单查询


    $router->resource('usersopencloses', UsersOpencloseController::class); //开关房记录

    $router->resource('collect', CollectController::class);  //信息收集



});






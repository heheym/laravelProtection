<?php

namespace App\Http\Controllers\Workerman;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkermanController extends Controller
{
    //
    public function index($arr=[])
    {
//        $ordersn = DB::table('ordersn')->where('key','SZGA8BOPZLBB')->first();
//        $data = ['srvkey'=>$ordersn->key,'KtvBoxid'=>$ordersn->KtvBoxid,'pay_time'=>$ordersn->pay_time,'leshua_order_id'=>$ordersn->leshua_order_id,'amount'=>$ordersn->amount];
//        $arr = $data;
//        var_dump($arr);
//        return;
        Log::getMonolog()->popHandler();
        Log::useDailyFiles(storage_path('logs/WkSendMessage.log'));

        /// 建立socket连接到内部推送端口
        $client = stream_socket_client('tcp://47.106.155.48:82', $errno, $errmsg, 1);
//        $client = stream_socket_client('tcp://127.0.0.1:82', $errno, $errmsg, 1);
// 推送的数据，包含uid字段，表示是给这个uid推送
//        $data = array('code'=>200, 'msg'=>'支付成功','data'=>$KtvBoxid);
// 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
//        var_dump($client);
        fwrite($client, json_encode($arr,JSON_UNESCAPED_UNICODE)."\n");
// 读取推送结果
        if(fread($client, 8192)=='success'){
            DB::table('order_sn')->where('leshua_order_id',$arr['leshua_order_id'])->update(['send_message'=>1]);
        }else{
            Log::info('推送失败,msg:'.fread($client, 8192).',arr:'.json_encode($arr,JSON_UNESCAPED_UNICODE).PHP_EOL);
        }

        fclose($client);
    }
}

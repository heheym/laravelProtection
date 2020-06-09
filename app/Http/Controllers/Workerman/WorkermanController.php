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
        $ordersn = DB::table('ordersn')->where('key','RXJWM7DTIP8X')->first();

        $data = ['srvkey'=>$ordersn->key,'KtvBoxid'=>$ordersn->KtvBoxid,'pay_time'=>$ordersn->pay_time,'leshua_order_id'=>$ordersn->leshua_order_id,'amount'=>$ordersn->amount];
//        $arr = $data;
//        var_dump($arr);
//        return;
        Log::getMonolog()->popHandler();
        Log::useDailyFiles(storage_path('logs/WkSendMessage.log'));

        /// 建立socket连接到内部推送端口
        $client = stream_socket_client('tcp://47.106.155.48:82', $errno, $errmsg, 1);
//        $client = stream_socket_client('tcp://127.0.0.1:82', $errno, $errmsg, 1);
        if(empty($arr)){
            fwrite($client, json_encode($data,JSON_UNESCAPED_UNICODE)."\n");
        }else{
            fwrite($client, json_encode($arr,JSON_UNESCAPED_UNICODE)."\n");
        }
// 读取推送结果
        /*
        if(fread($client, 8192)=='success'){
            $result = DB::table('order_sn')->where('leshua_order_id',$arr['leshua_order_id'])->update(['send_message'=>1]);
            if(!$result){
                Log::info('修改send_message失败,'.',arr:'.json_encode($arr,JSON_UNESCAPED_UNICODE).PHP_EOL);
            }
        }else{
            if(empty($arr)){
                Log::info('推送失败,msg:'.fread($client, 8192).',arr:'.json_encode($data,JSON_UNESCAPED_UNICODE).PHP_EOL);
            }else{
                Log::info('推送失败,msg:'.fread($client, 8192).',arr:'.json_encode($arr,JSON_UNESCAPED_UNICODE).PHP_EOL);
            }

        }*/
        $abc = trim(fread($client, 8192));
        if($abc=="success"){
            echo 1;
            var_dump($abc); ;
        }else{
            echo 2;
            var_dump($abc); ;
        }

        fclose($client);
    }
}

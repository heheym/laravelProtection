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

        if(empty($arr)){
            $ordersn = DB::table('ordersn')->where('key','RXJWM7DTIP8X')->first();
            $arr = ['srvkey'=>$ordersn->key,'KtvBoxid'=>$ordersn->KtvBoxid,'pay_time'=>$ordersn->pay_time,'leshua_order_id'=>$ordersn->leshua_order_id,'amount'=>$ordersn->amount];
        }

//        $arr = $data;
//        var_dump($arr);
//        return;
        Log::getMonolog()->popHandler();
        Log::useDailyFiles(storage_path('logs/WkSendMessage.log'));
        $externalContent = file_get_contents('http://checkip.dyndns.com/');
        preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
        $externalIp = $m[1];

        /// 建立socket连接到内部推送端口
        $client = stream_socket_client('tcp://'.$externalIp.':82', $errno, $errmsg, 1);
//        $client = stream_socket_client('tcp://127.0.0.1:82', $errno, $errmsg, 1);

        fwrite($client, json_encode($arr,JSON_UNESCAPED_UNICODE)."\n");

// 读取推送结果
        $abc = trim(fread($client, 8192));

        if($abc=='success'){
            $result = DB::table('ordersn')->where('leshua_order_id',$arr['leshua_order_id'])->update(['send_message'=>1]);
            if(!$result){
                Log::info('修改send_message失败,'.',arr:'.json_encode($arr,JSON_UNESCAPED_UNICODE).PHP_EOL);
            }else{
                Log::info('推送成功,'.',arr:'.json_encode($arr,JSON_UNESCAPED_UNICODE).PHP_EOL);
            }
        }else{
                Log::info('推送失败,msg:'.fread($client, 8192).',arr:'.json_encode($arr,JSON_UNESCAPED_UNICODE).PHP_EOL);
        }

        fclose($client);
    }
}

<?php

namespace App\Http\Controllers\Workerman;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class WorkermanController extends Controller
{
    //
    public function index()
    {
        // 建立socket连接到内部推送端口
//        $client = stream_socket_client('tcp://47.106.155.48:82', $errno, $errmsg, 1);
        $client = stream_socket_client('tcp://127.0.0.1:82', $errno, $errmsg, 1);
        // 推送的数据，包含uid字段，表示是给这个uid推送
        $data = array('uid'=>'uid1', 'percent'=>'成功');
        // 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
//        fwrite($client, json_encode($data)."\n");
        fwrite($client,'1'."\n");
        // 读取推送结果
        echo fread($client, 8192);
    }
}

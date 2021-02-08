<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Workerman\WorkermanController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CrontabController extends Controller
{
    //
    public function index()
    {
        $ordersn = DB::table('ordersn')->where('order_status','1')
            ->where('send_message','0')->where('confirm_order','0')
            // ->where('pay_time','>',date('Y-m-d H:i:s',time()-600))
            ->get();
        // dd($ordersn);
        if(count($ordersn)>0){
            foreach($ordersn as $v){
                $data = ['func'=>'push_pay','srvkey'=>$v->key,'KtvBoxid'=>$v->KtvBoxid,
                    'pay_time'=>$v->pay_time,'leshua_order_id'=>$v->leshua_order_id,
                    'amount'=>$v->amount,'openid'=>$v->openid];
                $worker = new WorkermanController();
                $worker->index($data);
            }
        }


    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\LeshuaHelper;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * 二维码链接页面
     *
     * @return \Illuminate\Http\Response
     */
    public function qrCodeUrl()
    {
        $ls_pay = new LeshuaHelper('0000000018', 'a1613a0e7cb9d3a51e33784ee4d212ac','ZFBZF',2);
        $order_id = $ls_pay->generate_order_id();
        $re = $ls_pay->getTdCode(1, $order_id);
        $url =$re->td_code;
        var_dump($re);
        return;

        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return '微信支付';
        }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false)
        {
            $ls_pay = new LeshuaHelper('0000000018', 'a1613a0e7cb9d3a51e33784ee4d212ac','ZFBZF',2);
            $order_id = $ls_pay->generate_order_id();
            $re = $ls_pay->getTdCode(1, $order_id);
            $url =$re->td_code;

            return redirect($url);
        };
        return view('order.qrCodeUrl')->with(['url'=>$url]);

    }

    //返回二维码链接
    public function generateQrCode()
    {
        return
    }



}

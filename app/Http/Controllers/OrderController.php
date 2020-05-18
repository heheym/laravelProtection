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

    //返回二维码链接,生成套餐支付订单返回接口
    public function generateQrCode()
    {
        $srvkey = \Request::header('tonkey');
        $srvkey = explode(' ',$srvkey)[0];

        if(empty($srvkey)){
            return response()->json(['code' => 500, 'msg' => '场所key错误', 'data' => null]);
        }
        $exists = DB::table('place')->where(['key'=>$srvkey])->exists();
        if(!$exists){
            return response()->json(['code' => 500, 'msg' => 'key不存在', 'data' => null]);
        }

        $post = json_decode(file_get_contents("php://input"), true);
        if(empty($post['setMeal_id'])){
            return response()->json(['code' => 500, 'msg' => '参数错误','data'=>null]);
        }
        $setMeal_id = $post['setMeal_id'];
        $setMeal_num = !empty($post['setMeal_num'])?$post['setMeal_num']:1;

        $exists = DB::table('setMeal')->where('setMeal_id',$setMeal_id)->exists();
        if(!$exists){
            return response()->json(['code' => 500, 'msg' => '套餐不存在','data'=>null]);
        }

        $setMeal = DB::table('setMeal')->where('setMeal_id',$post['setMeal_id'])->first();
        //setMeal_mode,套餐计费方式:1：按有效机顶盒数量，2按固定费用
        if($setMeal->setMeal_mode==1){

        }elseif($setMeal->setMeal_mode==2){
            $setMeal_totalprice = $setMeal->setMeal_price*$setMeal_num*$setMeal->setMeal_discount;
        }

        $insertData = array(
            'key'=>$srvkey,
            'order_sn' => $this->get_order_sn(), //订单号，显示用
            'order_sn_submit' => $this->get_order_sn(), //订单号，支付时提交用，每次变化
            'amount' => $setMeal_totalprice,
            'submit_time' => date('Y-m-d H:i:s',time()),
            'o_status' => 1  //订单是否有效  0无效，1有效
        );

        $result = DB::table('order')->insertGetId($insertData);
        if(!$result){
            return response()->json(['code' => 200, 'msg' => '生成订单失败','data'=>null]);
        }

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $data['code_url'] = $protocol.$domainName."/qrCodeUrl?sn=".$insertData['order_sn'];

        return response()->json(['code' => 200,
            'Payment_id' => $insertData['order_sn'],
            'setMeal_id'=>$setMeal_id,
            'setMeal_name'=>$setMeal->setMeal_name,
            'expired_date'=>'2023-12-12',
            'setMeal_days'=>365,
            'setMeal_totalprice'=> $setMeal_totalprice,
            'setMeal_num' => $setMeal_num,
            'data'=>$data
        ]);

    }

    /**
     * 扫二维码后访问的链接页面
     * @return \Illuminate\Http\Response
     */
    public function qrCodeUrl()
    {
        if(empty($_GET['sn'])){
            return response()->json(['code' => 500, 'msg' => '订单号不能为空', 'data' => null]);
        }
        $order_sn = $_GET['sn'];
        $exist = DB::table('order')->where('order_sn',$order_sn)->exists();
        if(!$exist){
            return response()->json(['code' => 500, 'msg' => '订单号不存在', 'data' => null]);
        }

        $third_order_id = $this->get_order_sn();
        //更新支付订单号
        Db::table('order')->where('order_sn',$order_sn)->update(['order_sn_submit'=>$third_order_id]);

        $order = DB::table('order')->where('order_sn',$order_sn)->first();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];

        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                return '微信支付';
        }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false)
        {
                $ls_pay = new LeshuaHelper('ZFBZF','0');
                $arr = [
                    'body'=>'快唱',
                    'sub_openid'=>'',
                    'third_order_id'=>$third_order_id,
                    'amount'=>$order->amount,
                    'notify_url'=>urlencode($protocol.$domainName.'/notify_url'),
                    'jump_url'=>urlencode($protocol.$domainName),
                    'order_expiration'=>60, //订单有效时长 支付宝为分钟，微信为秒
                ];
                $re = $ls_pay->getTdCode($arr);
                $url =$re->td_code;
                if(empty($url)){
                    return response()->json(['code' => 500, 'msg' => '订单错误', 'data' => null]);
                }
                return redirect($url);
        }else{
                $ls_pay = new LeshuaHelper('ZFBZF','0');
                $arr = [
                    'body'=>'快唱',
                    'sub_openid'=>'',
                    'third_order_id'=>$third_order_id,
                    'amount'=>$order->amount,
                    'notify_url'=>urlencode($protocol.$domainName.'/notify_url'),
                    'jump_url'=>urlencode($protocol.$domainName),
                    'order_expiration'=>60, //订单有效时长 支付宝为分钟，微信为秒
                ];
                $re = $ls_pay->getTdCode($arr);
                $url =$re->td_code;
                if(empty($url)){
                    return response()->json(['code' => 500, 'msg' => '订单错误', 'data' => null]);
                }
                return redirect($url);
        }

        return response()->json(['code' => 500, 'msg' => '请使用微信或支付宝扫码', 'data' => null]);
    }

    //生成订单号
    public function get_order_sn(){
        //防止重复订单号存在
        while (true) {
            $order_sn = date('YmdHis').rand(1000,9999); //订单号
            $count = Db::table('order')->where('order_sn', $order_sn)->count();
            if($count == 0){
                break;
            }
        }
        return $order_sn;
    }

    //支付成功，乐刷通知地址
    public function notify_url()
    {
        if(isset($_POST['status']) && ($_POST['status']== 2)){
            file_put_contents('1.txt',"订单号:".$_POST['third_order_id']."\r\n".
                "金额:".$_POST['amount']."\r\n".
                "支付时间:".$_POST['pay_time']."\r\n".
                "支付类型:".$_POST['pay_way']."\r\n"
            );

        };
    }
    
    //查询订单
    public function queryOrder()
    {

        if(empty($_GET['sn'])){
            return response()->json(['code' => 500, 'msg' => '订单号不能为空', 'data' => null]);
        }
        $order_sn = $_GET['sn'];
        $exist = DB::table('order')->where('order_sn',$order_sn)->exists();
        if(!$exist){
            return response()->json(['code' => 500, 'msg' => '订单号不存在', 'data' => null]);
        }
        $order = DB::table('order')->where('order_sn',$order_sn)->first();
        $ls_pay = new LeshuaHelper();
        $arr = [
            'order'=> $order->order_sn_submit
        ];
        $result = $ls_pay->queryOrder($arr);
        return $result;
    }


}

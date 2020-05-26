<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\LeshuaHelper;
use Illuminate\Support\Facades\Log;

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
     * 扫二维码后访问的链接页面,生成订单并转到支付页面
     * @return \Illuminate\Http\Response
     */
    public function qrCodeUrl()
    {
//        $srvkey = \Request::header('tonkey');
        $srvkey = isset($_GET['key'])?$_GET['key']:'';
        $KtvBoxid = isset($_GET['KtvBoxid'])?$_GET['KtvBoxid']:''; //机器码

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $notify_url = $protocol.$domainName.'/notifyUrl';
        $jump_url = $protocol.$domainName.'/jumpUrl';

        if(empty($srvkey)){
            return response()->json(['code' => 500, 'msg' => '场所key错误', 'data' => null]);
        }
        $exists = DB::table('place')->where(['key'=>$srvkey])->exists();
        if(!$exists){
            return response()->json(['code' => 500, 'msg' => 'key不存在', 'data' => null]);
        }
        $insertData = array(
            'key'=>$srvkey,
            'KtvBoxid'=>$KtvBoxid,
//            'order_sn' => $this->get_order_sn(), //订单号，显示用
            'order_sn_submit' => $this->get_order_sn(), //订单号，支付时提交用，每次变化
            'amount' => 0.01,
            'submit_time' => date('Y-m-d H:i:s',time()),
            'o_status' => 1  //订单是否有效  0无效，1有效
        );
        $result = DB::table('order')->insertGetId($insertData);

        //判断是支付宝还是微信
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                $ls_pay = new LeshuaHelper('WXZF','2');
//                $ls_pay = new LeshuaHelper('ZFBZF','2');
                $arr = [
                    'body'=>'快唱',
                    'sub_openid'=>'',
                    'third_order_id'=>$insertData['order_sn_submit'],
                    'amount'=>0.01,
                    'notify_url'=>urlencode($notify_url),
                    'jump_url'=>urlencode($jump_url),
                    'order_expiration'=>60, //订单有效时长 支付宝为分钟，微信为秒
                ];

                $re = $ls_pay->getTdCode($arr);
                $url =$re->jspay_url;
                if(empty($url)){
                    return response()->json(['code' => 500, 'msg' => '订单错误', 'data' => null]);
                }
                if(isset($re->leshua_order_id)){
                    DB::table('order')->where('order_sn_submit',$insertData['order_sn_submit'])->update(['leshua_order_id'=>$re->leshua_order_id]);
                }
                return redirect($url);
        }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false)
        {
                $ls_pay = new LeshuaHelper('ZFBZF','2');
//                $ls_pay = new LeshuaHelper('WXZF','2');
                $arr = [
                    'body'=>'快唱',
                    'sub_openid'=>'',
                    'third_order_id'=>$insertData['order_sn_submit'],
                    'amount'=>0.01,
                    'notify_url'=>urlencode($notify_url),
                    'jump_url'=>urlencode($jump_url),
                    'order_expiration'=>60, //订单有效时长 支付宝为分钟，微信为秒
                ];
                $re = $ls_pay->getTdCode($arr);
                $url =$re->jspay_url;
                if(empty($url)){
                    return response()->json(['code' => 500, 'msg' => '订单错误', 'data' => null]);
                }
                if(isset($re->leshua_order_id)){
                    DB::table('order')->where('order_sn_submit',$insertData['order_sn_submit'])->update(['leshua_order_id'=>$re->leshua_order_id]);
                }
                return redirect($url);
        }else{
                $ls_pay = new LeshuaHelper('WXZF','2');
                $arr = [
                    'body'=>'快唱',
                    'sub_openid'=>'',
                    'third_order_id'=>$insertData['order_sn_submit'],
                    'amount'=>0.01,
                    'notify_url'=>urlencode($notify_url),
                    'jump_url'=>urlencode($jump_url),
                    'order_expiration'=>60, //订单有效时长 支付宝为分钟，微信为秒
                ];
                $re = $ls_pay->getTdCode($arr);
                $url =$re->jspay_url;
                if(isset($re->leshua_order_id)) {
                    DB::table('order')->where('order_sn_submit', $insertData['order_sn_submit'])->update(['leshua_order_id' => $re->leshua_order_id]);
                }
                if(empty($url)){
                    return response()->json(['code' => 500, 'msg' => '订单错误', 'data' => null]);
                }
//                var_dump($arr['notify_url']);
                var_dump($re);
                return;
                return redirect($url);
        }

        return response()->json(['code' => 500, 'msg' => '请使用微信或支付宝扫码', 'data' => null]);
    }

    //支付成功，乐刷通知地址
    public function notifyUrl()
    {
        Log::getMonolog()->popHandler();
        Log::useDailyFiles(storage_path('logs/notifyUrl.log'));
        try{
            $post = file_get_contents("php://input");
            $xml_parser = xml_parser_create();
            if(!xml_parse($xml_parser,$post,true)){
                Log::info('不是xml格式'.PHP_EOL);
            }else {
                $re_obj = simplexml_load_string($post,'SimpleXMLElement',LIBXML_NOCDATA );
                Log::info($post.PHP_EOL);
            }
        }catch (\Exception $e){
            Log::getMonolog()->popHandler();
            Log::useDailyFiles(storage_path('logs/notifyUrl.log'));
            Log::info($e->getMessage());
        }
    }
    
    //支付成功，直接跳转地址 乐刷会带参数跳转leshuaOrderId，result
    public function jumpUrl()
    {
        $html='<div class="info" style="padding: 30px;
    background: #ff9326;
    color: #FFF;font-size:50px">您的订单支付成功</div>
<div class="mark" style="
  margin:auto;
  padding-top:130px;
  fill:#6eb700;">
  <img src="/img/yes.jpg" width=300 height=300 style="display:block;margin:auto"/>
</div>';
        if(isset($_GET['leshuaOrderId']) && isset($_GET['result'])){
            $leshuaOrderId = $_GET['leshuaOrderId'];
            $result = $_GET['result'];
            if($result==1){
                return '<div class="info" style="padding: 30px;
    background: #ff9326;
    color: #FFF;font-size:50px">您的订单支付成功</div>
<div class="mark" style="
  margin:auto;
  padding-top:130px;
  fill:#6eb700;">
  <img src="/img/yes.jpg" width=200 height=200 style="display:block;margin:auto"/>
</div>';
            }else {
                return '<div class="info" style="padding: 30px;
    background: #ff9326;
    color: #FFF;font-size:50px">您的订单没有支付</div>
<div class="mark" style="
  margin:auto;
  padding-top:130px;
  fill:#6eb700;">
  <img src="/img/no.jpg" width=200 height=200 style="display:block;margin:auto"/>
</div>';
            }
        }

        return '<div class="info" style="padding: 30px;
    background: #ff9326;
    color: #FFF;font-size:50px">订单失败</div>
<div class="mark" style="
  margin:auto;
  padding-top:130px;
  fill:#6eb700;">
  <img src="/img/no.jpg" width=200 height=200 style="display:block;margin:auto"/>
</div>';

//        if(!isset($_GET['sn'])){
//            return response()->json(['code' => 500, 'msg' => '订单号错误1', 'data' => null]);
//        }
//        $sn = $_GET['sn'];
//        echo($sn);
//
//        $order = DB::table('order')->where('order_sn_submit',$sn)->first();
//        if(empty($order)){
//            return response()->json(['code' => 500, 'msg' => '订单号错误2', 'data' => null]);
//        }
//        $queryOrder = $this->queryOrder($sn);
//        if($queryOrder==='0'){
//            return '支付失败';
//        }elseif($queryOrder==='2'){
//            return '支付成功';
//        }
    }
    
    //通过乐刷号查询订单，sn是乐刷号
    public function queryOrder($sn=0)
    {
        if(isset($_GET['sn'])){
            $sn = $_GET['sn'];
        }
        $order = DB::table('order')->where('order_sn_submit',$sn)->first();
        if(empty($sn)||empty($order)){
            return response()->json(['code' => 500, 'msg' => '订单号错误3', 'data' => null]);
        }
        $ls_pay = new LeshuaHelper();
        $arr = [
            'order'=> $order->leshua_order_id
        ];
        $result = $ls_pay->queryOrder($arr);
//        var_dump($result);
       if(isset($result->result_code)&&empty($result->result_code)){
            var_dump($result); ;
       }else{
           return response()->json(['code' => 500, 'msg' => '订单查询失败', 'data' => null]);
       }

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

}

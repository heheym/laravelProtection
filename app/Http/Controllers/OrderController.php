<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\LeshuaHelper;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Workerman\WorkermanController;

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
    public function getQrCodeUrl()
    {

        $srvkey = \Request::header('srvkey');

        if(empty($srvkey)){
            return response()->json(['code' => 500, 'msg' => '场所key错误', 'data' => null]);
        }
        $exists = DB::table('place')->where(['key'=>$srvkey])->exists();
        if(!$exists){
            return response()->json(['code' => 500, 'msg' => 'key不存在', 'data' => null]);
        }

        $post = json_decode(file_get_contents("php://input"), true);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
//        if(!is_array($post)){
//            return response()->json(['code' => 500, 'msg' => 'post数据错误', 'data' => null]);
//        }
        foreach($post as $k=>$v){
            if(!empty($v['KtvBoxid'])){
                if(empty($v['KtvBoxid'])){
                    return response()->json(['code' => 500, 'msg'=>'机器码KtvBoxid错误','data'=>null]);
                }
                $exists = DB::table('settopbox')->where(['key'=>$srvkey,'KtvBoxid'=>$v['KtvBoxid']])->exists();
                if(!$exists){
                    return response()->json(['code' => 500, 'msg'=>'场所没有该机器码','data'=>null]);
                }
                $post[$k]['qrcode'] = $protocol.$domainName."/qrCodeUrl?KtvBoxid=".$v['KtvBoxid'];
            }
        }

        return response()->json(['code' => 200, 'msg' => '请求成功', 'data' => $post,
            'place'=>$protocol.$domainName."/qrCodeUrl?srvkey=".$srvkey]);
    }

    /**
     * 扫二维码后访问的链接页面,生成订单并转到支付页面
     * @return \Illuminate\Http\Response
     */
    public function qrCodeUrl()
    {
//        $srvkey = \Request::header('tonkey');
        $srvkey = isset($_GET['srvkey'])?$_GET['srvkey']:'';
        $KtvBoxid = isset($_GET['KtvBoxid'])?$_GET['KtvBoxid']:''; //机器码
        $amount = isset($_GET['amount'])?$_GET['amount']:0.01; //机器码

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $notify_url = $protocol.$domainName.'/notifyUrl';
        $jump_url = $protocol.$domainName.'/jumpUrl';

        //机顶盒下单
        if(empty($srvkey)){
            if(empty($KtvBoxid)){
                return response()->json(['code' => 500, 'msg' => '机器码错误', 'data' => null]);
            }
            $exists = DB::table('settopbox')->where(['KtvBoxid'=>$KtvBoxid])->select('KtvBoxid','key')->first();
            if(empty($exists->KtvBoxid)){
                return response()->json(['code' => 500, 'msg' => '机器码不存在', 'data' => null]);
            }
            $ordersn = DB::table('ordersn')->where('order_status',1)
                ->where('KtvBoxid',$KtvBoxid)->orderBy('id','desc')->first();

            if(isset($ordersn->pay_time)){
                $tim = time()-strtotime($ordersn->pay_time);
                $timee = ceil((600-$tim)/60);

                if($tim<600){
                    return '     
    <div style="margin:0px;background:url(\'/img/back.jpg\') no-repeat;width:100%;height:90%;background-size:100% 100%; background-attachment:fixed;">
            <p style="font:normal normal 200 4em/40px Microsoft YaHei;color:rgb(77,148,255);text-align:center;margin-top:10%;">该房间已扫码支付</br><br/>请'.$timee.'分钟后再试</p>
            <img src="/img/no.jpg" style="position:absolute;width:19.2%;height:12%;left:42%;top:22%;">
            <img src="/img/wx.jpg" style="position:absolute;width:44%;height:28%;left:28%;top:62%;">
        </div>
';
                    return response()->json(['code' => 500, 'msg' => '请稍后再试', 'data' => null]);
                }
            }
            $insertData = array(
                'key'=>$exists->key,
                'KtvBoxid'=>$exists->KtvBoxid,
//            'order_sn' => $this->get_order_sn(), //订单号，显示用
                'order_sn_submit' => $this->get_order_sn(), //订单号，支付时提交用，每次变化
                'amount' => $amount,
                'submit_time' => date('Y-m-d H:i:s',time()),
                'o_status' => 1  //订单是否有效  0无效，1有效
            );
        }
        else{ //场所
            $exists = DB::table('place')->where(['key'=>$srvkey])->exists();
            if(!$exists){
                return response()->json(['code' => 500, 'msg' => '场所不存在', 'data' => null]);
            }
            $insertData = array(
                'key'=>$srvkey,
//            'order_sn' => $this->get_order_sn(), //订单号，显示用
                'order_sn_submit' => $this->get_order_sn(), //订单号，支付时提交用，每次变化
                'amount' => $amount,
                'submit_time' => date('Y-m-d H:i:s',time()),
                'o_status' => 1,  //订单是否有效  0无效，1有效
                'option' =>1
            );
        }

        $result = DB::table('ordersn')->insertGetId($insertData);

        //判断是支付宝还是微信
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                $ls_pay = new LeshuaHelper('WXZF','2');
//                $ls_pay = new LeshuaHelper('ZFBZF','2');
                $arr = [
                    'body'=>'快唱',
                    'sub_openid'=>'',
                    'third_order_id'=>$insertData['order_sn_submit'],
                    'amount'=>$amount,
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
                    DB::table('ordersn')->where('order_sn_submit',$insertData['order_sn_submit'])->update(['leshua_order_id'=>$re->leshua_order_id]);
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
                    'amount'=>$amount,
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
                    DB::table('ordersn')->where('order_sn_submit',$insertData['order_sn_submit'])->update(['leshua_order_id'=>$re->leshua_order_id]);
                }
                return redirect($url);
        }else{
                $ls_pay = new LeshuaHelper('WXZF','2');
                $arr = [
                    'body'=>'快唱',
                    'sub_openid'=>'',
                    'third_order_id'=>$insertData['order_sn_submit'],
                    'amount'=>$amount,
                    'notify_url'=>urlencode($notify_url),
                    'jump_url'=>urlencode($jump_url),
                    'order_expiration'=>60, //订单有效时长 支付宝为分钟，微信为秒
                ];
                $re = $ls_pay->getTdCode($arr);
                $url =$re->jspay_url;
                if(isset($re->leshua_order_id)) {
                    DB::table('ordersn')->where('order_sn_submit', $insertData['order_sn_submit'])->update(['leshua_order_id' => $re->leshua_order_id]);
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
                Log::info($post);
                if(isset($re_obj->status) && $re_obj->status==2){
                    $exists = DB::table('ordersn')->where('leshua_order_id',$re_obj->leshua_order_id)->exists();
                    if(!$exists){
                        return '000000';
                    }
                    $result = DB::table('ordersn')->where('leshua_order_id',$re_obj->leshua_order_id)->update(['order_status'=>1,'pay_time'=>$re_obj->pay_time,'pay_way'=>$re_obj->pay_way,'openid'=>$re_obj->openid]);
                    $ordersn = DB::table('ordersn')->where('leshua_order_id',$re_obj->leshua_order_id)->first();
                    if($ordersn->option==1){ //场所预付款
                        DB::table('place')->where('key',$ordersn->key)->increment('balanceSum',$ordersn->amount);
                        $place = DB::table('place')->where('key',$ordersn->key)->first();
                        $rechargeMoneyData = [
                            'srvkey'=>$ordersn->key,
                            'billno'=>'Y'.time(),
                            'sourceType'=>1,
                            'amount'=>$ordersn->amount,
                            'balance'=>$place->balanceSum,
                            'rechargeDate'=>$ordersn->pay_time,
                            'ordersnId' => $ordersn->id
                        ];
                        DB::table('rechargeMoney')->insert($rechargeMoneyData);
                    }
                    if($result){
                        Log::info('修改订单状态成功,leshua_order_id:'.$re_obj->leshua_order_id.PHP_EOL);
                        $worker = new WorkermanController();
                        if($ordersn->option==1){
                            $data = ['func'=>'place_push','srvkey'=>$ordersn->key,'pay_time'=>$ordersn->pay_time,'leshua_order_id'=>$ordersn->leshua_order_id,'amount'=>$ordersn->amount,'openid'=>$ordersn->openid];
                        }else{
                            $data = ['func'=>'push_pay','srvkey'=>$ordersn->key,'KtvBoxid'=>$ordersn->KtvBoxid,'pay_time'=>$ordersn->pay_time,'leshua_order_id'=>$ordersn->leshua_order_id,'amount'=>$ordersn->amount,'openid'=>$ordersn->openid];
                        }
                        $worker->index($data);
                    }else{
                        Log::info('订单状态已修改,leshua_order_id:'.$re_obj->leshua_order_id.PHP_EOL);
                    }
                    return '000000';
                }
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
                return '
      <div style="margin:0px;background:url(\'/img/back.jpg\') no-repeat;width:100%;height:90%;background-size:100% 100%; background-attachment:fixed;">
            <p style="font:normal normal 600 4.8em/30px Microsoft YaHei;color:rgb(77,148,255);text-align:center;margin-top:20%;f">支付成功!</p>
            <img src="/img/yes.jpg" style="position:absolute;width:19.2%;height:12%;left:42%;top:22%;">
            <img src="/img/wx.jpg" style="position:absolute;width:44%;height:28%;left:28%;top:62%;">
        </div>
';
            }else {
                return '     
    <div style="margin:0px;background:url(\'/img/back.jpg\') no-repeat;width:100%;height:90%;background-size:100% 100%; background-attachment:fixed;">
            <p style="font:normal normal 600 4.8em/30px Microsoft YaHei;color:rgb(77,148,255);text-align:center;margin-top:20%;f">支付失败!</p>
            <img src="/img/no.jpg" style="position:absolute;width:19.2%;height:12%;left:42%;top:22%;">
            <img src="/img/wx.jpg" style="position:absolute;width:44%;height:28%;left:28%;top:62%;">
        </div>
';
            }
        }

        return '
        <div style="margin:0px;background:url(\'/img/back.jpg\') no-repeat;width:100%;height:90%;background-size:100% 100%; background-attachment:fixed;">
            <p style="font:normal normal 600 4.8em/30px Microsoft YaHei;color:rgb(77,148,255);text-align:center;margin-top:20%;f">订单无效</p>
            <img src="/img/no.jpg" style="position:absolute;width:19.2%;height:12%;left:42%;top:22%;">
            <img src="/img/wx.jpg" style="position:absolute;width:44%;height:28%;left:28%;top:62%;">
        </div>
';


//        <iframe  src="https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=Mzg2NzAwMjM2MA==&scene=123&uin=MTE3OTk0MDcyMQ==&key=6cbe8a29ffaf79ebacbdad6238d3e88e979fcaf3dc74f548eaca0edc47937c518811e1aead86e30cc1377264ede22bb195afbd20aefb536dff6ef226bac8e41ac468a91a480122394db34e5c82a1f57c&devicetype=Windows" class="iframe" scrolling="no" security="restricted" sandbox="allow-scripts allow-same-origin allow-popups" width="100%" height="5000"></iframe>


//        if(!isset($_GET['sn'])){
//            return response()->json(['code' => 500, 'msg' => '订单号错误1', 'data' => null]);
//        }
//        $sn = $_GET['sn'];
//        echo($sn);
//
//        $order = DB::table('ordersn')->where('order_sn_submit',$sn)->first();
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
        $order = DB::table('ordersn')->where('order_sn_submit',$sn)->first();
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
            $count = Db::table('ordersn')->where('order_sn_submit', $order_sn)->count();
            if($count == 0){
                break;
            }
        }
        return $order_sn;
    }

    public function test()
    {
        return view('test');
    }

}

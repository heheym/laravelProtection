<?php
/**
 * Created by PhpStorm.
 * User: xiaobaidaren
 * Date: 15/5/2020
 * Time: 17:22
 */
namespace App\Http\Models;

Class LeshuaHelper
{
    // 测试环境地址
//    private $url = "https://t-paygate.lepass.cn/cgi-bin/lepos_pay_gateway.cgi";
    // 正式环境地址
     private $url = "https://paygate.leshuazf.com/cgi-bin/lepos_pay_gateway.cgi";

    private $merchant_id = '';
    private $key = '';

    // 交易成功回调地址，如不接收回调不必提供
    private $callback_url = "";

    //测试 0000000018，交易密钥 a1613a0e7cb9d3a51e33784ee4d212ac   通知密钥 D4B7951B628632950AA035FB64BDFAAC
    //快唱商户 7514317365
    //上海七晟科技 0413119717   6E9C35FC1B420E55220B5D23B9D7B523   967EBF00E6B5181D49C517B1987005F2
    //广州歌神娱乐 8311110454
    public function __construct($pay_way = '',$jspay_flag='',$merchant_id = '8311110454', $key = '6E9C35FC1B420E55220B5D23B9D7B523') {
        if(!empty($merchant_id)){
            $this->merchant_id = $merchant_id;
        }
        if(!empty($key)){
            $this->key = $key;
        }
        if(!empty($pay_way)){
            $this->pay_way = $pay_way;
        }
        if(isset($jspay_flag)){
            $this->jspay_flag = $jspay_flag;
        }
    }

    /**
     * 生成指定金额的二维码，服务窗支付
     * 仅支持支付宝，微信已经停用此支付方式
     */
    public function getTdCode($arr=[]){
        $param = array(
            'merchant_id'=>$this->merchant_id,
            'service'=>'get_tdcode',
            'pay_way'=>$this->pay_way,
            'body'=>$arr['body'],
            'jspay_flag'=>$this->jspay_flag,
            'nonce_str'=>$this->nonce_str(),
            'sub_openid'=>$arr['sub_openid'],
            'third_order_id'=> $arr['third_order_id'],
            'amount'=>$arr['amount']*100,
            'notify_url'=>$arr['notify_url'],
            'jump_url'=>$arr['jump_url'],
            'order_expiration' => $arr['order_expiration'],
        );
        $param_str = $this->sign($param);

        //response
        $filecontent = $this->post($this->url, $param_str);

        // echo($filecontent).PHP_EOL;
        $re_obj = simplexml_load_string($filecontent,'SimpleXMLElement',LIBXML_NOCDATA );


        return $re_obj;
    }

    public function queryOrder($args){
        $param = array(
            'merchant_id'=>$this->merchant_id,
            'service'=>'query_status',
            'nonce_str'=>$this->nonce_str(),
            'leshua_order_id'=>$args['order'],
        );
        //request
        $param_str = $this->sign($param);
        //response
        $filecontent = $this->post($this->url, $param_str);
//         echo($filecontent).PHP_EOL;
        $re_obj = simplexml_load_string($filecontent,'SimpleXMLElement',LIBXML_NOCDATA );

        return $re_obj;
    }

    /**
     * 被扫支付，刷卡支付
     */
    public function upTdCode($amount, $order_id, $tdcode){
        $param = array('merchant_id'=>$this->merchant_id,
            'service'=>'upload_authcode',
            'pay_way'=>'WXZF',
            'body'=>'支付',
            'auth_code'=>$tdcode,
            'nonce_str'=>$this->nonce_str(),
            'third_order_id'=>$order_id,
            'amount'=>$amount,
            'notify_url'=>$this->callback_url,
            't0'=>0,'callback_url'=>$this->callback_url);
        $param_str = $this->sign($param);
        // echo($param_str.PHP_EOL);

        //response
        $filecontent = $this->post($this->url, $param_str);
        // echo($filecontent).PHP_EOL;
        $re_obj = simplexml_load_string($filecontent,'SimpleXMLElement',LIBXML_NOCDATA);
        // var_dump($re_obj);
        return $re_obj;
    }


    //退款
    public function refund($amount, $order_id){
        $param = array('merchant_id'=>$this->merchant_id,
            'service'=>'unified_refund',
            'nonce_str'=>$this->nonce_str(),
            'third_order_id'=>$order_id,
            'refund_amount'=>$amount,
        );

        $param['merchant_refund_id'] = $param['third_order_id'].'T1';
        $param_str = $this->sign($param);

        //response
        $filecontent = $this->post($this->url, $param_str);
        $re_obj = simplexml_load_string($filecontent,'SimpleXMLElement',LIBXML_NOCDATA);
        return $re_obj;
    }

    public function post($url, $data){
        $ch = curl_init();
        $header = array();
        $header[] = 'Content-Type:application/x-www-form-urlencoded';
        // echo $url.PHP_EOL;
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, 0);//设定是否输出页面内容
        //curl_setopt($ch,CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1); // post,get 过去
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,10);

        //curl_setopt($ch, CURLOPT_PROXY, '192.168.0.12'); //代理服务器地址
        //curl_setopt($ch, CURLOPT_PROXYPORT, 8888); //代理服务器端口
        //echo('post ' . $url . '?' . $data);
        $filecontent = curl_exec($ch);

        return $filecontent;
    }

    /**
     * 参数签名
     */
    private function sign($param){
        ksort($param);
        $mparam = $param;
        $queryParam_arr = array();
        foreach($param as $k=>$v){
            $queryParam_arr[] = $k."=".$v;
        }
        $queryParam = implode("&", $queryParam_arr);
        $sign = strtoupper(md5($queryParam.'&key='.$this->key));
        $mqueryParam_arr = array();
        foreach($mparam as $k=>$v){
            $mqueryParam_arr[$k] = $v;
        }
        $mqueryParam_arr['sign'] = $sign;

        // echo($sign);
        return  http_build_query($mqueryParam_arr);
    }

    /**
     *
     */
    private function nonce_str() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = strlen($characters);
        $nonce_str = '';
        for ($i = 0; $i < 16; $i++) {
            $nonce_str .= $characters[rand(0, $length - 1)];
        }
        return $nonce_str;
    }

    /*
     * 生成订单号
     */
    function generate_order_id() {
        return strftime('%Y%m%d%H%M%S', time()) . rand(1000000, 9000000);
    }


}


?>
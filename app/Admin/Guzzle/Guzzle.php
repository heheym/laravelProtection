<?php
/**
 * Created by PhpStorm.
 * User: xiaobaidaren
 * Date: 13/10/2020
 * Time: 16:41
 */
namespace App\Admin\Guzzle;

use GuzzleHttp\Client;

use Illuminate\Support\Facades\Log;


class Guzzle
{

    protected $wrap = "\n================================换行====================================";

    public function index($url= 'http://engine.91kcapp.com/api/agent/online/agentupdate',$json=['source' => 1,'agentid'=>1111,'agentname'=>'真心换真情','country'=>'中国','provinceCode'=>111,'provinceCode'=>111,'channelNo'=>'A000111','agentLevel'=>1,'parentAgentid'=>22])
    {
        $token = session('productToken');

        if(!isset($token)) {
            $token = $this->login();
        }
        $response = $this->curl($token,$url,$json);

        return json_decode($response,true);
    }

//接口授权
    public function login()
    {
        $url = env('ProductAgentApi').'/api/agent/online/login';
        $client = new Client();
        $response = $client->post( $url , [
            'json' => ['userno' => 'companyManager','password'=>111111],
        ]);
        $response = json_decode( $response->getBody(), true);
        // dd($response);
        if($response['code'] == 200 && isset($response['data']['token'])){
            session(['productToken'=>$response['data']['token']]);
        }else{
            Log::channel('productagent')->info("接口授权:\n数据:\n{$response},\n返回:\n{$response}".$this->wrap);
        }
        $this->token = session('productToken');
        return session('productToken');
    }

    public function curl($token,$url,$json)
    {
        $client = new Client();
        $response = $client->post( $url , [
            'headers' => ['Authorization'=>$token],
            'json' => $json,
        ]);
        return $response->getBody()->getContents();
    }


 //更新机顶盒接口
    public function boxupdate($data)
    {
        $token = session('productToken');

        if(!isset($token)) {
            $token = $this->login();
        }
        $url= env('ProductAgentApi').'/api/agent/online/boxupdate';
        $response = $this->curl($token,$url,$data);
        // Log::info($response);
        $responseArray = json_decode($response,true);

        if($responseArray['code']!=200){
            $data = json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
            // Log::channel('productagent')->info("更新机顶盒接口:\n数据:\n{$data},\n返回:\n{$response}".$this->wrap);
            admin_toastr($responseArray['msg'], 'error');
        }else{
            admin_toastr($responseArray['msg'], 'success');
        }

        return $responseArray;
    }

//更新场所状态接口
    public function placestate($data)
    {
        // Log::info($data);
        // dd(123);
        $token = session('productToken');

        if(!isset($token)) {
            $token = $this->login();
        }
        $url= env('ProductAgentApi').'/api/agent/online/placestate';
        $response = $this->curl($token,$url,$data);
        // Log::info($response);
        $responseArray = json_decode($response,true);

        if($responseArray['code']!=200){
            $data = json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
            Log::info("更新场所状态接口:\n数据:\n{$data},\n返回:\n{$response}".$this->wrap);
            admin_toastr($responseArray['msg'], 'error');
        }else{
            admin_toastr($responseArray['msg'], 'success');
        }

        return $responseArray;
    }








    //更新代理商充值码接口
    public function czcode($data)
    {
        $token = session('productToken');

        if(!isset($token)) {
            $token = $this->login();
        }
        $url = env('ProductAgentApi').'/api/agent/online/czcode';
        $response = $this->curl($token,$url,$data);
        $responseArray = json_decode($response,true);
        if($responseArray['code']!=200){
            $data = json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
            Log::channel('productagent')->info("更新代理商充值码接口:\n数据:\n{$data},\n返回:\n{$response}".$this->wrap);
        }
        return $responseArray;
    }
    
    //删除代理商
    public function agentdelete($agentid)
    {
        $token = session('productToken');

        if(!isset($token)) {
            $token = $this->login();
        }
        if(!isset($agentid)){
            throw new \Exception('agentid不能为空');
        }
        $url = env('ProductAgentApi').'/api/agent/online/agentdelete';
        $json = ['agentid'=>$agentid];
        $response = $this->curl($token,$url,$json);
        $responseArray = json_decode($response,true);
        if($responseArray['code']!=200){
            $data = json_encode($json,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
            Log::channel('productagent')->info("删除代理商接口:\n数据:\n{$data},\n返回:\n{$response}".$this->wrap);
        }
        return $responseArray;
    }

    //更新分销商
    public function boxupdateNextAgent($data)
    {
        $token = session('productToken');

        if(!isset($token)) {
            $token = $this->login();
        }
        $url = env('ProductAgentApi').'/api/agent/online/boxupdateNextAgent';

        $response = $this->curl($token,$url,$data);
        $responseArray = json_decode($response,true);
        if($responseArray['code']!=200){
            $data = json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
            Log::channel('productagent')->info("更新分销商:\n数据:\n{$data},\n返回:\n{$response}".$this->wrap);
        }
        return $responseArray;
    }

    //初始化机顶盒接口
    public function boxreset($KtvBoxid)
    {
        $token = session('productToken');

        if(!isset($token)) {
            $token = $this->login();
        }
        if(!isset($KtvBoxid)){
            throw new \Exception('KtvBoxid不能为空');
        }
        $url = env('ProductAgentApi').'/api/agent/online/boxreset';
        $json = ['KtvBoxid'=>$KtvBoxid];
        $response = $this->curl($token,$url,$json);
        $responseArray = json_decode($response,true);
        if($responseArray['code']!=200){
            $data = json_encode($json,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
            Log::channel('productagent')->info("初始化机顶盒接口:\n数据:\n{$data},\n返回:\n{$response}".$this->wrap);
        }
        return $responseArray;
    }

    //获取交易流水查询接口
    public function billrecord($json)
    {
        $token = session('productToken');

        if(!isset($token)) {
            $token = $this->login();
        }

        $url = env('ProductAgentApi').'/api/agent/online/billrecord';

        $response = $this->curl($token,$url,$json);
        $responseArray = json_decode($response,true);
        if($responseArray['code']!=200){
            $data = json_encode($json,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
            Log::channel('productagent')->info("获取交易流水查询接口:\n数据:\n{$data},\n返回:\n{$response}".$this->wrap);
        }
        return $responseArray;
    }





}
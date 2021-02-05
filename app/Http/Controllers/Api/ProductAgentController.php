<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache; //缓存
use App\Api\Sms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use zgldh\QiniuStorage\QiniuStorage;
use Qiniu\Auth;

use Illuminate\Support\Facades\Log;


class ProductAgentController extends Controller
{

//接口授权
    public function login(Request $request)
    {
        $post = json_decode(file_get_contents("php://input"), true);
        if(empty($post['userno']) || empty($post['password'])){
            return response()->json(['code' => 1015, 'msg' => '账号或密码不能为空', 'data' => '',"success"=> false]);
        }
        $exist = DB::table('apiuser')->where(['userno'=>$post['userno'],'password'=>$post['password']])->exists();
        if(!$exist){
            return response()->json(['code' => 1015, 'msg' => '账号或密码错误', 'data' => '',"success"=> false]);
        }
        $token = str_random(60);
        DB::table('apiuser')->where(['userno'=>$post['userno'],'password'=>$post['password']])->update(['token'=>$token]);
        $arr = Cache::get('productToken')!=null?Cache::get('productToken') :[];
        array_push($arr,$token);
        Cache::forever('productToken', $arr);
        return response()->json(['code' => 200, 'data' => ['account'=>$post['userno'],'token'=>$token],"msg"=> '请求成功','success'=>true]);
    }

 //更新代理商接口
    public function agentupdate(Request $request)
    {
        // $exists = DB::table('agenttable')->get();
        // dd($exists);
        // Log::info(123);
        $token = \Request::header('Authorization');

        if(empty($token) || empty(Cache::get('productToken')) || !in_array($token,Cache::get('productToken'))){
            return response()->json(['code' => 1015, 'msg' => 'token无效', 'data' => '',"success"=> false]);
        }

        $post = json_decode(file_get_contents("php://input"), true);
        // Log::info($post);
        try{
            $data = ['agentid'=>$post['agentid'],'agentNo'=>$post['agentNo'],'agentName'=>$post['agentname'],
                    'country'=>$post['country'],'province_code'=>$post['provinceCode'],'city_code'=>$post['cityCode'],
                    'isappChanne'=>$post['isAppChannel'],'ChannelNo'=>$post['channelNo'],
                    'agentLevel'=>$post['agentLevel'], 'parentAgentid'=>$post['parentAgentid']];

            $exists = DB::table('agenttable')->where('agentid',$post['agentid'])->exists();

            // Log::info($data);
            if(!$exists){
                DB::table('agenttable')->insert($data);
            }else{
                DB::table('agenttable')->where('agentid',$post['agentid'])->update($data);
            }
        }catch (\Exception $e){
            return response()->json(['code' => 1015, 'msg' => $e->getMessage(), 'data' => '',"success"=> false]);
        }
        return response()->json(['code' => 200, 'data' => '','msg' => '请求成功', "success"=> true]);
    }

//更新机顶盒接口
    public function boxupdate(Request $request)
    {

        $token = \Request::header('Authorization');

        if(empty($token) || empty(Cache::get('productToken')) || !in_array($token,Cache::get('productToken'))){
            return response()->json(['code' => 1015, 'msg' => 'token无效', 'data' => '',"success"=> false]);
        }

        $post = json_decode(file_get_contents("php://input"), true);
        // Log::info($post);
        try{
            if(isset($post['isdelete']) && $post['isdelete']==1){
                DB::table('boxregister')->where('KtvBoxid',$post['KtvBoxid'])->delete();
            }else{
                $exists = DB::table('boxregister')->where('KtvBoxid',$post['KtvBoxid'])->exists();
                if(!$exists){
                    DB::table('boxregister')->insert($post);
                }else{
                    DB::table('boxregister')->where('KtvBoxid',$post['KtvBoxid'])->update($post);
                }
            }
        }catch (\Exception $e){
            return response()->json(['code' => 1015, 'msg' => $e->getMessage(), 'data' => '',"success"=> false]);
        }
        return response()->json(['code' => 200, 'data' => '','msg' => '请求成功', "success"=> true]);
    }

//删除代理商
    public function agentdelete(Request $request)
    {
        $token = \Request::header('Authorization');
        if(empty($token) || empty(Cache::get('productToken')) || !in_array($token,Cache::get('productToken'))){
            return response()->json(['code' => 1015, 'msg' => 'token无效', 'data' => '',"success"=> false]);
        }
        $post = json_decode(file_get_contents("php://input"), true);
        // Log::info($post);
        try{
            DB::table('agenttable')->where('agentid',$post['agentid'])->delete();
        }catch (\Exception $e){
            return response()->json(['code' => 1015, 'msg' => $e->getMessage(), 'data' => '',"success"=> false]);
        }
        return response()->json(['code' => 200, 'data' => '','msg' => '请求成功', "success"=> true]);
    }

//更新场所资料接口
    public function placeupdate(Request $request)
    {
        $token = \Request::header('Authorization');
        if(empty($token) || empty(Cache::get('productToken')) || !in_array($token,Cache::get('productToken'))){
            return response()->json(['code' => 1015, 'msg' => 'token无效', 'data' => '',"success"=> false]);
        }
        $post = json_decode(file_get_contents("php://input"), true);
        // Log::info($post);
        try{
            $exists = DB::table('place')->where('userno',$post['placeno'])->exists();
            $post['userno'] = $post['placeno']; //场所编号
            $post['roomtotal'] = $post['boxtotal']; //机顶盒数量
            unset($post['placeno']);
            unset($post['boxtotal']);
            if(!$exists){
                $post['key'] = strtoupper(str_random(12));
                DB::table('place')->insert($post);
            }else{
                $key = DB::table('place')->where('userno',$post['userno'])->value('key');
                if(empty($key)){
                    $post['key'] = strtoupper(str_random(12));
                }
                DB::table('place')->where('userno',$post['userno'])->update($post);
            }
        }catch (\Exception $e){
            return response()->json(['code' => 1015, 'msg' => $e->getMessage(), 'data' => '',"success"=> false]);
        }
        return response()->json(['code' => 200, 'data' => '','msg' => '请求成功', "success"=> true]);
    }



}




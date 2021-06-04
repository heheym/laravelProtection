<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Mockery\Exception;
use zgldh\QiniuStorage\QiniuStorage;

class MalaiController extends Controller
{


    //14.版本管理
    public function softVer(Request $request)
    {
        $post = json_decode(file_get_contents("php://input"), true);

        if(empty($post['updateVerNo'])){
            return response()->json(['code'=>500,'msg'=>'版本号不能为空','data'=>null]);
        }

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        if(!empty($post['updateVerSqlHttp'])){
            $post['updateSqlAddress'] = $protocol.$domainName."/uploads/song/".$post['updateVerSqlHttp'];
        }
        if(!empty($post['updateVerDbHttp'])){
            $post['updateDbAddress'] = $protocol.$domainName."/uploads/song/".$post['updateVerDbHttp'];
        }

        $data = DB::table('softmanage')->get();
        if(!$data){
            try{
                DB::table('softmanage')->insert($post);
            }catch (\Exception $e){
                return response()->json(['code'=>500,'msg'=>$e->getMessage(),'data'=>null]);
            }
        }else{
            try{
                DB::table('softmanage')->update($post);
            }catch (\Exception $e){
                return response()->json(['code'=>500,'msg'=>$e->getMessage(),'data'=>null]);
            }
        }

        return response()->json(['code'=>200,'msg'=>'请求成功','data'=>null]);
    }


    //14.获取版本下载地址
    public function getsoftver(Request $request)
    {
        $data = DB::table('softmanage')->select(['updateVerNo','updateVerMemo','updateSqlAddress','updateDbAddress'])->first();
        return response()->json(['code'=>200,'msg'=>'请求成功','data'=>$data]);
    }


}

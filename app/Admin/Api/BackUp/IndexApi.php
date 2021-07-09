<?php

namespace App\Admin\Api\BackUp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class IndexApi extends Controller
{


    //1.获取列表
    public function getlist(Request $request)
    {
        $post = json_decode(file_get_contents("php://input"), true);

        $signature = \Request::header('signature');
        if(empty($signature)){
            return response()->json(['code'=>500,'msg'=>'signature不能为空','data'=>null]);
        }
        if(empty($post['timestamp'])){
            return response()->json(['code'=>500,'msg'=>'timestamp不能为空','data'=>null]);
        }

        $signature1 = md5($post['timestamp'].'20210326f5ce6dce860673c2b0ecabcde');
        if(strtolower($signature) !== strtolower($signature1)){
            return response()->json(['code'=>500,'msg'=>'signature不合法','data'=>null]);
        }


        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];

        $data = DB::table('backup')->select('backupVerNo','backupDownLoadHttp')->where('isDownload',0)->get();

        return response()->json(['code'=>200,'msg'=>'请求成功','data'=>$data]);
    }


    //2.是否下载
    public function isdownload(Request $request)
    {
        $post = json_decode(file_get_contents("php://input"), true);

        $signature = \Request::header('signature');
        if(empty($signature)){
            return response()->json(['code'=>500,'msg'=>'signature不能为空','data'=>null]);
        }
        if(empty($post['timestamp'])){
            return response()->json(['code'=>500,'msg'=>'timestamp不能为空','data'=>null]);
        }

        $signature1 = md5($post['timestamp'].'20210326f5ce6dce860673c2b0ecabcde');
        if(strtolower($signature) !== strtolower($signature1)){
            return response()->json(['code'=>500,'msg'=>'signature不合法','data'=>null]);
        }

        if(empty($post['backupVerNo'])){
            return response()->json(['code'=>500,'msg'=>'backupVerNo不能为空','data'=>null]);
        }
        try{
            $exists = DB::table('backup')->where('backupVerNo',$post['backupVerNo'])->exists();
            if(!$exists){
                return response()->json(['code'=>500,'msg'=>'backupVerNo不合法','data'=>null]);
            }
            DB::table('backup')->where('backupVerNo',$post['backupVerNo'])->update(['isDownload'=>1,'downloadTime'=>date('Y-m-d H:i:s')]);
            if(file_exists('./backup/'.$post['backupVerNo'])){
                unlink('./backup/'.$post['backupVerNo']);
            }
        }catch (\Exception $e){
            return response()->json(['code'=>500,'msg'=>$e->getMessage(),'data'=>null]);
        }

        return response()->json(['code'=>200,'msg'=>'请求成功']);
    }


}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Mockery\Exception;
use zgldh\QiniuStorage\QiniuStorage;

class SongController extends Controller
{

    //2.添加高危歌曲
    public function addSongs(Request $request)
    {

        $post = json_decode(file_get_contents("php://input"), true);

        if ($post) {
            foreach ($post as $k => $v) {
                try {
                    $exists = DB::table('song_rights')->where('musicdbpk', $v['musicdbpk'])->exists();
                    if ($exists) {
                        DB::table('song_rights')->where('musicdbpk', $v['musicdbpk'])->update($v);
                    } else {
                        $result = DB::table('song_rights')->insert($v);
                    }
                }catch (\Exception $e){
                    return response()->json(['code' => 500, 'msg' => '传入信息出错', 'data' => null]);
                }
            }
            return response()->json(['code' => 200, 'msg' => '请求成功', 'data' => null]);
        }else{
            return response()->json(['code' => 500, 'msg' => '传入信息出错', 'data' => null]);
        }
    }

    //3.修改高危歌曲
    public function modifySongs(Request $request)
    {

        $post = json_decode(file_get_contents("php://input"), true);
        if(!empty($post['editField'])){
            try {
                $data['musicdbpk'] = $post['musicdbpk'];
                $data[$post['editField']] = $post['editValue'];

                $result = DB::table('song_rights')->where('musicdbpk', $data['musicdbpk'])->update($data);
            }catch (\Exception $e){
                return response()->json(['code'=>500,'msg'=>$e->getMessage(),'data'=>null]);
            }
            return response()->json(['code'=>200,'msg'=>'请求成功','data'=>null]);
        }else{
            return response()->json(['code'=>500,'msg'=>'传入信息出错','data'=>null]);
        }
    }

    //4.添加禁播歌曲
    public function addSongbanned()
    {
        $post = json_decode(file_get_contents("php://input"), true);

        if($post){
            foreach ($post as $k => $v) {
                try{
                    $exists = DB::table('song_banned')->where('musicdbpk', $v['musicdbpk'])->exists();
                    if ($exists) {
                        DB::table('song_banned')->where('musicdbpk', $v['musicdbpk'])->update($v);
                    } else {
                        $result = DB::table('song_banned')->insert($v);
                    }
                }catch (\Exception $e){
                    return response()->json(['code'=>500,'msg'=>$e->getMessage(),'data'=>null]);
                }

            }
            return response()->json(['code'=>200,'msg'=>'请求成功','data'=>null]);
        }else{
            return response()->json(['code'=>500,'msg'=>'传入信息出错','data'=>null]);
        }
    }

    //5.客户点播歌曲上传接口
    public function songsUpload()
    {
        $user = Auth::guard('api')->user();
        $post = json_decode(file_get_contents("php://input"), true);
        if($post){
            foreach($post as $k=>$v){
        $exists = DB::table("song_banned")->where(["songname"=>$v["songname"],"singer"=>$v["singer"]])->exists();
                if($exists){
                    $post[$k]["rurposetype"] = 2;
                }else{
                    $exists = DB::table("song_rights")->where(["songname"=>$v["songname"],"singer"=>$v["singer"],"langtype"=>$v["singer"]])->exists();
                    if($exists){
                        $post[$k]["rurposetype"] = 1;
                    }else{
                        $post[$k]["rurposetype"] = 0;
                    }
                }
                $post[$k]["userid"] = $user->id;
                $post[$k]["create_date"] = date("Y-m-d H:i:s");
            }
            $result = DB::table("users_songs")->insert($post);

            $data = [];
            foreach($post as $kk=>$vv){
                $data[$kk]['songkey'] = $vv['songkey'];
                $data[$kk]['state'] = $vv['rurposetype'];
            }
            if($result){
                return response()->json(['code'=>200,'msg'=>'请求成功','data'=>$data]);
            }
            return response()->json(['code'=>500,'msg'=>'请求失败','data'=>null]);
        }else{
            return response()->json(['code'=>500,'msg'=>'传入信息出错','data'=>null]);
        }
    }

    //6.场所预警接口
    public function warningSong()
    {
        $user = Auth::guard('api')->user();
        $date = date("Y-m-d H:i:s");

        $post = json_decode(file_get_contents("php://input"), true);

        $wangMode = DB::table('users')->where('id',$user->id)->value('wangMode');
        $warnMode = DB::table('warningMode')->where('id',$wangMode)->first();
        $warnMode = get_object_vars($warnMode);

        $warningTime = $warnMode['warningTime'];
        $warningCountRoom = $warnMode['warningCountRoom'];
        $date1 = date("Y-m-d H:i:s",strtotime("-".$warningTime."seconds"));


        $warningTime1 = $warnMode['warningTime1'];
        $warningCountRoom1 = $warnMode['warningCountRoom1'];
        $date2 = date("Y-m-d H:i:s",strtotime("-".$warningTime1."seconds"));


        if($post){  //有传房间号
            $sql = "select roomno,count(*) as count from users_songs where userid= ".$user->id." group by roomno";
            $sql1 = "select roomno as roomno1,count(*) as count1 from users_songs where userid= ".$user->id." and create_date >= '$date1' AND  create_date <= '$date' and rurposetype > 0 group by roomno ";
            $sql2 = "select roomno as roomno2,count(*) as count2 from users_songs where userid= ".$user->id." and create_date >= '$date2' AND  create_date <= '$date' and rurposetype > 0 group by roomno ";

            $sql3 = "select * from ($sql) a left join ($sql1) b on a.roomno=b.roomno1 left join ($sql2) c on a.roomno=c.roomno2";

            $result3 = DB::select($sql3);

            $tempArray = [];

            foreach($post as $v){
                array_push($tempArray,$v['roomno']);
            }

            $post = [];

            $sum = 0;  //房间预警数
            $place = 0;  //只要房间有预警2，场所就预警2

            foreach($result3 as $kk=>$vv){
                if($vv->count2>=$warningCountRoom1){
                    if(in_array($vv->roomno,$tempArray)){
                        $post[$kk]['roomno'] = $vv-> roomno;
                        $post[$kk]['roomstate'] = 2 ;
                        $place = 2;
                    }
                    $sum++;
                }elseif($vv->count1>=$warningCountRoom){
                    if(in_array($vv->roomno,$tempArray)){
                        $post[$kk]['roomno'] = $vv-> roomno;
                        $post[$kk]['roomstate'] = 1  ;
                    }
                    $sum++;
                }else{
                    if(in_array($vv->roomno,$tempArray)){
                        $post[$kk]['roomno'] = $vv-> roomno;
                        $post[$kk]['roomstate'] = 0 ;
                    }
                }
            }
            $placestate = 0;
            if($sum==1){
                $placestate = 1;
            }elseif($sum>1){
                $placestate = 2;
            }
            if($place>0){
                $placestate = 2;
            }
            sort($post);
        }else{  //没有传房间号
         $sql = "select roomno,count(*) as count from users_songs where userid= ".$user->id." group by roomno";
            $sql1 = "select roomno as roomno1,count(*) as count1 from users_songs where userid= ".$user->id." and create_date >= '$date1' AND  create_date <= '$date' and rurposetype > 0 group by roomno ";
            $sql2 = "select roomno as roomno2,count(*) as count2 from users_songs where userid= ".$user->id." and create_date >= '$date2' AND  create_date <= '$date' and rurposetype > 0 group by roomno ";

            $sql3 = "select * from ($sql) a left join ($sql1) b on a.roomno=b.roomno1 left join ($sql2) c on a.roomno=c.roomno2";

            $result3 = DB::select($sql3);

            $post = [];
            $sum = 0;
            $place = 0;  //只要房间有预警2，场所就预警2

            foreach($result3 as $kk=>$vv){

                $post[$kk]['roomno'] = $vv-> roomno;
                if($vv->count2>=$warningCountRoom1){
                    $post[$kk]['roomstate'] = 2 ;
                    $place = 2;
                    $sum++;

                }elseif($vv->count1>=$warningCountRoom){
                    $post[$kk]['roomstate'] = 1  ;
                    $sum++;
                }else{
                    $post[$kk]['roomstate'] = 0 ;
                }
            }
            $placestate = 0;
            if($sum==1){
                $placestate = 1;
            }elseif($sum>1){
                $placestate = 2;
            }
            if($place>0){
                $placestate = 2;
            }
        }
        return response()->json(['code'=>200,'placestate'=>$placestate,'data'=>$post]);
    }

    //7.删除歌曲
    public function delSongs()
    {
        $post = json_decode(file_get_contents("php://input"), true);
        if(!empty($post['musicdbpk'])&&!empty($post['deltype'])){
            if($post["deltype"]==1){  //高危歌曲
                $exists = DB::table('song_rights')->where('musicdbpk',$post['musicdbpk'])->exists();
                if(!$exists){
                    return response()->json(['code'=>500,'msg'=>'歌曲不存在','data'=>null]);
                }
                $result = DB::table('song_rights')->where('musicdbpk',$post['musicdbpk'])->delete();
                if($result){
                    return response()->json(['code'=>200,'msg'=>'请求成功','data'=>null]);
                }
                return response()->json(['code'=>500,'msg'=>'请求失败','data'=>null]);
            }else{  //禁播歌曲
                $exists = DB::table('song_banned')->where('musicdbpk',$post['musicdbpk'])->exists();
                if(!$exists){
                    return response()->json(['code'=>500,'msg'=>'歌曲不存在','data'=>null]);
                }
                $result = DB::table('song_banned')->where('musicdbpk',$post['musicdbpk'])->delete();
                if($result){
                    return response()->json(['code'=>200,'msg'=>'请求成功','data'=>null]);
                }
                return response()->json(['code'=>500,'msg'=>'请求失败','data'=>null]);
            }
        }else{
            return response()->json(['code'=>500,'msg'=>'传入信息出错','data'=>null]);
        }
    }

    //8.歌曲入库serviceAddSongs
    public function serviceAddSongs(Request $request)
    {
        $post = json_decode(file_get_contents("php://input"), true);

        if ($post) {
            foreach ($post as $k => $v) {
                try {
                    $exists = DB::table('song')->where('musicdbpk', $v['musicdbpk'])->exists();
                    $v['UpdateDate'] = date('Y-m-d H:i:s');
                    if ($exists) {
                        DB::table('song')->where('musicdbpk', $v['musicdbpk'])->update($v);
                    } else {
                        $result = DB::table('song')->insert($v);
                    }
                }catch (\Exception $e){
                    return response()->json(['code' => 500, 'msg' => '传入信息出错', 'data' => $e->getMessage()]);
                }
            }
            return response()->json(['code' => 200, 'msg' => '请求成功', 'data' => null]);
        }else{
            return response()->json(['code' => 500, 'msg' => '传入信息出错', 'data' => null]);
        }
    }

    //9.修改歌曲serviceModifySongs
    public function serviceModifySongs(Request $request)
    {

        $post = json_decode(file_get_contents("php://input"), true);
        if(!empty($post['editField'])){
            try {
                $data['musicdbpk'] = $post['musicdbpk'];
                $data[$post['editField']] = $post['editValue'];

                $exists = DB::table('song')->where('musicdbpk', $data['musicdbpk'])->exists();
                if(!$exists){
                    return response()->json(['code'=>500,'msg'=>'歌曲不存在','data'=>null]);
                }

                $result = DB::table('song')->where('musicdbpk', $data['musicdbpk'])->update($data);
            }catch (\Exception $e){
                return response()->json(['code'=>500,'msg'=>$e->getMessage(),'data'=>null]);
            }
            return response()->json(['code'=>200,'msg'=>'请求成功','data'=>null]);
        }else{
            return response()->json(['code'=>500,'msg'=>'传入信息出错','data'=>null]);
        }
    }

    //10.添加歌星serviceAddSinger
    public function serviceAddSinger(Request $request)
    {
        $post = json_decode(file_get_contents("php://input"), true);

        if ($post) {
            foreach ($post as $k => $v) {
                try {
                    $exists = DB::table('singer')->where('id', $v['id'])->exists();
                    $v['UpdateDate'] = date('Y-m-d H:i:s');
                    if ($exists) {
                        DB::table('singer')->where('id', $v['id'])->update($v);
                    } else {
                        $result = DB::table('singer')->insert($v);
                    }
                }catch (\Exception $e){
                    return response()->json(['code' => 500, 'msg' => '传入信息出错', 'data' => $e->getMessage()]);
                }
            }
            return response()->json(['code' => 200, 'msg' => '请求成功', 'data' => null]);
        }else{
            return response()->json(['code' => 500, 'msg' => '传入信息出错', 'data' => null]);
        }
    }

    //11.修改歌星serviceModifySinger
    public function serviceModifySinger(Request $request)
    {
        $post = json_decode(file_get_contents("php://input"), true);
        if(!empty($post['editField'])){
            try {
                $data['id'] = $post['id'];
                $data[$post['editField']] = $post['editValue'];
                $exists = DB::table('singer')->where('id', $data['id'])->exists();
                if(!$exists){
                    return response()->json(['code'=>500,'msg'=>'歌手不存在','data'=>null]);
                }
                DB::table('singer')->where('id', $data['id'])->update($data);
            }catch (\Exception $e){
                return response()->json(['code'=>500,'msg'=>$e->getMessage(),'data'=>null]);
            }
            return response()->json(['code'=>200,'msg'=>'请求成功','data'=>null]);
        }else{
            return response()->json(['code'=>500,'msg'=>'传入信息出错','data'=>null]);
        }
    }

    //12.歌曲上下架接口serviceChangeSongsStatus
    public function serviceChangeSongsStatus(Request $request)
    {
        $post = json_decode(file_get_contents("php://input"), true);
        if(!empty($post['musicdbpk'])){
            try {
                $data['musicdbpk'] = $post['musicdbpk'];
                $data['onlineStatus'] = $post['onlineStatus'];
                $exists = DB::table('song')->where('musicdbpk', $data['musicdbpk'])->exists();
                if(!$exists){
                    return response()->json(['code'=>500,'msg'=>'歌曲不存在','data'=>null]);
                }
                DB::table('song')->where('musicdbpk', $data['musicdbpk'])->update($data);
            }catch (\Exception $e){
                return response()->json(['code'=>500,'msg'=>$e->getMessage(),'data'=>null]);
            }
            return response()->json(['code'=>200,'msg'=>'请求成功','data'=>null]);
        }else{
            return response()->json(['code'=>500,'msg'=>'传入信息出错','data'=>null]);
        }
    }

    //13.删除歌星接口serviceDelSinger
    public function serviceDelSinger(Request $request)
    {
        $post = json_decode(file_get_contents("php://input"), true);
        if(!empty($post['id'])){
            $exists = DB::table('singer')->where('id',$post['id'])->exists();
            if(!$exists){
                return response()->json(['code'=>500,'msg'=>'歌星不存在','data'=>null]);
            }
            try {
                DB::table('singer')->where('id', $post['id'])->delete();
            }catch (\Exception $e){
                return response()->json(['code'=>500,'msg'=>$e->getMessage(),'data'=>null]);
            }
            return response()->json(['code'=>200,'msg'=>'请求成功','data'=>null]);
        }else{
            return response()->json(['code'=>500,'msg'=>'歌星id不能为空','data'=>null]);
        }
    }

    //14.更新版本接口serviceUpdateVer
    public function serviceUpdateVer(Request $request)
    {
        $post = json_decode(file_get_contents("php://input"), true);
        if(!empty($post['updateType'])){
            $array = [];
            if($post['updateType'] ==1){
                if(isset($post['updateVerNo'])){
                    $array['SoftseverVer'] = $post['updateVerNo'];
                }
                if(isset($post['updateVerHttp'])){
                    $array['SoftseverHttp'] = $post['updateVerHttp'];
                }
                if(isset($post['updateVerMemo'])){
                    $array['SoftseverMemo'] = $post['updateVerMemo'];
                }
                DB::table('parameterset')->update($array);
            }elseif($post['updateType'] ==2){
                if(isset($post['updateVerNo'])){
                    $array['SoftboxVer'] = $post['updateVerNo'];
                }
                if(isset($post['updateVerHttp'])){
                    $array['SoftboxHttp'] = $post['updateVerHttp'];
                }
                if(isset($post['updateVerMemo'])){
                    $array['SoftboxMemo'] = $post['updateVerMemo'];
                }
                DB::table('parameterset')->update($array);
            }elseif($post['updateType'] ==3){
                if(isset($post['updateVerNo'])){
                    $array['SoftsongDbVer'] = $post['updateVerNo'];
                }
                if(isset($post['updateVerHttp'])){
                    $array['SoftsongDbHttp'] = $post['updateVerHttp'];
                }
                DB::table('parameterset')->update($array);
            }else{
                return response()->json(['code'=>500,'msg'=>'updateType不存在','data'=>null]);
            }
            return response()->json(['code'=>200,'msg'=>'请求成功','data'=>null]);
        }else{
            return response()->json(['code'=>500,'msg'=>'updateType不存在','data'=>null]);
        }
    }

    //15.机顶盒预登记接口
    public function addRegbox(Request $request)
    {
        $post = json_decode(file_get_contents("php://input"), true);
        if(empty($post['KtvBoxid'])){
            return response()->json(['code'=>500,'msg'=>'KtvBoxid不能为空','data'=>null]);
        }
        $exists = DB::table('boxregister')->where('KtvBoxid',$post['KtvBoxid'])->exists();
        if($exists){
            return response()->json(['code'=>500,'msg'=>'机器码已经存在','data'=>null]);
        }
        try{
            $data = ['KtvBoxid'=>$post['KtvBoxid'],'CreateDate'=>date('Y-m-d H:i:s')];
            if(!empty($post['machineCode'])){
                $data['machineCode'] = $post['machineCode'];
            }
            DB::table('boxregister')->insert($data);
        }catch (\Exception $e){
            return response()->json(['code'=>500,'msg'=>$e->getMessage(),'data'=>null]);
        }
        return response()->json(['code'=>200,'msg'=>'请求成功','data'=>null]);
    }

}

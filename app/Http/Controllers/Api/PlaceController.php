<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache; //缓存
use App\Api\Sms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use zgldh\QiniuStorage\QiniuStorage;
use Qiniu\Auth;


class PlaceController extends Controller
{

    //服务端信息更新
    public function regsrv(Request $request)
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

//        file_put_contents("1.txt",file_get_contents("php://input"));
//        var_dump(file_get_contents("php://input"));

        if(empty($post['placehd'])){
            return response()->json(['code' => 500, 'msg' => '场所服务器ID不能为空', 'data' => null]);
        }
        $placehdExsist = DB::table('place')->where(['placehd'=>$post['placehd']])->where('key','<>',$srvkey)->exists();
        if($placehdExsist){
            return response()->json(['code' => 500, 'msg' => '场所服务器ID已存在', 'data' => null]);
        }

        try{
            DB::table('place')->where('key', $srvkey)->update($post);
        }catch (\Exception $e){
            return response()->json(['code' => 500, 'msg' => '传入信息出错', 'data' => $e->getMessage()]);
        }

        return response()->json(['code' => 200, 'msg' => '请求成功', 'data' => null]);

    }

    //机顶盒注册
    public function regbox(Request $request)
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
        if(empty($post['KtvBoxid'])){
            return response()->json(['code' => 500, 'msg' => 'KtvBoxid不能为空', 'data' => null]);
        }
    $exists = DB::table('settopbox')->where(['key'=>$srvkey,'KtvBoxid'=>$post['KtvBoxid']])->exists(); //已存在srvkey和ktvBoxid
        if($exists){
            DB::table('settopbox')->where(['key'=>$srvkey,'KtvBoxid'=>$post['KtvBoxid']])->update($post);
        }else{
            $boxRegisterExist = DB::table('boxregister')->where('KtvBoxid',$post['KtvBoxid'])->exists();
            if(!$boxRegisterExist){
                return response()->json(['code' => 500, 'msg' => '机器码未登记', 'data' => null]);
            }
            $exist = DB::table('settopbox')->where('key','<>',$srvkey)->where(['KtvBoxid'=>$post['KtvBoxid']])->exists();
            if($exist){
                return response()->json(['code' => 500, 'msg' => '机器码已存在', 'data' => null]);
            }
            if(!empty($post['machineCode'])){
            $exist = DB::table('settopbox')->where('key','<>',$srvkey)->where(['machineCode'=>$post['machineCode']])->exists();
                if($exist){
                    return response()->json(['code' => 500, 'msg' => '机顶盒MAC已存在', 'data' => null]);
                }
            }

            //判断是否超出有效数量
            $count1 = DB::table('settopbox')->where(['key'=> $srvkey])
                ->where(function ($query) {
                    $query->where('KtvBoxState',0)
                        ->orWhere('KtvBoxState', 1);
                })->count();
            $count2 = $count1+1;
            $roomtotal = DB::table('place')->where(['key'=> $srvkey])->value('roomtotal');
            if($count2>$roomtotal){
                return response()->json(['code' => 500, 'msg' => '机顶盒超过有效数量', 'data' => null]);
            }
            $machineCode = DB::table('boxregister')->where('KtvBoxid',$post['KtvBoxid'])->value('machineCode');
            $post['created_date'] = date('Y-m-d H:i:s');
            $post['key'] = $srvkey;
            $post['machineCode'] = $machineCode;

            try{
                DB::table('settopbox')->insert($post);
            }catch (\Exception $e){
                return response()->json(['code' => 500, 'msg' => $e->getMessage(), 'data' => null]);
            }
        }
        return response()->json(['code' => 200, 'msg' => '请求成功', 'data' => null]);
    }

    //服务端数据查询接口
    public function qrysrvmsg(Request $request)
    {

        $srvkey = \Request::header('srvkey');
        if(empty($srvkey)){
            return response()->json(['code' => 500, 'msg' => '场所key错误', 'data' => null]);
        }
        $exists = DB::table('place')->where(['key'=>$srvkey])->exists();
        if(!$exists){
            return response()->json(['code' => 500, 'msg' => 'key不存在', 'data' => null]);
        }
//        $post = json_decode(urldecode(file_get_contents("php://input")), true);
//        if(!$post){
//            return response()->json(['code' => 500, 'msg' => '传入信息出错', 'data' => null]);
//        }

        try{
//           $result = DB::table('place')->where('key', $srvkey)->select('roomtotal','expiredata','status','placehd','downloadMode','boxPass','FeesMode','Opening_price','Effective_time')->first();
           $result = DB::select("select * from place where place.key='$srvkey'");
           $result = $result[0];

        }catch (\Exception $e){
            return response()->json(['code' => 500, 'msg' => '传入信息出错', 'data' => $e->getMessage()]);
        }

        $data = DB::select("select settopbox.KtvBoxid,settopbox.machineCode,settopbox.KtvBoxState,settopbox.roomno,settopbox.FeesMode,settopbox.Opening1_time,settopbox.Opening1_price,settopbox.Effective1_time,settopbox.Opening2_time,settopbox.Opening2_price,settopbox.Effective2_time from settopbox  where settopbox.key='$srvkey' order by KtvBoxState desc,roomno asc");
        foreach($data as $k=>$v){
            if($v->FeesMode==0){
                $data[$k]->FeesMode = $result->FeesMode;
                $data[$k]->Opening1_time = $result->Opening1_time;
                $data[$k]->Opening1_price = $result->Opening1_price;
                $data[$k]->Effective1_time = $result->Effective1_time;
                $data[$k]->Opening2_time = $result->Opening2_time;
                $data[$k]->Opening2_price = $result->Opening2_price;
                $data[$k]->Effective2_time = $result->Effective2_time;
            }
        }

        $t1 = $result->expiredata;//你自己设置一个开始时间
        $t2 = date('Y-m-d H:i:s');//获取当前时间, 格式和$t1一致

        $t = strtotime($t1) - strtotime($t2);//拿当前时间-开始时间 = 相差时间
        $t = ceil($t/(3600*24));//此时间单位为 天
//        $t = $t/(3600*24);//此时间单位为 天
        if($t<0){
            $t=0;
        }
//        array_multisort(array_column($data, 'roomno'), SORT_ASC, $data);

        return response()->json(['code' => 200, 'roomtotal' => $result->roomtotal, 'expiredata' => $result->expiredata,
            'remainday'=>$t,'placehd'=>$result->placehd,'isenabled'=>$result->status,'boxPass'=>$result->boxPass,'downloadMode'=>$result->downloadMode,'apkUpdateMode'=>$result->apkUpdateMode,
            'isclosePingfen'=>$result->isclosePingfen,
            'iscloseSound'=>$result->iscloseSound,
            'FeesMode'=>$result->FeesMode,
            'Opening1_time'=>$result->Opening1_time,'Opening1_price'=>$result->Opening1_price,'Effective1_time'=>$result->Effective1_time,
            'Opening2_time'=>$result->Opening2_time,'Opening2_price'=>$result->Opening2_price,'Effective2_time'=>$result->Effective2_time,
            'warningRoomcount'=>$result->warningRoomcount,
            'warningCutsongcount'=>$result->warningCutsongcount,
            'data'=>$data]);
    }

    //机顶盒数据查询接口
    public function qryboxmsg(Request $request)
    {
        $KtvBoxid = \Request::header('KtvBoxid');
        if(empty($KtvBoxid)){
            return response()->json(['code' => 500, 'msg' => 'KtvBoxid不能为空', 'data' => null]);
        }
        try{
//           $result = DB::table('settopbox')->where('KtvBoxid', $KtvBoxid)
//               ->select('KtvBoxid','machineCode','KtvBoxState','roomno')->first();
            $settopboxExist = DB::table('settopbox')->where('KtvBoxid', $KtvBoxid)->exists();
            if($settopboxExist){
                return response()->json(['code' => 200, 'boxregState'=>1]);
            }
            $boxregisterExist = DB::table('boxregister')->where('KtvBoxid', $KtvBoxid)->exists();
            if($boxregisterExist){
                return response()->json(['code' => 200, 'boxregState'=>0]);
            }
            return response()->json(['code' => 500, 'msg'=>'机顶盒未登记','data'=>null]);

        }catch (\Exception $e){
            return response()->json(['code' => 500, 'msg' => '传入信息出错', 'data' => $e->getMessage()]);
        }

    }

    //机顶盒点播歌曲上传接口
    public function songWarning(Request $request)
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
        if(!is_array( $post )){
            return response()->json(['code' => 500, 'msg' => '数据出错', 'data' => $post]);
        }
        foreach($post as $k=>$v){
            if(empty($v['KtvBoxid'])||empty($v['musicdbpk'])){
                return response()->json(['code' => 500, 'msg' => '机器码或歌曲主键不能为空', 'data' => null]);
            }
            $post[$k]['srvkey'] = $srvkey;
            $post[$k]['KtvBoxid'] = trim($v['KtvBoxid']);
            $post[$k]['UploadDate'] = date('Y-m-d H:i:s');
//            $post[$k]['State'] = 0;
//
//            $song = DB::table('song')->where('musicdbpk',$v['musicdbpk'])->first();
//            if(empty($song)){
//                return response()->json(['code' => 500, 'msg' => 'musicdbpk不存在', 'data' => null]);
//            }
//            $exists = DB::table('song')->where(['Singer'=>$song->Singer,'Songname'=>$song->Songname,'LangType'=>$song->LangType,'IsAverSong'=>1])->exists();
//            if($exists){
//                $post[$k]['State'] = 1;
//            }
        }

        try{
            $result = DB::table('users_songs')->insert($post);
        }catch (\Exception $e){
            return response()->json(['code' => 500, 'msg' => '请求失败', 'data' => $e->getMessage()]);
        }

        return response()->json(['code' => 200, 'msg' => '请求成功', 'data' => null]);

    }

    //场所预警接口
    public function placeWarning()
    {
        $srvkey = \Request::header('srvkey');
        if(empty($srvkey)){
            return response()->json(['code' => 500, 'msg' => '场所key错误', 'data' => null]);
        }
        $exists = DB::table('place')->where(['key'=>$srvkey])->exists();
        if(!$exists){
            return response()->json(['code' => 500, 'msg' => 'key不存在', 'data' => null]);
        }
        $date = date("Y-m-d H:i:s");

        $post = json_decode(file_get_contents("php://input"), true);

        $wangMode = DB::table('place')->where('key',$srvkey)->value('wangMode');
        $warnMode = DB::table('warningmode')->where('id',$wangMode)->first();
        $warnMode = get_object_vars($warnMode);

        $warningTime = $warnMode['warningTime'];
        $warningCountRoom = $warnMode['warningCountRoom'];
        $date1 = date("Y-m-d H:i:s",strtotime("-".$warningTime."seconds"));


        $warningTime1 = $warnMode['warningTime1'];
        $warningCountRoom1 = $warnMode['warningCountRoom1'];
        $date2 = date("Y-m-d H:i:s",strtotime("-".$warningTime1."seconds"));


        if($post){  //有传房间号
            $sql = "select KtvBoxid,count(*) as count from users_songs where srvkey= '$srvkey' group by KtvBoxid";
            $sql1 = "select KtvBoxid as roomno1,count(*) as count1 from users_songs where srvkey= '$srvkey' and UploadDate >= '$date1' AND  UploadDate <= '$date' and State > 0 group by KtvBoxid";
            $sql2 = "select KtvBoxid as roomno2,count(*) as count2 from users_songs where srvkey= '$srvkey' and UploadDate >= '$date2' AND  UploadDate <= '$date' and State > 0 group by KtvBoxid";

        $sql3 = "select * from ($sql) a left join ($sql1) b on a.KtvBoxid=b.roomno1 left join ($sql2) c on a.KtvBoxid=c.roomno2";

            $result3 = DB::select($sql3);

            $tempArray = [];

            foreach($post as $v){
                array_push($tempArray,$v['KtvBoxid']);
            }

            $post = [];

            $sum = 0;  //房间预警数
            $place = 0;  //只要房间有预警2，场所就预警2



            foreach($result3 as $kk=>$vv){

                if($vv->count2>=$warningCountRoom1){

                    if(in_array($vv->KtvBoxid,$tempArray)){

                        $post[$kk]['KtvBoxid'] = $vv-> KtvBoxid;
                        $post[$kk]['KtvBoxState'] = 2 ;

                    }

                    $place = 2;
                    $sum++;
                }elseif($vv->count1>=$warningCountRoom){
                    if(in_array($vv->KtvBoxid,$tempArray)){
                        $post[$kk]['KtvBoxid'] = $vv-> KtvBoxid;
                        $post[$kk]['KtvBoxState'] = 1  ;
                    }
                    $sum++;
                }else{
                    if(in_array($vv->KtvBoxid,$tempArray)){
                        $post[$kk]['KtvBoxid'] = $vv-> KtvBoxid;
                        $post[$kk]['KtvBoxState'] = 0 ;
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
            $sql = "select KtvBoxid,count(*) as count from users_songs where srvkey= '$srvkey' group by KtvBoxid";
            $sql1 = "select KtvBoxid as roomno1,count(*) as count1 from users_songs where srvkey= '$srvkey' and UploadDate >= '$date1' AND  UploadDate <= '$date' and State > 0 group by KtvBoxid";
            $sql2 = "select KtvBoxid as roomno2,count(*) as count2 from users_songs where srvkey= '$srvkey' and UploadDate >= '$date2' AND  UploadDate <= '$date' and State > 0 group by KtvBoxid";

        $sql3 = "select * from ($sql) a left join ($sql1) b on a.KtvBoxid=b.roomno1 left join ($sql2) c on a.KtvBoxid=c.roomno2";

            $result3 = DB::select($sql3);

            $post = [];
            $sum = 0;
            $place = 0;  //只要房间有预警2，场所就预警2

            foreach($result3 as $kk=>$vv){

                $post[$kk]['KtvBoxid'] = $vv-> KtvBoxid;
                if($vv->count2>=$warningCountRoom1){
                    $post[$kk]['KtvBoxState'] = 2 ;
                    $place = 2;
                    $sum++;

                }elseif($vv->count1>=$warningCountRoom){
                    $post[$kk]['KtvBoxState'] = 1  ;
                    $sum++;
                }else{
                    $post[$kk]['KtvBoxState'] = 0 ;
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

    //获取系统参数接口
    public function parameter(Request $request)
    {
        $srvkey = \Request::header('srvkey');
        if(empty($srvkey)){
            return response()->json(['code' => 500, 'msg' => '场所key错误', 'data' => null]);
        }
        $exists = DB::table('place')->where(['key'=>$srvkey])->exists();
        if(!$exists){
            return response()->json(['code' => 500, 'msg' => 'key不存在', 'data' => null]);
        }

        try{
            $result = DB::table('parameterset')->first();


        }catch (\Exception $e){
            return response()->json(['code' => 500, 'msg' => '请求失败', 'data' => $e->getMessage()]);
        }


        return json_encode(['code'=>200,'SoftwareName'=>$result->SoftwareName,'SoftwareVerno'=>$result->SoftwareVerno,'NewSongHttp'=>$result->NewSongHttp,'SpeedLimit'=>$result->SpeedLimit,'LoginName'=>$result->LoginName,
            'UpdateMode'=>$result->UpdateMode,'SoftseverVer'=>$result->SoftseverVer,'SoftseverHttp'=>$result->SoftseverHttp,'SoftseverMemo'=>$result->SoftseverMemo,'SoftboxVer'=>$result->SoftboxVer,'SoftboxHttp'=>$result->SoftboxHttp,'SoftboxMemo'=>$result->SoftboxMemo,'SoftsongDbVer'=>$result->SoftsongDbVer,'SoftsongDbHttp'=>$result->SoftsongDbHttp,'SingerPicHttp'=>$result->SingerPicHttp,'SongNmelHttp'=>$result->SongNmelHttp,'SongPicHttp'=>$result->SongPicHttp,'AppPicHttp'=>$result->AppPicHttp,'WarningAtoBtime'=>$result->WarningAtoBtime,'WechatPublicHttp'=>$result->WechatPublicHttp],320);


    }

    //获取歌曲下载地址接口
    public function downsonghttp(Request $request)
    {
        $srvkey = \Request::header('srvkey');
        if(empty($srvkey)){
            return response()->json(['code' => 500, 'msg' => '场所key错误', 'data' => null]);
        }
        $exists = DB::table('place')->where(['key'=>$srvkey])->exists();
        if(!$exists){
            return response()->json(['code' => 500, 'msg' => 'key不存在', 'data' => null]);
        }
        $place = DB::table('place')->where(['key'=>$srvkey])->select('status','expiredata')->first();
        if($place->status==0){
            return response()->json(['code' => 500, 'msg' => '场所未启用', 'data' => null]);
        }
        if(strtotime($place->expiredata)<time()){
            return response()->json(['code' => 500, 'msg' => '场所过期', 'data' => null]);
        }

        $post = json_decode(file_get_contents("php://input"), true);

        $parameterset = DB::table('parameterset')->first();
        $accessKey = $parameterset->AccessKey;
        $secretKey = $parameterset->SecretKey;
        $bucket = $parameterset->DomainNameSpace;
        $domain = $parameterset->Domain;

        // 构建Auth对象
        $auth = new Auth($accessKey, $secretKey);
        $config = new \Qiniu\Config();
        $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);

        if(!is_array( $post )){
            return response()->json(['code' => 500, 'msg' => '数据出错', 'data' => $post]);
        }
        foreach($post as $k=>$v){
            if(empty($v['musicdbpk'])){
                return response()->json(['code' => 500, 'msg' => 'musicdbpk不能为空', 'data' => null]);
            }
            $fileName = DB::table('song')->where('musicdbpk',$v['musicdbpk'])->value('Filename');

            $exist= $bucketManager->stat($bucket, $fileName);
            if(!isset($exist[0]['fsize'])){
                return response()->json(['code' => 500,'msg'=>'文件不存在' ,'data' => null]);
            }
            // 私有空间中的外链 http://<domain>/<file_key>
            $baseUrl = $domain."/".$fileName;
            // 对链接进行签名
            $signedUrl = $auth->privateDownloadUrl($baseUrl);

            $data[$k]['musicdbpk'] = $v['musicdbpk'];
            $data[$k]['downhttp'] = $signedUrl;
        }

//        return response()->json(['code' => 200, 'data' => $data]);
        return json_encode(['code' => 200, 'data' => $data],JSON_UNESCAPED_SLASHES);

    }

    //歌曲下载成功上传接口
    public function downsongok(Request $request)
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

        if(!is_array( $post )){
            return response()->json(['code' => 500, 'msg' => '数据出错', 'data' => $post]);
        }
        foreach($post as $k=>$v){
            $v['srvkey'] = $srvkey;
            $v['created_date'] = date('Y-m-d H:i:s');
            DB::table('songdownload')->insert($v);
        }

        return response()->json(['code' => 200, 'msg' => '请求成功','data'=>null]);

    }

    //机顶盒是否登记接口
    public function isboxreg(Request $request)
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

        if(empty($post['KtvBoxid'])){
            return response()->json(['code'=>500,'msg'=>'KtvBoxid不能为空','data'=>null]);
        }
        $exists = DB::table('boxregister')->where('KtvBoxid',$post['KtvBoxid'])->exists();
        if(!$exists){
            return response()->json(['code'=>500,'msg'=>'机器码不存在','data'=>null]);
        }
        return response()->json(['code' => 200, 'msg' => '已经登记','data'=>null]);
    }

    //场所获取广告接口
    public function posterList(Request $request)
    {
        $srvkey = \Request::header('srvkey');
        if(empty($srvkey)){
            return response()->json(['code' => 500, 'msg' => '场所key错误', 'data' => null]);
        }
        $exists = DB::table('place')->where(['key'=>$srvkey])->exists();
        if(!$exists){
            return response()->json(['code' => 500, 'msg' => 'key不存在', 'data' => null]);
        }

        $areaId = DB::table('place')->leftJoin('china_area', 'place.placArea', '=', 'china_area.code')->where(['key'=>$srvkey])->value('china_area.id');

        $host = $_SERVER['HTTP_HOST'];


        $sql = "select PosterTab.Poster_name,PosterTab.Enddate as enddate,PosterTab.Postertime,PosterTab.File_type,
        PosterTab.Poster_filename,PosterTab.Poster_content
        from PosterTab left join PosterSet on PosterTab.Poster_id=PosterSet.Poster_id 
        where (PosterSet.Ly_type=4 and PosterSet.Srvkey='$srvkey') or (PosterSet.Ly_type=3 and PosterSet.Areaid=$areaId) 
        or PosterSet.Ly_type=5 group by PosterTab.Poster_id";

        $data = DB::select($sql);

        $parameterset = DB::table('parameterset')->first();
        $accessKey = $parameterset->AccessKey;
        $secretKey = $parameterset->SecretKey;
        $bucket = $parameterset->posterDomainSpace;
        $domain = $parameterset->posterDomain;
        // 对链接进行签名
        $auth = new Auth($accessKey, $secretKey);

        foreach($data as $v){
            $baseUrl= $domain."/".$v->Poster_filename;
            $v->Posterhttp = $auth->privateDownloadUrl($baseUrl,86400);
        }
        return json_encode(['code'=>200,'data'=>$data],320);




    }


}

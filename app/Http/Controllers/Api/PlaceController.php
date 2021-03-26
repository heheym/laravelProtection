<?php

namespace App\Http\Controllers\Api;
use App\Admin\Guzzle\Guzzle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache; //缓存
use App\Api\Sms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use zgldh\QiniuStorage\QiniuStorage;
use Qiniu\Auth;

use Illuminate\Support\Facades\Log;


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

       // file_put_contents("1.txt",file_get_contents("php://input"));
       // var_dump(file_get_contents("php://input"));

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
                $id = DB::table('settopbox')->insertGetId($post);
                if(empty($post['roomno'])){
                    DB::table('settopbox')->where('id',$id)->update(['roomno'=>'Z'.$id]);
                }
            }catch (\Exception $e){
                return response()->json(['code' => 500, 'msg' => $e->getMessage(), 'data' => null]);
            }
        }
//  {   "KtvBoxid":"ADAABBFFSSDD",//机器码
// 	"boxstate":1,//状态: 0=待审核,1=正常,2=返修,3=过期,4=作废
// "placeno": "1610349985", //场所编号(可空)
// "startdate": "2022-01-12"  //启用时间
// }
        $boxRegisterExist = DB::table('boxregister')->where('KtvBoxid',$post['KtvBoxid'])->value('isOem');
        if(!isset($boxRegisterExist)){
            return response()->json(['code' => 500, 'msg' => '机器码未登记', 'data' => null]);
        }
        $json = ['KtvBoxid'=>$post['KtvBoxid'],'startdate'=>date('Y-m-d H:i:s'),'isOem'=>0];
        if(isset($post['KtvBoxState'])){
            $json['boxstate'] = $post['KtvBoxState'];
        }
        $guzzle = new Guzzle();
        $guzzle->boxupdate($json);

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

        return response()->json(['code' => 200,
            'placename'=>$result->placename,'placeaddress'=>$result->placeaddress,'phone'=>$result->phone,
            'paytest'=>$result->paytest,
            'roomtotal' => $result->roomtotal, 'expiredata' => $result->expiredata,
            'remainday'=>$t,'placehd'=>$result->placehd,'isenabled'=>$result->status,'boxPass'=>$result->boxPass,'downloadMode'=>$result->downloadMode,'apkUpdateMode'=>$result->apkUpdateMode,
            'isclosePingfen'=>$result->isclosePingfen,
            'iscloseSound'=>$result->iscloseSound,
            'iscloseVoice' => $result->iscloseVoice,
            'FeesMode'=>$result->FeesMode,
            'FeesScanMode'=>$result->FeesScanMode,
            'balanceSum'=>$result->balanceSum,
            'Opening1_time'=>$result->Opening1_time,'Opening1_price'=>$result->Opening1_price,'Effective1_time'=>$result->Effective1_time,
            'Opening2_time'=>$result->Opening2_time,'Opening2_price'=>$result->Opening2_price,'Effective2_time'=>$result->Effective2_time,
            'warningCutsongcount'=>$result->warningCutsongcount,
            'warningCutsongcounttime'=>$result->warningCutsongcounttime,
            'warningCutcompanycount'=>$result->warningCutcompanycount,
            'warningCutcompanycounttime'=>$result->warningCutcompanycounttime,
            'warningRoomcount'=>50,
            'warningRoomtime'=>30,
//            'warningCutsongcount'=>8,
            'warningCutsongtime'=>30,
            'isBuyCopyrightfee'=>$result->isBuyCopyrightfee,
            'shoppingMallId'=>$result->shoppingMallId,
            'publicPlaycount'=>$result->publicPlaycount,
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
//        Log::getMonolog()->popHandler();
//        Log::useDailyFiles(storage_path('logs/usersong.log'));

        $srvkey = \Request::header('srvkey');

        if(empty($srvkey)){
            return response()->json(['code' => 500, 'msg' => '场所key错误', 'data' => null]);
        }
        $exists = DB::table('place')->where(['key'=>$srvkey])->exists();
        if(!$exists){
            return response()->json(['code' => 500, 'msg' => 'key不存在', 'data' => null]);
        }
//        Log::info(file_get_contents("php://input").PHP_EOL);
        $post = json_decode(file_get_contents("php://input"), true);

//        file_put_contents('1.txt',file_get_contents("php://input"));
        if(!is_array( $post )){
            return response()->json(['code' => 500, 'msg' => '数据出错', 'data' => $post]);
        }
        $temp = [];
        foreach($post as $k=>$v){
            if(empty($v['KtvBoxid'])||empty($v['musicdbpk'])){
                return response()->json(['code' => 500, 'msg' => '机器码或歌曲主键不能为空', 'data' => null]);
            }
            if(!isset($v['playtime']) || !isset($v['State'])){
                return response()->json(['code' => 500, 'msg' => '时间或预警类型不能为空', 'data' => null]);
            }
            $temp[$k]['srvkey'] = $srvkey;
            $temp[$k]['KtvBoxid'] = trim($v['KtvBoxid']);
            $temp[$k]['musicdbpk'] = trim($v['musicdbpk']);
            $temp[$k]['UploadDate'] = $v['playtime'];
            $temp[$k]['State'] = $v['State'];
            $temp[$k]['Playfile'] = isset($v['Playfile'])?$v['Playfile']:'';
        }

        try{
            $result = DB::table('users_songs')->insert($temp);
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
            'UpdateMode'=>$result->UpdateMode,'SoftseverVer'=>$result->SoftseverVer,'SoftseverHttp'=>$result->SoftseverHttp,'SoftseverMemo'=>$result->SoftseverMemo,'SoftboxVer'=>$result->SoftboxVer,'SoftboxHttp'=>$result->SoftboxHttp,'SoftboxMemo'=>$result->SoftboxMemo,'SoftsongDbVer'=>$result->SoftsongDbVer,'SoftsongDbHttp'=>$result->SoftsongDbHttp,'SoftsongSqlFile'=>$result->SoftsongSqlFile,'SingerPicHttp'=>$result->SingerPicHttp,'SongNmelHttp'=>$result->SongNmelHttp,'SongPicHttp'=>$result->SongPicHttp,'AppPicHttp'=>$result->AppPicHttp,'WarningAtoBtime'=>$result->WarningAtoBtime,'WechatPublicHttp'=>$result->WechatPublicHttp,'ServerTime'=>date('Y-m-d H:i:s')],320);
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

    //唱片公司异常接口
    public function companyWarning(Request $request)
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
        $days = !empty($post['days'])?$post['days']:30;

        $enddate = date('Y-m-d');
        $startdate = date('Y-m-d',strtotime("-$days days"));
//        var_dump($enddate.$startdate);

        $sql = "select RecordCompany as recordCompany,count(*) as abnormalCount from warningcompany where creatdate>='$startdate' and creatdate<='$enddate'  group by RecordCompany";

        $data = DB::select($sql);

        return response()->json(['code' => 200,'data'=>$data]);
    }

    ///紧急下架歌曲api/songs/urgentDelsong
    public function urgentDelsong(Request $request)
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
        if(empty($post['SoftsongDbVer'])){
            return response()->json(['code' => 500, 'msg' => 'SoftsongDbVer错误', 'data' => null]);
        }
        $data = DB::table('urgentDelsong')->where('SoftsongDbVer','>=',$post['SoftsongDbVer'])->select('musicdbpk')->get();
        return response()->json(['code' => 200,'data'=>$data]);
    }

    //紧急预警唱片公司
    public function urgentCompany()
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
        foreach($post as $k=>$v){
            if(empty($v['companyid']) || empty($v['occurrencetime'])){
                return response()->json(['code' => 500, 'msg' => 'companyid或occurrencetime不能为空', 'data' => null]);
            }
            $v['srvkey'] = $srvkey;
            $exist = DB::table('urgentCompany')->where(['companyid'=>$v['companyid']])->exists();
            if($exist){
                try{
                    DB::table('urgentCompany')->where(['companyid'=>$v['companyid']])->update($v);
                }catch(\Exception $e){
            return response()->json(['code' => 500, 'msg' => '数据错误', 'data' => $e->getMessage()]);
                }
            }
            else{
                try{
                    DB::table('urgentCompany')->insert($v);
                }catch (\Exception $e){
            return response()->json(['code' => 500, 'msg' => '数据错误', 'data' => $e->getMessage()]);
                }
            }
        }
        return response()->json(['code' => 200, 'msg' => '请求成功', 'data' => null]);
    }

//获取紧急预警唱片公司列表
    public function urgentCompanylist()
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
        $days = isset($post['days']) ? $post['days'] : 2;
        $time = strtotime('-'.$days.' day', time());
        $beginTime = date('Y-m-d 00:00:00', $time);
$data = DB::table('urgentCompany')->where([['occurrencetime','>',$beginTime]])->select('companyid')->get();
        return response()->json(['code' => 200,'data' => $data]);
    }

//场所新增提交补歌接口
    public function busongAdd()
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
        try{
            $exists = DB::table('song')->where(['Songname'=>$post['songname'],'Singer'=>$post['singer']])->exists();
        }catch (\Exception $e){
            return response()->json(['code' => 500, 'msg' => '格式错误', 'data' => $e->getMessage()]);
        }
        if($exists){
            return response()->json(['code' => 500, 'msg' => '曲库中已存在', 'data' => null]);
        }

        try{
            $buSongExists = DB::table('busong')->where(['svrkey'=>$srvkey,'songname'=>$post['songname'],'singer'=>$post['singer']])->exists();
        }catch (\Exception $e){
            return response()->json(['code' => 500, 'msg' => '格式错误', 'data' => $e->getMessage()]);
        }
        if(!$buSongExists){
            $post['svrkey'] = $srvkey;
            DB::table('busong')->insert($post);
        }else{
            DB::table('busong')->where(['svrkey'=>$srvkey,'songname'=>$post['songname'],'singer'=>$post['singer']])->update(['createdate'=>date('Y-m-d H:i:s')]);
        }
        return response()->json(['code' => 200, 'msg' => '请求成功', 'data' => null]);

    }

//补歌数据查询接口
    public function busongplacelist()
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
        $currentPage = isset($post['current_page']) ? $post['current_page'] : 1;
        $itemPerPage = 10;

        $where = [];
        $where[] = ['svrkey','=',$srvkey];
        if(isset($post['startdate'])){
            $where[] = ['createdate','>=',$post['startdate']];
        }
        if(isset($post['enddate'])){
            $where[] = ['createdate','<=',$post['enddate']." 23:59:59"];
        }
        $data = DB::table('busong')->where($where)->offset(($currentPage-1)*$itemPerPage)->limit($itemPerPage)->get();
        $count = DB::table('busong')->where($where)->count();
        $totoalPage = ceil($count/$itemPerPage);

        return response()->json(['code'=>200,'current_page'=>$currentPage,'totoal_page'=>$totoalPage,'data'=>$data]);

    }


    //上传异常支付帐号接口
    public function urgentPaymentno()
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
        if(!isset($post['notype']) || !isset($post['paymentno'])){
            return response()->json(['code' => 500, 'msg' => '账号类型或支付帐号不能为空', 'data' => null]);
        }
        $exists = DB::table('urgentPaymentno')->where(['notype'=>$post['notype'],'paymentno'=>$post['paymentno']])->exists();
        if($exists){
            DB::table('urgentPaymentno')->where(['notype'=>$post['notype'],'paymentno'=>$post['paymentno']])->update(['createdate'=>date('Y-m-d H:i:s')]);
        }else{
            try{
                DB::table('urgentPaymentno')->insert($post);
            }catch (\Exception $e){
                return response()->json(['code' => 500, 'msg' => '数据错误', 'data' => $e->getMessage()]);
            }
        }
        return response()->json(['code'=>200,'msg'=>'请求成功','data'=>null]);
    }

    //获取异常支付帐号列表
    public function urgentPaymentlist()
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
        $days = isset($post['days']) ? $post['days'] : 30;

        $time = strtotime('-'.$days.' day', time());
        $beginTime = date('Y-m-d 00:00:00', $time);

        $where[] = ['createdate','>',$beginTime];
        if(isset($post['notype'])){
            $where[] = ['notype','=',$post['notype']];
        }
        try{
            $data = DB::table('urgentPaymentno')->where($where)->select('notype','paymentno')->get();
        }catch (\Exception $e){
            return response()->json(['code' => 500, 'msg' => '数据错误', 'data' => $e->getMessage()]);
        }
        return response()->json(['code'=>200,'data'=>$data]);
    }

    //获取异常支付帐号列表
    public function uploadfile()
    {
        if(!isset($_FILES["file"]["error"]) ){
            return response()->json(['code' => 500, 'msg' => '参数错误', 'data' => null]);
        }
        if ($_FILES["file"]["error"] > 0)
        {
            return response()->json(['code' => 500, 'msg' => '上传出错', 'data' => null]);
        }
        else
        {
//            echo "上传文件名: " . $_FILES["file"]["name"] . "<br>";
//            echo "文件类型: " . $_FILES["file"]["type"] . "<br>";
//            echo "文件大小: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
//            echo "文件临时存储的位置: " . $_FILES["file"]["tmp_name"] . "<br>";
            $dir = "../upload/";
            if (!is_dir($dir)){
                mkdir($dir, 0777);
            }
                // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
                move_uploaded_file($_FILES["file"]["tmp_name"], $dir. $_FILES["file"]["name"]);
                return response()->json(['code' => 200, 'msg' => $_FILES["file"]["name"].'上传成功', 'data' => null]);
//            }
        }
    }

    //开关房记录上传接口
    public function ktvonoff()
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
        if(!isset($post)){
            return response()->json(['code' => 500, 'msg' => '数据为空', 'data' => null]);
        }

        foreach($post as $k=>$v){
            $exists = DB::table('users_openclose')->where(['srvkey'=>$srvkey,'KtvBoxid'=>$v['KtvBoxid'],'opendate'=>$v['opendate']])->exists();
            if($exists){
                try{
                    $data = DB::table('users_openclose')->where(['srvkey'=>$srvkey,'KtvBoxid'=>$v['KtvBoxid'],'opendate'=>$v['opendate']])->update($v);
                }catch (\Exception $e){
                return response()->json(['code' => 500, 'msg' => '保存错误', 'data' => $e->getMessage()]);
                }
            }else{
                $v['srvkey'] = $srvkey;
                try{
                    $data = DB::table('users_openclose')->insert($v);
                }catch (\Exception $e){
                return response()->json(['code' => 500, 'msg' => '保存错误', 'data' => $e->getMessage()]);
                }
            }
        }
        return response()->json(['code'=>200,'msg'=>'请求成功','data'=>null]);
    }

    //热点歌曲可预先下载列表
    public function hotspotsong()
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
        if(!isset($post['SoftsongDbVer'])){
            return response()->json(['code' => 500, 'msg' => 'SoftsongDbVer不能为空', 'data' => null]);
        }
        $data = DB::table('hotspotsong')->where('SoftsongDbVer',$post['SoftsongDbVer'])->select('musicdbpk')->get();
        return response()->json(['code' => 200,'data' => $data]);
    }

    //场所换房接口
    public function exchangroom()
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
        if(!isset($post['oldKtvBoxid']) || !isset($post['newKtvBoxid'])){
            return response()->json(['code' => 500, 'msg' => 'oldKtvBoxid或newKtvBoxid不能为空', 'data' => null]);
        }
        $exists1 = DB::table('settopbox')->where(['KtvBoxid'=>$post['oldKtvBoxid']])->value('key');
        $exists2 = DB::table('settopbox')->where(['KtvBoxid'=>$post['newKtvBoxid']])->value('key');
        if(!$exists1){
            return response()->json(['code' => 500, 'msg' => 'oldKtvBoxid不存在', 'data' => null]);
        }
        if(!$exists2){
            return response()->json(['code' => 500, 'msg' => 'newKtvBoxid不存在', 'data' => null]);
        }
        if($exists1 != $exists2){
            return response()->json(['code' => 500, 'msg' => '两个机顶盒不在同一场所', 'data' => null]);
        }

        $ordersn = DB::table('ordersn')->where(['KtvBoxid'=>$post['oldKtvBoxid'],'order_status'=>1])->orderBy('pay_time','desc')->first();
        if(!isset($ordersn->id) || $ordersn->pay_time < date('Y-m-d')){
            return response()->json(['code' => 500, 'msg' => '该房间今天没有开房', 'data' => null]);
        }else{
            DB::table('ordersn')->where('id',$ordersn->id)->update(['KtvBoxid'=>$post['newKtvBoxid']]);
        }
        $post['ordersnId'] = $ordersn->id;
        $post['exchangDate'] = date('Y-m-d H:i:s');
        try{
            $data = DB::table('exchangroom')->insert($post);
        }catch(\Exception $e){
            return response()->json(['code' => 500, 'msg' => '保存错误', 'data' => $e->getMessage()]);
        }

        return response()->json(['code' => 200,'msg' => '请求成功','data'=>null]);
    }

//预付款开房扣费接口
    public function openfeeroom()
    {
        $signature = \Request::header('signature');
        if(empty($signature)){
            return response()->json(['code' => 500, 'msg' => 'signature错误', 'data' => null]);
        }

        $post = json_decode(file_get_contents("php://input"), true);
        if(empty($post['srvkey']) || empty($post['timestamp'])){
            return response()->json(['code' => 500, 'msg' => 'srvkey或timestamp不能为空', 'data' => null]);
        }
        if(empty($post['KtvBoxid']) || empty($post['paymentmoney'])){
            return response()->json(['code' => 500, 'msg' => 'KtvBoxid或paymentmoney不能为空', 'data' => null]);
        }

        $signature1 = md5($post['srvkey'].$post['timestamp']."20210326f5ce6dce860673c2b0ec458a96ddfd");
        if($signature !== $signature1){
            return response()->json(['code' => 500, 'msg' => 'signature不正确', 'data' => null]);
        }

        $srvkey = $post['srvkey'];
        $KtvBoxid = $post['KtvBoxid'];
        $paymentmoney = $post['paymentmoney'];

        $place = DB::table('place')->where(['key'=>$srvkey])->first();
        if(empty($place->key)){
            return response()->json(['code' => 500, 'msg' => 'key不存在', 'data' => null]);
        }

        if($place->balanceSum < $paymentmoney){
            return response()->json(['code' => 300, 'msg' => '余额不足','balanceSum'=> $place->balanceSum , 'data' => null]);
        }
        $result = DB::table('place')->where(['key'=>$srvkey])->decrement('balanceSum',$paymentmoney);
        if($result){
            $place = DB::table('place')->where(['key'=>$srvkey])->first();
            $rechargeListData = [
                'KtvBoxid' => $KtvBoxid,
                'srvkey' => $srvkey,
                'paymentmoney' => $paymentmoney,
                'payment_type' => 1,
                'createDate' => date('Y-m-d H:i:s'),
            ];
            DB::table('rechargeList')->insert($rechargeListData);
            return response()->json(['code' => 200, 'msg' => '请求成功','balanceSum'=> $place->balanceSum , 'data' => null]);
        }else{
            return response()->json(['code' => 500, 'msg' => '请求失败', 'data' => null]);
        }

    }


//查询场所预付款余额接口
    public function remainingsum()
    {
        $srvkey = \Request::header('srvkey');
        if(empty($srvkey)){
            return response()->json(['code' => 500, 'msg' => '场所key错误', 'data' => null]);
        }
        $exists = DB::table('place')->where(['key'=>$srvkey])->exists();
        if(!$exists){
            return response()->json(['code' => 500, 'msg' => 'key不存在', 'data' => null]);
        }

        $place = DB::table('place')->where('key',$srvkey)->first();

        return response()->json(['code' => 200, 'msg' => '请求成功','FeesScanMode'=> $place->FeesScanMode , 'balanceSum'=>$place->balanceSum,
            'data' => null]);

    }


}

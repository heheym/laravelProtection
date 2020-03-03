<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache; //缓存
use App\Api\Sms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class FeeController extends Controller
{
    //服务端信息更新
    public function setMealKtv(Request $request)
    {
        $tonkey = \Request::header('tonkey');
        $tonkey = explode(' ',$tonkey);

        if(empty($tonkey[0])){
            return response()->json(['code' => 500, 'msg' => '场所key错误', 'data' => null]);
        }
        if(empty($tonkey[1])){
            return response()->json(['code' => 500, 'msg' => 'placehd错误', 'data' => null]);
        }
        $exists = DB::table('place')->where(['key'=>$tonkey[0]])->exists();
        if(!$exists){
            return response()->json(['code' => 500, 'msg' => 'key不存在', 'data' => null]);
        }


        try{
            $place = DB::table('place')->where('key', $tonkey[0])->select('roomtotal','expiredata')->first();
            $data = DB::table('setMeal')->select('setMeal_id','setMeal_days','setMeal_mode','setMeal_name',
                'setMeal_price','validDate','setMeal_giveDays')->get()->map(function ($value) {return (array)$value;})
                ->toArray();

            foreach($data as $k=>$v){
                if($v['setMeal_mode'] == 1){
                    $data[$k]['setMeal_totalprice'] = $v['setMeal_price']*$place->roomtotal;
                }elseif($v['setMeal_mode'] == 2){
                $data[$k]['setMeal_totalprice'] = $v['setMeal_price'];
                }
            }
        }catch (\Exception $e){
            return response()->json(['code' => 500, 'msg' => '传入信息出错', 'data' => $e->getMessage()]);
        }

        $t1 = $place->expiredata;//你自己设置一个开始时间
        $t2 = date('Y-m-d H:i:s');//获取当前时间, 格式和$t1一致

        $t = strtotime($t1) - strtotime($t2);//拿当前时间-开始时间 = 相差时间
        $t = ceil($t/(3600*24));//此时间单位为 天
//        $t = $t/(3600*24);//此时间单位为 天
        if($t<0){
            $t=0;
        }

        return response()->json(['code' => 200, 'roomtotal' => $place->roomtotal, 'expiredata' => $place->expiredata,
            'remainday'=>$t,'data'=>$data]);
    }

}

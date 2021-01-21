<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Place extends Model
{
    //
    public $table  = 'place';
    public $timestamps  = false;
    protected $hidden = [

    ];
    protected $appends = ['merchant','merchantfirst'];


    // public function province()
    // {
    //     return $this->hasOne(ChinaArea::class,'code','province');
    // }
    //
    // public function city()
    // {
    //     return $this->hasOne(ChinaArea::class,'code','city');
    // }

    public function merchantset(){
        return $this->hasMany(MerchantSet::class , 'svrkey','key');
    }

    /**
     * @return mixed
     * 商户号
     */
    public function getMerchantfirstAttribute()
    {
        if($this->merchantset){
            $key = $this->key;  //子表id
            $merchant = DB::table('MerchantSet')
                ->where(['svrkey'=>$key,'isMain'=>1])
                ->get()
                ->map(function ($value) {return (array)$value;})->toArray();
            if(empty($merchant)){
                $merchant = [[
      'svrkey' => $this->key,
      'shareproportion' => '10']];
            }
            return $merchant;

        }
    }

    /**
     * @return mixed
     * 商户号
     */
    public function setMerchantfirstAttribute($extra)
    {
//        dd($extra);
        if($this->merchantset){
            $key = $this->key;  //子表id
            foreach($extra as $k=>$v){
                $extra[$k]['svrkey'] = $this->key;
                $extra[$k]['isMain'] = 1;
            }
            DB::table('MerchantSet')->where('svrkey',$key)->delete();
            DB::table('MerchantSet')->insert($extra);
        }
    }

    /**
     * @return mixed
     * 商户号
     */
    public function getMerchantAttribute()
    {
        if($this->merchantset){
            $key = $this->key;  //子表id
            $merchant = DB::table('MerchantSet')
                ->where('svrkey','=',$key)
                ->skip(1)
                ->take(99)
                ->get()
                ->map(function ($value) {return (array)$value;})->toArray();

            return $merchant;
        }
    }

    /**
     * @return mixed
     * 商户号
     */
    public function setMerchantAttribute($extra)
    {
//        dd($extra);
        if($this->merchantset){
            $key = $this->key;  //子表id
            foreach($extra as $k=>$v){
                $extra[$k]['svrkey'] = $this->key;
            }
            DB::table('MerchantSet')->insert($extra);
        }
    }

//    public function province(){
//        return $this->hasOne(ChinaArea::class , 'code','province');
//    }
}

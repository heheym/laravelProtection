<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class SoftVersion extends Model
{
    protected $table = 'softversion';
    // protected $primaryKey = 'Poster_id';
    public $timestamps = false;
    // protected $appends = ['place','address'];

//     public function posterset()
//     {
//         return $this->hasMany(PosterSet::class,'Poster_id','Poster_id');
//     }
//
//     public function toArray()
//     {
//         $toArray = parent::toArray();
//         $toArray['Poster_name'] = $this->Poster_name;
// //        $toArray['Poster_filename'] = $this->Poster_Poster_filename;
//         return $toArray;
//     }
//
//     public function getPlaceAttribute($value)
//     {
//         if($this->posterset){
//             $Srvkey = $this->posterset()->get(['Srvkey']);
//
//             $data =  DB::table('place')->whereIn('key',$Srvkey)->pluck('placename','key');
//             $temp = [];
//
//             foreach($data as $k=>$v){
//                 $temp[$v] = $k;
//             }
//             return $temp;
//         }
//
//     }
//
//
//     public function getAddressAttribute($value)
//     {
//
//     }
//
//     public function getPosterFilenameAttribute($value)
//     {
//         return 'uploads/files/'.$value;
//     }
// //    public function setPosterFilenameAttribute($value)
// //    {
// //        dd(123);
// //        $res = explode('/', $value);//$value  是upoades/images/map/1.jpg
// //        $this->attributes['Poster_filename'] = end($res);//1.jpg9
// ////        $this->attributes['Poster_filename'] = 456;//1.jpg9
// //    }
//
//
//
//     public static function boot()
//     {
//         parent::boot();
//         static::saving(function ($model) {
//             define("Place",$model->attributes['place']);
//             define("Address",$model->attributes['address']);
//             unset($model->place);
//             unset($model->address);
// //            dd($model);
//         });
//
//         static::saved(function (Model $model){
//             DB::table('PosterSet')->where('Poster_id',$model->Poster_id)->delete();
//             //场所
//             foreach(Place as $k=> $v){
//                 $data[$k]= ['Poster_id'=>$model->Poster_id,'Ly_type'=>4];
//                 $data[$k]['Srvkey']= $v;
//             }
//             if(!empty($data)){
//                 DB::table('PosterSet')->insert($data);
//             }
//             if(!empty(Address)){
//                 if(Address == 1){
//                     DB::table('PosterSet')->insert(['Poster_id'=>$model->Poster_id,'Ly_type'=>5]);
//                 }else{
//                     $arr = explode(',',Address);
//                     foreach($arr as $k=>$v){
//                         $data[$k]= ['Poster_id'=>$model->Poster_id,'Ly_type'=>3];
//                         $data[$k]['Areaid']= $v;
//                     }
//                     DB::table('PosterSet')->insert($data);
//                 }
//             }
//
//         });
//     }


}

<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SetTopBox extends Model
{
    protected $table = 'settopbox';
    public $timestamps = false;

    public function place(){
        return $this->belongsTo(Place::class, 'key' , 'key');
    }

    public static function boot()
    {
        parent::boot();


        static::saving(function ($model) {
//            dd($model->id);
//            $model->roomno = !empty($model->roomno)?$model->roomno:'J'.$model->id;
        });

        static::saved(function (Model $model) {
            $roomno = DB::table('settopbox')->where('id',$model->id)->value('roomno');
            if(empty($roomno)){
                DB::table('settopbox')->where('id',$model->id)->update(['roomno'=>'J'.$model->id]);
            }
        });
    }
}

<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Promote extends Model
{
    protected $table = 'promote';
    public $timestamps = false;


//    public function promoteSong(){
//        return $this->hasMany(PromoteSong::class , 'promoteId');
//    }

    public function getSongAttribute($extra)
    {
//        var_dump(array_values(json_decode($extra, true) ?: []));
        return array_values(json_decode($extra, true) ?: []);
    }

    public function setSongAttribute($extra)
    {
        $this->attributes['song'] = json_encode(array_values($extra));
    }


//    public function getSongnumAttribute($value){
//        return 123;
//    }
}

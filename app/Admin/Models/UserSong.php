<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class UserSong extends Model
{
     protected $table = 'users_songs';
//    protected $primaryKey  = 'musicdbpk';
    public $timestamps  = false;
    public function place(){
        return $this->belongsTo(Place::class, 'srvkey' , 'key');
    }
}

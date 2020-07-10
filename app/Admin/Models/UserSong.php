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
//
    public function settopbox()
    {
        return $this->belongsTo(SetTopBox::class, 'KtvBoxid' , 'KtvBoxid');
    }

    public function song()
    {
        return $this->belongsTo(Song::class, 'musicdbpk' , 'musicdbpk');
    }

}
